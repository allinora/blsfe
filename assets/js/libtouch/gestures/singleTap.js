var SingleTapGesture = function(callback) {
	SingleTapGesture.prototype.constructor.call(this, callback);
	
	this.maxDelay = 600;
	this.maxDistance = 30;
};
SingleTapGesture.prototype = new TouchGesture();

SingleTapGesture.prototype.first = function(ctx) {
	this.t0 = new Date().getTime();
};

SingleTapGesture.prototype.update = function(ctx) {
};

SingleTapGesture.prototype.last = function(ctx) {
	var touch = null;
	for (var i in ctx.touches) {
		touch = ctx.touches[i];
		break;
	}
	
	var valid = ctx.count === 1 && touch.dragDistance2() < this.maxDistance * this.maxDistance;
	
	if (valid) {
		var dt = new Date().getTime() - this.t0;
		if (dt < this.maxDelay) {
			//yay.
			this.triggerCallback();
		}
	}
};
