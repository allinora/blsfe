var DrawableTransform = function() {
	this.Scale = new Matrix();
	this.Rotation = new Matrix();
	this.Translation = new Matrix();
	this.Pivot = new Vertex(0,0);
	
	this._SRT = new Matrix();
	
	this._composition = {
		scale: { x:1, y:1 },
		translation: { x:0, y:0 },
		rotation: 0
	};
	
	this._invalidated = [];
};

DrawableTransform.prototype.toJSON = function() {
	var data = this.decompose();
	data.pivot = { x:this.Pivot.x, y:this.Pivot.y };
	return data;
};

DrawableTransform.prototype.fromJSON = function(m) {
	this.rotateTo(m.rotation);
	this.translateTo(m.translation.x, m.translation.y);
	this.scaleTo(m.scale.x, m.scale.y);
	this.setPivot(new Vertex(m.pivot.x, m.pivot.y));
};

DrawableTransform.prototype._pivotify = function(M) {
	if (this.Pivot.x === 0 && this.Pivot.y === 0)
		return M;
	else
		return Matrix.CreateTranslation(this.Pivot.x, this.Pivot.y).
			multiplySelf(M).
			multiplySelf(Matrix.CreateTranslation(-this.Pivot.x, -this.Pivot.y));
};

DrawableTransform.prototype.getMatrix = function() {
	// T * ( P * S * R * Pi )
	
	if (!this._SRT) {
		this._SRT = 
			this.Translation.multiply(
				this._pivotify(this.Scale.multiply(this.Rotation))
			)
	}
	return this._SRT;
};

DrawableTransform.prototype.getInvertMatrix = function () {
	if (!this._SRTInvert) {
		var Rinvert = Matrix.CreateRotation(-this._composition.rotation);
		var Sinvert = Matrix.CreateScale(1/this._composition.scale.x, 1/this._composition.scale.y);
		var Tinvert = Matrix.CreateTranslation(-this._composition.translation.x, -this._composition.translation.y);
		
		this._SRTInvert = 
			this._pivotify(Rinvert.multiply(Sinvert)).multiply(
				Tinvert
			);
	}
	
	
	
	return this._SRTInvert;
};

DrawableTransform.prototype.onInvalidate = function (cb) {
	this._invalidated.push(cb);
};

DrawableTransform.prototype.invalidate = function(skipEvent) {
	this._SRT = null;
	this._SRTInvert = null;
	if (!skipEvent) {
		for (var i=0; i<this._invalidated.length; i++)
			this._invalidated[i]();
	}
};

DrawableTransform.prototype.setPivot = function(v) {
	this.Pivot = v;
	this.invalidate();
};

DrawableTransform.prototype.scaleTo = function (sx, sy) {
	if (sy === undefined)
		sy = sx;
	
	this.Scale = Matrix.CreateScale(sx, sy);
	this._composition.scale.x = sx;
	this._composition.scale.y = sy;
	this.invalidate();
};

DrawableTransform.prototype.rotateTo = function (r) {
	this.Rotation = Matrix.CreateRotation(r);
	this._composition.rotation = r;
	this.invalidate();
};

DrawableTransform.prototype.translateTo = function (tx, ty) {
	this.Translation = Matrix.CreateTranslation(tx, ty);
	this._composition.translation.x = tx;
	this._composition.translation.y = ty;
	this.invalidate();
};

DrawableTransform.prototype.rotateBy = function (a) {
	this.Rotation.multiplySelf(Matrix.CreateRotation(a));
	this._composition.rotation += r;
	this.invalidate();
};

DrawableTransform.prototype.translateBy = function (dx,dy) {
	this.Translation.multiplySelf(Matrix.CreateTranslation(dx,dy));
	this._composition.translation.x += dx;
	this._composition.translation.y += dy;
	this.invalidate();
};

