jQuery(document).ready(function($) {

	var $Form = $("#original_post_ranking_widget_form");

	// reset
	$Form.children("p.reset").children("input").click(function() {
		$Form.children("table").children("tbody").children("tr").children("td").children("textarea").val("");

		$Form.submit();
	});




	// toggle adder
	$("body.widgets-php .original-post-ranking-widget-ranklists .add h4 a.add-toggle").live("click", function() {
		$(this).parents('div:first').toggleClass( 'wp-hidden-children' );
		return false;
	});

	// add button
	$("body.widgets-php .original-post-ranking-widget-ranklists .add p input.id-add-button").live("click", function() {
		var $WidgetForm = $(this).parent().parent().parent().parent().parent();
		$('div.widget-control-actions input', $WidgetForm).click();
		
		return false;
	});

	// delete button
	$("body.widgets-php .original-post-ranking-widget-ranklists .list .ranking-list .alignright .id-remove-button").live('click', function() {
		var $WidgetForm = $(this).parent().parent().parent().parent().parent().parent();
		var $ID = parseFloat($(this).parent().parent().children(".alignleft").children(".no").text()) - 1;
		var $List = $(this).parent().parent();
		
		console.log($(this).attr("rel"));
		console.log($(this).parent().parent().children(".alignleft").children(".no").val());
		console.log();

		$('body.widgets-php .original-post-ranking-widget-ranklists .list input.delete').val($ID);
		$List.toggleClass("Deleted");
		$List.animate({ height: "toggle", opacity: 0 }, 1000);
		$('div.widget-control-actions input', $WidgetForm).click();
		
		return false;
	});

	// sort
	$("body.widgets-php .original-post-ranking-widget-ranklists .list").live("mouseover", function() {
		var $WidgetForm = $(this).parent().parent().parent().parent().parent().parent();
		
		
		$("body.widgets-php .original-post-ranking-widget-ranklists .list").sortable({
			"placeholder": "widget-placeholder",
			update:function() {
				$(this).parent().css({ "opacity":"0.5" });
				var $WidgetForm = $(this).parent().parent().parent();
				$('div.widget-control-actions input', $WidgetForm).click();
			}
		});
	});


});
