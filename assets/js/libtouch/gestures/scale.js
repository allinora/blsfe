var ScaleGesture = function(callback, progress, abort, start) {
	ScaleGesture.prototype.constructor.call(this, callback, progress, abort, start);
};
ScaleGesture.prototype = new TouchGesture();

ScaleGesture.prototype.first = function(ctx) {
	this.valid = ctx.count <= 2;
	this.delta = 0;
	this.center = null;
	
	var touches = [];
	var j=0;
	for (var i in ctx.touches) {
		this.t0 = ctx.touches[i].pagePos;
		touches[j++] = ctx.touches[i];
	}
	
	this.started = ctx.count == 2;
	if (this.started) {
		this.center = touches[0].pagePos.add(touches[1].pagePos).scaleBy(0.5);
		this.startCallback(this.center);
	}
};

ScaleGesture.prototype.update = function(ctx) {
	if (this.valid) {
		
		var t0;
		var count = 0;
		for (var i in ctx.touches) {
			if (!t0)
				t0 = ctx.touches[i].pagePos;
			if (ctx.touches[i].hold)
				count++;
		}
		
		if (ctx.count > 2 || (ctx.count == 2 && count < ctx.count))
			this.valid = false;
		else if (ctx.count == 1 && t0.subtract(this.t0).getLength(true) > 20)
			this.valid = false;
		
		if (!this.valid) {
			this.abortCallback();
		}
		else if (ctx.count == 2) { 
			var j = 0;
			var t = [];
			for (var i in ctx.touches) {
				t[j++] = ctx.touches[i];
				if (j == 2)
					break;
			}
			
			if (!this.started) {
				this.center = t[0].pagePos.add(t[1].pagePos).scaleBy(0.5);
				this.startCallback(this.center);
				this.started = true;
			}
			
			var delta0 = t[0].initialPagePos.subtract(t[1].initialPagePos).getLength(true);
			var delta1 = t[0].pagePos.subtract(t[1].pagePos).getLength(true);
			this.delta = (delta1-delta0) / delta0;
			//this.center = t[0].pagePos.add(t[1].pagePos).scaleBy(0.5);
			this.progressCallback(this.delta, this.center);
		}
	}
};

ScaleGesture.prototype.last = function(ctx) {
	if (this.valid)
		this.triggerCallback(this.delta, this.center);
};
