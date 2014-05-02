(function (window, $) {
  "use strict";

  var textareas = [],
    $editableContent;

  if (window.Teapotio == undefined ||
      window.Teapotio.wysiwyg == undefined ||
      window.Teapotio.wysiwyg.pen == undefined) {
    return;
  }

  textareas = $('.Message-body textarea');

  textareas.each(function (index, element) {
    $editableContent = $('<div></div>');
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
})(window, jQuery);
