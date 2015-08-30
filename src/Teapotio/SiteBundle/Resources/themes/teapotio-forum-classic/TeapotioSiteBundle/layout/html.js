(function (window, $) {
  var Container = function () {
    var self = this,
      initialize,
      registerPageLoadEvent,
      togglePageLoadAnimation,
      pushStateNumber = 0,
      events = [],
      selector = 'body > .Container',
      $container = $(selector);

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
    // this.updateToolbar = function (xdebugToken) {
    //   var currentElement;
    //
    //   if (typeof Sfjs !== "undefined") {
    //       currentElement = $('.sf-toolbar')[0];
    //       Sfjs.load(currentElement.id, '/_wdt/'+ xdebugToken);
    //   }
    // };

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
      self.registerEvent(function () {
        EntityMessageForm.attachTo('.EntityTopic-form--new, .EntityMessage-form--new');
        EntityMessageForm.attachTo(
          '.EntityTopic-form--edit, .EntityMessage-form--edit',
          { isEdit: true }
        );
      });

      // Register dynamic page load event
      self.registerEvent(function ($container) {
        $container.find("a[data-dynamic!='false'][data-toggle!='true'][data-external!='true']").click(function (event) {
          var data;

          event.preventDefault();

          togglePageLoadAnimation(true);
          $.get($(this).attr('href'), {json: 1}, function (data, status, xhr) {
            if (!data.html) {
              return;
            }

            self.inject(data, $container);

            // self.updateToolbar(xhr.getResponseHeader('X-Debug-Token'));

            togglePageLoadAnimation(false);

            return;
          });

          pushStateNumber++;

          data = {n: pushStateNumber, t: 'main-view', p: $(this).attr('href')};

          window.history.pushState(data, null, $(this).attr('href'));
        });

        window.onpopstate = function (event) {
          if ($container.length !== 0 && event.state !== null && event.state.t === 'main-view') {
            togglePageLoadAnimation(true);

            $.get(event.state.p, {json: 1}, function (data) {
              self.inject(data, $container);

              togglePageLoadAnimation(false);
            });
          } else {
            window.location = window.location.href;
          }
        };
      });
    };

    togglePageLoadAnimation = function (bool) {
      if (bool == true && !$container.hasClass('is-loading')) {
        $container.addClass('is-loading');
      } else if (bool == false && $container.hasClass('is-loading')) {
        $container.removeClass('is-loading');
      }
    };

    // Initialize object
    initialize();
  };

  $(document).ready(function () {
    window.Container = new Container();
  });
})(window, jQuery);
