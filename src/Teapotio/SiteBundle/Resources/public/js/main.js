(function (window, document) {

    $(document).ready(function (event) {

        $("#overlay")
            // .height($(document).height())
            .click(function(event){
                $(this).addClass('hide');
            });

        $("#nav-components a.to-main").on('click', function (event) {
            $("#nav-components").slideUp(100, function () {
                $(this).children().css('display', 'none');
            });

        });

        $(".form-save").on('submit', function(event){

            var form = this;

            $(form).find('.feedback').html('');

            $.post($(this).attr('action'), $(this).serialize(), function(data) {
                if (data.success === 0) {
                    $(form).find('.feedback').html('<div class="alert alert-danger">'+ data.message +'</div>');
                }
                else {
                    $(form).find('.feedback').html('<div class="alert alert-success">'+ data.message +'</div>');
                }
            });

            event.preventDefault();
        });

        $("#nav-horizontal a").click(function (event) {
            event.preventDefault();

            var targetSelector = $(this).attr('href');
            var $targetElement = $(targetSelector);
            var targetDisplay = $targetElement.css('display');
            var display;

            $('#nav-components .nav-component').css('display', 'none');

            if (targetDisplay === 'block') {
                display = 'none';
            } else {
                display = 'block';
            }

            $('#nav-components, ' + targetSelector).css('display', display);

        });

        $("#nav-components .collapse").click(function (event) {
            event.preventDefault();
            $('#nav-components .nav-component, #nav-components').css('display', 'none');
        });

        $('.toggle-visibility').on('click', function(event){
            var target = $(this).attr('data-target');
            var targets = target.split(',');

            for (var i = 0; i < targets.length; i++) {
                if ($(targets[i]).css('display') === 'block') {
                    $(targets[i]).css('display', 'none');
                } else {
                    $(targets[i]).css('display', 'block');
                }
            }

            Teapotio.ui.toggleElementLabel($(this));

            Teapotio.ui.toggleElementClass($(this));

            event.preventDefault();
        });

        $("#nav-list-boards .expand").on('click', function(event){
            var $list = $(this).parent().next('ul');

            $list.slideToggle();
            Teapotio.ui.toggleElementIcon($(this));

            event.preventDefault();
        });

        $(".boards-expand-list").on('click', function(event){
            var $list = $(".boards-hidden-list");

            $list.slideToggle();
            Teapotio.ui.toggleElementIcon($(this));
            event.preventDefault();

        });

        $(".expand-subcategory").on('click', function(event){
            var uls = $(this).siblings('ul');

            $(uls[0]).slideToggle();
            Teapotio.ui.toggleElementIcon($(this));

            event.preventDefault();
        });
    });

})(window, document);