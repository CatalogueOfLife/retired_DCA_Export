$(function() {
	$('#classification input').autocomplete( {
		source : function(request, response) {
			var currentRank = this.element.context.id;
			var url = 'includes/ajax_search.php?rank=' + currentRank;
			$('input').each(function() {
				var id = $(this).attr('id');
				var val = $(this).val();
				url += '&' + id + '=' + val;
			});
			$.getJSON(url, function(data) {
				response(data, function() {
				});
			});
		},
		minLength : 0
	});
	$('.showall').click(function() {
		var rankId = '#' + this.previousElementSibling.id;
		$(rankId).focus();
		$(rankId).autocomplete("search", "");
	});
	$('span, button', '#classification').button();
	$('#radio').buttonset();
	$('#classification #reset').click(function() {
		$('input:text', '#classification').val('');
		return false;
	})

});
