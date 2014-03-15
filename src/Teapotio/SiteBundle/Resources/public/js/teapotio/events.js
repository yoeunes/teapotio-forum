(function (window, $) {
    var Teapotio = window.Teapotio || {};

    Teapotio.events = {
        selectors: {
            content: '.content-wrapper',
            btn: {
                reply: '.message-reply',
                quote: '.message-quote',
                toggle:'.btn-toggle',
                main: 'a.to-main'
            },
            wysiwyg: {
                wrapper: '.redactor_wysiwyg'
            }
        },

        initialize: function () {
            this.load($('body'));
            this.popstate();
        },

        load: function ($wrapper) {
            this.buttonReply($wrapper);
            this.buttonQuote($wrapper);
            this.buttonToggle($wrapper);
            this.wysiwygWrapperClick($wrapper);
            this.toMain($wrapper);
        },

        buttonReply: function ($wrapper) {
            $wrapper.find(this.selectors.btn.reply).on('click', function (event) {
                event.preventDefault();
                Teapotio.wysiwyg.fnEventBtnReply(this);
            });
        },

        buttonQuote: function ($wrapper) {
            $wrapper.find(this.selectors.btn.quote).on('click', function (event) {
                event.preventDefault();
                Teapotio.wysiwyg.fnEventBtnQuote(this);
            });
        },

        buttonToggle: function ($wrapper) {
            $wrapper.find(this.selectors.btn.toggle).on('click', function (event) {
                event.preventDefault();
                Teapotio.page.fnEventToggle(this);
            });
        },

        wysiwygWrapperClick: function ($wrapper) {
            $wrapper.find(this.selectors.wysiwyg.wrapper).on('click', function (event) {
                event.preventDefault();
                Teapotio.wysiwyg.fnEventWrapperClick(this);
            });
        },

        toMain: function ($wrapper) {
            $wrapper.find(this.selectors.btn.main).on('click', function (event) {
                event.preventDefault();
                Teapotio.page.fnEventLoadPage(this);
            });
        },

        popstate: function () {
            window.onpopstate = function (event) {
                Teapotio.page.fnEventPopstate(event);
            };
        }
    };

    Teapotio.events.initialize();

    window.Teapotio = Teapotio;

})(window, jQuery);
