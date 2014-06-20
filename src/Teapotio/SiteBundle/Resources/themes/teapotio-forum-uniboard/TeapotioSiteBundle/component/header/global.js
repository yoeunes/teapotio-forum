(function ($) {
  'use strict';

  $('.Header.Header--global .Header-hamburger').click(function (event) {
    var extClass = 'Header-navigation--extended',
      $navigationElement = $(this).parent().children('.Header-navigation');

    if ($navigationElement.hasClass(extClass)) {
      $navigationElement.removeClass(extClass);
    } else {
      $navigationElement.addClass(extClass);
    }
  });
})(jQuery);
