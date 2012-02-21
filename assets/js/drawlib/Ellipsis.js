var Ellipsis = function(dw, dh) {
	Ellipsis.prototype.constructor.call(this);
	
	this._dw = dw;
	this._dh = dh;
	this.setSize(dw,dh);
};

Ellipsis.prototype = new Shape();

Ellipsis.PRECISION = 6;

Ellipsis.prototype.setSize = function (dw, dh) {
	this.clearVertices();
	for (var a=0; a<Ellipsis.PRECISION; a++) {
		var alpha = a / Ellipsis.PRECISION * 2 * Math.PI;
		
		var x = (Math.cos(alpha) + 1)/2 * dw;
		var y = (Math.sin(alpha) + 1)/2 * dh;
		this.addVertex(new Vertex(x,y));
	}
	
	this.invalidate();
};
