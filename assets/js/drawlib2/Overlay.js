var Overlay = function(asset, cache) {
	Overlay.prototype.constructor.call(this);
	
	this.asset = asset;
	this.cache = cache === undefined ? true:cache;
	
	for (var i in this.styles) {
		this.styles[i].fillColor = "transparent";
	}
	
	this.settings.hackNoRotation = false;
	
	this._triggeredLoad = false;
	this.resize(0,0);
	
	this.events["load"] = [];
};

Overlay.prototype = new Rectangle(0,0);

Overlay.prototype.loaded = function() {
	return this._drawing && this._drawing.assets[this.asset] && this._drawing.assets[this.asset].loaded;
};

Overlay.prototype.ensureLoaded = function () {
	var loading = this._drawing.assets[this.asset];
	if (!loading) {
		console.log("Overlay autoload: "+this.asset);
		var that=this;
		this._drawing.loadAsset(this.asset, function() {
			that.trigger("load");
			this._triggeredLoad=true;
		}, this.cache);
	}
};

Overlay.prototype._render = function (vertexBuffer, ctx, style) {
	this.ensureLoaded();
	if (!this.loaded())
		return;
	if (!this._triggeredLoad && this.loaded()) {
		this.trigger("load");
		this._triggeredLoad=true;
	}
	
	var image = this._drawing.assets[this.asset];
	
	if (this._width == 0 || this._height == 0) {
		this.resize(image.width, image.height);
	}
	
	var T = this.getMatrix();
	
	//rotate stuff
	ctx.save();
	
	if (!this.settings.hackNoRotation) {
		//for some odd reasons, seems the canvas rotation is going on the wrong logical way...
		var transform = T.decompose(true);
		T = T.multiply(Matrix.CreateRotation(-2*transform.rotation));
	}
	
	ctx.setTransform(
		T.data[0],
		T.data[1],
		T.data[3],
		T.data[4],
		T.data[2],
		T.data[5]
	);
	
	ctx.drawImage(image, 0,0);
	
	//unrotate stuff
	ctx.restore();
	
	Rectangle.prototype._render.call(this, vertexBuffer, ctx, style);
};

Overlay.prototype.applyTransform = function() {
	throw "Cannot apply transform on an Overlay instance"
};
