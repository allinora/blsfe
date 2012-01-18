var DrawableTransform = function(m) {
	this.matrix = m || new Matrix();
	this._validatedVertices = null;
	
	this._invalidated = [];
};

DrawableTransform.prototype.onInvalidate = function (cb) {
	this._invalidated.push(cb);
};

DrawableTransform.prototype.invalidate = function(skipEvent) {
	this._validatedVertices = null;
	if (!skipEvent) {
		for (var i=0; i<this._invalidated.length; i++)
			this._invalidated[i]();
	}
};

DrawableTransform.prototype.scaleTo = function (sx, sy) {
	if (sy === undefined)
		sy = sx;
	
	var dec = this.decompose();
	this.matrix = this.matrix.
		multiply(Matrix.CreateScale(1/dec.scale.x, 1/dec.scale.y)).
		multiply(Matrix.CreateScale(sx, sy));
	this.invalidate();
};

DrawableTransform.prototype.rotateTo = function (r) {
	var dec = this.decompose();
	this.matrix = this.matrix.
		multiply(Matrix.CreateRotation(-dec.rotation)).
		multiply(Matrix.CreateRotation(r));
	this.invalidate();
};

DrawableTransform.prototype.translateTo = function (tx, ty) {
	var dec = this.decompose();
	this.matrix = this.matrix.
		multiply(Matrix.CreateTranslation(-dec.translation.x, -dec.translation.y)).
		multiply(Matrix.CreateTranslation(tx, ty));
	this.invalidate();
};

DrawableTransform.prototype.rotateBy = function (a) {
	this.matrix = this.matrix.multiply(Matrix.CreateRotation(a));
	this.invalidate();
};

DrawableTransform.prototype.translateBy = function (dx,dy) {
	this.matrix = this.matrix.multiply(Matrix.CreateTranslation(dx,dy));
	this.invalidate();
};

DrawableTransform.prototype.scaleBy = function (sx,sy) {
	if (sy === undefined)
		sy = sx;
	this.matrix = this.matrix.multiply(Matrix.CreateScale(sx,sy));
	this.invalidate();
};

DrawableTransform.prototype.multiply = function (vertex) {
	return this.matrix.multiplyVertex(vertex);
	this.invalidate();
};

DrawableTransform.prototype.decompose = function() {
	var res = this.matrix.decomposeQR();
	var Q = res.Q;
	var R = res.R;
	
	var cosA = Q.data[0];
	var sinA = -Q.data[1];
	var r = Math.atan2(sinA, cosA);
	if (r < 0)
		r += Math.PI*2;
	
	var sx = R.data[0];
	var sy = R.data[4];
	var tx = R.data[2] ;/// sx;
	var ty = R.data[5] ;/// sy;
	
	return {
		rotation: r,
		translation: { x:tx, y:ty },
		scale: { x:sx, y:sy }
	};
};

var Matrix = function(a11, a12, a13, a21, a22, a23, a31, a32, a33) {
	this.data = [
		a11 || 1, a12 || 0, a13 || 0,
		a21 || 0, a22 || 1, a23 || 0,
		a31 || 0, a32 || 0, a33 || 1
	];
};

Matrix.prototype.toString = function() {
	var fn = function(n) {
		var s = Math.round(Math.abs(n)*1000)/1000;
		var sgn = n >= 0 ? " " : "-";
		var left = (parseInt(s)).toString();
		var right = (parseInt((s-left)*1000)).toString();
		
		while (right.length < 3)
			right = right + " ";
		
		left = sgn + left;
		while (left.length < 5)
			left = " " + left;
		
		return left + "." + right;
	};
	
	return "<Matrix "+fn(this.data[0])+" "+fn(this.data[1])+" "+fn(this.data[2])+"\n"+
		   "         "+fn(this.data[3])+" "+fn(this.data[4])+" "+fn(this.data[5])+"\n"+
		   "         "+fn(this.data[6])+" "+fn(this.data[7])+" "+fn(this.data[8])+" >";
};

Matrix.prototype.multiplyVertex = function (vertex) {
	var ret = new Vertex(0,0,0);
	ret.x = this.data[3*0 + 0] * vertex.x +
			this.data[3*0 + 1] * vertex.y +
			this.data[3*0 + 2] * vertex.w;
	ret.y = this.data[3*1 + 0] * vertex.x +
			this.data[3*1 + 1] * vertex.y +
			this.data[3*1 + 2] * vertex.w;
	ret.w = this.data[3*2 + 0] * vertex.x +
			this.data[3*2 + 1] * vertex.y +
			this.data[3*2 + 2] * vertex.w;
	
	return ret;
};

Matrix.prototype.getRow = function (i) {
	return new Vertex(
		this.data[3*i + 0],
		this.data[3*i + 1],
		this.data[3*i + 2]
	);
};

Matrix.prototype.getColumn = function (i) {
	return new Vertex(
		this.data[3*0 + i],
		this.data[3*1 + i],
		this.data[3*2 + i]
	);
};

Matrix.prototype.setRow = function (i, V) {
	this.data[3*i + 0] = V.x;
	this.data[3*i + 1] = V.y;
	this.data[3*i + 2] = V.w;
};

Matrix.prototype.setColumn = function (i, V) {
	this.data[3*0 + i] = V.x;
	this.data[3*1 + i] = V.y;
	this.data[3*2 + i] = V.w;
};

Matrix.prototype.transpose = function() {
	var m = new Matrix();
	for (var i=0; i<3; i++) {
		m.setColumn(i, this.getRow(i));
	}
	return m;
};

Matrix.prototype.decomposeQR = function() {
	
	var a1 = this.getColumn(0);
	var a2 = this.getColumn(1);
	var a3 = this.getColumn(2);
	
	var u1 = a1;
	var e1 = u1.normalize();
	
	var u2 = a2.subtract(a2.project(e1));
	var e2 = u2.normalize();
	
	var u3 = a3.subtract(a3.project(e1)).subtract(a3.project(e2));
	var e3 = u3.normalize();
	
	var Q = new Matrix();
	Q.setColumn(0, e1);
	Q.setColumn(1, e2);
	Q.setColumn(2, e3);
	
	var QT = Q.transpose();
	var R = QT.multiply(this);
	
	return {
		Q: Q,
		R: R
	};
};

Matrix.prototype.clone = function () {
	var m = new Matrix();
	for (var i=0; i<9; i++)
		m.data[i] = this.data[i];
	return m;
};

Matrix.prototype.multiply = function (right) {
	var m = this.clone();
	
	for (var u=0; u<3; u++) {
		for (var v=0; v<3; v++) {
			var r = 0.0;
			for (var k=0; k<3; k++) {
				r += this.data[u*3+k] * right.data[3*k+v];
			}
			m.data[u*3+v] = r;
		}
	}
	
	return m;
};

Matrix.CreateRotation = function (r) {
	return new Matrix(
		Math.cos(r), -Math.sin(r), 0,
		Math.sin(r), Math.cos(r), 0
	);
};

Matrix.CreateScale = function (sx, sy) {
	return new Matrix(
		sx, 0, 0,
		0, sy, 0
	);
};

Matrix.CreateTranslation = function (dx, dy) {
	return new Matrix(
		1, 0, dx,
		0, 1, dy
	);
};

