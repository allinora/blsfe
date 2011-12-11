$(function() {
    $("#tabs").tabs({ cookie: { expires: 30 } });
	$( "button").button(); // Apply jquery-ui style
	
	$("a.imageManagerx").fancybox({
		type: 'iframe',
		modal: false,
		autoScale: false,
		width: '100%',
		height: '100%'
		
	});
	
});
