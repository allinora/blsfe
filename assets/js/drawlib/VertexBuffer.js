var VertexBuffer = function() {
	this.data = new VertexBuffer._class(12);
	this._head=0;
};
VertexBuffer._class = window.Float64Array || window.Float32Array || Array;
//VertexBuffer._class = Array;

VertexBuffer.prototype.add = function(v) {
	if (this._head+3 > this.data.length) {
		var tmp = new VertexBuffer._class(this.data.length * 2);
		for (var i=0; i<this.data.length; i++)
			tmp[i] = this.data[i];
		this.data = tmp;
		//console.log("xxx had to extend to "+this.data.length)
	}
	this.addUnsafe(v);
};

VertexBuffer.prototype.addUnsafe = function(v) {
	this.data[this._head++] = v.x;
	this.data[this._head++] = v.y;
	this.data[this._head++] = v.w;
};

VertexBuffer.prototype.length = function() { return this._head; };

VertexBuffer.prototype.empty = function(n) {
	if (n && n > this.data.length)
		this.data = new VertexBuffer._class(n);
	this._head=0;
};
