$(function() {
	$('.comments').each(function(){
		object_id=jQuery(this).attr("object_id");
		object_type=jQuery(this).attr("object_type");
		_url="/core/comments/show/html/?object_type="+object_type+"&object_id="+object_id;
		jQuery(this).load(_url);
	});
});
