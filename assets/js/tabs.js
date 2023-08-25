jQuery(document).ready(function($) {
	
	$( '#wplp-tabs:nth-child(1n)' ).tabs();
 
	//hover states on the static widgets
	$('#dialog_link, ul#icons li').hover(
	function() { $(this).addClass('ui-state-hover'); },
	function() { $(this).removeClass('ui-state-hover'); }
	);
	
});


jQuery(document).ready(function($) {
  
	$( "#accordion:nth-child(1n)" ).accordion({

		heightStyle: "content",
		active: false,
		collapsible: true
	
	});
  
});