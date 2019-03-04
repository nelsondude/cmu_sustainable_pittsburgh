(function($){

	$(document).ready(function() {
	
		$('a.submission' , '.content').each(function(){
			var name = $(this).text().split("- ").pop();	 
			var _href = $(this).attr("href");
			$(this).attr("href", _href + '&name=' + encodeURIComponent(name));		
		});
		
		$('.pos-content legend').empty();
		
		$('.element-itemname').hide();
		
		var fin = decodeURIComponent(GetURLParameter('name'));
		$('.submission .headline').text(fin);
		$('.element-itemname input').val(fin);
	
		function GetURLParameter(sParam)
		{
			var sPageURL = window.location.search.substring(1);
			var sURLVariables = sPageURL.split('&');
			for (var i = 0; i < sURLVariables.length; i++)
			{
				var sParameterName = sURLVariables[i].split('=');
				if (sParameterName[0] == sParam)
				{
					return sParameterName[1];
				}
			}
		}

	});

})(jQuery);