Array.prototype.first = function (predicate) {
	for (var i=0; i<this.length; i++) {
		if (predicate(this[i])) {
			return this[i];
		}
	}
	return undefined;
};

Array.prototype.any = function (predicate) {
	if (this.length == 0) return false;
	return this.first(predicate) !== undefined;
};

Array.prototype.all = function (predicate) {
	return !this.any(function(x){ return !predicate(x); });
};

Array.prototype.each = function (fn) {
	for (var i=0; i<this.length; i++)
		fn(this[i]);
	return this;
};

Array.prototype.map = function (fn) {
	var ret = [];
	for (var i=0; i<this.length; i++)
		ret.push( fn(this[i]) );
	return ret;
};
