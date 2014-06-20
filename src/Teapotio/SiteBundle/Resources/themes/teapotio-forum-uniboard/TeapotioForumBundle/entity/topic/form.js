(function (window, $) {
  "use strict";

  var textareas = [],
    $editableContent;

  if (window.Teapotio == undefined ||
      window.Teapotio.wysiwyg == undefined ||
      window.Teapotio.wysiwyg.pen == undefined) {
    return;
  }

  $(document).ready(function () {
    window.Teapotio.wysiwyg.pen.loadAllWithOverlay($('.Message-body textarea'));
  });

})(window, jQuery);
