//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.tel = {

	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		if(quninfo.telphone==''){
			$('#btn_tel').hide();
			return ;
		}
		if(in_pc==true){
			$('#btn_tel').click(function(){				
				layer.alert("你确认要拨打圈主的电话吗?<br>TA的号码是："+quninfo.telphone);
			});
		}else{
			$('#btn_tel').click(function(){			
				window.location.href="tel:"+quninfo.telphone;
				return ;

				layer.confirm("<a href='tel:"+quninfo.telphone+"'>你确认要拨打圈主的电话吗?<br>TA的号码是："+quninfo.telphone+"</a>",{
					btn:['确认','取消'],
					btn1:function(){
						window.location.href="tel:"+quninfo.telphone;
					}
				});
			});
		}
	},

	finish:function(res){  //所有模块加载完才执行
	},
	logic_init:function(res){  //init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		
	},
}



//对聊天内容进行重新转义显示
format_content.tel = function(res,type){
}

//类接口,WebSocket下发消息的回调接口
ws_onmsg.tel = function(obj){
}