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

BoundingBox.prototype.include = function (bb) {
	if (!bb)
		return;
	
	if (isNaN(this.tl.x))debugger;

	this.tl.x = Math.min(this.tl.x, bb.tl.x);
	this.tl.y = Math.min(this.tl.y, bb.tl.y);
	this.br.x = Math.max(this.br.x, bb.br.x);
	this.br.y = Math.max(this.br.y, bb.br.y);
	
	if (isNaN(this.tl.x))debugger;
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
	this.tl = drawableTransform.multiply(this.tl);
	this.br = drawableTransform.multiply(this.br);
};

BoundingBox.prototype.render = function (ctx, thickness, color) {
	ctx.beginPath();
	ctx.rect(this.tl.x, this.tl.y, this.br.x-this.tl.x, this.br.y-this.tl.y);
	
	ctx.strokeStyle = color;
	ctx.lineWidth = thickness;
	ctx.stroke();
};

BoundingBox.prototype.visible = function(drawing) {
	return this.tl.x < drawing._w && this.br.x >= 0 &&
		this.tl.y < drawing._h && this.br.y >= 0;
};
