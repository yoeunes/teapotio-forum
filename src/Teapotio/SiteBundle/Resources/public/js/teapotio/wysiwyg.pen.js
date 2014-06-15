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

    loadAllWithOverlay: function ($elements) {
      $elements.each(function (index, element) {
        var $editableContent = $('<div></div>');
        $editableContent.prependTo($(element).parent());

        $(element).css({display: 'none'});
        $editableContent.html($(element).text());

        if ($(element).attr('required')) {
          $editableContent.attr('required', $(element).attr('required'));
        }

        window.Teapotio.wysiwyg.pen.loadWithOverlay($editableContent[0]);

        $editableContent.keypress(function (event) {
          $(element).text($(this).html());
        });
      });
    }
  };

  window.Teapotio = Teapotio;

})(window, jQuery);
