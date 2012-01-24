/* @this: a <ul> element, or a list of <ul> elements
 * 
 * @Args:
 * 		.hideBordersBelow (int)		: hides left/right arrows if not enough data to show
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
	$("ul").Cowrousel({
		loop: true,
		hideBordersBelow: 0,
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
	return $(this).each(function (j, el) {
		var $this = $(el);
		
		if (!$this.is("ul")) {
			throw "Acordeon plugin must be cast on an UL element";
		}
		
		var $carousel = $("<div>").addClass("cowrousel");
		$carousel;
		var $left = $("<div></div>").addClass("left").addClass("nav");
		$("<div>").appendTo($left);
		var $right = $("<div></div>").addClass("right").addClass("nav");
		$("<div>").appendTo($right);
		var $content = $("<div>").addClass("content");
		
		$carousel.insertBefore($this);
		var $ul = $this.detach();
		var $lis = $ul.find("li");
		$lis.width(Args.elementWidth).height(Args.height).css("marginRight", Args.spacing);
		
		$carousel.append($left).append($content).append($right);
		$ul.appendTo($content);
		
		Args.width = Args.numElementsVisible * (Args.elementWidth + Args.spacing) - Args.spacing;
		
		$content.height(Args.height).width(Args.width);
		$left.css("marginTop", (Args.height-$left.outerHeight())/2);
		$right.css("marginTop", (Args.height-$right.outerHeight())/2);
		
		//fix floating
		$("<div>").css("clear", "both").insertAfter($carousel);
		
		//hide borders?
		if ($lis.length < Args.hideBorderBelow) {
			$left.css("visibility", "hidden");
			$right.css("visibility", "hidden");
		}
		
		//dupplicate <li> for looping
		if (Args.loop) {
			$lis.each(function (i, li) {
				var $li = $(li);
				$li.clone().addClass("cloned").appendTo($ul);
			});
		}
		
		var timer = null;
		var resetTimer = function () {
			stopTimer();
			if (Args.autoInterval != undefined && Args.autoInterval > 0) {
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
			$ul.find("li:last").detach().appendTo($ul);
			var x = parseInt($ul.css("marginLeft"));
			$ul.css("marginLeft", (x + Args.elementWidth+Args.spacing)+"px");
		};
		var bufferLeft = function () {
			$ul.find("li:last").detach().prependTo($ul);
			var x = parseInt($ul.css("marginLeft"));
			$ul.css("marginLeft", (x - (Args.elementWidth+Args.spacing))+"px");
		};
		
		//another nice function to fade arrows when they're useless.
		var magicArrows = function (sgn) {
			if (!Args.loop) {
				var sdx = (sgn > 0 ? 1 : -1) * (Args.elementWidth + Args.spacing);
				var S = sdx == 0 ? 0 : Args.speed;
				
				var _dx = $ul.find("li:last").offset().left + sdx;
				var _x = $content.offset().left + $content.innerWidth();
				$right.find("*").stop(true, true).animate({ opacity: _dx < _x ? 0.0 : 1.0 }, S);
				
				_dx = $ul.find("li:first").offset().left + sdx;
				_x = $content.offset().left;
				$left.find("*").stop(true, true).animate({ opacity: _dx >= _x ? 0.0 : 1.0 }, S);
			}
		};
		
		//bind some stuff...
		$right.click(function() {
			if($ul.is(":animated")) {
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
			if($ul.is(":animated")) {
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
	});
};
