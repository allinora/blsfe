var Rectangle = function(w, h) {
	Rectangle.prototype.constructor.call(this);
	
	this._width=w;
	this._height=h;
	
	this.resize(w,h);
};
Rectangle.prototype = new Shape();

Rectangle.prototype.resize = function (x0, y0, w,h) {
	if (w === undefined || h === undefined) {
		w = x0;
		h = y0;
		x0 = 0;
		y0 = 0;
	}
	
	this.vertices = [];
	this.vertices.push(new Vertex(x0, y0));
	this.vertices.push(new Vertex(w, y0));
	this.vertices.push(new Vertex(w, h));
	this.vertices.push(new Vertex(x0, h));
	
	this._width=w;
	this._height=h;
	
	this.invalidate();
};
