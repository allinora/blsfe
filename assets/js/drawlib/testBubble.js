$.testBubble = function(x,y) {
	var img = $("#testBubble");
	if (img.length == 0) {
		img = $("<img>").attr("id", "testBubble").attr("src", "hi.png").width(219).height(157).appendTo("body");
	}
	
	var center = { x:70, y:img.height()+1 };
	
	img.css({
		position: "absolute",
		left: (x-center.x)+"px",
		top: (y-center.y)+"px"
	});
};

$.testBubble_in = function() {
	$("#testBubble").show();
};

$.testBubble_out = function() {
	$("#testBubble").hide();
};
