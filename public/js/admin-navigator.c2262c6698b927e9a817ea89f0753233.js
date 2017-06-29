'use strict';

(function () {
  var vue_state = {
    selected_outlet: null,
    outlet_id: null,
    outlets: [],
    base_url: null
  };

  // What server give us
  var server_state = window.navigator_state || {};
  window.navigator_vue_state = Object.assign(vue_state, server_state);

  // Ok, build it
  new Vue({
    el: '#outlet_select',
    data: window.navigator_vue_state,
    mounted: function mounted() {
      console.log('navigator mounted');
      var self = this;
      document.addEventListener('outlet_id', function (e) {
        var data = e.detail;

        var outlet_id = data.outlet_id;


        self.outlet_id = outlet_id;
      });
    },

    watch: {
      outlet_id: function outlet_id(_outlet_id) {
        //if outlet_id resolved, update info
        var matched_outlets = this.outlets.filter(function (outlet) {
          return outlet.id == _outlet_id;
        });
        var selected_outlet = matched_outlets[0] || {};
        // Ok update outlet
        this.selected_outlet = selected_outlet;
      }
    },
    methods: {
      _switchOutlet: function _switchOutlet(outlet_id) {
        // Update new selected outlet_id for it self
        this.outlet_id = outlet_id;
        // Build data to notify out
        var data = { outlet_id: outlet_id };
        //notify it out, who catch get data to move on
        document.dispatchEvent(new CustomEvent('switch-outlet', { detail: data }));
      },
      _goToPage: function _goToPage(whichPage) {
        var base_url = this.base_url;

        var current_url = window.location.href;
        var redirect_url = base_url + '/' + whichPage;

        var is_on_admin_page = base_url == current_url;
        // On admin base self redirect
        // On reservation or setting page
        // Let them self redirect
        if (is_on_admin_page) {
          window.location.href = redirect_url;
        }
        // notify it out
        var data = { redirect_url: redirect_url };
        document.dispatchEvent(new CustomEvent('go-to-page', { detail: data }));
      }
    }
  });
})();