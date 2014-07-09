(function ($) {
  var Container = function () {
    var self = this,
      initialize,
      registerPageLoadEvent,
      pushStateNumber = 0,
      events = [],
      selector = 'body > .Container';

    /**
     * Register an event so we can reload them everytime we ajax some content
     * on the page
     *
     * @param  function  eventCallback
     */
    this.registerEvent = function (eventCallback) {
      events.push(eventCallback);
    };

    /**
     * Inject HTML content into the page
     *
     * @param  {object}  data  requires key 'title' and 'html'
     */
    this.inject = function (data, $target) {
      // Change title from the ajax response
      document.title = data.title;

      // Insert HTML into the content wrapper
      $target.html(data.html);

      // Go back up
      window.scrollTo(0,0);

      initializeEvents();
    };

    /**
     * Whenever we load content dynamically in the page, we update the
     * SF toolbar
     *
     * @param  string  xdebugToken
     */
    this.updateToolbar = function (xdebugToken) {
      var currentElement;

      if (typeof Sfjs !== "undefined") {
          currentElement = $('.sf-toolbar')[0];
          Sfjs.load(currentElement.id, '/_wdt/'+ xdebugToken);
      }
    };

    initialize = function () {
      registerPageLoadEvent();

      initializeEvents();
    };

    initializeEvents = function () {
      for (var i = 0; i < events.length; i++) {
        events[i]($(selector));
      }
    };

    /**
     * Register the page load event
     * It will dynamically inject content into the page
     */
    registerPageLoadEvent = function () {
      // Register dynamic page load event
      self.registerEvent(function ($container) {
        $container.find('a').click(function (event) {
          var data;

          event.preventDefault();

          $.get($(this).attr('href'), function (data, status, xhr) {
            if (!data.html) {
              return;
            }

            self.inject(data, $container);

            self.updateToolbar(xhr.getResponseHeader('X-Debug-Token'));

            return;
          });

          pushStateNumber++;

          data = {n: pushStateNumber, t: 'main-view', p: $(this).attr('href')};

          window.history.pushState(data, null, $(this).attr('href'));
        });
      });
    };

    // Initialize object
    initialize();
  };

  window.Container = new Container();
})(jQuery);
