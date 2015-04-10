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

		/* Sortable collection fields */
		$( ".collection" ).sortable({
			items: "li:not(.find)"
		});

		/* Date fields */
		/* Polyfill */
		var inputElem = document.createElement("input");
		inputElem.setAttribute("type", "date");
		if(inputElem.type === "text") {
			$("input[type=date]")
				.datepicker({
					dateFormat: 'yy-mm-dd',
					firstDay: 1,
					dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
					dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
					monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']
				});
		}
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