(function (window, $) {
    var Teapotio = window.Teapotio || {};

    Teapotio.wysiwyg = {

        emptyMarkup: '<p>&#8203;​</p>',

        selectors: {
            btn: {
                reply: '.message-reply',
                quote: '.message-quote'
            }
        },

        initialize: function () {
            this.loadWithToolbar(null);
            this.loadWithOverlay(null);
        },

        loadWithToolbar: function ($element) {
            var self = this,
                editors,
                options,
                buttons;

            buttons = [
              'formatting', '|', 'bold', 'italic', 'deleted', 'underline', '|',
              'unorderedlist', 'orderedlist', '|',
              'image', 'video', 'link', '|',
              'alignleft', 'aligncenter', 'alignright', '|', 'horizontalrule'];

            options = {
                buttons: buttons,
                convertVideoLinks: true,
                toolbarFixed: true,
                toolbarFixedBox: true,
                focusCallback: function (e) {
                    var className = 'redactor_wysiwyg-initial';

                    if ($(e.currentTarget).hasClass(className)) {
                        $(e.currentTarget)
                            .removeClass('redactor_wysiwyg-initial')
                            .html(self.emptyMarkup);
                    }
                },
                keyupCallback: function (e) {
                    self.focusAway(this.$element);
                },
                changeCallback: function (html) {
                  // console.log(html);
                }
            };

            if ($element === null) {
                $editors = $('.wysiwyg');
            } else {
                $editors = $element.find('.wysiwyg');
            }

            $editors.each(function (index, element) {
                $(element).redactor(options);
            });
        },

        loadWithOverlay: function ($element) {
            var self = this,
                editors,
                options;

            options = {
                air: true,
                airButtons: ['formatting', '|', 'bold', 'italic', 'deleted', '|',
                          'unorderedlist', 'orderedlist', '|',
                          'image', 'video', 'table', 'link', '|', 'alignment', '|', 'horizontalrule'],
                convertVideoLinks: true,
            };

            if ($element === null) {
                $editors = $('.wysiwyg-overlay');
            } else {
                $editors = $element.find('.wysiwyg-overlay');
            }

            $editors.each(function (index, element) {
                $(element).redactor(options);
            });
        },

        fnEventBtnReply: function (btn) {
            var self = this;

            $.get($(btn).attr('href'), function (response) {
                self.insert(response.html, $('#message-reply-to-topic .wysiwyg').first());
            });
        },

        fnEventBtnQuote: function (btn) {
            var self = this;

            $.get($(btn).attr('href'), function (response) {
                self.insertBlock(response.html, $('#message-reply-to-topic .wysiwyg').first());
            });
        },

        fnEventWrapperClick: function (wrapper) {
            this.focusAway($(wrapper).next());
        },

        insert: function (html, $element) {
            $element.redactor('insertHtmlAdvanced', html);
            this.focusAway($element);
        },

        insertBlock: function (html, $element) {
            $(html).appendTo($element.prev());
            this.focusAway($element);
        },

        focusAway: function ($element) {
          var $activeBlock = $($element.redactor('getParent')),
              $wysiwygElement = $activeBlock.parents('.wysiwyg-block').first(),
              $wysiwygElementNeighbor = $wysiwygElement.next();

          if ($wysiwygElement.length === 0) {
            return;
          }

          if ($wysiwygElementNeighbor.length === 0) {
            $wysiwygElementNeighbor = $(this.emptyMarkup);
            $wysiwygElementNeighbor.insertAfter($wysiwygElement);
          }

          $element.redactor('setCaret', $wysiwygElementNeighbor, 0);
        }
    };

    Teapotio.wysiwyg.initialize();

    window.Teapotio = Teapotio;

})(window, jQuery);
