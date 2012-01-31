$(function(){
	$("div").each(function(){
		if ($(this).attr("src")){
			$(this).load($(this).attr("src"));
		}
	})
})