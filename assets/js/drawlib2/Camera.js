var Camera = function(drawing) {
	console.log("new camera");
	var _this=this;
	
	this.settings = {
		canScale: true,
		canMove: true,
		canRotate: true
	};
	
	this.transform = new DrawableTransform();
	
	this.name = "Camera";
	this.drawing = drawing;
	
	this.transform.setPivot(new Vertex(this.drawing._w/2, this.drawing._h/2));
	
	this.transform.onInvalidate(function() {
		_this.invalidate();
	});
	
	console.log("camera constructor done");
};

Camera.prototype.invalidate = function() {
	this.transform.invalidate(true); //skip event
	if (this.drawing) {
		this.drawing.invalidate(true); //recurse
		this.drawing.render();
	}
};

Camera.prototype.zoomTo = function(z, duration, complete) {
	var T = this.transform.clone();
	T.scaleTo(z,z);
	this.transform.interpolateTo(T, duration, function(pct) {
		if (pct == 1 && typeof complete == 'function')
			complete();
	});
};

Camera.prototype.moveTo = function(x,y, duration, complete) {
	var T = this.transform.clone();
	T.translateTo(x,y);
	T.setPivot(new Vertex(this.drawing._w/2-x, this.drawing._h/2-y));
	
	this.transform.interpolateTo(T, duration, function(pct) {
		if (pct == 1 && typeof complete == 'function')
			complete();
	});
};

Camera.prototype.rotateTo = function(a, duration, complete) {
	var T = this.transform.clone();
	T.rotateTo(a);
	
	this.transform.interpolateTo(T, duration, function(pct) {
		if (pct == 1 && typeof complete == 'function')
			complete();
	});
};

Camera.prototype.reset = function (duration, complete) {
	var T = this.transform.clone();
	T.translateTo(0,0);
	T.scaleTo(1,1);
	T.rotateTo(0);
	
	this.transform.interpolateTo(T, duration, function(x) {
		if (x == 1 && typeof complete == 'function')
			complete();
	});
};
