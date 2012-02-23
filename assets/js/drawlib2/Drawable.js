var Drawable = function() {
	var _this=this;
	
	this.settings = {
		canSelect: true,
		clickThrough: false, //cannot be selected, but children can
		canDelete: true,
		canScale: true,
		canMove: true,
		canRotate: true,
		canRender: true,
		canSendEvents: true,
		
		computeVertices: true,
		
		closePath: true,
		fillShape: true
	};
	
	this.state = Drawable.State.Default;
	
	this.styles = {};
	for (var i in Drawable.DefaultStyles) {
		this.styles[i] = Drawable.DefaultStyles[i].clone();
	}
	
	this.transform = new DrawableTransform();
	this._vertices = [];
	this._vertexBuffer = new VertexBuffer();
	this._transformedVertexBuffer = new VertexBuffer();
	this.boundingBox = new BoundingBox();
	
	this._parent = null;
	this._children = [];
	
	this._init = true;
	
	this.events = {
		"click": [],
		"mouseover": [],
		"mouseout": [],
		"select": [],
		"deselect": []
	};
	
	var _this=this;
	this.bind("mouseover", function(m) {
		if (m)
			this._mouse = m;
		
		if (this.settings.canSelect) {
			if (!this.settings.clickThrough) {
				var before = this.mouseOverDrawable;
				this.mouseOverDrawable = this._getChildUnderMouse();
				
				if (before != this.mouseOverDrawable) {
					if (before) {
						before.trigger("mouseout");
					}
					if (this.mouseOverDrawable) {
						var t = this.transform.decompose();
						this.mouseOverDrawable.trigger("mouseover", this._mouse.subtract(new Vertex(t.translation.x, t.translation.y)));
					}
				}
				
				if (!this.mouseOverDrawable && this.state == "default") {
					this.state = "hover";
				}
			}
			else {
				for (var i=0; i<this._children.length; i++) {
					var c = this._children[i];
					if (c._hitTest(this._mouse)) {
						this.mouseOverDrawable = c;
						var t = this.transform.decompose();
						this.mouseOverDrawable.trigger("mouseover", this._mouse.subtract(new Vertex(t.translation.x, t.translation.y)));
					}
					else if (c.state == "hover") {
						c.trigger("mouseout");
						if (c === this.mouseOverDrawable)
							this.mouseOverDrawable = null;
					}
				}
			}
		}
		
		if (this._parent) {
			for (var i=0; i<this._parent._children.length; i++) {
				var c = this._parent._children[i];
				if (c !== this) {
					c.trigger("mouseout");
					if (c === this._parent.mouseOverDrawable)
						this._parent.mouseOverDrawable = null;
				}
			}
		}
		
		
	}).bind("mouseout", function() {
		if (this.state == "hover")
			this.state = "default";
		//this._mouse = null;
		
		for (var i=0; i<this._children.length; i++) {
			var c = this._children[i];
			if (c.state == "hover" && !c._hitTest(this._mouse)) {
				c.trigger("mouseout");
				if (c === this.mouseOverDrawable)
					this.mouseOverDrawable = null;
			}
		}
		
		if (this.mouseOverDrawable != null) {
			this.mouseOverDrawable.trigger("mouseout");
			this.mouseOverDrawable = null;
		}
	}).bind("click", function() {
		//...
		if (typeof G_vmlCanvasManager != 'undefined') {
			this.mouseOverDrawable = this._getChildUnderMouse();
		}
		
		if (this.settings.canSelect) {
			if (this.mouseOverDrawable) {
				var t = this.transform.decompose();
				var smouse = this._mouse.subtract(new Vertex(t.translation.x, t.translation.y));
				this.mouseOverDrawable.trigger("click", smouse);
			}
			else if (!this.settings.clickThrough) {
				console.log("Selecting " + this.name);
				if (Drawable.Selected) {
					Drawable.Selected.trigger("deselect");
					Drawable.Selected.state = "default";
					//allows cross-canvas behavior
					if (Drawable.Selected._parent && Drawable.Selected._parent.canvas) {
						Drawable.Selected._parent.render();
					}
				}
				Drawable.Selected = this;
				this.trigger("select");
				this.state = "selected";
			}
		}
	});
	
	this.transform.onInvalidate(function() {
		_this.invalidate();
	});
	
	this._mouse = new Vertex();
	this.mouseOverDrawable = null;
	
	this.name = "Drawable_" + Drawable._count;
	Drawable._count++;
	
	this._cachedMatrix = null;
};

Drawable.prototype.getMatrix = function() {
	if (this._cachedMatrix == null) {
		if (this._parent) {
			this._cachedMatrix = this._parent.getMatrix().multiply(this.transform.getMatrix());
		}
		else {
			this._cachedMatrix = this.transform.getMatrix();
		}
	}
	
	return this._cachedMatrix;
};

