var DragGesture = function(callback, progress, abort, start) {
	DragGesture.prototype.constructor.call(this, callback, progress, abort, start);
};
DragGesture.prototype = new TouchGesture();

DragGesture.prototype.first = function(ctx) {
	this.valid = ctx.count == 1;
	var touch;
	for (var i in ctx.touches) {
		touch = ctx.touches[i];
		break;
	}
	this.v0 = touch.pagePos;
	this.v = new Vertex();
	
	if (this.valid)
		this.startCallback();
};

DragGesture.prototype.update = function(ctx) {
	if (this.valid) {
		this.valid = ctx.count == 1;
		if (!this.valid) {
			this.abortCallback();
		}
		else {
			var touch;
			for (var i in ctx.touches) {
				touch = ctx.touches[i];
				break;
			}
			
			this.v = touch.pagePos.subtract(this.v0);
			this.progressCallback(this.v.x, this.v.y);
		}
	}
};

DragGesture.prototype.last = function(ctx) {
	if (this.valid)
		this.triggerCallback(this.v.x, this.v.y);
};
