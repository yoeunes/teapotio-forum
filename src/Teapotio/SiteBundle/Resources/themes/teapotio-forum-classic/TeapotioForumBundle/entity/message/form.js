(function (window, $) {
  "use strict";

  var EntityMessageForm = flight.component(function () {
    this.attributes({
      wysiwygSelector: '.wysiwyg',
      isEdit: false,
    });

    this.doSubmit = function (event) {
      var content = this.$node.find('.EntityMessage-form-editableBody').html();
      var reset = this.$node.find('.EntityMessage-form-editableBody').attr('data-reset');

      if (reset === 'false' || content === '') {
        event.preventDefault();
        return;
      }

      this.select('wysiwygSelector').val(content);
    };

    this.toggleEditor = function () {
      var val
        , $newElement;

      val = this.select('wysiwygSelector').val();
      val = val.replace(/(?:\r\n|\r|\n)/g, '<br />');

      $newElement = $('<div></div>')
        .addClass('EntityMessage-form-editableBody')
        .attr('contenteditable', 'true')
        .attr('data-reset', this.attr.isEdit)
        .html(val);

      $newElement.on('focus', function (event) {
        if ($newElement.attr('data-reset') === 'false') {
          $newElement.text('').attr('data-reset', 'true');
        }
      });

      this.select('wysiwygSelector')
        .before($newElement)
        .css('display', 'none');
    };

    this.after('initialize', function () {
      this.toggleEditor();
      this.on('submit', this.doSubmit);
    });
  });

  window.EntityMessageForm = EntityMessageForm;
})(window, jQuery);
