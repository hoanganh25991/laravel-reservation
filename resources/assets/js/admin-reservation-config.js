const CHANGE_ADMIN_STEP = 'CHANGE_ADMIN_STEP';
class AdminConfig {
	/**
	 * @namespace Redux
	 */
	constructor(){
		this.buildRedux();

		this.event();

		this.view();
	}

	buildRedux(){
		let self = this;
		let default_state = this.defaultState();
		let rootReducer = function(state = default_state, action){
			switch(action.type){
				case CHANGE_ADMIN_STEP: {
					return Object.assign({}, state, {
						admin_step: self.adminStepReducer(state.admin_step, action)
					});
				}
				default:
					return state;
			}
		}

		let hackStore = function(dispatch){
			return function(action){
				console.info(action.type);
				store.prestate = store.getState();

				return dispatch(action);
			}
		}

		window.store = Redux.createStore(rootReducer, Redux.applyMiddleware(hackStore));

		/**
		 * Hacking ino store
		 */
		store.getPrestate = function(){
			return store.prestate;
		}

		store.getLastAction = function(){
			return store.last_action;
		}
	}

	defaultState(){
		return {
			admin_step: '#settings'
		}
	}

	adminStepReducer(state = '', action){
		switch(action.type){
			case CHANGE_ADMIN_STEP:
				return action.step;
			default:
				return state;
		}
	}

	findView(){
		if(this._hasFindView){
			return;
		}
		this._hasFindView = true;

		this.admin_step_go = document.querySelectorAll('.go');
	}

	event(){
		let admin_step_go = this.admin_step_go;
		admin_step_go
			.forEach((el)=>{

				el.addEventListener('click', ()=>{
					let destination = el.getAttribute('destination');
					store.dispatch({type: CHANGE_FORM_STEP, form_step: destination});

					if(destination == 'form-step-3'){
						store.dispatch({type: AJAX_CALL, ajax_call: 1});
					}
				});


			});
	}

	view(){
		let store = window.store;

		store.subscribe(()=>{
			let state = store.getState();
			let prestate = store.getPrestate();

			if(state.admin_step != prestate.admin_step){
				
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