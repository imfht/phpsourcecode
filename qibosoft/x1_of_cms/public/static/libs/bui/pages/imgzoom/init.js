//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.imgzoom = {
	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		var d_url = typeof(api)=='object'?'':'/';
		loader.import(d_url+"public/static/libs/bui/pages/imgzoom/style.css",function(src){});
		loader.import(d_url+"public/static/libs/bui/pages/imgzoom/zoom.html",function(res){
			router.$("#chat_main").append(res);
		});
	},
	once:function(res){	//只加载一次
	},
	finish:function(res){  //所有模块加载完才执行
	},
	logic_init:function(res){ //init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
	},
}


//页面数据渲染完毕后执行的接口
format_content.imgzoom = function(res,type){
	if(typeof(ImagesZoom)=='object'){
		ImagesZoom.init({
			"elem": "#chat_win .chat-content"
		});
	}else{
		jQuery.getScript( (typeof(api)=='object'?'':'/')+"public/static/libs/bui/pages/imgzoom/zoom.js" ).done(function() {
			if(typeof(ImagesZoom)=='object'){
				ImagesZoom.init({
					"elem": "#chat_win .chat-content"
				});
			}
		}).fail(function() {
			layer.msg('public/static/libs/bui/pages/imgzoom/zoom.jss加载失败',{time:800});
		});
	}	
}