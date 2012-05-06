function companyImage(lyr,id,url){
    $('.fancybox-close').trigger('click');
	$('#'+lyr).val(id);
	$('#companyImageManager-'+lyr).html('<img src="'+url+'/100">');
}
function systemImage(lyr,id,url){
    $('.fancybox-close').trigger('click');
	$('#'+lyr).val(id);
	$('#systemImageManager-'+lyr).html('<img src="'+url+'/100">');
}
