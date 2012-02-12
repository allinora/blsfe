var Camera = function(drawing) {
	var _this=this;
	
	this.settings = {
		canScale: true,
		canMove: true,
		canRotate: true
	};
	
	this.transform = new DrawableTransform();
	
	this.transform.onInvalidate(function() {
		_this.invalidate();
	});
	
	this.name = "Camera";
	this.drawing = drawing;
	
	this._viewport = { w:drawing._w, h:drawing._h };
	this._center = new Vertex(this._viewport.w/2, this._viewport.h/2);
	this._zoom = 1.0;
	this._rotation = 0;
	this._matrix = null;
	
	this.transform.matrix = this.getMatrix();
};

Camera.prototype.getMatrix = function() {
	this._matrix = new Matrix().
		multiply(Matrix.CreateTranslation(-this._viewport.w/2, -this._viewport.h/2)).
		multiply(Matrix.CreateRotation(this._rotation)).
		multiply(Matrix.CreateScale(this._zoom, this._zoom)).
		multiply(Matrix.CreateTranslation(this._viewport.w* (-1 + 1/this._zoom) + this._center.x, this._viewport.h * (-1 + 1/this._zoom) + this._center.y));
	return this._matrix;
};

Camera.prototype.invalidate = function() {
	this.transform.invalidate(true); //skip event
	if (this.drawing) {
		this.drawing.invalidate(true); //recurse
		this.drawing.render();
	}
};

Camera.prototype.zoomTo = function(z, duration) {
	this._zoom = z;
	this.transform.interpolateTo(this.getMatrix(), duration);
};

Camera.prototype.moveTo = function(x,y, duration) {
	this._center.x = x;
	this._center.y = y;
	this.transform.interpolateTo(this.getMatrix(), duration);
};

Camera.prototype.rotateTo = function(a, duration) {
	this._rotation = a;
	this.transform.interpolateTo(this.getMatrix(), duration);
};

Camera.prototype.reset = function (duration) {
	this._zoom=1;
	this._center = new Vertex(this._viewport.w/2, this._viewport.h/2);
	this._rotation = 0;
	
	this.transform.interpolateTo(this.getMatrix(), duration);
};
