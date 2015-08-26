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
		$( ".fields .items" ).sortable();

		/* Sortable collection fields */
		$( ".collection" ).sortable({
			items: "li:not(.find)"
		});

		/* Content list */
		$("#contents_list")
			.each(function() {
				var lowerLimit = $(this).data("lower-limit") || 0;
				$(this).css("counter-reset", "num-item " + lowerLimit); // Set first position number
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
						this.setOptions({
							maxDate: value ? Date.parseDate(value, "Y-m-d").dateFormat("Y/m/d") : false
						})
					}
					if ($input.hasClass("end")){
						var value = $input.siblings("input[type=date]").val();
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
				format:"Y-m-dTH:i:00Z",
				onShow:function( ct , $input){
					this.setOptions({
						minDate: false,
						maxDate: false
					});
					if ($input.hasClass("start")){
						var value = $input.siblings("input[type=date]").val();
						this.setOptions({
							maxDate: value ? Date.parseDate(value, "Y-m-d").dateFormat("Y/m/d") : false
						})
					}
					if ($input.hasClass("end")){
						var value = $input.siblings("input[type=date]").val();
						this.setOptions({
							minDate: value ? Date.parseDate(value, "Y-m-d").dateFormat("Y/m/d") : false
						})
					}
				}

				});
		}

		/* Textarea WYSIWYG  */
		CKEDITOR.replaceAll("richtext");

		/* Tags */
		$(".tags")
			.filter(function() {
				var options = {};

				if(typeof $(this).attr("readonly") !== "undefined") {
					options.readOnly = true;
				}
				if(typeof $(this).attr("list") !== "undefined") {
					var idList = $(this).attr("list");
					var autocompleteList = $("#"+idList + " option")
						.map(function(){
							return $(this).attr("value");
						}).get();
					//options.autocomplete: { source: true };
					options.availableTags = autocompleteList;
				}

				$(this).tagit(options);

				var sortableTags = $(".tags.sortable");
				$(sortableTags).siblings(".tagit").sortable({
					stop: function(event,ui) {
						$(sortableTags).val(
							$(".tagit-label",$(this))
								.clone()
								.text(function(index,text){ return (index == 0) ? text : "," + text; })
								.text()
						);
					}
		  	      });

			/* Alias Id. */
			//$(".aliasId").aliasIdFormat();
			$("input").aliasIdFormat();

			/* Select simple and multiple */
			$('.field.select option[value=""]').html(""); // Resolve bug of selectivity for empty options
			$('.field.select').selectivity({
				allowClear: true,
				placeholder: "Select option…"
			});
		});

		/* Enumerated */
		// Return a helper with preserved width of cells
		var fixHelper = function(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
			});
			return ui;
		};

		$(".result_table.enumerated tbody").sortable({
			helper: fixHelper
		});

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