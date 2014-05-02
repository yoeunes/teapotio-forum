(function () {

    $(document).ready(function (event) {
        $('.btn-children-inherit').click(function(event){
            event.preventDefault();

            var target = $(this).attr('data-target');
            var inputs = $(this).parent().parent().parent('tr').find('input');

            var values = [];

            for (var i = 0; i < 10; i++) {
                values[i] = inputs[i].checked;
            }

            $('.'+ target).each(function(index, element){
                var chk = $(element).find('input');

                for (var i = 0; i < 10; i++) {
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

})();
