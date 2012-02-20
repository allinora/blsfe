var Overlay = function(asset, NOT_USED) {
	Overlay.prototype.constructor.call(this);
	
	//compatibility issue...
	if (NOT_USED) {
		this.asset = NOT_USED;
	}
	else
		this.asset = asset;
	
	for (var i in this.styles) {
		this.styles[i].fillColor = "transparent";
	}
	
	this.events["assetloaded"] = [];
	
	this.resize(0,0);
};

Overlay.prototype = new Rectangle(0,0);

Overlay.prototype.ensureLoaded = function () {
	var loading = this._drawing.assets[this.asset];
	if (!loading) {
		console.log("Overlay autoload: "+this.asset);
		var that=this;
		this._drawing.loadAsset(this.asset, function() {
			that.trigger("assetLoaded");
		});
	}
};

Overlay.prototype._beforeRender = function() {
	var image = this._drawing.assets[this.asset];
	if (!image) return;
	
	if (this._width != image.width || this._height != image.height) {
		this.resize(image.width, image.height);
	}
};

(function() {
	var _superRender = Overlay.prototype._render;
	Overlay.prototype._render = function (ctx, style) {
		this.ensureLoaded();
		var image = this._drawing.assets[this.asset];
		var T = this.getMatrix();
		var transform = T.decompose();
		
		//rotate stuff
		ctx.save();
		
		//for some odd reasons, seems the canvas rotation is going on the wrong logical way...
		var M = T.multiply(Matrix.CreateRotation(-2*transform.rotation));
		
		ctx.setTransform(
			M.data[0],
			M.data[1],
			M.data[3],
			M.data[4],
			M.data[2],
			M.data[5]
		);
		
		ctx.drawImage(image, 0,0);
		
		//unrotate stuff
		ctx.restore();
		
		_superRender.call(this, ctx, style);
	};
})();

Overlay.prototype.applyTransform = function() {
	throw "Cannot apply transform on an Overlay instance"
};
