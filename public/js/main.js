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
		// 1990-12-31T23:59:60Z
		var inputElem = document.createElement("input");
		inputElem.setAttribute("type", "date");
		if(inputElem.type === "text") {
			$("input[type=date]").datetimepicker({
				lang:'es',
				timepicker:false,
				format:"Y-m-d",
				onShow:function( ct , $input){
					this.setOptions({
						minDate: false,
						maxDate: false
					});
					if ($input.hasClass("start")){
						var value = $input.siblings("input[type=date]").val();
//console.log("max"+value, Date.parseDate(value, "Y-m-d"),"max null");
						this.setOptions({
							maxDate: value ? Date.parseDate(value, "Y-m-d").dateFormat("Y/m/d") : false
						})
					}
					if ($input.hasClass("end")){
						var value = $input.siblings("input[type=date]").val();
//console.log("min"+value, Date.parseDate(value, "Y-m-d"), "min null");
						this.setOptions({
							minDate: value ? Date.parseDate(value, "Y-m-d").dateFormat("Y/m/d") : false
						})
					}
				}
			}
		)}
		inputElem.setAttribute("type", "datetime");
		if(inputElem.type === "text") {
			$("input[type=datetime]").datetimepicker({
				lang:'es',
				step: 30,
				dayOfWeekStart: 1,
				format:"Y-m-dTH:i:sZ",
				onShow:function( ct , $input){
					this.setOptions({
						minDate: false,
						maxDate: false
					});
					if ($input.hasClass("start")){
						var value = $input.siblings("input[type=date]").val();
//console.log("max"+value, Date.parseDate(value, "Y-m-d"),"max null");
						this.setOptions({
							maxDate: value ? Date.parseDate(value, "Y-m-d").dateFormat("Y/m/d") : false
						})
					}
					if ($input.hasClass("end")){
						var value = $input.siblings("input[type=date]").val();
//console.log("min"+value, Date.parseDate(value, "Y-m-d"), "min null");
						this.setOptions({
							minDate: value ? Date.parseDate(value, "Y-m-d").dateFormat("Y/m/d") : false
						})
					}
				}

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