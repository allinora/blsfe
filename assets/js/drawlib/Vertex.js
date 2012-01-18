var Vertex = function(x,y,w) {
	this.x = x ===undefined ? 0 : x;
	this.y = y ===undefined ? 0 : y;
	this.w = w ===undefined ? 1 : w;
};

Vertex.prototype.clone = function () {
	return new Vertex(this.x, this.y, this.w);
};

Vertex.prototype.distance2 = function (that) {
	var dx = this.x - that.x;
	var dy = this.y - that.y;
	return dx*dx + dy*dy;
};

Vertex.prototype.multiply = function (vertex) {
	return new Vertex(this.x*vertex.x, this.y*vertex.y, this.w*vertex.w);
};

Vertex.prototype.getLength = function (xyOnly) {
	if (xyOnly)
		return Math.sqrt(this.x*this.x + this.y*this.y);
	else
		return Math.sqrt(this.x*this.x + this.y*this.y + this.w*this.w);
};

Vertex.prototype.normalize = function () {
	return this.scaleBy(1/this.getLength());
};

Vertex.prototype.dot = function (vertex) {
	return this.x*vertex.x + this.y*vertex.y + this.w*vertex.w;
};

Vertex.prototype.project = function (vertex) {
	var u = this.dot(vertex);
	var v = vertex.dot(vertex);
	var s = u/v;
	return vertex.scaleBy(s);
};

Vertex.prototype.distance = function (that) {
	return Math.sqrt(this.distance2(that));
};

Vertex.prototype.add = function (that) {
	return new Vertex(this.x+that.x, this.y+that.y, this.w+that.w);
};
Vertex.prototype.subtract = function (that) {
	return new Vertex(this.x-that.x, this.y-that.y, this.w-that.w);
};
Vertex.prototype.scaleBy = function (u,v,w) {
	if (v === undefined) {
		v = u;
	}
	if (w === undefined) {
		w = u;
	}
	return new Vertex(this.x * u, this.y * v, this.w * w);
};

Vertex.prototype.json = function() {
	var u = (this.x) % 1;
	var v = (this.y) % 1;
	
	return {
		struct: "vertex",
		x: Math.round(1000*u)/1000,
		y: Math.round(1000*v)/1000
	};
};

Vertex.prototype.toString = function() {
	//return JSON.stringify(this.json());
	return "[X="+this.x+" Y="+this.y+" W="+this.w+"]";
};
