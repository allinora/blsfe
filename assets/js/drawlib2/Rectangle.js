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
	
	this.clearVertices();
	this.addVertex(new Vertex(x0, y0));
	this.addVertex(new Vertex(x0+w, y0));
	this.addVertex(new Vertex(x0+w, y0+h));
	this.addVertex(new Vertex(x0, y0+h));
	
	this._width=w;
	this._height=h;
	
	this.invalidate();
};
