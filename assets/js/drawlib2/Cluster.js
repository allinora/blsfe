var Cluster = function(clusterBasePath, ExportSettings) {
	Cluster.prototype.constructor.call(this);
	this.clusterBasePath = clusterBasePath;
	this.level = -1;
	this.ExportSettings = ExportSettings;
	
	this.settings.fill = false;
	this.settings.stroke = false;
	
	this.settings.canSelect = false;
	this.settings.computeVertices = false;
	
	//this.resize(ExportSettings.WIDTH, ExportSettings.HEIGHT);
	
	this.POW2_ZOOM_STEP = this.fastPow2(this.ExportSettings.ZOOM_STEP);
	
	level = ExportSettings.ZOOM_MAX;
	
	for (var i=level; i>=0; i--) {
		this._updateTotalClusters(this.ExportSettings.ZOOM_MAX-i);
		var zoomFactor = this.fastPow2((i-this.ExportSettings.ZOOM_MAX) * this.ExportSettings.ZOOM_STEP);
		
		var layer = new Drawable();
		layer._render = function(){};
		this.addChild(layer);
		layer.settings.canRender = i == level;
		for (var u=0; u<this._totalClustersX; u++) {
			for (var v=0; v<this._totalClustersY; v++) {
				var tile = new Overlay(this._getClusterUrl(i, u, v));
				tile.transform.scaleBy(zoomFactor, zoomFactor);
				tile.transform.translateBy(u*this.ExportSettings.CLUSTER_WIDTH*zoomFactor, v*this.ExportSettings.CLUSTER_HEIGHT*zoomFactor);
				tile.settings.canRender = false;
				tile.settings.stroke = false;
				tile.settings.fill = false;
				tile.settings.hackNoRotation = true;
				tile.settings.computeVertices = false;
				tile.resize(this.ExportSettings.CLUSTER_WIDTH, this.ExportSettings.CLUSTER_HEIGHT);
				//tile.settings.rotationSupport = false;
				layer.addChild(tile);
			}
		}
	}
	
	this.setZoomLevel(level);
};

Cluster.prototype = new Rectangle();

Cluster.prototype._updateTotalClusters = function(level) {
	var scaledWidth = this.fastRound(this.ExportSettings.WIDTH / this.fastPow2(this.ExportSettings.ZOOM_MAX-level)); 
	var scaledHeight = this.fastRound(this.ExportSettings.HEIGHT / this.fastPow2(this.ExportSettings.ZOOM_MAX-level)); 
	this._totalClustersX = Math.ceil(scaledWidth / this.ExportSettings.CLUSTER_WIDTH);
	this._totalClustersY = Math.ceil(scaledHeight / this.ExportSettings.CLUSTER_HEIGHT);
};

Cluster.prototype._getClusterUrl = function(level, u, v) {
	u = u|0;
	v = v|0;
	return this.clusterBasePath + "/"+level+"/"+u+"_"+v+".jpg?nocache=3";
};

Cluster.prototype.setZoomLevel = function (newLevel) {
	if (newLevel < 0 || newLevel > this.ExportSettings.ZOOM_MAX || newLevel == this.level)
		return;
	//console.log("setZoomLevel", newLevel)
	for (var i=0; i<this._children.length; i++) {
		var diff = this.ExportSettings.ZOOM_MAX-i;
		this._children[i].settings.canRender = (diff == newLevel || diff == newLevel+1);
	}
	
	this.level = newLevel;
};

Cluster.prototype._render = function(_, ctx) {
	if (this._parent && this._parent !== this._drawing)
		throw "Cluster can only be added to a Drawing object";
	var t = this._drawing.camera.transform.decompose();
	
	var zoomFactor = 1;
	
	var mi = this._drawing.camera.transform.getInvertMatrix();
	var tl = mi.multiplyVertex(new Vertex(0,0));
	
	for (var i=0; i<=this.ExportSettings.ZOOM_MAX; i++) {
		var layer = this._children[i];
		if (layer.settings.canRender) {
			this._updateTotalClusters(i);
			
			var p = tl.scaleBy(t.scale.x, t.scale.y);
			
			var inv_screenTileWidth = 1/(zoomFactor * this.ExportSettings.CLUSTER_WIDTH * t.scale.x);
			var inv_screenTileHeight = 1/(zoomFactor * this.ExportSettings.CLUSTER_HEIGHT * t.scale.y);
			
			var _u0 = p.x * inv_screenTileWidth;
			var _v0 = p.y * inv_screenTileHeight;
			
			var u0 = _u0|0;//Math.floor(_u0);
			var v0 = _v0|0;//Math.floor(_v0);
			
			var u1 = (_u0 + this._drawing._w * inv_screenTileWidth)|0;
			var v1 = (_v0 + this._drawing._h * inv_screenTileHeight)|0;
			
			u0--;
			v0--;
			u1++;
			v1++;
			
			var _k=0;
			for (var u=0; u<this._totalClustersX; u++) {
				for (var v=0; v<this._totalClustersY; v++) {
					var tile = layer._children[_k++];
					//if (!tile)
					//	break;
					tile.settings.canRender = u > u0 && u < u1 && v > v0 && v < v1;
				}
			}
		}
		
		zoomFactor /= this.POW2_ZOOM_STEP;
	}
};

Cluster.prototype.dumpVisible = function() {
	for (var i=0; i<this._children.length; i++) {
		var layer = this._children[i];
		var numvisible=0;
		for (var j=0; j<layer._children.length; j++)
			if (layer._children[j].settings.canRender)
				numvisible++;
		
		console.group("Layer "+i, "CanRender="+layer.settings.canRender, "|children|="+layer._children.length, "|visible|="+numvisible);
		
		if (layer.settings.canRender) {
			this._updateTotalClusters(i);
			for (var j=0; j<layer._children.length; j++) {
				var tile = layer._children[j];
				if (!tile.settings.canRender)
					continue;
				
				
				var u = (j / this._totalClustersY)|0;
				var v = j % this._totalClustersY;
				console.log("Cluster", "u="+u, "v="+v, tile)
			}
		}
		
		console.groupEnd();
	}
};
