/* global window */
(function ($) {
  'use strict';

  $(document).ready(function (event) {
    var deletedClass = 'is-deleted';

    $('.TopicRow.TopicRow--individual')
      .find('.TopicRow-actions-delete')
      .click(function (event) {
        var $this = $(this),
          $rowElement = $this.parent().parent();

        event.preventDefault();

        $.get($this.attr('href'), function (data) {
          if (data.toggle) {
            $rowElement.addClass(deletedClass);
          } else {
            $rowElement.removeClass(deletedClass);
          }
        });
      });
  });
})(jQuery);
