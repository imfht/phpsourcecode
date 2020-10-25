//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.givermb = {

	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		$('#btn_givermb').click(function(){
			if(uid>0){
				layer.alert('只有群聊才能使用打赏圈主');
				return ;
			}
			bui.load({ 
				url: "/public/static/libs/bui/pages/givermb/givermb.html",
				param:{
					uid:uid,
				}
			});
			router.$(".hack_wrap").hide();
		});
	},

	finish:function(res){  //所有模块加载完才执行
	},
	logic_init:function(res){  //init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
	},

}