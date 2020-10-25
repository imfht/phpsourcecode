/* Copyright(c) 2008 Wang, Chun-Pin All rights reserved.
 *
 * Version:     $Id: ajax.js,v 1.1 2008/11/30 03:31:44 alex Exp $
 *
 */

/**
 * Send AJAX request.
 * 
 * ALEXWANG.Ajax.request( option );
 * 
 * Option parameters is an object:
 * .url {String} 
 *     The URL to which to send the request.
 * .params {Object/String/Function} (Optional)
 *     An object containing properties which are used as parameters 
 *     to the request, a url encoded string or a function to call to
 *     get either. 
 * .method {String} (Optional) 
 *     The HTTP method to use for the request. Defaults to the "GET" 
 * 	if no parameters are being sent, and "POST" if parameters are
 * 	being sent.
 * .callback {Function} (Optional)
 *     The function to be called upon receipt of the HTTP response.
 * 	The callback is called regardless of success or failure and
 * 	is passed the following parameters:
 *     .options {Object} The parameter to the request call. 
 *     .success {Boolean} True if the request succeeded. 
 *     .response {Object} The XMLHttpRequest object containing 
 * 	                   the response data. 
 * .scope {Object} (Optional)
 *     The scope in which to execute the callbacks: The "this" 
 * 	object for the callback function. Defaults to the browser
 * 	window.
 * 
 * @example 
 * ALEXWANG.Ajax.request({
 *        url: 'ajax_handler.php',
 *        method: "POST",
 *        param: {
 *            action: 'album_update',
 *            album_id: album_id,
 *            album_name: value
 *        },
 *        callback: function (option, success, response) {
 *            if (success) {
 *                // do something
 *            } else {
 *                // do something
 *            }
 *        },
 *        scope: this
 *    });
 */
ALEXWANG.Ajax = function()
{
	/* Encode special charactors in URL.
	 * We must at least replace % & = + to avoid error
	 */
	var urlEncode = function(obj) {

		if (!obj) { return ""; }

		var buf=[];

		for (var key in obj) {
			var ov = obj[key], k = encodeURIComponent(key);
			var type=typeof ov;

			if(type=="undefined"){
				buf.push(k,"=&");
			}else{
				if(type!="function"&&type!="object"){
					buf.push(k,"=",encodeURIComponent(ov),"&");
				}else{
					if(ov instanceof Array){
						if(ov.length){
							for(var i=0,len=ov.length;i<len;i++){
								buf.push(k,"=",encodeURIComponent(ov[i]===undefined?"":ov[i]),"&");
							}
						}else{
							buf.push(k,"=&");
						}
					}
				}
			}
        }
		buf.pop();
		return buf.join("");
	};

	

	/* Make HTTP request. Create a HTTP request and return.
	 * Return values:
	 *	Success: HttpRequest
	 *	Fail:	false
	 */
	var httpInit = function() {
		var request = false;
	
		/* The following part is for IE, Firefox does not support new ActiveXObject() */
	
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		try {
			request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e2) {
				request = false;
			}
		}
		@end @*/
	
		/* This part is for firefox */
		if (!request && typeof XMLHttpRequest != 'undefined') {
			request = new XMLHttpRequest();
		}
	
		if (!request) {
			//alert("Failed to Initializing XMLHttpRequest!");
		}
	
		return request;
	};

	return {
		request: function(option) {
			var HttpRequest = false;
			var method = "POST";
			var param = "";

			// Handle method
			if (option.method && typeof option.method == "string") {
				if (option.method.toUpperCase() == "GET") {
					method = "GET";
				}
			} else {
				// no method
				if (!option.param) {
					method = "GET";
				}
			}

			// Handle param
			if (typeof option.param == "object") {
                param = urlEncode(option.param);
			} else if (typeof option.param == "function") {
                param = option.param.call(option.scope||window, option);
            } else if (typeof option.param == "string") {
				param = option.param;
			}

			HttpRequest = httpInit();
			if (!HttpRequest) {
				return false;
			}

			if (method == "GET" && param !== "") {
				if (option.url.indexOf("?") == -1) {
					option.url = option.url+'?'+param;
				} else {
					option.url = option.url+'&'+param;
				}
			}
			HttpRequest.open(method, option.url, true);

			HttpRequest.onreadystatechange = function() {
				if (HttpRequest.readyState == 4) {
					// Safari will have undefined HttpRequest.status if nothing return.
					if (HttpRequest.status == 200 || !HttpRequest.status) {
						var response = HttpRequest.responseText;
						option.callback.apply(option.scope || window, [option, true, response]);
					} else {
						option.callback.apply(option.scope || window, [option, false, response]);
					}
					HttpRequest = null;
				}
			};

			if (method == "POST") {
				// Specify that the body of the request contains form data
				HttpRequest.setRequestHeader("Content-Type", 
												  "application/x-www-form-urlencoded");
				HttpRequest.send(param);
			} else {
				HttpRequest.send(null);
			}
			
			return true;
		}
	};
}();

