var Shape = function() {
	Shape.prototype.constructor.call(this);
	
	this.settings.canSelect = true;
	this.settings.canDelete = true;
	this.settings.canScale = true;
	this.settings.canMove = true;
	this.settings.canRender = true;
	
	this.settings.fill = true;
	this.settings.stroke = true;
};
Shape.prototype = new Drawable();

Shape.prototype._render = function (vertexBuffer, ctx, style) {
	//first pass - fill
	if (this.settings.fill) {
		ctx.beginPath();
		for (var j=0; j<this._vertices.length*3; j+=3) {
			if (j==0) {
				ctx.moveTo(vertexBuffer[j], vertexBuffer[j+1]);
				continue;
			}
			
			ctx.lineTo(vertexBuffer[j], vertexBuffer[j+1]);
		}
		ctx.closePath();
		ctx.fillStyle = style.fillColor;
		ctx.fill();
	}
	
	//2nd pass - stroke
	if (this.settings.stroke) {
		ctx.beginPath();
		for (var j=0; j<this._vertices.length*3; j+=3) {
			if (j==0) {
				ctx.moveTo(vertexBuffer[j], vertexBuffer[j+1]);
				continue;
			}
			
			switch (style.linestyle) {
				case DrawableStyle.Linestyle.Dot:
					ctx.dashedLine(
						vertexBuffer[j-3], vertexBuffer[j-2],
						vertexBuffer[j], vertexBuffer[j+1]
					);
					break;
				
				case DrawableStyle.Linestyle.Simple:
					ctx.lineTo(vertexBuffer[j], vertexBuffer[j+1]);
					break;
			}
		}
		if (this.settings.closePath) {
			switch (style.linestyle) {
				case DrawableStyle.Linestyle.Dot:
					ctx.dashedLine(
						vertexBuffer[this._vertices.length*3-3], vertexBuffer[this._vertices.length*3-2],
						vertexBuffer[0], vertexBuffer[1]
					);
					break;
				
				case DrawableStyle.Linestyle.Simple:
					ctx.lineTo(vertexBuffer[0], vertexBuffer[1]);
					break;
			}
			//ctx.closePath();
		}
		
		ctx.strokeStyle = style.lineColor;
		ctx.lineWidth = style.thickness;
		ctx.stroke();
	}
};
