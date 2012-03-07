var TouchContext = function() {
	this.reset();
	
	this.gestures = [];
};

var TouchState = function(t, ctx) {
	this.ctx = ctx;
	this.identifier = t.identifier;
	this.hold = true;
	this.update(t);
	this.initialPagePos = this.pagePos.clone();
	this.creationTime = new Date().getTime();
}

TouchState.prototype.update = function(t) {
	this.pagePos = new Vector(t.pageX, t.pageY);
	this.updateTime = new Date().getTime();
};

TouchState.prototype.toString = function() {
	return "["+this.identifier+" | hold:"+this.hold+" | page:"+this.pagePos.x+","+this.pagePos.y+"]";
};

TouchState.prototype.dragDistance2 = function() {
	return this.initialPagePos.distance2(this.pagePos);
};
TouchState.prototype.dragDistance = function() {
	return this.initialPagePos.distance(this.pagePos);
};

TouchContext.prototype.handle = function(t, hold) {
	if (!t) return;
	var touch = this.touches[t.identifier];
	if (touch)
		touch.update(t);
	else {
		this.count++;
		touch = this.touches[t.identifier] = new TouchState(t, this);
	}
	touch.hold = hold;
};

TouchContext.prototype.handleGestures = function(event) {
	this.hold = 0;
	for (var i=0; i<this.touches.length; i++) {
		if (this.touches[i].hold)
			this.hold++;
	}
	
	for (var i=0; i<this.gestures.length; i++) {
		if (event == "first")
			this.gestures[i].first(this);
		
		this.gestures[i].update(this);
		
		if (event == "last")
			this.gestures[i].last(this);
	}
};

TouchContext.prototype.reset = function() {
	this.touches = {};
	this.count = 0;
};

TouchContext.prototype.debug = function(label) {
	if (label)
		console.log("--- "+label+" ---");
	for (var i in this.touches) {
		console.log(this.touches[i].toString());
	}
};

var TouchGesture = function(callback, progressCallback, abortCallback, startCallback) {
	this.triggerCallback = callback || function(){};
	this.progressCallback = progressCallback || function(){};
	this.abortCallback = abortCallback || function(){};
	this.startCallback = startCallback || function(){};
};

TouchGesture.prototype.register = function(ctx) {
	ctx.gestures.push(this);
};
TouchGesture.prototype.unregister = function(ctx) {
	var tmp = [];
	for (var i=0; i<ctx.gestures.length; i++) {
		if (ctx.gestures[i] !== this)
			tmp.push(ctx.gestures[i]);
	}
	ctx.gestures = tmp;
};

TouchGesture.prototype.first = function(ctx) {
};
TouchGesture.prototype.update = function(ctx) {
};
TouchGesture.prototype.last = function(ctx) {
};

$.fn.addTouchGesture = function(tg) {
	var ctx = $(this).data("touchContext");
	if (!ctx)
		return false;
	tg.register(ctx);
	return $(this);
};
$.fn.removeTouchGesture = function(tg) {
	var ctx = $(this).data("touchContext");
	if (!ctx)
		return $(this);
	tg.unregister(ctx);
	return $(this);
};

//jquery stuff

$.fn.touch = function (disable) {
	var $this = $(this);
	
	if (disable) {
		$this.unbind("touchstart touchmove touchend");
		$this.data("touchContext", null);
		return $this;
	}
	
	var ctx = new TouchContext();
	$this.data("touchContext", ctx);
	
	$this.bind("touchstart", function(e) {
		e = e.originalEvent;
		var isEmpty = ctx.count == 0;
		
		for (var i=0; i<e.changedTouches.length; i++) {
			ctx.handle(e.changedTouches[i], true);
		}
		
		e.preventDefault();
		
		ctx.handleGestures(isEmpty ? "first" : null);
		
		var je = $.Event("touchUpdate");
		je.touches = ctx.touches;
		$this.trigger(je);
	});

	$this.bind("touchmove", function(e) {
		e = e.originalEvent;
		for (var i=0; i<e.changedTouches.length; i++) {
			ctx.handle(e.changedTouches[i], true);
		}
		
		e.preventDefault();
		
		ctx.handleGestures();
		
		var je = $.Event("touchUpdate");
		je.touches = ctx.touches;
		$this.trigger(je);
	});

	$this.bind("touchend", function(e) {
		e = e.originalEvent;
		for (var i in ctx.touches)
			ctx.touches[i].hold = false;
		for (var i=0; i<e.touches.length; i++) {
			ctx.handle(e.touches[i], true);
		}
		
		e.preventDefault();
		
		var je = $.Event("touchUpdate");
		je.touches = ctx.touches;
		$this.trigger(je);
		
		ctx.handleGestures("last");
		
		if (e.touches.length == 0)
			ctx.reset();
	});
	
	$this.bind("touchcancel", function(e) {
		for (var i in ctx.touches)
			ctx.touches[i].hold = false;
		
		e.preventDefault();
		
		var je = $.Event("touchUpdate");
		je.touches = ctx.touches;
		$this.trigger(je);
		
		ctx.handleGestures("last");
		
		ctx.reset();
	});
	
	return $this;
};
