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
		
		closePath: true,
		fillShape: true
	};
	
	this.state = Drawable.State.Default;
	
	this.styles = {};
	for (var i in Drawable.DefaultStyles) {
		this.styles[i] = Drawable.DefaultStyles[i].clone();
	}
	
	this.transform = new DrawableTransform();
	this.averageCenter = new Vertex(0,0);
	this.vertices = [];
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
	}).bind("mouseout", function() {
		if (this.state == "hover")
			this.state = "default";
		
		//this._mouse = null;
		
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
	
	this.transform.onInvalidate = function() {
		_this.invalidate();
	};
	
	this._mouse = new Vertex();
	this.mouseOverDrawable = null;
	
	this.name = "Drawable_" + Drawable._count;
	Drawable._count++;
};

Drawable.prototype.addChild = function (c) {
	c._parent = this;
	this._children.push(c);
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
		if (c.settings.canSelect && 
			(c.state == "default" || c.state == "hover" || c.state == "selected") && 
			c._hitTest(this._mouse)) {
				return c;
		}
	}
	return null;
};

Drawable.Selected = null;

Drawable.prototype.invalidate = function() {
	this.transform.invalidate(true);
};

Drawable.prototype.applyTransform = function () {
	for (var i=0; i<this.vertices.length; i++) {
		if (this.vertices[i])
			this.vertices[i] = this.transform.multiply(this.vertices[i]);
	}
	this.transform = new DrawableTransform();
	this.invalidate();
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
	if (!this.settings.canRender)
		return;
	
	var style = this.styles[this.state];
	if (!style)
		return;
	
	if (!ctx)
		return;
	
	//init right before rendering
	if (this.init) {
		this.invalidate();
		this.init = false;
	}
	
	if (typeof this._beforeRender == "function") {
		this._beforeRender();
	}
	
	//apply transform on vertices and compute bbox
	if (this.transform._validatedVertices == null) {
		this.transform._validatedVertices = [];
		var xmin = Infinity;
		var xmax = -Infinity;
		var ymin = Infinity;
		var ymax = -Infinity;
		
		var T = null;
		if (this._parent)
			T = this._parent.transform.matrix.multiply(this.transform.matrix);
		else
			T = this.transform.matrix;
		
		this.averageCenter.x = 0;
		this.averageCenter.y = 0;
		var count=0;
		for (var i=0; i<this.vertices.length; i++) {
			if (this.vertices[i]) {
				var v = T.multiplyVertex(this.vertices[i]);
				this.transform._validatedVertices.push(v);
				if (v.x < xmin) xmin = v.x;
				if (v.x > xmax) xmax = v.x;
				if (v.y < ymin) ymin = v.y;
				if (v.y > ymax) ymax = v.y;
				
				this.averageCenter.x += this.vertices[i].x;
				this.averageCenter.y += this.vertices[i].y;
				count++;
			}
		}
		this.averageCenter.x /= count;
		this.averageCenter.y /= count;
		this.boundingBox.resize(xmin, ymin, xmax-xmin, ymax-ymin);
	}
	
	//temporarily swap vertices with transformed vertices
	var _v = this.vertices;
	this.vertices = this.transform._validatedVertices;
	
	//shadow pass
	if (style.shadow.x != 0 || style.shadow.y != 0) {
		ctx.shadowOffsetX = style.shadow.x;
		ctx.shadowOffsetY = style.shadow.y;
		ctx.shadowBlur = style.shadow.blur;
		ctx.shadowColor = style.shadow.color;
		
		this._render(ctx, style);
		
		ctx.shadowOffsetX = 0;
		ctx.shadowOffsetY = 0;
		ctx.shadowBlur = 0;
		ctx.shadowColor = "transparent black";
	}
	
	//normal pass
	this._render(ctx, style);
	
	//swap vertices back
	this.vertices = _v;
	_v = null;
	
	//render bounds
	if (Drawable.RENDER_BOUNDINGBOXES) {
		this.boundingBox.render(ctx, 1, "#000");
	}
	
	//then render children
	for (var i=0; i<this._children.length; i++)
		this._children[i].render(ctx);
};

Drawable.prototype._hitTest = function (vertex) {
	//bounding box check
	if (this.boundingBox) {
		if (!this.boundingBox.hitTest(vertex))
			return false;
	}
	
	//polygon check
	if (this.transform._validatedVertices) {
		var i=0;
		var j=0;
		var c=false;
		var nvert = this.transform._validatedVertices.length;
		var testx = vertex.x;
		var testy = vertex.y;
		for (i = 0, j = nvert-1; i < nvert; j = i++) {
			if (this.transform._validatedVertices[i] && this.transform._validatedVertices[j]) {
				if ( ((this.transform._validatedVertices[i].y>testy) != (this.transform._validatedVertices[j].y>testy)) &&
					(testx < (this.transform._validatedVertices[j].x-this.transform._validatedVertices[i].x) * (testy-this.transform._validatedVertices[i].y) / (this.transform._validatedVertices[j].y-this.transform._validatedVertices[i].y) + this.transform._validatedVertices[i].x) )
					c = !c;
			}
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

Drawable.RENDER_BOUNDINGBOXES = false;

Drawable.prototype._beforeRender = null;

/* abstract methods */

Drawable.prototype._render = function(ctx, style) {
	throw "Drawable._render must be implemented.";
};