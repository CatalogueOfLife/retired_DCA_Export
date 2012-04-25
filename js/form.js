$(function() {
	$('#classification input').autocomplete( {
		source : function(request, response) {
			var currentRank = this.element.context.id;
			var url = 'includes/ajax_search.php?rank=' + currentRank;
			$('input:text').each(function() {
				var id = $(this).attr('id');
				var val = $(this).val();
				url += '&' + id + '=' + val;
			});
			$.getJSON(url, function(data) {
				if (data == 'too_many') {
					alert("Too many results to display. Please extend the search string or select a higher taxon first.");
					data = null;
				}
				response(data, function() {
				});
			});
		},
		minLength : 0,
		delay: 1000
	});
	$('.showall').click(function() {
		$(this).prev().focus().autocomplete("search", "", { delay: 0 });
	});
	$('span, button', '#classification').button();
	$('#radio').buttonset();
	$('#classification #reset').click(function() {
		$('input:text', '#classification').val('');
		$('input:radio[name="block"]').filter('[value="4"]').attr('checked', true).button('refresh');
		return false;
	});

	$("#radio label").tooltip({
		position: "bottom center",
		offset: [10, 0],
		effect: "fade",
		opacity: 0.8
	});
});

