var Overlay = function(drawing, asset, w, h) {
	Overlay.prototype.constructor.call(this);
	this.asset = asset;
	this._drawing = drawing;
	
	for (var i in this.styles) {
		this.styles[i].fillColor = "transparent";
	}
	
	this.resize(w,h);
};

Overlay.prototype = new Rectangle(0,0);

Overlay._superRender = Overlay.prototype._render;
Overlay.prototype._render = function (ctx, style) {
	var image = this._drawing.assets[this.asset];
	var transform = this.transform.decompose();
	
	//rotate stuff
	ctx.save();
	
	//for some odd reasons, seems the canvas rotation is going on the wrong logical way...
	var M = this.transform.matrix.clone().multiply(Matrix.CreateRotation(-2*transform.rotation));
	
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
	
	Overlay._superRender.call(this, ctx, style);
};

Overlay.prototype.applyTransform = function() {};
