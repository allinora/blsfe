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
	
	this._background = {
		val: "#888",
		type: "color",
		rw: Infinity,
		rh: Infinity
	};
	
	this.camera = new Camera(this);
	this._drawing = this;
};
Drawing.prototype = new Drawable();

Drawing.prototype.getJPEGData = function(quality, callback) {
	if (window.Worker) {
		var myThreadedEncoder = new JPEGEncoderThreaded(jpg_lib_path+"/jpeg_encoder_threaded_worker.js");
		myThreadedEncoder.encode(this.ctx.getImageData(0,0, this._w, this._h), quality, callback);
	}
	else {
		var myEncoder = new JPEGEncoder();
		var jpg = myEncoder.encode(this.ctx.getImageData(0,0, this._w, this._h), quality);
		callback(jpg);
	}
};

Drawing.prototype.setBackground = function (a, repeatW, repeatH) {
	var asset = this.assets[a];
	if (!asset) {
		//color
		this._background.val = a;
		this._background.type = "color";
	}
	else {
		//picture
		this._background.rw = repeatW || Infinity;
		this._background.rh = repeatH || Infinity;
		this._background.val = asset;
		this._background.type = "asset";
	}
};

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
	ctx.fillStyle = "#FFFFFF";
	ctx.fillRect(0,0, this.canvas.width, this.canvas.height);
	
	//then paint background
	if (this._background.type == "color") {
		ctx.fillStyle = this._background.val;
		ctx.fillRect(0,0, this.canvas.width, this.canvas.height);
	}
	else if (this._background.type == "asset") {
		for (var u=0; u<this.canvas.width+this._background.rw; u+=this._background.rw) {
			for (var v=0; v<this.canvas.height+this._background.rh; v+=this._background.rh) {
				ctx.drawImage(this._background.val, u, v);
			}
		}
	}
};

Drawing.prototype.getMatrix = function() {
	return this.camera.transform.matrix;
};

Drawing.prototype.loadAsset = function (url, callback) {
	console.log(this.name+".loadAsset("+url+")");
	
	var image = new Image();
	var _this = this;
	image.onload = function() {
		_this.render();
		if (typeof callback == 'function')
			callback();
	};
	image.src = url;
	this.assets[url] = image;
};

Drawing._superRender = Drawing.prototype.render;
Drawing.prototype.render = function() {
	Drawing._superRender.call(this, this.ctx);
};
