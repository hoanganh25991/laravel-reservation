class BookingForm {
	/** @namespace action.adult_pax */
	/** @namespace action.children_pax */
	/** @namespace action.dialog_has_data */
	/** @namespace action.exceed_min_exist_time */

	/** @namespace window.booking_form_state */
	/** @namespace $ */
	/** @namespace moment */

	constructor(){
		this.buildRedux();
		this.bindView();
		this.regisEvent();

		this.initView();
	}

	buildRedux(){
		//assign default state
		//may from server
		//or self build
		this.state = this.defaultState();
		let scope = this;
		let reducer = Redux.combineReducers({
			available_time: scope.buildAvailableTimeReducer(),
			reservation: scope.buildReservationReducer(),
			init_view: scope.buildInitViewReducer(),
			outlet: scope.buildOutletReducer(),
			dialog: scope.buildDialogReducer(),
			pax: scope.buildPaxReducer(),
			ajax_call: scope.buildAjaxCallReducer(),
			has_selected_day: scope.buildHasSelectedDay()
		});

		window.store = Redux.createStore(reducer);
	}

	defaultState(){
		if(window.booking_form_state)
			return window.booking_form_state;

		let state = {
			init_view: false,
			outlet: {
				id: 1,
				name: 'FUCK YOU'
			},
			pax: {
				adult: 1,
				children: 0
			},
			reservation: {
				date: moment(),
				time: ''
			},
			dialog: {
				show: false,
				stop: {
					has_data: false,
					exceed_min_exist_time: false
				},
				min_exist_time: 500 //ms
			},
			available_time: {},
			ajax_call: false,
			has_selected_day: false,
		};

		return state;
	}

	buildOutletReducer(){
		let _state = this.state.outlet;

		return function(state = _state, action){
			switch(action.type){
				case 'CHANGE_OUTLET':
					return action.outlet
				default:
					return state
			}
		};
	}

	buildPaxReducer(){
		let _state = this.state.pax;

		return function(state = _state, action){
			switch(action.type){
				case 'CHANGE_ADULT_PAX':
					return Object.assign({}, state, {
						adult: action.adult_pax
					});
				case 'CHANGE_CHILDREN_PAX':
					return Object.assign({}, state, {
						children: action.children_pax
					});
				default:
					return state
			}
		};
	}

	buildReservationReducer(){
		let _state = this.state.reservation;
		return function(state = _state, action){
			switch(action.type){
				case 'CHANGE_RESERVATION_DATE':
					return Object.assign({}, state, {
						date: action.date
					});
				case 'CHANGE_RESERVATION_TIME':
					return Object.assign({}, state, {
						time: action.time
					});
				default:
					return state
			}
		};
	}

	buildDialogReducer(){
		let _state = this.state.dialog;
		return function(state = _state, action){
			switch(action.type){
				case 'DIALOG_SHOW':
					return Object.assign({}, state, {
						loading: action.loading
					});
				case 'DIALOG_HAS_DATA':
					return Object.assign({}, state, {
						stop: {
							has_data: action.dialog_has_data
						}
					});
				case 'DIALOG_EXCEED_MIN_EXIST_TIME':
					return Object.assign({}, state, {
						stop: {
							exceed_min_exist_time: action.exceed_min_exist_time
						}
					});
				default:
					return state
			}
		};
	}

	buildAvailableTimeReducer(){
		let _state = this.state.available_time;
		return function(state = _state, action){
			switch(action.type){
				case 'CHANGE_AVAILABLE_TIME':
					return Object.assign({}, state, action.available_time);
				default:
					return state;
			}
		};
	}

	buildInitViewReducer(){
		let _state = this.state.init_view;
		return function(state = _state, action){
			switch(action.type){
				case 'INIT_VIEW':
					return true;
				default:
					return state;
			}
		}
	}

	buildAjaxCallReducer(){
		let _state = this.state.ajax_call;
		let scope = this;
		return function(state = _state, action){
			switch(action.type){
				case 'AJAX_CALL':
					scope.ajaxCall();
					return true;
				default:
					return state;
			}
		}
	}

	buildHasSelectedDay(){
		let _state = this.state.has_selected_day;
		return function(state = _state, action){
			switch(action.type){
				case 'HAS_SELECTED_DAY':
					return true;
				default:
					return state;
			}
		}
	}

	bindView(){
		this.findView();
		let store = window.store;
		/**
		 * Debug state
		 * @type {Element}
		 */
		let pre = document.querySelector('#expand');
		if(!pre){
			let body = document.querySelector('body');
			pre = document.createElement('pre');
			body.appendChild(pre);
		}

		store.subscribe(()=>{
			let state = store.getState();
			pre.innerHTML = syntaxHighlight(JSON.stringify(state, null, 4));
			/**
			 * Update input outlet name
			 */
			this.input_outlet.value = state.outlet.name;
			this.label.innerText    = state.reservation.date.format('MMM D Y');
			this.inpute_date.value  = state.reservation.date.format('Y-MM-DD');

			// if(state.ajax_call == true)
			// 	this.updateSelectView(state.available_time);
			this.updateSelectView(state.available_time);
		});


	}

	initView(){
		let action = {
			type: 'INIT_VIEW'
		}

		store.dispatch(action);
	}

	findView(){
		let calendarDiv = $('#calendar-box');

		this.calendar = calendarDiv.Calendar();
		this.day_tds = calendarDiv.find('td.day');
		this.label = document.querySelector('#reservation_date');
		this.select = document.querySelector('select[name="reservation_time"]');
		this.form = document.querySelector('#booking-form');

		this.adult_pax_select = document.querySelector('select[name="adult_pax"]');
		this.children_pax_select = document.querySelector('select[name="children_pax"]');

		this.ajax_dialog = $('#ajax-dialog');

		this.outlet_select = document.querySelector('select[name="outlet_id"]');
		this.inpute_date = document.querySelector('input[name="reservation_date"]');
		this.input_outlet = document.querySelector('input[name="outlet_name"]');

		this.time_select = document.querySelector('select[name="reservation_time"]');
		console.log(this.time_select);
	}

	updateSelectView(available_time) {
	    let selected_day_str = moment().format('Y-MM-DD');

	    let available_time_on_selected_day = available_time[selected_day_str];
	    if (typeof available_time_on_selected_day == 'undefined')
	        return;

	    if (available_time_on_selected_day.length == 0) {
	        let default_time = {
	            time: 'N/A',
	            session_name: ''
	        };

	        available_time_on_selected_day.push(default_time);
	    }

	    let selectDiv = this.select;
		if(selectDiv.available_time){
			if(selectDiv.available_time == available_time)
				return;
		}
		selectDiv.available_time = available_time;
	    //reset selectDiv options
	    selectDiv.innerHTML = '';
	    available_time_on_selected_day.forEach(time => {
	        //console.log(time);
	        let optionDiv = document.createElement('option');

	        optionDiv.setAttribute('value', time.time);
	        //noinspection JSUnresolvedVariable
	        optionDiv.innerText = time.session_name + ' ' + time.time;

	        selectDiv.appendChild(optionDiv);
	    });
	}

	regisEvent(){
		let store = window.store;

		let outlet_select = this.outlet_select;
		outlet_select.addEventListener('change', function(){
			let selectedOption = outlet_select.selectedOptions[0];

			let action = {
				type: 'CHANGE_OUTLET',
				outlet: {
					id: selectedOption.value,
					name: selectedOption.innerText
				}
			};

			store.dispatch(action);
		});

		let adult_pax_select = this.adult_pax_select;
		adult_pax_select.addEventListener('change', function(){
			let selectedOption = outlet_select.selectedOptions[0];

			let action = {
				type: 'CHANGE_ADULT_PAX',
				adult_pax: selectedOption.value
			};

			store.dispatch(action);
		});

		let children_pax_select = this.children_pax_select;
		children_pax_select.addEventListener('change', function(){
			let selectedOption = outlet_select.selectedOptions[0];

			let action = {
				type: 'CHANGE_CHILDREN_PAX',
				children_pax: selectedOption.value
			};

			store.dispatch(action);
		});

		document.addEventListener('user-select-day', function(e){
			let date = moment(e.detail.day, 'Y-M-D');

			let action = {
				type: 'CHANGE_RESERVATION_DATE',
				date
			};

			store.dispatch(action);

			let action2 = {
				type: 'HAS_SELECTED_DAY'
			}

			store.dispatch(action2);

			let action3 = {
				type: 'AJAX_CALL'
			}
			store.dispatch(action3);

		});

		let time_select = this.time_select;
		time_select.addEventListener('change', function(){
			console.log('time change');
			let selectedOption = time_select.selectedOptions[0];

			let action = {
				type: 'CHANGE_RESERVATION_TIME',
				time: selectedOption.value
			};

			store.dispatch(action);
		});
	}

	ajaxCall(){
		let form = this.form;
		let data =
			$(form)
				.serializeArray()
				.reduce((carry, item) =>{
					carry[item.name] = item.value;
					return carry;
				}, {});

		$.ajax({
			url: '',
			method: 'POST',
			data,
			success(res) {
				console.log(res);
				let action = {
					type: 'DIALOG_HAS_DATA',
					dialog_has_data: true
				}

				store.dispatch(action);

				let action2 = {
					type: 'CHANGE_AVAILABLE_TIME',
					available_time: res
				}

				store.dispatch(action2);
			},
			error(res) {
				let action = {
					type: 'DIALOG_HAS_DATA',
					dialog_has_data: false
				}

				store.dispatch(action);

			}
		});
	}

	// _regisPaxChange(element, eventName) {
	//     eventName = eventName || "pax-change";
	//
	//     element.onchange = function(e) {
	//         if (!e.target.value) return;
	//
	//         //let num_pax = e.target.value;
	//         let select_name = element.getAttribute('name');
	//
	//         var event = new CustomEvent(eventName, {
	//             detail: {
	//                 select_name
	//             },
	//             bubbles: true,
	//             cancelable: true
	//         });
	//
	//         element.dispatchEvent(event);
	//
	//     };
	// }
	//
	// listenUserSelectDay() {
	//     let scope = this;
	//     document.addEventListener('user-select-day', function(e) {
	//         scope.dayPicked = true;
	//         scope.changeLabel(e);
	//         scope.setInputReservationDate(e);
	//         scope.ajaxAvailableTime(e);
	//         scope.storeSelectedDay(e);
	//     });
	// }
	//
	// changeLabel(e) {
	//     let d = this._getDate(e.detail.day);
	//
	//     this.label.innerText = d.format('MMM DD Y');
	// }
	//
	// setInputReservationDate(e) {
	//     let d = this._getDate(e.detail.day);
	//
	//     this.inpute_date.value = d.format('Y-MM-DD');
	// }
	//
	// setInputOutletName() {
	//     let selectedOption = this.outlet_select.selectedOptions[0];
	//     if (typeof selectedOption == 'undefined')
	//         return
	//
	//     let outlet_name = selectedOption.innerText;
	//     this.input_outlet.value = outlet_name;
	// }
	//
	// ajaxAvailableTime(e) {
	//     //ajax request
	//     //ask for available date base on form info
	//     let form = this.form;
	//     let data = $(form).serializeArray().reduce((carry, item) => {
	//         carry[item.name] = item.value;
	//
	//         return carry;
	//     }, {});
	//
	//     let selectElement = this.select;
	//
	//     var loadingDialog = new CustomEvent("loading-dialog", {
	//         bubbles: true,
	//         cancelable: true
	//     });
	//
	//     form.dispatchEvent(loadingDialog);
	//
	//     $.ajax({
	//         url: '',
	//         method: 'POST',
	//         data,
	//         success(res) {
	//             console.log(res);
	//
	//             var event = new CustomEvent("has-ajax-response", {
	//                 detail: {
	//                     res
	//                 },
	//                 bubbles: true,
	//                 cancelable: true
	//             });
	//
	//             selectElement.dispatchEvent(event);
	//         },
	//         error(res) {
	//             console.log(res);
	//         }
	//     });
	// }
	//
	// storeSelectedDay(e) {
	//     let day_info = e.detail.day.split('-');
	//     this.selected_day = `${day_info[0]}-${this._prefix2Dec(day_info[1])}-${this._prefix2Dec(day_info[2])}`;
	// }
	//
	// _getDate(day_str) {
	//     return moment(day_str, 'Y-M-D');
	// }
	//
	// listenHasAjaxResponse() {
	//     let scope = this;
	//     document.addEventListener('has-ajax-response', function(e) {
	//         scope.updateCalendarView(e);
	//         scope.updateSelectView(e);
	//
	//         document.dispatchEvent(new CustomEvent('stop-dialog'));
	//     });
	// }
	//
	// updateCalendarView(e) {
	//     let scope = this;
	//     let res = e.detail.res;
	//
	//     let available_days = Object.keys(res);
	//
	//     this.day_tds.each(function() {
	//         let td = $(this);
	//         let td_day_str = `${td.attr('year')}-${scope._prefix2Dec(td.attr('month'))}-${scope._prefix2Dec(td.attr('day'))}`;
	//
	//         if (available_days.includes(td_day_str)) {
	//             scope._pickable(td);
	//         } else {
	//             scope._unpickable(td);
	//         }
	//     });
	// }
	//
	// _prefix2Dec(val) {
	//     if (val < 10)
	//         return `0${val}`;
	//
	//     return val;
	// }
	//
	// _pickable(td) {
	//     td.removeClass('past');
	//     td.addClass('day');
	// }
	//
	// _unpickable(td) {
	//     td.removeClass('day');
	//     td.addClass('past');
	// }
	//
	// updateSelectView(e) {
	//     let res = e.detail.res;
	//     let selected_day_str = this.selected_day || new Date().toISOString().substr(0, 10);
	//
	//     let available_time = res[selected_day_str];
	//     if (typeof available_time == 'undefined')
	//         return;
	//
	//     if (available_time.length == 0) {
	//         let default_time = {
	//             time: 'N/A',
	//             session_name: ''
	//         };
	//
	//         available_time.push(default_time);
	//     }
	//
	//     let selectDiv = this.select;
	//     //reset selectDiv options
	//     selectDiv.innerHTML = '';
	//     available_time.forEach(time => {
	//         //console.log(time);
	//         let optionDiv = document.createElement('option');
	//
	//         optionDiv.setAttribute('value', time.time);
	//         //noinspection JSUnresolvedVariable
	//         optionDiv.innerText = time.session_name + ' ' + time.time;
	//
	//         selectDiv.appendChild(optionDiv);
	//     });
	// }
	//
	// listenPaxChange() {
	//     let scope = this;
	//     document.addEventListener('pax-change', function(e) {
	//         let shouldCallAjax = true;
	//         if (typeof scope.dayPicked == 'undefined') {
	//             shouldCallAjax = false;
	//         }
	//
	//         if (shouldCallAjax) {
	//             scope.ajaxAvailableTime(e);
	//         }
	//     });
	// }
	//
	// listenLoadingDialog() {
	//     let scope = this;
	//     document.addEventListener('loading-dialog', function(e) {
	//         console.log('loading dialog');
	//         scope.ajax_dialog.modal('show');
	//     });
	// }
	//
	// listenStopDialog() {
	//     let scope = this;
	//     document.addEventListener('stop-dialog', function(e) {
	//         console.log('stop dialog');
	//         scope.ajax_dialog.modal('hide');
	//     });
	// }
	//
	// listenOutletChange() {
	//     let scope = this;
	//     document.addEventListener('outlet-change', function(e) {
	//         scope.setInputOutletName(e);
	//
	//         let shouldCallAjax = true;
	//         if (typeof scope.dayPicked == 'undefined') {
	//             shouldCallAjax = false;
	//         }
	//
	//         if (shouldCallAjax) {
	//             scope.ajaxAvailableTime(e);
	//         }
	//
	//         if (shouldCallAjax) {
	//             scope.ajaxAvailableTime(e);
	//         }
	//
	//     });
	// }

}

// calendarHandler($);
let bookingForm = new BookingForm();

// bookingForm.regisEvent();