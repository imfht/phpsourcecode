var LayIm;

var is_first_load = true;
//接收各种WS的消息处理
WS.onmsg(function(obj){
	if(obj.type=='count'){
		if(is_first_load===true){	//避免反复执行
			is_first_load = false;
			if(WS.guest_id()>0){	//游客现在才获取到IP
				var timer = setInterval(function() {
					if(LayIm!=undefined){
						clearInterval( timer );
						LayIm.config( get_config() );	//初始化游客聊天窗口,登录用户的话,不在这里处理,需要查询数据库聊天记录
					}
					console.log('游客LayIm加载中...');
				},30);			
			}
		}
	}else if(obj.type=='have_new_msg' || obj.type=='qun_sync_msg'){
		var data;
		if(obj.type=='qun_sync_msg'){	//兼容群聊的模式 两端直接连通的情况
			data = obj.data[0];
			if(data.uid==WS.my_uid()){
				return ;
			}
		}else{	//私信的专有模式,两端并没有直接连通
			data = obj.data.msgdata;
		}		
		LayIm.getMessage({
			username: data.from_username
			,avatar: data.from_icon==''?'/public/static/images/noface.png':data.from_icon
			,id: data.qun_id>0 ? -data.qun_id : data.uid
			,type: data.qun_id>0 ? "group" : "friend"
			,content: data.content
			,timestamp:data.full_time*1000
			,is_new:true	//需要声音提醒有新消息
      });

	}else if(obj.type=='check_online'){
		if(obj.online==1){
			$(".layim-chat-status").html('<span style="color:#10ff3a;">对方在线</span>');
		}else{
			$(".layim-chat-status").html('<span style="color:#eee;">对方不在线</span>');
			//LayIm.setChatStatus('<span style="color:blue;">对方不在线</span>');
		}
	}
});

//根据游客或者是登录用户获取不同的配置参数
function get_config(obj){
	//客服列表数据
	let kefu_list = [];
	KF.kefu_list.forEach(function(rs,id){
		kefu_list.push({
			id:id,
			username:rs.name,
			avatar:rs.icon?rs.icon:'/public/static/images/noface.png',
			sign:rs.sign ? rs.sign : '顾客至上，用心服务',
		});
	});
	var local = layui.data('layim-mobile')[WS.my_uid()] || {}; //本地缓存数据

	var init = {};
	if(obj && obj.init){	//登录用户获取到数据库聊天记录的情况, 游客的话,就没有从数据库获取聊天记录了
		init = obj.init;
		local.history = init.history;
		layui.data('layim-mobile', {
			key: WS.my_uid()
			,value: local
		});
	}else{
		//这里修改缓存,只为了默认展开客服列表而已
		local.spread0 = "true";
		layui.data('layim-mobile', {
			key: WS.is_login() ? WS.my_uid() : WS.guest_id()	//注意,guest_id这个值不会马上得到,需要在WS.onmsg执行后,才有值
			,value: local
		});
		init = {
			mine: {
				username: WS.is_login() ? '会员' : '访客',
				id: WS.is_login() ? WS.my_uid() : WS.guest_id(),
				status:'online',
				sign:'欢迎你的到来!',
				avatar:'/public/static/images/noface.png',
			},
			friend: [{
				groupname:'客服列表',
				id:1,
				online:2,
				list:kefu_list,
			},{
				groupname:'交流合作',
				id:2,
				online:0,
				list:[],
			}],
			group: [],
		};
	}


	return {
		//上传图片接口
		uploadImage: WS.is_login()?{url: '/index.php/index/attachment/upload/dir/chatpic/module/chat.html'}:false
		
		//上传文件接口
		,uploadFile: false
		
		//,brief: true

		,init: init
		
		//扩展更多列表
		,moreList: [{
		  alias: 'find'
		  ,title: '发现'
		  ,iconUnicode: '&#xe628;' //图标字体的unicode，可不填
		  ,iconClass: '' //图标字体的class类名
		},{
		  alias: 'share'
		  ,title: '分享与邀请'
		  ,iconUnicode: '&#xe641;' //图标字体的unicode，可不填
		  ,iconClass: '' //图标字体的class类名
		}]
		,brief:WS.is_login()?false:true
		,tabIndex: WS.is_login()?0:1 //登录用户就显示会话记录,未登录用户就显示好友列表
		,isNewFriend: false //是否开启“新的朋友”
		,isgroup: WS.is_login()?true:false //是否开启“群聊”
		//,chatTitleColor: '#c00' //顶部Bar颜色
		,title: WS.is_login()?'即时消息':'在线客服' //应用名，默认：我的IM
	}
}


