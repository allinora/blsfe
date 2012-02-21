var DrawableTransform = function(m) {
	this.matrix = m || new Matrix();
	
	this._invalidated = [];
};

DrawableTransform.prototype.onInvalidate = function (cb) {
	this._invalidated.push(cb);
};

DrawableTransform.prototype.invalidate = function(skipEvent) {
	if (!skipEvent) {
		for (var i=0; i<this._invalidated.length; i++)
			this._invalidated[i]();
	}
};

DrawableTransform.prototype.scaleTo = function (sx, sy) {
	if (sy === undefined)
		sy = sx;
	
	var dec = this.decompose();
	this.matrix.
		multiplySelf(Matrix.CreateScale(1/dec.scale.x, 1/dec.scale.y)).
		multiplySelf(Matrix.CreateScale(sx, sy));
	this.invalidate();
};

DrawableTransform.prototype.rotateTo = function (r) {
	var dec = this.decompose();
	this.matrix.
		multiplySelf(Matrix.CreateRotation(-dec.rotation)).
		multiplySelf(Matrix.CreateRotation(r));
	this.invalidate();
};

DrawableTransform.prototype.translateTo = function (tx, ty) {
	var dec = this.decompose();
	this.matrix.
		multiplySelf(Matrix.CreateTranslation(-dec.translation.x, -dec.translation.y)).
		multiplySelf(Matrix.CreateTranslation(tx, ty));
	this.invalidate();
};

DrawableTransform.prototype.rotateBy = function (a) {
	this.matrix.multiplySelf(Matrix.CreateRotation(a));
	this.invalidate();
};

DrawableTransform.prototype.translateBy = function (dx,dy) {
	this.matrix.multiplySelf(Matrix.CreateTranslation(dx,dy));
	this.invalidate();
};

DrawableTransform.prototype.scaleBy = function (sx,sy) {
	if (sy === undefined)
		sy = sx;
	this.matrix.multiplySelf(Matrix.CreateScale(sx,sy));
	this.invalidate();
};

DrawableTransform.prototype.multiply = function (vertex) {
	return this.matrix.multiplyVertex(vertex);
};

DrawableTransform.prototype.decompose = function() {
	return this.matrix.decompose();
};

DrawableTransform.prototype.interpolateTo = function (that, duration, easing, stepCallback) {
	var that = that.matrix ? that.matrix : that;
	var _this=this;
	this.matrix.interpolateTo(that, duration, easing, function() {
		_this.invalidate();
	});
};

(function() {
	var dataClass = Array;
	if (window.Float64Array)
		dataClass = Float64Array;
	else if (window.Float32Array)
		dataClass = Float32Array;
	
	dataClass = Array;
	
	if (dataClass !== Array) {
		Matrix = function(a11, a12, a13, a21, a22, a23, a31, a32, a33) {
			this.data = new dataClass(9);
			if (arguments.length == 0) {
				this.data[0] = 1;
				this.data[4] = 1;
				this.data[8] = 1;
			}
			else {
				this.data[0] = a11;
				this.data[1] = a12;
				this.data[2] = a13;
				
				this.data[3] = a21;
				this.data[4] = a22;
				this.data[5] = a23;
				
				this.data[6] = a31;
				this.data[7] = a32;
				this.data[8] = a33;
			}
			
			this.interpolationTimer=null;
		};
	}
	else {
		Matrix = function(a11, a12, a13, a21, a22, a23, a31, a32, a33) {
			this.data = [
				a11 || 1, a12 || 0, a13 || 0,
				a21 || 0, a22 || 1, a23 || 0,
				a31 || 0, a32 || 0, a33 || 1
			];
			
			this.interpolationTimer=null;
		};
	}
})();

Matrix.DEBUG = false;
Matrix.resetDebug = function() {
	Matrix.debug = {
		decompose: 0,
		add: 0,
		clone: 0,
		multiply: 0,
		multiplyVertex: 0
	};
};
Matrix.resetDebug();

Matrix.prototype.decompose = function() {
	if (Matrix.DEBUG)
		Matrix.debug.decompose++;
	
	var res = this.decomposeQR();
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
	if (Matrix.DEBUG)
		Matrix.debug.multiplyVertex++;
	
	var ret = new Vertex(0,0,0);
	ret.x = this.data[0] * vertex.x +
			this.data[1] * vertex.y +
			this.data[2] * vertex.w;
	ret.y = this.data[3] * vertex.x +
			this.data[4] * vertex.y +
			this.data[5] * vertex.w;
	ret.w = this.data[6] * vertex.x +
			this.data[7] * vertex.y +
			this.data[8] * vertex.w;
	
	return ret;
};

