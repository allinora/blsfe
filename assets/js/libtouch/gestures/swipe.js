var SwipeGesture = function(direction, minDistance, callback, progress, abort) {
	SwipeGesture.prototype.constructor.call(this, callback, progress, abort);
	
	this.minDistance = minDistance;
	this.direction = direction; //right, left, up, down
};
SwipeGesture.prototype = new TouchGesture();

SwipeGesture.prototype.first = function(ctx) {
	if (ctx.count == 1)
		this.swiping = true;
	else
		this.swiping = false;
	this.delta = 0;
};

SwipeGesture.prototype.update = function(ctx) {
	if (this.swiping && ctx.count > 1) {
		this.swiping = false;
		this.abortCallback();
	}
	else if (this.swiping) {
		var touch = null;
		for (var i in ctx.touches) {
			touch = ctx.touches[i];
			break;
		}
		
		var dv = touch.pagePos.subtract(touch.initialPagePos);
		
		if (this.direction == "left" || this.direction == "right") {
			//X axis logic
			if (Math.abs(dv.x) >= this.minDistance) {
				if (Math.abs(dv.y) > Math.abs(dv.x)/2) {
					//not going into the X axis, abort
					this.abortCallback();
					this.swiping = false;
				}
				else {
					if ((dv.x > 0) == (this.direction == "right")) {
						this.delta = Math.abs(dv.x) - this.minDistance;
						if (this.delta > 0)
							this.progressCallback(this.delta);
					}
					else {
						//going the other way - abort
						this.abortCallback();
						this.swiping = false;
					}
				}
			}
		}
		else {
			//Y axis logic
			if (Math.abs(dv.y) >= this.minDistance) {
				if (Math.abs(dv.x) > Math.abs(dv.y)/2) {
					//not going into the Y axis, abort
					this.abortCallback();
					this.swiping = false;
				}
				else {
					if ((dv.y > 0) == (this.direction == "bottom" || this.direction == "down")) {
						this.delta = Math.abs(dv.y) - this.minDistance;
						if (this.delta > 0)
							this.progressCallback(this.delta);
					}
					else {
						//going the other way - abort
						this.abortCallback();
						this.swiping = false;
					}
				}
			}
		}
	}
};

SwipeGesture.prototype.last = function(ctx) {
	if (this.swiping && this.delta > 0) {
		this.triggerCallback(this.delta);
	}
};
