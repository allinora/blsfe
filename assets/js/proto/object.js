(function() {
	var base = Object.prototype.toString;
	Object.prototype.toString = function(debug) {
		if (debug) {
			var s = "[";
			for (var key in this) {
				s += " "+key+"=\""+this[key]+"\"";
			}
			return s + " ]";
		}
		else {
			return base.call(this);
		}
	};
	
	Object.sort = function (object, comparer) {
		var sortable = [];
		
		for (var key in object) {
			sortable.push({ key:key, value:object[key] });
		}
		
		sortable.sort(comparer);
		return sortable;
	};
})();
