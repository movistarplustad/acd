(function ($) {
  $.fn.colorValue = function () {
    var $inputColor = $(this);
    var $inputAlfa = $(this).parent().find(".field.componentalfa");
    var $labelClear = $(this).parent().find(".action.empty");
    $labelClear.find(".clear").addClass("hidden");
    $(this).on("change", function () {
      var colorrgb = $(this).parent().find(".componentrgb").first().val();
      var coloralfa = $(this).parent().find(".componentalfa").first().val();
      var components = '';
      var colorvalue;

      // components.push(parseInt(colorrgb.substring(1, 3), 16));
      // components.push(parseInt(colorrgb.substring(3, 5), 16));
      // components.push(parseInt(colorrgb.substring(7, 5), 16));
      components = colorrgb;
      if(typeof coloralfa === "undefined") {
        colorvalue = components;
      }
      else {
        components += parseInt(coloralfa).toString(16).padStart(2, '0');
        colorvalue = components;
      }
      $(this).parent().find(".colorvalue .value").html(colorvalue);

      // Clear widget
      $labelClear.removeClass("unset");
      $labelClear.find("input").prop('checked', false);
    })


    $labelClear.find("input").on("change", function () {
      if (this.checked) {
        $inputColor.val("");
        $inputAlfa.val("");
        $inputColor.parent().find(".colorvalue .value").html("unset");
        $labelClear.addClass("unset");
      }
      else {
        $labelClear.removeClass("unset");
      }
    })

    return this;
  };
}(jQuery));