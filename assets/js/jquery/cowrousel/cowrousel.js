/* 
 * @Args:
 * 		.loop (bool)				: allows looping over the nav items
 * 		.speed (int)				: time interval (ms) to slide the content
 * 		.slideCallback (function)	: fired whenever the user clicks on the prev/next arrows
 * 		.height (int)				: height of the widget
 * 		.numElementsVisible (int)	: number of <li> to show on the sliding window
 * 		.elementWidth (int)			: width of all <li> 
 * 		.autoInterval (int)			: delay in ms before autoscrolling the widget
 * 		.spacing (int)				: spacing in pixels between two <li>
 * 
 * Example:
 * 
	$(".foo").Cowrousel({
		loop: true,
		speed: 100,
		height: 90,
		numElementsVisible: 3,
		elementWidth: 100,
		autoInterval: 2000,
		spacing: 15,
		slideCallback: function(x) { console.log("SlideCallback"); }
	});
 */
 
$.fn.Cowrousel = function (Args) {
	var $this = $(this);
	
	var $carousel = $("<div>").addClass("cowrousel");
	var $left = $("<div></div>").addClass("left").addClass("nav");
	$("<div>").appendTo($left);
	var $right = $("<div></div>").addClass("right").addClass("nav");
	$("<div>").appendTo($right);
	var $content = $("<div>").addClass("content");
	
	$this.empty().append($carousel);
	var $ul = $("<ul>");
	
	$carousel.append($left).append($content).append($right);
	$ul.appendTo($content);
	
	Args.width = Args.numElementsVisible * (Args.elementWidth + Args.spacing) - Args.spacing;
	
	$content.height(Args.height).width(Args.width);
	$left.css("marginTop", (Args.height-$left.outerHeight())/2);
	$right.css("marginTop", (Args.height-$right.outerHeight())/2);
	
	//fix floating
	$("<div>").css("clear", "both").insertAfter($carousel);
	
	var cowrouselIndex = 0;
	
	var onContentChanged = function() {
		//delete clones
		$ul.find("li.cloned").remove();
		
		var $lis = $ul.find("li");
		
		//hide borders?
		if ($lis.length <= Args.numElementsVisible) {
			$left.css("visibility", "hidden");
			$right.css("visibility", "hidden");
		}
		else {
			$left.css("visibility", "visible");
			$right.css("visibility", "visible");
		}
		
		//dupplicate <li> for looping
		if (Args.loop && $lis.length > Args.numElementsVisible && $lis.length < Args.numElementsVisible*2) {
			$lis.each(function (i, li) {
				var $li = $(li);
				var $clone = $li.clone().addClass("cloned").appendTo($ul);
			});
		}
		
		resetTimer();
	};
	
	var addLI = function () {
		var li = $("<li>");
		li.width(Args.elementWidth).height(Args.height).css("marginRight", Args.spacing);
		li.appendTo($ul);
		
		return li;
	};
	
	var removeLI = function (li) {
		li = $(li);
		li.remove();
	};
	
	var timer = null;
	var resetTimer = function () {
		stopTimer();
		if (Args.autoInterval > 0 && $ul.find("li").length > Args.numElementsVisible) {
			timer = setTimeout(function(){ $right.click(); }, Args.autoInterval);
		}
	};
	var stopTimer = function () {
		if (timer != null)
			clearTimeout(timer);
		timer = null;
	};
	
	//the goddamn functions (enjoy)
	var bufferRight = function () {
		$ul.find("li:first").detach().appendTo($ul);
		var x = parseInt($ul.css("marginLeft"));
		$ul.css("marginLeft", (x + Args.elementWidth+Args.spacing)+"px");
	};
	var bufferLeft = function () {
		$ul.find("li:last").detach().prependTo($ul);
		var x = parseInt($ul.css("marginLeft"));
		$ul.css("marginLeft", (x - (Args.elementWidth+Args.spacing))+"px");
	};
	
	//another nice function to fade arrows when they're useless.
	var magicArrows = function () {
		if (!Args.loop) {
			$right.find("*").stop(true, true).animate({ opacity: cowrouselIndex == $ul.find("li").length ? 0.0 : 1.0 }, S);
			
			_dx = $ul.find("li:first").offset().left + sdx;
			_x = $content.offset().left;
			$left.find("*").stop(true, true).animate({ opacity: cowrouselIndex == 0 ? 0.0 : 1.0 }, S);
		}
	};
	
	//bind some stuff...
	$right.click(function() {
		if($ul.is(":animated") || $ul.find("li").length <= Args.numElementsVisible) {
			return;
		}
		
		$ul.stop(true, true);
		
		var _dx = $ul.find("li:last").offset().left;
		var _x = $content.offset().left + $content.innerWidth();
		
		if (_dx < _x) {
			if (!Args.loop)
				return false;
			bufferRight();
		}
		
		cowrouselIndex++;
		$ul.animate(
			{ "marginLeft": "-="+(Args.elementWidth+Args.spacing)+"px" },
			Args.speed
		);
		resetTimer();
		magicArrows(-1);
		
		//callback
		if (Args && typeof Args.slideCallback == "function") {
			Args.slideCallback();
		}
	});
	
	$left.click(function() {
		if($ul.is(":animated") || $ul.find("li").length <= Args.numElementsVisible) {
			return;
		}
		
		$ul.stop(true, true);
		
		var _dx = $ul.find("li:first").offset().left;
		var _x = $content.offset().left;
		
		if (_dx >= _x) {
			if (!Args.loop)
				return false;
			bufferLeft();
		}
		
		cowrouselIndex--;
		$ul.animate(
			{ "marginLeft": "+="+(Args.elementWidth+Args.spacing)+"px" },
			Args.speed
		);
		resetTimer();
		magicArrows(1);
		
		//callback
		if (Args && typeof Args.slideCallback == "function") {
			Args.slideCallback();
		}
	});
	
	$this.hover(
		function () {
			stopTimer();
		},
		function () {
			resetTimer();
		}
	);
	
	magicArrows(0);
	resetTimer();
	
	//add custom functions
	$this.addEntry = function (content) {
		var li = addLI();
		li.append(content);
		onContentChanged();
	};
	
	$this.addEntries = function (contents) {
		for (var i=0; i<contents.length; i++) {
			var li = addLI();
			li.append(contents[i]);
		}
		onContentChanged();
	};
	
	$this.removeEntry = function (li) {
		removeLI(li);
		onContentChanged();
	};
	
	onContentChanged();
	
	return $this;
};
