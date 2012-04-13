var SingleDoubleTapGesture = function(callback) {
	SingleDoubleTapGesture.prototype.constructor.call(this, callback);
	
	this.maxDelay = 400;
	this.maxDistance = 40;
	
	this.taps = 0;
};
SingleDoubleTapGesture.prototype = new TouchGesture();

SingleDoubleTapGesture.prototype.first = function(ctx) {
	if (this.taps == 0) {
		this.t0 = new Date().getTime();
		this.valid = true;
	}
	this.taps++;
};

SingleDoubleTapGesture.prototype.update = function(ctx) {
};

SingleDoubleTapGesture.prototype.last = function(ctx) {
	var touch = null;
	for (var i in ctx.touches) {
		touch = ctx.touches[i];
		break;
	}
	
	this.valid = this.valid && ctx.count === 1 && touch.dragDistance2() < this.maxDistance * this.maxDistance;
	
	if (!this.valid) {
		this.taps = 1;
		this.t0 = new Date().getTime();
		this.valid = true;
	}
	else {
		if (this.taps == 2) {
			var dt = new Date().getTime() - this.t0;
			if (dt < this.maxDelay) {
				//yay.
				this.taps = 0;
				this.triggerCallback(touch.pagePos.x, touch.pagePos.y);
			}
			else {
				//invalid
				this.taps = 1;
				this.t0 = new Date().getTime();
				this.valid = true;
			}
		}
		else {
			//wait...
		}
	}
};
