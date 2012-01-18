var BoundingBox = function () {
	this.tl = new Vertex(0,0);
	this.br = new Vertex(0,0);
};

BoundingBox.prototype.hitTest = function (v) {
	return this.tl.x <= v.x && this.br.x > v.x &&
		this.tl.y <= v.y && this.br.y > v.y;
};

BoundingBox.prototype.TL = function() {
	return this.tl.clone();
};
BoundingBox.prototype.BR = function() {
	return this.br.clone();
};
BoundingBox.prototype.TR = function() {
	return new Vertex(this.br.x, this.tl.y);
};
BoundingBox.prototype.BL = function() {
	return new Vertex(this.tl.x, this.br.y);
};

BoundingBox.prototype.resize = function (x0, y0, w,h) {
	if (w === undefined || h === undefined) {
		w = x0;
		h = y0;
		x0 = 0;
		y0 = 0;
	}
	
	this.br.x = x0+w;
	this.br.y = y0+h;
	this.tl.x = x0;
	this.tl.y = y0;
};

BoundingBox.prototype.clone = function () {
	var bb = new BoundingBox();
	bb.tl = this.tl.clone();
	bb.br = this.br.clone();
	return bb;
};

BoundingBox.prototype.applyTransform = function (drawableTransform) {
	this.tl = drawableTransform.matrix.multiplyVertex(this.tl);
	this.br = drawableTransform.matrix.multiplyVertex(this.br);
};

BoundingBox.prototype.render = function (ctx, thickness, color) {
	var moved = false;
	var lastVertex = null;
	var firstVertex = null;
	
	var vertices = [
		new Vertex(this.tl.x, this.tl.y),
		new Vertex(this.br.x, this.tl.y),
		new Vertex(this.br.x, this.br.y),
		new Vertex(this.tl.x, this.br.y)
	];
	
	ctx.beginPath();
	for (var j in vertices) {
		if (!moved) {
			ctx.moveTo(vertices[j].x, vertices[j].y);
			moved = true;
			lastVertex = vertices[j];
			firstVertex = vertices[j];
			continue;
		}
		
		ctx.lineTo(vertices[j].x, vertices[j].y);
		
		lastVertex = vertices[j];
	}
	ctx.lineTo(firstVertex.x, firstVertex.y);
	
	ctx.strokeStyle = color;
	ctx.lineWidth = thickness;
	ctx.stroke();
};