DrawableTransform.prototype.scaleBy = function (sx,sy) {
	if (sy === undefined)
		sy = sx;
	this.Scale.multiplySelf(Matrix.CreateScale(sx,sy));
	this._composition.scale.x *= sx;
	this._composition.scale.y *= sy;
	this.invalidate();
};

DrawableTransform.prototype.multiply = function (vertex) {
	return this.getMatrix().multiplyVertex(vertex);
};

DrawableTransform.prototype.decompose = function(wantRotation) {
	//return this.getMatrix().decompose(wantRotation);
	var t = { _composition:{ translation:{}, scale:{}, rotation:0 } };
	this._copyCompositionDataTo(t);
	return t._composition;
};

DrawableTransform.prototype.clone = function () {
	var t = new DrawableTransform();
	t.Rotation = this.Rotation.clone();
	t.Scale = this.Scale.clone();
	t.Translation = this.Translation.clone();
	t.Pivot = this.Pivot.clone();
	if (this._SRT)
		t._SRT = this._SRT.clone();
	
	this._copyCompositionDataTo(t);
	return t;
};

DrawableTransform.prototype.decomposeAndImport = function (M) {
	if (M instanceof Array)
		M = new Matrix(M[0],M[1],M[2],M[3],M[4],M[5],M[6],M[7],M[8]);
	
	var m = M.decompose(true);
	this.rotateTo(m.rotation);
	this.translateTo(m.translation.x, m.translation.y);
	this.scaleTo(m.scale.x, m.scale.y);
	this.setPivot(new Vertex(0,0));
	
	console.log("DT.decomposeAndImport result:")
	console.log("base:");
	console.log(M+"");
	console.log("imported:");
	console.log(this.getMatrix()+"");
	console.log("------------------------------");
};

DrawableTransform.prototype._copyCompositionDataTo = function (t) {
	t._composition.translation.x = this._composition.translation.x;
	t._composition.translation.y = this._composition.translation.y;
	t._composition.scale.x = this._composition.scale.x;
	t._composition.scale.y = this._composition.scale.y;
	t._composition.rotation = this._composition.rotation;
};
DrawableTransform.prototype._interpolateCompositionData = function (from, to, pct) {
	this._composition.translation.x = from._composition.translation.x + (to._composition.translation.x-from._composition.translation.x)*pct;
	this._composition.translation.y = from._composition.translation.y + (to._composition.translation.y-from._composition.translation.y)*pct;
	this._composition.scale.x = from._composition.scale.x + (to._composition.scale.x-from._composition.scale.x)*pct;
	this._composition.scale.y = from._composition.scale.y + (to._composition.scale.y-from._composition.scale.y)*pct;
	this._composition.rotation = from._composition.rotation + (to._composition.rotation-from._composition.rotation)*pct;
};

DrawableTransform.prototype.interpolateTo = function (thatTransform, duration, stepCallback) {
	var _this=this;
	
	if (this.interpolationTimer) {
		return;
	}
	
	if (duration <= 0) {
		_this.Scale = thatTransform.Scale.clone();
		_this.Rotation = thatTransform.Rotation.clone();
		_this.Translation = thatTransform.Translation.clone();
		_this.Pivot = thatTransform.Pivot.clone();
		thatTransform._copyCompositionDataTo(_this);
		this.invalidate();
		return;
	}
	
	var t0 = new Date().getTime();
	var T0 = this.clone();
	this.interpolationTimer = setInterval(function() {
		var dt = new Date().getTime()-t0;
		var pct = Math.min(1, dt/duration);
		
		if (pct == 1) {
			_this.Scale = thatTransform.Scale.clone();
			_this.Rotation = thatTransform.Rotation.clone();
			_this.Translation = thatTransform.Translation.clone();
			_this.Pivot = thatTransform.Pivot.clone();
			thatTransform._copyCompositionDataTo(_this);
			clearInterval(_this.interpolationTimer);
			_this.interpolationTimer=null;
		}
		else {
			_this.Scale.interpolate(thatTransform.Scale, pct);
			_this.Rotation.interpolate(thatTransform.Rotation, pct);
			_this.Translation.interpolate(thatTransform.Translation, pct);
			_this.Pivot = thatTransform.Pivot.subtract(T0.Pivot).scaleBy(pct).add(T0.Pivot);
			_this._interpolateCompositionData(T0, thatTransform, pct);
		}
		_this.invalidate();
		
		if (typeof stepCallback == "function")
			stepCallback(pct);
	}, 17);
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
		multiplyVertex: 0,
		fastVerticesMultiply: 0
	};
};
Matrix.resetDebug();

