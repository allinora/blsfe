var Cluster = function(clusterBasePath, ExportSettings, level, uParent, vParent, debug) {
	Cluster.prototype.constructor.call(this);
	this.clusterBasePath = clusterBasePath;
	this.level = level = level === undefined ? ExportSettings.ZOOM_MAX : level;
	this.ExportSettings = ExportSettings;
	
	debug = debug || 0;
	var tabz = "";
	for (var i=0; i<debug; i++)
		tabz += "   ";
	
	for (var i in this.styles) {
		this.styles[i].fillColor = "transparent";
	}
	
	this.resize(ExportSettings.WIDTH, ExportSettings.HEIGHT);
	
	//create children tree
	//each child contains 4 children (half sized)
	
	var scale = 1 / Math.pow(2, level);
	
	var u0, v0, numClustersX, numClustersY;
	if (uParent !== undefined && vParent !== undefined) {
		numClustersX = Math.pow(2, ExportSettings.ZOOM_STEP);
		numClustersY = Math.pow(2, ExportSettings.ZOOM_STEP);
		
		u0 = uParent * numClustersX;
		v0 = vParent * numClustersY;
	}
	else {
		u0 = v0 = 0;
		numClustersX = Math.ceil(ExportSettings.WIDTH * scale / ExportSettings.CLUSTER_WIDTH);
		numClustersY = Math.ceil(ExportSettings.HEIGHT * scale / ExportSettings.CLUSTER_HEIGHT);
	}
	
	this.transform.scaleBy(scale);
	
	this.settings.canSelect = false;
	
	if (level === ExportSettings.ZOOM_MAX) {
		this.settings.canRender = true;
	}
	else {
		this.settings.canRender = false;
	}
	
	for (var u=u0; u<u0+numClustersX; u++) {
		for (var v=v0; v<v0+numClustersY; v++) {
			var overlay = new Overlay( this._getClusterUrl(level, u, v) );
			overlay.transform.scaleBy(1/scale);
			overlay.transform.translateBy((u-u0)*ExportSettings.CLUSTER_WIDTH, (v-v0)*ExportSettings.CLUSTER_HEIGHT);
			this.addChild(overlay);
			
			//add another cluster on top of it...
			if (level > 0) {
				var cluster = new Cluster(clusterBasePath, ExportSettings, level-1, u,v, debug+1);
				cluster.transform.scaleBy(1/Math.pow(2, ExportSettings.ZOOM_STEP));
				overlay.addChild(cluster);
			}
		}
	}
};

Cluster.prototype = new Rectangle(0,0);

Cluster.prototype._getClusterUrl = function(level, u, v) {
	u = parseInt(u);
	v = parseInt(v);
	return this.clusterBasePath + "/"+level+"/"+u+"_"+v+".jpg";
};

Cluster.prototype.setZoomLevel = function (x) {
	if (x < 0 || x > this.ExportSettings.ZOOM_MAX)
		return;
	
	var loop = function(depth) {
		for (var i=0; i<this._children.length; i++) {
			var overlay = this._children[i];
			if (depth == x) {
				overlay.settings.canRender = true;
				overlay.settings.canRenderChildren = false;
				//overlay.ensureLoaded();
			}
			else {
				overlay.settings.canRender = false;
				overlay.settings.canRenderChildren = true;
				
				//render children clusters
				for (var j=0; j<overlay._children.length; j++) {
					var subCluster = overlay._children[j];
					subCluster.canRender = true;
					loop.call(subCluster, depth-1);
				}
			}
		}
	};
	
	this.level = x;
	loop.call(this, this.ExportSettings.ZOOM_MAX);
};

Cluster.prototype.autoZoomLevel = function() {
	//find the deepest visible overlay...
	var overlay = this._children[0];
	var depth = this.ExportSettings.ZOOM_MAX-this.level;
	console.log("XXX DEPTH = "+depth);
	for (var i=0; i<depth; i++) {
		var subCluster = overlay._children[0];
		overlay = subCluster._children[0];
	}
	
	var transform = overlay.getMatrix().decompose();
	
	if (transform.scale.x > 1.0) {
		console.log("autoZoom: ++");
		this.setZoomLevel(this.level-1);
		return true;
	}
	else if (transform.scale.x <= 1/Math.pow(2, this.ExportSettings.ZOOM_STEP)) {
		console.log("autoZoom: --");
		this.setZoomLevel(this.level+1);
		return true;
	}
	else {
		return false;
	}
};