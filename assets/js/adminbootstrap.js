$(function() {
    $("#tabs").tabs({ cookie: { expires: 30 } });
	$( "button").button(); // Apply jquery-ui style
	
	$("a.imageManager").fancybox({
		type: 'iframe',
		modal: false,
		autoScale: false,
		width: '100%',
		height: '100%'
		
	});
	
});

function systemImage(lyr,id,url){
	//$('#fancybox-close').show();
    $('#fancybox-close').trigger('click');
	$('#'+lyr).val(id);
	$('#systemImageManager-'+lyr).html('<img src="'+url+'/100">');  // Another thumb
}