Matrix.prototype.decompose = function(wantRotation) {
	//throw "Matrix.decompose should not be called";
	
	if (arguments.length == 0)
		wantRotation=true;
	
	if (Matrix.DEBUG)
		Matrix.debug.decompose++;
	
	var res = this.decomposeQR(wantRotation);
	var Q = res.Q;
	var R = res.R;
	
	if (wantRotation) {
		var cosA = Q.data[0];
		var sinA = -Q.data[1];
		var r = Math.atan2(sinA, cosA);
		if (r < 0)
			r += Math.PI*2;
	}
	else
		var r = null;
	
	var sx = R.data[0];
	var sy = R.data[4];
	var tx = R.data[2] ;/// sx;
	var ty = R.data[5] ;/// sy;
	
	return ret = {
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
	if (Matrix.DEBUG)
		Matrix.debug.fastVerticesMultiply++;
		
	//if (Matrix.debug.fastVerticesMultiply==1000)
	//debugger;
	
	if (computeBox) {
		var MAXVAL = Number.MAX_VALUE /1024;
		var xmin=MAXVAL, xmax=-MAXVAL, ymin=MAXVAL, ymax=-MAXVAL;
	}
	
	var numVertices = vertexBuffer.length();
	transformedVertexBuffer.empty(numVertices);
	transformedVertexBuffer._head = numVertices;
	
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
		if (xmin === MAXVAL || ymin === MAXVAL)
			return { xmin:-MAXVAL, xmax:MAXVAL, ymin:-MAXVAL, ymax:MAXVAL };
		else
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
	/* \ 1 2
	 * 3 \ 5
	 * 6 7 \
	 */
	var tmp;
	tmp=this.data[1]; this.data[1] = this.data[3]; this.data[3] = tmp;
	tmp=this.data[2]; this.data[2] = this.data[6]; this.data[6] = tmp;
	tmp=this.data[5]; this.data[5] = this.data[7]; this.data[7] = tmp;
	return this;
};

Matrix.prototype.decomposeQR = function(wantQ) {
	//throw "Matrix.decomposeQR should not be called.";
	var a1 = this.getColumn(0);
	var a2 = this.getColumn(1);
	var a3 = this.getColumn(2);
	
	var e1 = a1;
	e1.normalizeFast();
	
	var e2 = a2.subtract(a2.project(e1));
	e2.normalizeFast();
	
	var e3 = a3.subtract(a3.project(e1)).subtract(a3.project(e2));
	e3.normalize();
	
	var Q = new Matrix(
		e1.x, e2.x, e3.x,
		e1.y, e2.y, e3.y,
		e1.w, e2.w, e3.w
	);
	
	if (wantQ) {
		var QT = Q.clone();
		QT.transpose();
		QT.multiplySelf(this);
		return { Q:Q, R:QT };
	}
	else {
		Q.transpose();
		Q.multiplySelf(this);
		return { Q:null, R:Q };
	}
};

Matrix.prototype.clone = function () {
	if (Matrix.DEBUG)
		Matrix.debug.clone++;
	
	var m = new Matrix(
		this.data[0],this.data[1],this.data[2],
		this.data[3],this.data[4],this.data[5],
		this.data[6],this.data[7],this.data[8]
	);
	
	//for (var i=0; i<9; i++)
	//	m.data[i] = this.data[i];
	//m.data = this.data.slice(0, 9);
	
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
			stepCallback(1);
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
				stepCallback(pct);
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

