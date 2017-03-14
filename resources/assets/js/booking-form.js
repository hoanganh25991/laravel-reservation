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

		this.bindListener();

		this.initView();
	}

	buildRedux(){
		//assign default state
		//may from server
		//or self build
		this.state = this.defaultState();
		let scope = this;
		let reducer = Redux.combineReducers({
			has_selected_day : scope.buildHasSelectedDayReducer(),
			available_time   : scope.buildAvailableTimeReducer(),
			reservation      : scope.buildReservationReducer(),
			ajax_call        : scope.buildAjaxCallReducer(),
			init_view        : scope.buildInitViewReducer(),
			outlet           : scope.buildOutletReducer(),
			dialog           : scope.buildDialogReducer(),
			pax              : scope.buildPaxReducer(),
		});

		window.store = Redux.createStore(reducer);

		/**
		 * Enhance store with prestate
		 */
		let o_dispatch = store.dispatch;
		store.dispatch = function(action){
			store.prestate = store.getState();
			o_dispatch(action);
		}

		store.getPrestate = function(){
			return store.prestate;
		}
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
				case 'DIALOG_SHOW':
					return Object.assign({}, state, {
						show: action.show
					});
				case 'DIALOG_HAS_DATA':
					state.stop.has_data = action.dialog_has_data;
					return JSON.parse(JSON.stringify(state));
				case 'DIALOG_EXCEED_MIN_EXIST_TIME':
					state.stop.exceed_min_exist_time = action.exceed_min_exist_time;
					return JSON.parse(JSON.stringify(state));
				case 'DIALOG_HIDE':
					state.show = false;
					state.stop.has_data = false;
					return JSON.parse(JSON.stringify(state));
				case 'DIALOG_HIDDEN':
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
					return action.ajax_call;
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

	bindListener(){
		let store = window.store;
		let scope = this;
		// let prestate;
		store.subscribe(()=>{
			let state = store.getState();
			let prestate = store.getPrestate();

			if(prestate){
				/**
				 * Update available time
				 * When user change his condition
				 *
				 * #require has_selected_day
				 */
				if(prestate.has_selected_day
					&& (prestate.pax.adult != state.pax.adult
					||prestate.pax.children != state.pax.children
					||prestate.outlet.id != state.outlet.id)
				){
					// prestate = state;
					scope.ajaxCall();
				}

				if(prestate.has_selected_day == false && state.has_selected_day == true){
					// prestate = state;
					scope.ajaxCall();
				}

				if(prestate.reservation.date != state.reservation.date){
					// prestate = state;
					scope.ajaxCall();
				}

			}

			if(state.dialog.show == true
				&& state.dialog.stop.has_data == true
				&& state.dialog.stop.exceed_min_exist_time == true){
				// prestate = state;
				store.dispatch({type: 'DIALOG_HIDE'});
				this.ajax_dialog.modal('hide');
			}

			//update prestate
			// prestate = state;

			// if(state.ajax_call == true){
			// 	store.dispatch({type:'DIALOG_SHOW', show: true})
			//
			// 	scope.ajaxCall();
			// 	store.dispatch({type: 'AJAX_CALL', ajax_call: false});
			//
			// 	let timeout = setTimeout(function(){
			// 		store.dispatch({type: 'DIALOG_EXCEED_MIN_EXIST_TIME', exceed_min_exist_time: true});
			// 		clearTimeout(timeout);
			// 	}, state.dialog.min_exist_time);
			// }
		});

	}

	bindView(){
		this.findView();
		let store = window.store;
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
			let prestate = store.getPrestate();
			pre.innerHTML = syntaxHighlight(JSON.stringify(state, null, 4));
			/**
			 * Update input outlet name
			 */

			if(prestate.outlet.name != state.outlet.name){
				this.input_outlet.value = state.outlet.name;
			}

			if(prestate.reservation.date != state.reservation.date){
				this.label.innerText    = state.reservation.date.format('MMM D Y');
				this.inpute_date.value  = state.reservation.date.format('Y-MM-DD');
			}

			if(prestate.outlet.name != state.outlet.name){
				this.reservation_title.innerText  = state.outlet.name;
			}

			// if(state.ajax_call == true)
			// 	this.updateSelectView(state.available_time);
			if(prestate.available_time != state.available_time){
				this.updateSelectView(state.available_time);
				this.updateCalendarView(state.available_time);
			}

			if(state.dialog.show == true){
				this.ajax_dialog.modal('show');
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
		this.btnNext  = document.querySelector('#btn_next');
		this.queryView = document.querySelector('#query-time');
		this.fullfillView = document.querySelector('#fullfill-info');
	}

	updateSelectView(available_time) {
	    let selected_day_str = moment().format('Y-MM-DD');

	    let available_time_on_selected_day = available_time[selected_day_str];
	    if (typeof available_time_on_selected_day == 'undefined'){
			console.info('No available time on select day');
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
		let store = window.store;
		let scope = this;

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
		});

		let adult_pax_select = this.adult_pax_select;
		adult_pax_select.addEventListener('change', function(){
			let selectedOption = adult_pax_select.selectedOptions[0];

			store.dispatch({
				type: 'CHANGE_ADULT_PAX',
				adult_pax: selectedOption.value
			});
		});

		let children_pax_select = this.children_pax_select;
		children_pax_select.addEventListener('change', function(){
			let selectedOption = children_pax_select.selectedOptions[0];

			store.dispatch({
				type: 'CHANGE_CHILDREN_PAX',
				children_pax: selectedOption.value
			});
		});

		document.addEventListener('user-select-day', function(e){
			let date = moment(e.detail.day, 'Y-M-D');

			store.dispatch({
				type: 'CHANGE_RESERVATION_DATE',
				date
			});

			store.dispatch({
				type: 'HAS_SELECTED_DAY'
			});
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

		let ajax_dialog = this.ajax_dialog;
		ajax_dialog.on('hidden.bs.modal', function(){
			// store.dispatch({type: 'DIALOG_HIDE'});
			console.log('dialog hidden');
			//can dispatch something here
			//but it NOT DIALOG_HIDE
			//bcs right after state change, should dispatch hide
			//any other come later may re run on this function
			store.dispatch({type: 'DIALOG_HIDDEN'});
		});

		let btnNext = this.btnNext;
		btnNext.addEventListener('click', function(){
			scope.gotoFullfillView();
		});

		// let form = this.form;
		// form.addEventListener('submit', (e)=>{
		// 	console.log('submit');
		// 	e.preventDefault();
		// });
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

		let store = window.store;
		let state = store.getState();
		store.dispatch({
			type: 'DIALOG_SHOW',
			show: true
		});

		let timeout = setTimeout(function(){
			store.dispatch({type: 'DIALOG_EXCEED_MIN_EXIST_TIME', exceed_min_exist_time: true});
			clearTimeout(timeout);
		}, state.dialog.min_exist_time);

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

				store.dispatch( {
					type: 'AJAX_CALL',
					ajax_call: false
				});
			},
			error(res){
				console.log(res);
			}
		});
	}

	gotoFullfillView(){
		let a = this.queryView;
		let b = this.fullfillView;


		a.style.transform = 'scale(0,0)';
		b.style.transform = 'scale(1,1)';
	}
}

let bookingForm = new BookingForm();

