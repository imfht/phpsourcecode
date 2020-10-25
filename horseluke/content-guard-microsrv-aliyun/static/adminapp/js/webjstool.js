/*
 * 完全独立文件，请保持完全独立，即单独引入均可工作
 * 请保证有且只有引用一次，否则会出现覆盖问题！
 */

!(function () {
	
	if(typeof this.webjstool  != "undefined"){
		return ;
	}
	
	var tool = {};
	tool.VERSION= '0.1.0';
	
	this.webjstool = tool;
	
})();

//配置
(function (tool) {
	
	if(typeof tool.cfg  != "undefined"){
		return ;
	}
	tool.cfg = {};
	
	var cfg = {};
	
	tool.cfg.get = function(str, def){
		if(str){
			def = def || null;
			return cfg[str] == undefined ? def : cfg[str];
		}else{
			return cfg;
		}
	};
	
	tool.cfg.set = function(val){
		for(var i in val){
			cfg[i] = val[i];
		}
	};
	
})(this.webjstool);


//url
(function (tool) {
	
	if(typeof tool.url  != "undefined"){
		return ;
	}
	
	tool.url={};
	
	var queryString;
	tool.url.parseQueryString = function(){
		queryString = tool.url.parseStr(window.location.search.substr(1).split('&'));
		return queryString;
	};
	
	tool.url.getQueryString = function(str, def){
		if(typeof queryString == "undefined"){
			tool.url.parseQueryString();
		}
		if(str){
			def = def || null;
			return typeof queryString[str] == "undefined" ? def : queryString[str];
		}else{
			return queryString;
		}
	};
	
	tool.url.parseStr = function(a){
	    if (a == "") return {};
	    var b = {};
	    for (var i = 0; i < a.length; ++i)
	    {
	        var p=a[i].split('=', 2);
	        if (p.length == 1)
	            b[p[0]] = "";
	        else
	            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
	    }
	    return b;
	};
	
	tool.url.toQueryString = function(urlParam, permitNull){
		permitNull = permitNull || false;
    	var urlq = "";
    	if(typeof urlParam == "string" || typeof urlParam == "number"){
    		urlq = urlParam;
    	}else if(typeof urlParam == 'object'){
    		var finalUrlPath = [];
    		for(var i in urlParam){
    			if(!permitNull && urlParam[i] == null){
    				continue;
    			}
    			var key = encodeURIComponent(i);
    			finalUrlPath.push(key + "=" + encodeURIComponent(urlParam[i]));
    		}
    		urlq = finalUrlPath.join("&");
    	}
    	return urlq;
	}
	
})(this.webjstool);
