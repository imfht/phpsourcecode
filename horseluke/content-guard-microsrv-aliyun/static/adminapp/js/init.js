requirejs.onError = function (err) {
    if (err.requireType === 'timeout') {
    	var msg = "资源载入失败，请刷新页面重试。\r\n\r\nTechnical Detail " + err;
    	alert(msg);
    }else{
    	throw err;
    }
};

require.config({
    urlArgs: "_xver=1",   //http://stackoverflow.com/questions/8315088/prevent-requirejs-from-caching-required-scripts
    baseUrl: "./static/adminapp/jsmod",
    paths: {
        "jquery": "../../jquery_1.11.1/jquery.min",
        "template": "../../artTemplate/template",
        "webjstool": '../js/webjstool',
        "bootstrap_jquery_intergrade": '../../bootstrap-3.3.5/js/bootstrap.min',
        "AMapLoader": 'http://webapi.amap.com/maps?v=1.3&key=9e2d2aa1abff4de8516d614a8136ff2f&callback=amapReallyLoadedcallback_func'
    },
    shim: {
        'AMapLoader': {
            exports: 'amapReallyLoadedcallback_var',
            init: function(){
            	//window.amapReallyLoadedcallback_var = 0;
            	window.amapReallyLoadedcallback_func = function(){
            		//window.amapReallyLoadedcallback_var = 1;
            	}
            }
        },
        'bootstrap_jquery_intergrade': {
            deps: ['jquery'],
            exports: 'bootstrap_jquery_intergrade_on_boot',
            init: function(){
            	window.bootstrap_jquery_intergrade_on_boot = 1;
            }
        },
        'webjstool': {
            exports: 'webjstool'
        }
    }
});

require(['bootstrap_jquery_intergrade'], function(a){});    //载入Bootstrap资源

