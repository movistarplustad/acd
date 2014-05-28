var editor = {
	$iDelete : null,
	init : function() {
		/* Delete buttons */
		$iDelete = $('input[value=delete]');

		/* Prevent accidental  delete*/
		$iDelete.bind("click", editor.confirmDelete);
	},
	confirmDelete : function(e) {
		var bDelete = window.confirm("remove the structure?");
		if(!bDelete) {
			e.preventDefault();
		}
	}
};
$(document).ready(editor.init);