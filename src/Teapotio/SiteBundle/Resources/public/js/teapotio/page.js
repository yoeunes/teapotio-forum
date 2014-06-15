(function (window, $) {
  var Teapotio = Teapotio || {};

  Teapotio.page = {

    pushStateNumber: 0,

    /**
     * Inject HTML content into the page
     *
     * @param  {object}  data  requires key 'title' and 'html'
     */
    inject: function (data, $target, callback) {
      // Change title from the ajax response
      document.title = data.title;

      // Insert HTML into the content wrapper
      $target.html(data.html);

      // Go back up
      window.scrollTo(0,0);

      callback();
    },

    updateToolbar: function (xdebugToken) {
      var currentElement;

      if (typeof Sfjs !== "undefined") {
          currentElement = $('.sf-toolbar')[0];
          Sfjs.load(currentElement.id, '/_wdt/'+ xdebugToken);
      }
    },

    fnEventToggle: function (btn) {
      var self = this;

      $.get($(btn).attr('href'), function(data) {
        var i;

        if (data.success !== 0) {
          i = $(btn).find('i');

          Teapotio.ui.toggleIcon($(i));
          Teapotio.ui.toggleElementLabel($(btn));
          Teapotio.ui.toggleElementClass($(btn));
        }
      });
    },

    fnEventLoadPage: function ($btn, $target, callback) {
        var self = this,
          data;

        $.get($btn.attr('href'), function (data, status, xhr) {
          if (!data.html) {
            return;
          }

          self.inject(data, $target, callback);

          self.updateToolbar(xhr.getResponseHeader('X-Debug-Token'));

          return;
        });

        this.pushStateNumber++;

        data = {n: this.pushStateNumber, t: 'main-view', p: $btn.attr('href')};

        window.history.pushState(data, null, $btn.attr('href'));
    },

    fnEventPopstate: function (event) {
        var self = this,
          state = event.state;

        if ($(this.selectors.content).length !== 0 && state !== null && state.t === 'main-view') {
          $.get(state.p, function (data) {
            self.inject(data);
          });
        }
    }
  };

  window.Teapotio = Teapotio;

})(window, jQuery);
