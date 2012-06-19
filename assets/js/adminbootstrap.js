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
    $(".sortable").tablesorter(); 

  	$('.tablesorter tr:odd').addClass("odd");
  	$('.tablesorter tr:even').addClass("even");
    
	$( "input.date" ).datepicker({
		'dateFormat' : 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		yearRange: 'c-50:c+50'
	});

	$('input.datetimepicker').datetimepicker({
		'dateFormat' : 'yy-mm-dd',
		'timeFormat': 'hh:mm:ss'
	});
	$('#logout').click(function(){
		self.location="/core/admin/?op=logout";
	});

});
