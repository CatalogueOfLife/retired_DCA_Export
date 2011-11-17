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
				response(data, function() {
				});
			});
		},
		minLength : 0
	});
	$('.showall').click(function() {
		$(this).prev().focus().autocomplete("search", "");
	});
	$('span, button', '#classification').button();
	$('#radio').buttonset();
	$('#classification #reset').click(function() {
		$('input:text', '#classification').val('');
		$('input:radio[name="block"]').filter('[value="4"]').attr('checked', true).button('refresh');
		return false;
	});

	$("#blah").mouseover(function() {
		var width = 250;
		var height = 270;
		var posX = $(this).offset().left - $(document).scrollLeft() - width + $(this).outerWidth();
		var posY = $(this).offset().top - $(document).scrollTop() + $(this).outerHeight();
		$("#dialog").dialog({width:width, height:height ,position:[posX, posY]});
	});

});

