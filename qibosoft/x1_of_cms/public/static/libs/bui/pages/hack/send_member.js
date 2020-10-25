//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.send_member = {

	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		$('#btn_send_member').click(function(){
			layer.prompt({
				  formType: 0,
				  value: '',
				  title: '你要发信息给哪个用户名?',
				  //area: ['100px', '20px'] //formType:2 自定义文本域宽高
				}, function(value, index, elem){
					layer.close(index);
					$.get(get_uid_by_name_url+"?name="+value,function(res){
						if(res.code==0){
							layer.msg("你现在可以给他发消息了");
							uid = res.data.uid;					
							set_user_name(uid);
							show_msg_page = 1;
							showMoreMsg(uid);
						}else{
							layer.alert('当前用户不存在!');
						}
					});
			});
		});
	},

	finish:function(res){  //所有模块加载完才执行
	},
	logic_init:function(res){ //init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
	},

}