Matrix.prototype.applyMultiplyVertices = function(transformedVertexBuffer, vertexBuffer, computeBox) {
	if (computeBox) {
		var xmin=Infinity, xmax=-Infinity, ymin=Infinity, ymax=-Infinity;
	}
	
	transformedVertexBuffer.empty(vertexBuffer.length());
	transformedVertexBuffer._head = transformedVertexBuffer.length();
	
	var numVertices = vertexBuffer.length();
	for (var i=0; i<numVertices; i+=3) {
		var x = vertexBuffer.data[i];
		var y = vertexBuffer.data[i+1];
		var w = vertexBuffer.data[i+2];
		
		var X = transformedVertexBuffer.data[i] = this.data[0] * x +
				this.data[1] * y +
				this.data[2] * w;
		var Y = transformedVertexBuffer.data[i+1] = this.data[3] * x +
				this.data[4] * y +
				this.data[5] * w;
		//var W = vertexBuffer[i*3+2] = this.data[6] * x +
		//		this.data[7] * y +
		//		this.data[8] * w;
		
		if (computeBox) {
			if (X < xmin) xmin = X;
			if (X > xmax) xmax = X;
			if (Y < ymin) ymin = Y;
			if (Y > ymax) ymax = Y;
		}
	}
	
	if (computeBox) {
		return { xmin:xmin, xmax:xmax, ymin:ymin, ymax:ymax };
	}
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
	if (Matrix.DEBUG)
		Matrix.debug.clone++;
	
	var m = new Matrix();
	
	//for (var i=0; i<9; i++)
	//	m.data[i] = this.data[i];
	m.data = this.data.slice(0, 9);
	
	return m;
};

Matrix.prototype.stopInterpolate = function () {
	if (this.interpolationTimer) {
		clearInterval(this.interpolationTimer);
		this.interpolationTimer = null;
	}
};

Matrix.prototype.add = function (that) {
	if (Matrix.DEBUG)
		Matrix.debug.add++;
	
	for (var i=0; i<this.data.length; i++)
		this.data[i] += that.data[i];
	return this;
};
Matrix.prototype.subtract = function (that) {
	for (var i=0; i<this.data.length; i++)
		this.data[i] -= that.data[i];
	return this;
};
Matrix.prototype.interpolate = function (that, percent, easing) {
	for (var i=0; i<this.data.length; i++) {
		if (typeof easing != "function")
			this.data[i] = this.data[i] * (1-percent) + that.data[i] * percent;
		else
			this.data[i] = easing(this.data[i], that.data[i], percent);
	}
	return this;
};

Matrix.prototype.interpolateTo = function (that, duration, easing, stepCallback) {
	this.stopInterpolate();
	
	var a0 = this.clone();
	
	if (duration <= 0) {
		this.data = that.clone().data;
		if (typeof stepCallback == "function")
			stepCallback();
	}
	else {
		var t0 = new Date().getTime();
		var _this = this;
		this.interpolationTimer = setInterval(function() {
			var dt = new Date().getTime() - t0;
			var pct = Math.min(dt/duration, 1.0);
			var aN = a0.clone().interpolate(that, pct, easing);
			_this.data = aN.data;
			
			if (pct == 1) {
				_this.stopInterpolate();
			}
			
			if (typeof stepCallback == "function")
				stepCallback();
		}, 10);
	}
};

Matrix.prototype.multiply = function (right) {
	var data = this.data.slice(0,9);
	
	for (var u=0; u<3; u++) {
		for (var v=0; v<3; v++) {
			var r = 0.0;
			for (var k=0; k<3; k++) {
				r += this.data[u*3+k] * right.data[3*k+v];
			}
			data[u*3+v] = r;
		}
	}
	
	var m = new Matrix();
	m.data=data;
	return m;
};

Matrix.prototype.multiplySelf = function (right) {
	var data = this.data.slice(0,9);
	
	for (var u=0; u<3; u++) {
		for (var v=0; v<3; v++) {
			var r = 0.0;
			for (var k=0; k<3; k++) {
				r += this.data[u*3+k] * right.data[3*k+v];
			}
			data[u*3+v] = r;
		}
	}
	this.data=data;
	return this;
};

Matrix.CreateRotation = function (r) {
	return new Matrix(
		Math.cos(r), -Math.sin(r), 0,
		Math.sin(r), Math.cos(r), 0,
		0, 0, 1
	);
};

Matrix.CreateScale = function (sx, sy) {
	return new Matrix(
		sx, 0, 0,
		0, sy, 0,
		0, 0, 1
	);
};

Matrix.CreateTranslation = function (dx, dy) {
	return new Matrix(
		1, 0, dx,
		0, 1, dy,
		0, 0, 1
	);
};

