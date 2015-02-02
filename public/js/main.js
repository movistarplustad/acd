var editor = {
	$body : null,
	$iDelete : null,
	init : function() {
		/* Tools menu */
		editor.$body = $("body");
		$("#header-menu .tools-menu")
			.bind("click", function(e) {
				editor.$body.toggleClass("withOptions");
				e.preventDefault();
			})
		/* Delete buttons */
		editor.$iDelete = $('input[value=delete]');

		/* Prevent accidental  delete*/
		editor.$iDelete.bind("click", editor.confirmDelete);
	},
	confirmDelete : function(e) {
		var bDelete = window.confirm("remove permanently this element?");
		if(!bDelete) {
			e.preventDefault();
		}
	}
};
var isSupported = document.getElementById && document.getElementsByTagName;
if (isSupported) {
	document.documentElement.className = "js";
}
$(document).ready(editor.init);