(function (window, $) {
    var Teapotio = window.Teapotio || {};

    Teapotio.wysiwyg = {

        emptyMarkup: '<p>​</p>',

        selectors: {
            btn: {
                reply: '.message-reply',
                quote: '.message-quote'
            }
        },

        initialize: function () {
            this.load(null);
        },

        load: function ($element) {
            var self = this,
                editors,
                options;

            options = {
                buttons: ['formatting', '|', 'bold', 'italic', 'deleted', '|',
                          'unorderedlist', 'orderedlist', '|',
                          'image', 'video', 'table', 'link', '|', 'alignment', '|', 'horizontalrule'],
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

        fnEventBtnReply: function (btn) {
            var self = this;

            $.get($(btn).attr('href'), function (response) {
                self.insert(response.html, $('#message-reply-to-topic .wysiwyg').first());
            });
        },

        fnEventBtnQuote: function (btn) {
            var self = this;

            $.get($(btn).attr('href'), function (response) {
                self.insert(response.html + self.emptyMarkup, $('#message-reply-to-topic .wysiwyg').first());
            });
        },

        fnEventWrapperClick: function (wrapper) {
            console.log($(wrapper).next().redactor('getBlock'));
        },

        insert: function (html, $element) {
            $element.redactor('insertHtmlAdvanced', html);
        }
    };

    Teapotio.wysiwyg.initialize();

    window.Teapotio = Teapotio;

})(window, jQuery);