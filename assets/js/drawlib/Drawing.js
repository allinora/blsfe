var Drawing = function(w,h, mouseShiftVertex) {
	Drawing.prototype.constructor.call(this);
	
	mouseShiftVertex = mouseShiftVertex || new Vertex(0,0);
	
	this.settings.canSelect = true;
	this.settings.canDelete = false;
	this.settings.canScale = false;
	this.settings.canMove = false;
	this.settings.canRender = true;
	this.settings.clickThrough = true;
	
	this._mouseShiftVertex = mouseShiftVertex;
	this._w=w;
	this._h=h;
	this._createCanvas();
	
	this.assets = {};
	this.mouseOverDrawable = null;
};
Drawing.prototype = new Drawable();

Drawing.prototype._createCanvas = function () {
	var container=null;
	if (this.canvas) {
		container = $(this.canvas).parent()[0];
	}
	
	//IE...
	var el = document.createElement('canvas');
	el.setAttribute("width", this._w);
	el.setAttribute("height", this._h);
	if (typeof G_vmlCanvasManager != 'undefined') {
		el=G_vmlCanvasManager.initElement(el);
	}
	var ctx = el.getContext('2d');
	
	this.canvas = el;
	
	this.ctx = ctx;
	
	var $c = $(this.canvas);
	var _this=this;
	this.mouseOverDrawable = null;
	
	if (container) {
		$(container).append($c);
	}
	
	if (typeof G_vmlCanvasManager != 'undefined') {
		$c = $c.find("div"); //...
		$c.width(this._w).height(this._h);
	}
	
	$c.mousemove(function(e) {
		var m = new Vertex(
			e.pageX - $c.offset().left + _this._mouseShiftVertex.x,
			e.pageY - $c.offset().top + _this._mouseShiftVertex.y
		);
		_this.trigger("mouseover", m);
		
		_this.render();
	});
	
	$c.click(function() {
		_this.trigger("click");
		_this.render();
	});
	
	$c.mouseout(function() {
		_this.trigger("mouseout");
		_this.render();
	});
};

Drawing.prototype._render = function (ctx, style) {
	//clear context
	ctx.fillStyle = "#4A4A4A";
	ctx.fillRect(0,0, this.canvas.width, this.canvas.height);
};

Drawing.prototype.loadAsset = function (url) {
	var image = new Image();
	var _this = this;
	image.onload = function() {
		_this.render();
	};
	image.src = url;
	this.assets[url] = image;
};

Drawing._superRender = Drawing.prototype.render;
Drawing.prototype.render = function() {
	Drawing._superRender.call(this, this.ctx);
};
