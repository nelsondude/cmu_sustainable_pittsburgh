/* Copyright (C) YOOtheme GmbH, http://www.gnu.org/licenses/gpl.html GNU/GPL */

jQuery(function($) {

    var config = $('html').data('config') || {};

    // Social buttons
    $('article[data-permalink]').socialButtons(config);

	$(document).ready(function(){
		$(".close").click(function(){
			$(this).parent().hide();
		});
		
		$(".rs_calendar_module .hasTooltip[data-original-title]").each(function(i,e){
			$(this).prepend('<span class="faketool">' + $(this).attr('data-original-title') + '</span>');
		});
		
	});
	
});
