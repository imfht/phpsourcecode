jQuery.fn.inputChange = function(callback){
	var _elem = this;
	var _flag = true;
	_elem.bind("compositionstart", function(event){
		_flag = 0;
	});
	_elem.bind("compositionend", function(event){
		_flag = 1;
	});
	_elem.bind("input propertychange", function(event){
		setTimeout(function(){
			if(_flag == 1 && Object.prototype.toString.call(callback) === "[object Function]") {
				callback(_elem);
			}
		}, 10);
	});
}
