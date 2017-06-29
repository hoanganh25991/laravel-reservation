(function(){
  let vue_state = {
    selected_outlet: null,
    outlet_id: null,
    outlets: [],
    base_url: null,
  };

  // What server give us
  let server_state           = window.navigator_state || {};
  window.navigator_vue_state = Object.assign(vue_state, server_state);

  // Ok, build it
  new Vue({
    el: '#outlet_select',
    data: window.navigator_vue_state,
    mounted(){
      console.log('navigator mounted');
      let self = this;
      document.addEventListener('outlet_id', function(e){
        let data = e.detail;

        let {outlet_id} = data;

        self.outlet_id = outlet_id;
      });
    },
    watch: {
      outlet_id(outlet_id){
        //if outlet_id resolved, update info
        let matched_outlets = this.outlets.filter(function(outlet){return outlet.id == outlet_id});
        let selected_outlet = matched_outlets[0] || {};
        // Ok update outlet
        this.selected_outlet= selected_outlet;
      }
    },
    methods: {
      _switchOutlet(outlet_id){
        // Update new selected outlet_id for it self
        this.outlet_id = outlet_id;
        // Build data to notify out
        let data      = {outlet_id};
        //notify it out, who catch get data to move on
        document.dispatchEvent(new CustomEvent('switch-outlet', {detail: data}));
      },

      _goToPage(whichPage){
        let {base_url} = this;
        let current_url = window.location.href;
        let redirect_url = `${base_url}/${whichPage}`;

        let is_on_admin_page = base_url == current_url;
        // On admin base self redirect
        // On reservation or setting page
        // Let them self redirect
        if(is_on_admin_page){
          window.location.href = redirect_url;
        }
        // notify it out
        let data = {redirect_url};
        document.dispatchEvent(new CustomEvent('go-to-page', {detail: data}));
      }
    }
  });
})();