var chat_Log;
//登录用户获取聊天记录
$(function(){
if( WS.is_login() ){
	$.get("/index.php/index/wxapp.layim/msg_user_list.html",function(res){
		if(res.code==0){
			chat_Log = res.data;
		}
	});
}
});

//有聊天记录的,登录用户初始化
function login_member_set_im(data){
	LayIm.config( get_config( data ? {init: data} : {}) );
	KF.kefu_list.forEach(function(rs,id){
		LayIm.addList({
			type: 'friend'
			,avatar: rs.icon?rs.icon:'/public/static/images/noface.png'
			,username: rs.name
			,groupid: 1
			,id: id
			,remark: rs.sign ? rs.sign : '顾客至上，用心服务'
		});
	});
}


//重置会话窗口
KF.chat_win = function(touid){
	if( WS.is_login()  ){
		login_member_set_im(chat_Log);
	}
	if(typeof(touid)=='object'){
		var o = touid;
		var username=o.username,type=o.type||'friend',icon=o.icon||'/public/static/images/noface.png',id=o.uid;
	}else{
		if(!touid){
			$(".layui-layim-min").trigger("click");
			return ;
		}
		LayIm.setFriendStatus(touid, 'online');
		var username,user = WS.user_db(touid)||KF.kefu_list[touid];
		if(user){
			username = user.name;
		}else if(touid==WS.kefu()){
			username = '客服MM';
		}else if(WS.my_uid()==WS.kefu()){
			username = '客户';
		}else{
			username = '网友';
		}
		var type = 'friend',icon = user&&user.icon ? user.icon : '/public/static/images/noface.png',id = touid;
	}
	LayIm.chat({
		name: username
		,type: type
		,avatar: icon
		,id: id,
	});
}




