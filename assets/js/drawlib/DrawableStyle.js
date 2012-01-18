var DrawableStyle = function(p) {
	if (!p)
		p = {};
	this.thickness = p.thickness || p.T || 2.0;
	this.lineColor = p.lineColor || p.L || "#ff0000";
	this.fillColor = p.fillColor || p.F || "rgba(255,0,0, 0.25)";
	this.linestyle = p.linestyle || p.S || DrawableStyle.Linestyle.Simple;
};

DrawableStyle.Linestyle = {
	None: 0,
	Simple: 1,
	Dot: 2
};

DrawableStyle.prototype.clone = function() {
	return new DrawableStyle(this);
};
