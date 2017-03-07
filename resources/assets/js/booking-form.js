class BookingForm {

	// static getMonth_name(){
	// 	return  ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	// }
	static month_name(){
		return  ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	}


	constructor(){
		let calendarDiv = $('#calendar-box');

		this.calendar = calendarDiv.Calendar();
		this.day_tds  = calendarDiv.find('td.day');
		this.label    = document.querySelector('#reservation_date');
		this.select   = document.querySelector('select[name="reservation_time"]');
		this.form     = document.querySelector('#booking-form');

		this.adult_pax_select    = document.querySelector('select[name="adult_pax"]');
		this.children_pax_select = document.querySelector('select[name="children_pax"]');

		this.ajax_dialog   = $('#ajax-dialog');

		this.outlet_select = document.querySelector('select[name="outlet_id"]');
		this.inpute_date   = document.querySelector('input[name="reservation_date"]');
		this.input_outlet = document.querySelector('input[name="outlet_name"]');

	}

	regisEvent(){
		this.listenUserSelectDay();
		this.listenHasAjaxResponse();

		this._regisPaxChange(this.adult_pax_select);
		this._regisPaxChange(this.children_pax_select);
		this._regisPaxChange(this.outlet_select, 'outlet-change');

		this.listenPaxChange();

		this.listenLoadingDialog();
		this.listenStopDialog();

		this.listenOutletChange();
	}

	_regisPaxChange(element, eventName){
		eventName = eventName || "pax-change";

		element.onchange = function(e){
			if(!e.target.value) return;

			//let num_pax = e.target.value;
			let select_name = element.getAttribute('name');

			var event = new CustomEvent(eventName, {
				detail: {select_name},
				bubbles: true,
				cancelable: true
			});

			element.dispatchEvent(event);

		};
	}

	listenUserSelectDay(){
		let scope = this;
		document.addEventListener('user-select-day', function(e){
			scope.changeLabel(e);
			scope.setInputReservationDate(e);
			scope.ajaxAvailableTime(e);
			scope.storeSelectedDay(e);
		});
	}

	changeLabel(e){
		let d = this._getDate(e.detail.day);

		let month_name = BookingForm.month_name();

		this.label.innerText = `${month_name[d.getMonth()]} ${d.getDate()} ${d.getFullYear()}`;
	}

	setInputReservationDate(e){
		let d = this._getDate(e.detail.day);

		this.inpute_date.value = d.toISOString().substr(0, 10);
	}

	setInputOutletName(){
		let selectedOption = this.outlet_select.selectedOptions[0];
		if(typeof selectedOption == 'undefined')
			return

		let outlet_name         = selectedOption.innerText;
		this.input_outlet.value = outlet_name;
	}

	ajaxAvailableTime(e){
		//ajax request
		//ask for available date base on form info
		let form = this.form;
		let data = $(form).serializeArray().reduce((carry, item) =>{
			carry[item.name] = item.value;

			return carry;
		}, {});

		let selectElement = this.select;

		var loadingDialog = new CustomEvent("loading-dialog", {bubbles: true, cancelable: true});

		form.dispatchEvent(loadingDialog);

		$.ajax({
			url: '',
			method: 'POST',
			data,
			success(res){
				console.log(res);

				var event = new CustomEvent("has-ajax-response", {
					detail: {res},
					bubbles: true,
					cancelable: true
				});

				selectElement.dispatchEvent(event);
			},
			error(res){
				console.log(res);
			}
		});
	}

	storeSelectedDay(e){
		let day_info = e.detail.day.split('-');
		this.selected_day = `${day_info[0]}-${this._prefix2Dec(day_info[1])}-${this._prefix2Dec(day_info[2])}`;
	}

	_getDate(day_str){
		return new Date(day_str);
	}

	listenHasAjaxResponse(){
		let scope = this;
		document.addEventListener('has-ajax-response', function(e){
			scope.updateCalendarView(e);
			scope.updateSelectView(e);

			document.dispatchEvent(new CustomEvent('stop-dialog'));
		});
	}

	updateCalendarView(e){
		let scope = this;
		let res = e.detail.res;

		let available_days = Object.keys(res);

		this.day_tds.each(function(){
			let td = $(this);
			let td_day_str = `${td.attr('year')}-${scope._prefix2Dec(td.attr('month'))}-${scope._prefix2Dec(td.attr('day'))}`;

			if(available_days.includes(td_day_str)){
				scope._pickable(td);
			}else{
				scope._unpickable(td);
			}
		});
	}

	_prefix2Dec(val){
		if(val < 10)
			return `0${val}`;

		return val;
	}

	_pickable(td){
		td.removeClass('past');
		td.addClass('day');
	}

	_unpickable(td){
		td.removeClass('day');
		td.addClass('past');
	}

	updateSelectView(e){
		let res = e.detail.res;
		let selected_day_str = this.selected_day || new Date().toISOString().substr(0, 10);

		let available_times = res[selected_day_str];
		if(typeof available_times == 'undefined')
			return;

		if(available_times.length == 0){
			let default_time = {
				time: 'N/A',
				session_name: ''
			};

			available_times.push(default_time);
		}

		let selectDiv = this.select;
		//reset selectDiv options
		selectDiv.innerHTML = '';
		available_times.forEach(time => {
			//console.log(time);
			let optionDiv = document.createElement('option');

			optionDiv.setAttribute('value', time.time);
			//noinspection JSUnresolvedVariable
			optionDiv.innerText = time.session_name + ' ' + time.time;

			selectDiv.appendChild(optionDiv);
		});
	}

	listenPaxChange(){
		let scope = this;
		document.addEventListener('pax-change', function(e){
			let select_name = e.detail.select_name;
			let shouldCallAjax = true;
			if(typeof scope[`${select_name}Changed`] == 'undefined'){
				scope[`${select_name}Changed`] = true;
				shouldCallAjax = false;
			}

			if(shouldCallAjax){
				scope.ajaxAvailableTime(e);
			}
		});
	}

	listenLoadingDialog(){
		let scope = this;
		document.addEventListener('loading-dialog', function(e){
			console.log('loading dialog');
			scope.ajax_dialog.modal('show');
		});
	}

	listenStopDialog(){
		let scope = this;
		document.addEventListener('stop-dialog', function(e){
			console.log('stop dialog');
			scope.ajax_dialog.modal('hide');
		});
	}

	listenOutletChange(){
		let scope = this;
		document.addEventListener('outlet-change', function(e){
			scope.setInputOutletName(e);

			let shouldCallAjax = true;

			if(typeof scope.outletChanged == 'undefined'){
				scope.outletChanged = true;
				shouldCallAjax = false;
			}

			if(shouldCallAjax){
				scope.ajaxAvailableTime(e);
			}

		});
	}

}

// calendarHandler($);
let bookingForm = new BookingForm();
bookingForm.regisEvent();