Drawable.prototype.addChild = function (c, drawing) {
	c._parent = this;
	
	c._setDrawing(drawing || this._drawing, true);
	
	this._children.push(c);
};

Drawable.prototype._setDrawing = function (drawing, deep) {
	this._drawing = drawing;
	if (deep) {
		for (var i=0; i<this._children.length; i++) {
			this._children[i] && this._children[i]._setDrawing(drawing, true);
		}
	}
};

Drawable.Deselect = function () {
	if (!Drawable.Selected)
		return;
	
	Drawable.Selected.state = "default";
	Drawable.Selected.trigger("deselect");
	Drawable.Selected = null;
};

Drawable.prototype._getChildUnderMouse = function() {
	for (var i=this._children.length-1; i>=0; i--) {
		var c = this._children[i];
		if (c.settings.canSelect && !c.settings.clickThrough &&
			(c.state == "default" || c.state == "hover" || c.state == "selected") && 
			c._hitTest(this._mouse)) {
				return c;
		}
	}
	return null;
};

Drawable.Selected = null;

Drawable.prototype.invalidate = function(deep) {
	//console.log(this.name, "CALL", "invalidate", deep);
	this.transform.invalidate(true);
	this.boundingBox = null;
	this._transformedVertexBuffer.empty();
	this._cachedMatrix = null;
	if (deep) {
		for (var i=0; i<this._children.length; i++) {
			this._children[i] && this._children[i].invalidate(true);
		}
	}
};

Drawable.prototype.applyTransform = function () {
	var tmp = [];
	for (var i=0; i<this._vertices.length; i++) {
		tmp[i] = this.transform.getMatrix().multiplyVertex(this._vertices[i]);
	}
	
	this.clearVertices();
	for (var i=0; i<tmp.length; i++)
		this.addVertex(tmp[i]);
	
	this.transform.scaleTo(1,1);
	this.transform.translateTo(0,0);
	this.transform.rotateTo(0);
	this.invalidate(true);
};

Drawable.prototype.bind = function (event, callback) {
	event = (event || "").toLowerCase();
	if (this.events[event] === undefined) {
		throw "Unknown Drawable event '"+event+"'";
	}
	this.events[event].push(callback);
	return this;
};
Drawable.prototype.trigger = function (event, E) {
	if (!this.settings.canSendEvents)
		return false;
		
	event = (event || "").toLowerCase();
	if (this.events[event] === undefined) {
		throw "Unknown Drawable event '"+event+"'";
	}
	for (var i=0; i<this.events[event].length; i++)
		this.events[event][i].call(this, E);
	return this;
};

Drawable.DefaultStyles = {
	"default": new DrawableStyle(),
	"hover": new DrawableStyle(),
	"selected": new DrawableStyle()
};

Drawable._count = 0;

Drawable.State = {
	Default: "default",
	Hover: "hover",
	Selected: "selected"
};

Drawable.prototype.render = function (ctx) {
	var renderSelf = this.settings.canRender;
	
	if (!renderSelf)
		return;
	
	this._drawing._nodesVisisted++;
	
	var style = this.styles[this.state];
	//if (!style)
	//	return;
	
	//if (!ctx)
	//	return;
	
	//init right before rendering
	if (this.init) {
		this.invalidate();
		this.init = false;
	}
	
	//apply transform on vertices and compute bbox
	if (this.settings.computeVertices && this._transformedVertexBuffer.length() == 0) {
		var T = this.getMatrix();
		var bounds = T.applyMultiplyVertices(this._transformedVertexBuffer, this._vertexBuffer, true);
		if (!this.boundingBox)
			this.boundingBox = new BoundingBox();
		this.boundingBox.resize(bounds.xmin, bounds.ymin, bounds.xmax-bounds.xmin, bounds.ymax-bounds.ymin);
	}
	
	if (!this.settings.computeVertices || this === this._drawing || this.boundingBox.visible(this._drawing)) {
		//shadow pass
		if (style.shadow.x != 0 || style.shadow.y != 0) {
			ctx.shadowOffsetX = style.shadow.x;
			ctx.shadowOffsetY = style.shadow.y;
			ctx.shadowBlur = style.shadow.blur;
			ctx.shadowColor = style.shadow.color;
			
			this._render(this._transformedVertexBuffer.data, ctx, style);
			
			ctx.shadowOffsetX = 0;
			ctx.shadowOffsetY = 0;
			ctx.shadowBlur = 0;
			ctx.shadowColor = "transparent black";
		}
		
		//normal pass
		this._render(this._transformedVertexBuffer.data, ctx, style);
		this._drawing._renderedCount++;
		
		//render bounds
		if (Drawable.RENDER_BOUNDINGBOXES) {
			this.boundingBox.render(ctx, 1, "red");
		}
	}
	
	//then render children
	for (var i=0; i<this._children.length; i++) {
		this._children[i].render(ctx);
		this.boundingBox && this.boundingBox.include(this._children[i].boundingBox);
	}
};

