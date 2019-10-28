(function ($) {
  $.fn.colorValue = function () {
    $(this).on("change", function () {
      var colorrgb = $(this).parent().find(".componentrgb").first().val();
      var coloralfa = $(this).parent().find(".componentalfa").first().val();
      var components = [];
      var colorvalue;

      components.push(parseInt(colorrgb.substring(1, 3), 16));
      components.push(parseInt(colorrgb.substring(3, 5), 16));
      components.push(parseInt(colorrgb.substring(7, 5), 16));
      if(typeof coloralfa === "undefined") {
        colorvalue = "rgb(" + components.join() + ")";
      }
      else {
        components.push(parseFloat(coloralfa));
        colorvalue = "rgba(" + components.join() + ")";
      }
      $(this).parent().find(".colorvalue").html(colorvalue);
    })

    return this;
  };
}(jQuery));