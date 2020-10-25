if(!window.befen) {
	window.befen = {};
}

befen.each = function(obj, fun) {
	if (isArray(obj)) {
		for (var i = 0, len = obj.length; i < len; i++) {
			if (fun.call(obj[i], i, obj[i]) === false) {
				break;
			}
		}
	} else {
		for (var key in obj) {
			if (obj.hasOwnProperty(key)) {
				if (fun.call(obj[key], key, obj[key]) === false) {
					break;
				}
			}
		}
	}
}

befen.inArray = function(value, array) {
	if(typeof value == "string" || typeof value == "number") {
		for (var i in array) {
			if(value == array[i]) {
				return true;
			}
		}
	}
	return false;
}

befen.dataType = function(data, type) {
	if(Object.prototype.toString.call(type) == "[object Undefined]") {
		return Object.prototype.toString.call(data);
	} else {
		return (type === Object.prototype.toString.call(data));
	}
}


befen.isArray = function(_Array) {
	return befen.dataType(_Array, "[object Array]");
}

befen.isObject = function(_Object) {
	return befen.dataType(_Array, "[object Object]");
}

befen.isFunction = function(_Function) {
	return befen.dataType(_Array, "[object Function]");
}