Drawable.prototype._hitTest = function (vertex) {
	//bounding box check
	if (this.boundingBox) {
		if (!this.boundingBox.hitTest(vertex))
			return false;
	}
	
	//polygon check
	if (this._transformedVertexBuffer.length() > 0) {
		var i=0;
		var j=0;
		var c=false;
		var nvert = this._transformedVertexBuffer.length() /3;
		var testx = vertex.x;
		var testy = vertex.y;
		for (i = 0, j = nvert-1; i < nvert; j = i++) {
			if ( ((this._transformedVertexBuffer.data[3*i+1]>testy) != (this._transformedVertexBuffer.data[3*j+1]>testy)) &&
				(testx < (this._transformedVertexBuffer.data[3*j]-this._transformedVertexBuffer.data[3*i]) * (testy-this._transformedVertexBuffer.data[3*i+1]) / (this._transformedVertexBuffer.data[3*j+1]-this._transformedVertexBuffer.data[3*i+1]) + this._transformedVertexBuffer.data[3*i]) )
				c = !c;
		}
		return c;
	}
	
	//unknown, requires render...
	return false;
};

Drawable.prototype.deleteChild = function (child) {
	var c = [];
	for (var i=0; i<this._children.length; i++) {
		if (this._children[i] != child)
			c.push(this._children[i]);
	}
	this._children = c;
	
	child._parent = null;
	
	if (Drawable.Selected == child) {
		Drawable.Selected.trigger("deselect");
		Drawable.Selected = null;
	}
};
Drawable.prototype.deleteChildAt = function (p) {
	var c = [];
	for (var i=0; i<this._children.length; i++) {
		if (i != p)
			c.push(this._children[i]);
		else {
			this._children[i]._parent = null;
			if (Drawable.Selected == this._children[i]) {
				Drawable.Selected.trigger("deselect");
				Drawable.Selected = null;
			}
		}
	}
	this._children = c;
};

Drawable.RENDER_BOUNDINGBOXES = false;

Drawable.prototype.addVertex = function (v) {
	this._vertices.push(v);
	this._vertexBuffer.add(v);
	this._transformedVertexBuffer.empty();
};
Drawable.prototype.removeVertexAt = function (j) {
	var tmp = [];
	var tmp2 = new VertexBuffer._class((this._vertices.length-1)*3);
	
	var k=0;
	for (var i=0; i<this._vertices.length; i++) {
		if (i !== j) {
			tmp.push(this._vertices[i]);
			tmp2[k++] = this._vertexBuffer.data[i*3];
			tmp2[k++] = this._vertexBuffer.data[i*3+1];
			tmp2[k++] = this._vertexBuffer.data[i*3+2];
		}
	}
	
	this._vertices = tmp;
	this._vertexBuffer.data = tmp2;
	this._vertexBuffer._head = k;
	this._transformedVertexBuffer.empty();
};
Drawable.prototype.clearVertices = function() {
	this._vertices = [];
	this._vertexBuffer.empty();
	this._transformedVertexBuffer.empty();
};

Drawable.prototype.getAverageCenter = function() {
	var v = new Vertex(0,0);
	for (var i=0; i<this._vertices.length; i++) {
		v.x += this._vertices[i].x;
		v.y += this._vertices[i].y;
	}
	v.x /= this._vertices.length;
	v.y /= this._vertices.length;
	return v;
};

Drawable.prototype.fastRound = function(somenum) {
	return (0.5 + somenum) | 0;
};
Drawable.prototype.fastFloor = function(somenum) {
	return somenum | 0;
};
Drawable.prototype.fastPow2 = function(i) {
	if (i !== (i|0)) {
		return Math.pow(2, i);
	}
	else {
		if (i < 0)
			return 1 / (1 << -i);
		else
			return 1 << i;
	}
};

Drawable.prototype.dumpTree = function () {
	console.groupCollapsed(this, "Name="+this.name, "CanRender="+this.settings.canRender);
	for (var i=0; i<this._children.length; i++)
		this._children[i].dumpTree();
	console.groupEnd();
};

/* abstract methods */

Drawable.prototype._render = function(vertexBuffer, ctx, style) {
	throw "Drawable._render must be implemented.";
};
