(function (window, $) {
  $(document).ready(function () {
    $('.ComponentBoardPermissionsForm')
      .find('.ComponentBoardPermissionsRowBoard .ComponentBoardPermissionsRowBoard-inheritButton')
      .click(function(event){
        event.preventDefault();

        var target = $(this).attr('data-target');
        var inputs = $(this).parent().parent().parent('tr').find('input');

        var values = [];

        for (var i = 0; i < 11; i++) {
          values[i] = inputs[i].checked;
        }

        $('.'+ target).each(function(index, element) {
          var chk = $(element).find('input');

          for (var i = 0; i < 11; i++) {
            if (values[i] === true) {
              $(chk[i]).attr('checked', 'checked');
            }
            else {
              $(chk[i]).removeAttr('checked');
            }
          }
        });
      });
  });
})(window, jQuery);
