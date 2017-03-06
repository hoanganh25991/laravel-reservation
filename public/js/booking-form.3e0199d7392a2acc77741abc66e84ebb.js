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
		this.day_tds = calendarDiv.find('td.day');
		this.label = document.querySelector('#reservation_time');
		this.select = document.querySelector('select[name="reservation_time"]');
		this.form = document.querySelector('#booking-form');
	}

	regisEvent(){
		this.listenUserSelectDay();
		this.listenHasAjaxResponse();
	}

	listenUserSelectDay(){
		let scope = this;
		document.addEventListener('user-select-day', function(e){
			scope.changeLabel(e);
			scope.ajaxAvailableTime(e);
			scope.storeSelectedDay(e);
		});
	}

	changeLabel(e){
		let d = this._getDate(e.detail.day);

		let month_name = BookingForm.month_name();

		this.label.innerText = `${month_name[d.getMonth()]} ${d.getDate()} ${d.getFullYear()}`;
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
		if(typeof res[selected_day_str] == 'undefined')
			return;

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


}

// calendarHandler($);
let bookingForm = new BookingForm();
bookingForm.regisEvent();
