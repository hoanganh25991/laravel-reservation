(function(){
  let frontend_state = {selected_outlet_id: null};

  // Build vue_state
  window.vue_state = Object.assign({}, frontend_state, state);

  let vue = new Vue({
    el: '#app',
    data: window.vue_state,
    mounted(){
      document.dispatchEvent(new CustomEvent('vue-mounted'));
    },
    updated(){
      this._goToAdminReservations();
    },
    methods: {
      _goToAdminReservations(){
        let vue = this;

        //noinspection JSUnresolvedVariable
        let outlet_id = vue.selected_outlet_id;

        let redirect_url = `admin/reservations?outlet_id=${outlet_id}`;

        window.location.href = vue._url(redirect_url);
      },

      _url(path){
        //noinspection JSUnresolvedVariable
        let base_url = this.base_url || '';

        if(base_url.endsWith('/')){
          base_url = path.substr(1);
        }

        if(path.startsWith('/')){
          path = path.substr(1);
        }

        return `${base_url}/${path}`;
      }
    }
  });
})()