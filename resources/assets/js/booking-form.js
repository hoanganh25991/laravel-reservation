class BookingForm {
	/** @namespace action.adult_pax */
	/** @namespace action.children_pax */
	/** @namespace action.dialog_has_data */
	/** @namespace action.exceed_min_exist_time */

	/** @namespace window.booking_form_state */
	/** @namespace $ */
	/** @namespace moment */
	/** @namespace Vue */

	constructor(){
		this.buildRedux();

		this.buildVue();

		this.bindView();

		this.regisEvent();

		this.bindListener();

		this.initView();


		BookingForm.logObjectAssignPer();

		// this.modalSelfDispatch();
		BookingForm.modalSelfDispatch();
	}

	static logObjectAssignPer(){
		let o_assign = Object.assign;

		Object.assign = function(...args){
			if(Object.keys(args[0]).length > 0){
				console.time('obj assign');
				o_assign.apply(Object, args);
				console.timeEnd('obj assign');
			}

			return o_assign.apply(Object, args);

		}
	}

	static modalSelfDispatch(){
		// let o_modal = $.fn.modal;
		//
		// $.fn.modal = function(b, d){
		// 	if(b == 'hide'){
		// 		store.dispatch({type: 'DIALOG_HIDDEN'});
		// 	}
		//
		// 	o_modal.apply(this, [b,d]);
		// };
	}
	buildRedux(){
		//assign default state
		//may from server
		//or self build
		this.state = this.defaultState();
		let self = this;
		let reducer = Redux.combineReducers({
			has_selected_day : self.buildHasSelectedDayReducer(),
			available_time   : self.buildAvailableTimeReducer(),
			reservation      : self.buildReservationReducer(),
			ajax_call        : self.buildAjaxCallReducer(),
			init_view        : self.buildInitViewReducer(),
			form_step        : self.buildFormStepReducer(),
			customer         : self.buildCustomerReducer(),
			pax_over         : self.buildPaxOverReducer(),
			outlet           : self.buildOutletReducer(),
			dialog           : self.buildDialogReducer(),
			pax              : self.buildPaxReducer(),
		});

		window.store = Redux.createStore(reducer);

		/**
		 * Enhance store with prestate
		 */
		let o_dispatch = store.dispatch;
		store.dispatch = function(action){
			console.info(action.type);
			store.prestate = store.getState();
			o_dispatch(action);
		}

		store.getPrestate = function(){
			return store.prestate;
		}
	}

	buildPaxOverReducer(){
		let _state = this.state.pax_over;

		return function(state = _state, action){
			switch(action.type){
				case 'PAX_OVER':
					return 'none';
				default:
					return state;
			}
		};
	}

	getVueState(){
		if(typeof window.vue_state == 'undefined'){
			window.vue_state = this.defaultState();
		}

		return window.vue_state;
	}


	buildVue(){
		let vue_state = this.getVueState();

		// let form_vue = new Vue({
		new Vue({
			el: '#form-step-container',
			data: vue_state
		});
		// this.form_vue = form_vue;
	}

	shallowEqual(objA, objB) {
		if (objA === objB) {
			return true;
		}

		let keysA = Object.keys(objA),
			keysB = Object.keys(objB),
			hasOwn;

		if (keysA.length !== keysB.length) {
			return false;
		}

		// Test for A's keys different from B.
		hasOwn = Object.prototype.hasOwnProperty;
		for (let i = 0; i < keysA.length; i++) {
			if (!hasOwn.call(objB, keysA[i]) ||
				objA[keysA[i]] !== objB[keysA[i]]) {
				return false;
			}
		}
		return true;
	}

	defaultState(){
		if(window.booking_form_state)
			return window.booking_form_state;

		let state = {
			init_view: false,
			outlet: {
				id: 1,
				name: 'HoiPOS Cafe (West)'
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
				min_exist_time: 690 //ms
				// min_exist_time: 5000 //ms
			},
			available_time: {},
			ajax_call: 0,
			has_selected_day: false,
			form_step: 'form-step-1',
			customer: {
				first_name: 'Anh',
				last_name : 'Le Hoang',
				email: 'lehoanganh25991@gmail.com',
				phone_country_code: '+65',
				phone: '903865657',
				remarks: 'hello world'
			},
			pax_over: "block"
		};

		return state;
	}

	buildCustomerReducer(){
		let _state = this.state.customer;

		return function(state = _state, action){
			switch(action.type){
				case 'CHANGE_CUSTOMER_SANLUTATION':
					return Object.assign({}, state, {
						salutation: action.salutation
					});
				case 'CHANGE_CUSTOMER_FIRST_NAME':
					return Object.assign({}, state, {
						first_name: action.first_name
					});
				case 'CHANGE_CUSTOMER_LAST_NAME':
					return Object.assign({}, state, {
						last_name: action.last_name
					});
				case 'CHANGE_CUSTOMER_EMAIL':
					return Object.assign({}, state, {
						email: action.email
					});
				case 'CHANGE_CUSTOMER_PHONE_COUNTRY_CODE':
					return Object.assign({}, state, {
						phone_country_code: action.phone_country_code
					});
				case 'CHANGE_CUSTOMER_PHONE':
					return Object.assign({}, state, {
						phone: action.phone
					});
				case 'CHANGE_CUSTOMER_REMARKS':
					return Object.assign({}, state, {
						remarks: action.remarks
					});
				default:
					return state;
			}
		};
	}

	buildOutletReducer(){
		let _state = this.state.outlet;

		return function(state = _state, action){
			switch(action.type){
				case 'CHANGE_OUTLET':
					return action.outlet;
				default:
					return state;
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
					return state;
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
					return state;
			}
		};
	}

	buildDialogReducer(){
		let _state = this.state.dialog;
		return function(state = _state, action){
			switch(action.type){
				case 'SHOW_DIALOG':
					return Object.assign({}, state, {
						show: action.show
					});
				case 'DIALOG_HAS_DATA':
					state.stop.has_data = action.dialog_has_data;
					return JSON.parse(JSON.stringify(state));
				case 'DIALOG_EXCEED_MIN_EXIST_TIME':
					state.stop.exceed_min_exist_time = action.exceed_min_exist_time;
					return JSON.parse(JSON.stringify(state));
				case 'DIALOG_HIDDEN':
					state.stop.has_data = false;
					state.stop.exceed_min_exist_time = false;
					return JSON.parse(JSON.stringify(state));
				default:
					return state;
			}
		};
	}

	buildAvailableTimeReducer(){
		let _state = this.state.available_time;
		return function(state = _state, action){
			switch(action.type){
				case 'CHANGE_AVAILABLE_TIME':
					if(Array.isArray(action.available_time)){
						action.available_time = {};
					}
					// return Object.assign({}, state, action.available_time);
					return action.available_time;
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
		return function(state = _state, action){
			switch(action.type){
				case 'AJAX_CALL':
					return (Number(state) + Number(action.ajax_call));
				default:
					return state;
			}
		}
	}

	buildHasSelectedDayReducer(){
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

	buildFormStepReducer(){
		let _state = this.state.form_step;
		return function(state = _state, action){
			switch(action.type){
				case 'CHANGE_FORM_STEP':
					return action.form_step;
				default:
					return state;
			}
		}
	}

	bindListener(){
		let store = window.store;
		let self = this;
		store.subscribe(()=>{
			if(store.SELF_DISPATCH_FLAG == true){
				store.SELF_DISPATCH_FLAG = false;
				return;
			}

			let state = store.getState();
			let prestate = store.getPrestate();

			if(prestate.ajax_call < state.ajax_call){
				self.ajaxCall();
			}

			if(prestate.dialog.show == false && state.dialog.show == true){
				self.ajax_dialog.modal('show');
				// store.dispatch({type: ''});
			}

			if(prestate.dialog.show == true && state.dialog.show == false){
				self.ajax_dialog.modal('hide');
			}

			if(Number(state.pax.adult) + Number(state.pax.children) > 10){
				store.SELF_DISPATCH_FLAG = true;
				store.dispatch({
					type: 'PAX_OVER'
				});
			}

			if(state.dialog.show == true && state.dialog.stop.has_data == true && state.dialog.stop.exceed_min_exist_time == true){
				// store.SELF_DISPATCH_FLAG = true;
				store.dispatch({
					type: 'SHOW_DIALOG',
					show: false
				});
			}

		});

	}

	computeDialogShow(){
		// let state = store.getState();
		// if(state.dialog.show == true && state.dialog.stop.has_data == true && state.dialog.stop.exceed_min_exist_time == true){
		// 	store.dispatch({type: 'SHOW_DIALOG', show: false});
		// }
	}

	bindView(){
		this.findView();
		let store = window.store;
		let self = this;
		// this.form_vue.$data = store.getState();
		// let form_vue = this.form_vue;
		/**
		 * Debug state
		 * @type {Element}
		 */
		// let pre = document.querySelector('#expand');
		let pre = document.querySelector('#redux-state');
		if(!pre){
			let body = document.querySelector('body');
			pre = document.createElement('pre');
			body.appendChild(pre);
		}

		store.subscribe(()=>{
			let state    = store.getState();
			//update this way for vue see it
			let vue_state = self.getVueState();
			Object.assign(vue_state, state);

			//debug
			let prestate = store.getPrestate();
			pre.innerHTML = syntaxHighlight(JSON.stringify(state, null, 4));

			/**
			 * Available time change
			 * @type {boolean}
			 */
			let available_time_change = (prestate.available_time != state.available_time);
			if(available_time_change){
				requestAnimationFrame(()=>{
					this.updateSelectView(state.available_time);
				});
				requestAnimationFrame(()=>{
					this.updateCalendarView(state.available_time);
				});
			}
			/**
			 * Form step change
			 */
			let form_step_change = (prestate.form_step != state.form_step)
									|| (prestate.init_view == false
										&& state.form_step == 'form-step-1');
			if(form_step_change){
				console.info('pointToFormStep');
				this.pointToFormStep();
			}
		});


	}

	initView(){
		let action = {
			type: 'INIT_VIEW'
		}

		store.dispatch(action);
	}

	findView(){
		if(typeof this._hasRunFindView == 'undefined'){
			this._hasRunFindView = true;
		}else{
			console.info('findView has run');
			return;
		}

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

		this.reservation_title = document.querySelector('#reservation_title');

		/**
		 * Swap view
		 */
		// this.btnNext  = document.querySelector('#btn_next');
		// this.queryView = document.querySelector('#query-time');
		// this.fullfillView = document.querySelector('#fullfill-info');

		this.form_step_container = document.querySelector('#form-step-container');
		this.btn_form_nexts      = document.querySelectorAll('button.btn-form-next');

		/**
		 * Customer info
		 */
		this.customer_phone_country_code_input = document.querySelector('input[name="phone_country_code"]');
		this.customer_salutation_select = document.querySelector('select[name="salutation"]');
		this.customer_remarks_textarea  = document.querySelector('textarea[name="remarks"]');
		this.customer_firt_name_input   = document.querySelector('input[name="first_name"]');
		this.customer_last_name_input   = document.querySelector('input[name="last_name"]');
		this.customer_email_input       = document.querySelector('input[name="email"]');
		this.customer_phone_input       = document.querySelector('input[name="phone"]');

		/**
		 * Reservation form step 2 header summary
		 */
	}

	updateSelectView(available_time) {
	    let selected_day_str = moment().format('Y-MM-DD');

	    let available_time_on_selected_day = available_time[selected_day_str];
	    if (typeof available_time_on_selected_day == 'undefined'){
			// console.info('No available time on select day');
		    // return;
		    available_time_on_selected_day = [];
	    }

	    if (available_time_on_selected_day.length == 0) {
	        let default_time = {
	            time: 'N/A',
	            session_name: ''
	        };

	        available_time_on_selected_day.push(default_time);
	    }

		let selectDiv = this.select;
		// if(selectDiv.available_time){
		// 	if(selectDiv.available_time == available_time)
		// 		return;
		// }
		// selectDiv.available_time = available_time;
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

	updateCalendarView(available_time) {
		if(Object.keys(available_time).length == 0)
			return

		let calendar = this.calendar;
		this._addCalendarHelper(calendar);

		// if(calendar.available_time){
		// 	calendar.available_time == available_time;
		// 	return;
		// }
		//
		// calendar.available_time = available_time;

	    let available_days = Object.keys(available_time);
	    this.day_tds.each(function() {
	        let td = $(this);
	        let td_day_str = `${td.attr('year')}-${calendar._prefix2Dec(td.attr('month'))}-${calendar._prefix2Dec(td.attr('day'))}`;

	        if (available_days.includes(td_day_str)) {
		        calendar._pickable(td);
	        } else {
		        calendar._unpickable(td);
	        }
	    });

	}

	_addCalendarHelper(calendar){
		if(!calendar._prefix2Dec || !calendar._pickable || calendar._unpickable){
			calendar._prefix2Dec = function(val) {
				if (val < 10)
					return `0${val}`;

				return val;
			}

			calendar._pickable = function(td){
				td.removeClass('past');
				td.addClass('day');
			}

			calendar._unpickable = function(td){
				td.removeClass('day');
				td.addClass('past');
			}
		}
	}

	regisEvent(){
		this.findView();
		let store = window.store;
		let self = this;

		let outlet_select = this.outlet_select;
		outlet_select.addEventListener('change', function(){
			let selectedOption = outlet_select.selectedOptions[0];

			store.dispatch({
				type: 'CHANGE_OUTLET',
				outlet: {
					id: selectedOption.value,
					name: selectedOption.innerText
				}
			});

			self.computeAjaxCall();
		});

		let adult_pax_select = this.adult_pax_select;
		adult_pax_select.addEventListener('change', function(){
			let selectedOption = adult_pax_select.selectedOptions[0];

			store.dispatch({
				type: 'CHANGE_ADULT_PAX',
				adult_pax: selectedOption.value
			});

			self.computePaxOver();

			self.computeAjaxCall();
		});

		let children_pax_select = this.children_pax_select;
		children_pax_select.addEventListener('change', function(){
			let selectedOption = children_pax_select.selectedOptions[0];

			store.dispatch({
				type: 'CHANGE_CHILDREN_PAX',
				children_pax: selectedOption.value
			});

			self.computePaxOver();

			self.computeAjaxCall();
		});

		document.addEventListener('user-select-day', function(e){
			let date = moment(e.detail.day, 'Y-M-D');

			store.dispatch({
				type: 'CHANGE_RESERVATION_DATE',
				date
			});

			let state = store.getState();
			if(state.has_selected_day == false){
				store.dispatch({type: 'HAS_SELECTED_DAY'});
			}

			self.computeAjaxCall();
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

		let btn_form_nexts = this.btn_form_nexts;
		btn_form_nexts
			.forEach((btn)=>{
				btn.addEventListener('click', ()=>{
					let destination = btn.getAttribute('destination');
					store.dispatch({type: 'CHANGE_FORM_STEP', form_step: destination});
				});
			});
		/**
		 * Handle customer change info
		 */
		this.customer_salutation_select
			.addEventListener('change', function(){
				//binding in this way to get out this as email input
				let salutation = this.selectedOptions[0].value;
				store.dispatch({type: 'CHANGE_CUSTOMER_SALUTATION', salutation});
			});

		this.customer_firt_name_input
		    .addEventListener('change', function(){
			    //binding in this way to get out this as email input
			    let first_name = this.value;
			    store.dispatch({type: 'CHANGE_CUSTOMER_FIRST_NAME', first_name});
		    });

		this.customer_last_name_input
		    .addEventListener('change', function(){
			    //binding in this way to get out this as email input
			    let last_name = this.value;
			    store.dispatch({type: 'CHANGE_CUSTOMER_LAST_NAME', last_name});
		    });

		this.customer_email_input
		    .addEventListener('change', function(){
			    //binding in this way to get out this as email input
			    let email = this.value;
			    store.dispatch({type: 'CHANGE_CUSTOMER_EMAIL', email});
		    });

		this.customer_phone_country_code_input
		    .addEventListener('change', function(){
			    //binding in this way to get out this as email input
			    let phone_country_code = this.value;
			    store.dispatch({type: 'CHANGE_CUSTOMER_PHONE_COUNTRY_CODE', phone_country_code});
		    });

		this.customer_phone_input
		    .addEventListener('change', function(){
			    //binding in this way to get out this as email input
			    let phone = this.value;
			    store.dispatch({type: 'CHANGE_CUSTOMER_PHONE', phone});
		    });

		this.customer_remarks_textarea
		    .addEventListener('change', function(){
			    //binding in this way to get out this as email input
			    let remarks = this.value;
			    store.dispatch({type: 'CHANGE_CUSTOMER_REMARKS', remarks});
		    });

		this.ajax_dialog
			.on('hidden.bs.modal', function(){
				store.dispatch({type: 'DIALOG_HIDDEN'});
			});

		this.ajax_dialog
			.on('shown.bs.modal', function(){
				let state = store.getState();
				setTimeout(function(){
					store.dispatch({type: 'DIALOG_EXCEED_MIN_EXIST_TIME', exceed_min_exist_time: true});
					self.computeDialogShow();
				}, state.dialog.min_exist_time);
			});
	}

	computeAjaxCall(){
		let state = store.getState();
		let prestate = store.getPrestate();

		if(state.has_selected_day
		&& (prestate.pax.adult != state.pax.adult
		||prestate.pax.children != state.pax.children
		||prestate.outlet.id != state.outlet.id
		||prestate.reservation.date != state.reservation.date)){
			store.dispatch({type: 'AJAX_CALL', ajax_call: 1});
		}

		if(prestate.has_selected_day == false && state.has_selected_day == true){
			store.dispatch({type: 'AJAX_CALL', ajax_call: 1});
		}
	}

	computePaxOver(){
		// let state = store.getState();
		//
		// if((Number(state.pax.adult) + Number(state.pax.children)) > 10){
		// 	store.dispatch({type: 'PAX_OVER'});
		// }
	}

	ajaxCall(){
		// console.info('ajax call');
		let store = window.store;
		let state = store.getState();
		let self = this;

		store.dispatch({type: 'SHOW_DIALOG', show: true});



		let data = {
			outlet_id: state.outlet.id,
			// outlet_name: state.outlet.name,
			adult_pax: state.pax.adult,
			children_pax: state.pax.children
		};



		$.ajax({
			url: '',
			method: 'POST',
			data,
			success(res) {
				console.log(res);

				store.dispatch({
					type: 'CHANGE_AVAILABLE_TIME',
					available_time: res
				});
			},
			complete(){
				store.dispatch( {
					type: 'DIALOG_HAS_DATA',
					dialog_has_data: true
				});

				self.computeDialogShow();
			},
			error(res){
				console.log(res);
			}
		});
	}

	pointToFormStep(){
		let state = store.getState();

		let form_step_container = this.form_step_container;
		form_step_container
			.querySelectorAll('.form-step')
			.forEach((step)=>{
				let form_step = step.getAttribute('id');
				let transform = 'scale(0,0)';
				if(form_step == state.form_step){
					transform = 'scale(1,1)';
				}

				step.style.transform = transform;
			});
	}
}

let bookingForm = new BookingForm();

