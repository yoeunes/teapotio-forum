(function (window, $) {
  var Teapotio = window.Teapotio || {};

  Teapotio.wysiwyg = Teapotio.wysiwyg || {};

  Teapotio.wysiwyg.pen = {

    loadWithOverlay: function ($element) {
      var self = this,
          options;

      options = {
        editor: $element, // {DOM Element} [required]
        class: 'pen', // {String} class of the editor,
        debug: false, // {Boolean} false by default
        textarea: '', // fallback for old browsers
        list: ['bold', 'italic', 'underline', 'h2', 'h3', 'p'], // editor menu list
        stay: false
      }

      return new Pen(options);
    },
  };

  window.Teapotio = Teapotio;

})(window, jQuery);
