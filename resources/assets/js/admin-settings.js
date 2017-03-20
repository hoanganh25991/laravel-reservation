const INIT_VIEW = 'INIT_VIEW';
const CHANGE_ADMIN_STEP = 'CHANGE_ADMIN_STEP';

class AdminSettings {
	/**
	 * @namespace Redux
	 * @namespace moment
	 */
	constructor(){
		this.buildRedux();

		this.buildVue();

		this.event();

		this.view();

		this.initView();
	}

	buildRedux(){
		let self = this;
		let default_state = this.defaultState();
		let rootReducer = function(state = default_state, action){
			switch(action.type){
				case INIT_VIEW:
					return Object.assign({}, state, {
						init_view: self.initViewReducer(state.init_view, action)
					});
				case CHANGE_ADMIN_STEP: {
					return Object.assign({}, state, {
						admin_step: self.adminStepReducer(state.admin_step, action)
					});
				}
				default:
					return state;
			}
		}

		window.store = Redux.createStore(rootReducer);
		/**
		 * Helper function
		 */
		let o_dispatch = store.dispatch;
		store.dispatch = function(action){
			console.info(action.type);
			store.prestate = store.getState();
			store.last_action = action.type;
			o_dispatch(action);
		}

		store.getPrestate = function(){
			return store.prestate;
		}

		store.getLastAction = function(){
			return store.last_action;
		}
	}

	defaultState(){
		let default_state  = window.state || {};
		let frontend_state = {
			init_view : false,
			admin_step: '#buffer'
		};

		return Object.assign(frontend_state, default_state);
	}

	buildVue(){
		let state = this.getVueState();
		this.vue = new Vue({
			el: '#app',
			data: state
		});
	}

	getVueState(){
		if(typeof window.vue_state != 'undefined'){
			return window.vue_state;
		}

		// window.vue_state = store.getState();
		/**
		 * Above assign go wrong
		 * BCS vue will modifed on given state
		 * Which will change state of store
		 * >>> hard to understand workflow
		 */
		window.vue_state = Object.assign({}, store.getState());

		return window.vue_state;
	}

	initViewReducer(state, action){
		switch(action.type){
			case INIT_VIEW:{
				return true;
			}
			default:
				return state;
		}
	}

	initView(){
		store.dispatch({type: INIT_VIEW});
	}

	adminStepReducer(state, action){
		switch(action.type){
			case CHANGE_ADMIN_STEP:
				return action.step;
			default:
				return state;
		}
	}

	findView(){
		/**
		 * Only run one time
		 */
		if(this._hasFindView){
			return;
		}
		this._hasFindView = true;

		this.admin_step_go = document.querySelectorAll('.go');
		this.admin_step    = document.querySelectorAll('#admin-step-container .admin-step');
	}

	event(){
		this.findView();

		this.admin_step_go
			.forEach((el)=>{
				el.addEventListener('click', ()=>{
					let destination = el.getAttribute('destination');
					store.dispatch({type: CHANGE_ADMIN_STEP, step: destination});
				});
			});
	}

	view(){
		let store = window.store;
		let self  = this;

		/**
		 * Debug state
		 */
		let pre = document.querySelector('#redux-state');
		if(!pre){
			let body = document.querySelector('body');
			pre = document.createElement('pre');
			body.appendChild(pre);
		}

		store.subscribe(()=>{
			let state = store.getState();
			let prestate = store.getPrestate();

			/**
			 * Update state for vue
			 * @type {boolean}
			 */
			let vue_state = self.getVueState();
			Object.assign(vue_state, state);

			//debug
			pre.innerHTML = syntaxHighlight(JSON.stringify(state, null, 4));

			let first_view  = prestate.init_view == false && state.init_view == true;
			let change_step = prestate.admin_step != state.admin_step;
			let run_admin_step = first_view || change_step;
			if(run_admin_step){
				self.pointToAdminStep();
			}
		});
	}

	pointToAdminStep(){
		let state = store.getState();

		this.admin_step
			.forEach((step)=>{
				let admin_step = step.getAttribute('id');
				let transform = 'scale(0,0)';
				if('#' + admin_step == state.admin_step){
					transform = 'scale(1,1)';
				}
				step.style.transform = transform;
			});
	}
}

let adminSettings = new AdminSettings();