layui.config({
  version: true
}).use('mobile', function(){
  var mobile = layui.mobile
  ,layim = mobile.layim;
  //,layer = mobile.layer;  
  
  //layim.config( get_config() );  //对于单独的聊天界面,可以事先初始化,给用户更快的看到界面,因为对于游客来说,连接ws服务器需要时间,对于登录用户获取聊天记录也需要时间
 
  //监听点击“新的朋友”
  layim.on('newFriend', function(){
    layim.panel({
      title: '新的朋友' //标题
      ,tpl: '<div style="padding: 10px;">自定义模版，{{d.data.test}}</div>' //模版
      ,data: { //数据
        test: '么么哒'
      }
    });
  });
  
  //查看聊天信息
  layim.on('detail', function(data){
    //console.log(data); //获取当前会话对象
    layim.panel({
      title: data.name + ' 聊天信息' //标题
      ,tpl: '<div style="padding: 10px;">自定义模版，<a href="http://www.layui.com/doc/modules/layim_mobile.html#ondetail" target="_blank">参考文档</a></div>' //模版
      ,data: { //数据
        test: '么么哒'
      }
    });
  });
  
  //监听点击更多列表
  layim.on('moreList', function(obj){
    switch(obj.alias){
      case 'find':
        layer.msg('自定义发现动作');
        
        //模拟标记“发现新动态”为已读
        layim.showNew('More', false);
        layim.showNew('find', false);
      break;
      case 'share':
        layim.panel({
          title: '邀请好友' //标题
          ,tpl: '<div style="padding: 10px;">自定义模版，{{d.data.test}}</div>' //模版
          ,data: { //数据
            test: '么么哒'
          }
        });
      break;
    }
  });
  
  //监听返回
  layim.on('back', function(){
    //如果你只是弹出一个会话界面（不显示主面板），那么可通过监听返回，跳转到上一页面，如：history.back();
  });
  
  //监听自定义工具栏点击，以添加代码为例
  layim.on('tool(code)', function(insert, send){
    insert('[pre class=layui-code]123[/pre]'); //将内容插入到编辑器
    send();
  });
  
  //监听发送消息
  layim.on('sendMessage', function(data){
    var To = data.to;
    //console.log(data);

	WS.postmsg({
		content:data.mine.content,
		uid:data.to.id,
	});

    //演示自动回复
	/*
    setTimeout(function(){
      var obj = {};
      if(To.type === 'group'){
        obj = {
          username: '模拟群员'+(Math.random()*100|0)
          ,avatar: layui.cache.dir + 'images/face/'+ (Math.random()*72|0) + '.gif'
          ,id: To.id
          ,type: 'group'
          ,content: autoReplay[Math.random()*9|0]
        }
      } else {
        obj = {
          username: To.name
          ,avatar: To.avatar
          ,id: To.id
          ,type: To.type
          ,content: autoReplay[Math.random()*9|0]
        }
      }
      layim.getMessage(obj);
    }, 3000);*/
  });
  
  //模拟收到一条好友消息
  /*
  setTimeout(function(){
    layim.getMessage({
      username: "贤心"
      ,avatar: "http://tp1.sinaimg.cn/1571889140/180/40030060651/1"
      ,id: "100001"
      ,type: "friend"
      ,cid: Math.random()*100000|0 //模拟消息id，会赋值在li的data-cid上，以便完成一些消息的操作（如撤回），可不填
      ,content: "嗨，欢迎体验LayIM。演示标记："+ new Date().getTime()
    });

	layui.data('layim-mobile', {
      key: 2
      ,value: {rr:Math.random()}
    });

  }, 2000);
  */
  
  //监听查看更多记录
  layim.on('chatlog', function(data, ul){
    console.log(data);
    layim.panel({
      title: '与 '+ data.name +' 的聊天记录' //标题
      ,tpl: '<div style="padding: 10px;">这里是模版，{{d.data.test}}</div>' //模版
      ,data: { //数据
        test: 'Hello'
      }
    });
  });

  //监听聊天窗口的切换
	  layim.on('chatChange', function(res){
		var type = res.data.type;
		uid = res.data.id;
		
		if(uid>0){
			$.get("/index.php/index.php/index/wxapp.layim/set_read.html?uid="+uid,function(res){}); //标注已读
		}else{
			WS.link({"uid":uid,"kefu":0});	//跟圈子建立连接通道,才能收到那边新的即时消息
		}

		if(!WS.is_login()){
			console.log('游客的话,就不获取数据的记录了' );
			return ;
		}else{
			console.log('切换了窗口,UID是',uid);
		}
		
		var str = $.cookie('wap_layim_msg_id');
		if(str && str.indexOf(","+uid+",")>-1 && $(".layim-"+type+uid+" .layim-msg-status").html()<1){
			return ;
		}
		str = str ? str+uid+"," : ","+uid+"," ;
		$.cookie('wap_layim_msg_id', str, { expires: 3, path: '/' });	//expires的COOKIE时间单位是分钟

		$(".layim-"+type+uid+" .layim-msg-status").html(0).hide();
		
		//var index = layer.msg("请稍候,正在加载数据...",{time:500});
		$.get("/index.php/index.php/index/wxapp.layim/get_more_msg.html?rows=20&uid="+uid,function(res){
			//layer.close(index);
			if(res.code==0){
				for(var i=res.data.length-1;i>=0;i--){
					var rs = res.data[i];
					layim.getMessage({
						username: rs.from_username
						,avatar: rs.from_icon
						,timestamp:rs.full_time*1000
						,id: uid
						,type: type //"friend"
						,mine:WS.my_uid()==rs.uid?true:false
						,content: rs.content
					});
				}
			}else{
				//layer.msg('没有任何聊天记录!');
			}
		});

		if(type === 'friend'){			
		  //模拟标注好友状态
		  //layim.setChatStatus('<span style="color:#FF5722;">在线</span>');
		} else if(type === 'group'){
		  //模拟系统消息
		  /*
		  layim.getMessage({
			system: true
			,id: res.data.id
			,type: "group"
			,content: '模拟群员'+(Math.random()*100|0) + '加入群聊'
		  });*/
		}
	  });
  
	//模拟"更多"有新动态
	layim.showNew('More', true);
	layim.showNew('find', true);

	LayIm = layim;
});