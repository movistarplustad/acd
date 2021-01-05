(function ( $ ) {
	$.fn.aliasIdFormat = function() {
		$(this).on("blur", function() {
			var untrimmed = $(this).val();
			$(this).val($.trim(untrimmed));
		})

		return this;
	};
}( jQuery ));