var Shape = function() {
	Shape.prototype.constructor.call(this);
	
	this.settings.canSelect = true;
	this.settings.canDelete = true;
	this.settings.canScale = true;
	this.settings.canMove = true;
	this.settings.canRender = true;
};
Shape.prototype = new Drawable();

Shape.prototype._render = function (ctx, style) {
	var moved = false;
	var lastVertex = null;
	var firstVertex = null;
	
	//first pass - fill
	if (this.settings.fillShape) {
		moved = false;
		ctx.beginPath();
		for (var j in this.vertices) {
			if (!this.vertices[j])
				continue;
			
			if (!moved) {
				ctx.moveTo(this.vertices[j].x, this.vertices[j].y);
				moved = true;
				lastVertex = this.vertices[j];
				firstVertex = this.vertices[j];
				continue;
			}
			
			ctx.lineTo(this.vertices[j].x, this.vertices[j].y);
			lastVertex = this.vertices[j];
		}
		ctx.closePath();
		ctx.fillStyle = style.fillColor;
		ctx.fill();
	}
	
	//2nd pass - stroke
	moved = false;
	ctx.beginPath();
	for (var j in this.vertices) {
		if (!this.vertices[j])
			continue;
		
		if (!moved) {
			ctx.moveTo(this.vertices[j].x, this.vertices[j].y);
			moved = true;
			lastVertex = this.vertices[j];
			firstVertex = this.vertices[j];
			continue;
		}
		
		switch (style.linestyle) {
			case DrawableStyle.Linestyle.Dot:
				ctx.dashedLine(
					lastVertex.x, lastVertex.y,
					this.vertices[j].x, this.vertices[j].y
				);
				break;
			
			case DrawableStyle.Linestyle.Simple:
				ctx.lineTo(this.vertices[j].x, this.vertices[j].y);
				break;
		}
		
		lastVertex = this.vertices[j];
	}
	if (this.settings.closePath) {
		switch (style.linestyle) {
			case DrawableStyle.Linestyle.Dot:
				ctx.dashedLine(
					lastVertex.x, lastVertex.y,
					firstVertex.x, firstVertex.y
				);
				break;
			
			case DrawableStyle.Linestyle.Simple:
				ctx.lineTo(firstVertex.x, firstVertex.y);
				break;
		}
	}
	
	ctx.strokeStyle = style.lineColor;
	ctx.lineWidth = style.thickness;
	ctx.stroke();
};
