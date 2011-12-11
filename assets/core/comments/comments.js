$(function() {
	$('.comments').each(function(){
		object_id=jQuery(this).attr("object_id");
		object_type=jQuery(this).attr("object_type");
		_url="/core/comments/show/html/?object_type="+object_type+"&object_id="+object_id;
		jQuery(this).load(_url, function(){
			jQuery("abbr.commenttimeago").timeago();
		  
		});
	});
});


function handleCommentAdd(f,lyr){
	_url="/core/comments/add/?object_type="+f.object_type.value+"&object_id="+f.object_id.value+"&comment="+escape(f.comment.value);
	$.get(_url, function(){
		_url="/core/comments/show/html?object_type="+f.object_type.value+"&object_id="+f.object_id.value;
		lyr.load(_url);
	});
	return false;
}

	
