layer.closeAll = function(){console.log('不允许使用把全部层一次关闭,不然会影响到其它插件');};

//底部扩展键
$(function() {
    $('#doc-dropdown-js').dropdown({justify: '#doc-dropdown-justify-js'});
});

$(function(){
	$(".office_text").panel({iWheelStep:32});
});

//功能切换
$(document).ready(function(){
	$(".sidestrip_icon a").click(function(){ 
		var i = $(this).index();
		$(".sidestrip_icon a").eq(i).addClass("cur").siblings().removeClass('cur');
		$(".middle").hide().eq(i).show();
		//$("#windows_body ul").hide();
		//$("#windows_body ul").eq(i).show();
		if(i==0){
			$("#windows_body ul").hide();
			$(".pc_show_all_msg").show();
			
		}
		if(i==2){
			chat_type = 'tongji';
		}else{
			chat_type = 'chat';
		}
	});

	$(".wx_search button").click(function(){
		$(".wx_search input").val('');
		$(".pc_msg_user_list>li").show();
	});
	$(".wx_search input").keyup(function(){
		var word = $(this).val();
		var obj = $(".pc_msg_user_list>li");
		if(word===''){
			obj.show();
		}else{
			console.log('长 '+word,obj.length);
			if(obj.length>=50 && user_scroll==true){
				showMore_User();
			}
			obj.each(function(){
				if($(this).find('.user_name').html().indexOf(word)==-1){
					$(this).hide();
				}else{
					$('.pc_msg_user_list').css('top',0);
					$(this).show();
				}
			});
		}
	});
});



//三图标
window.onload=function(){
	function a(){
		var si1 = document.getElementById('si_1');
		var si2 = document.getElementById('si_2');
		var si3 = document.getElementById('si_3');
		si1.onclick=function(){
			si1.style.background="url(/public/static/libs/amazeui/images/icon/head_2_1.png) no-repeat"
			si2.style.background="";
			si3.style.background="";
		};
		si2.onclick=function(){
			si2.style.background="url(/public/static/libs/amazeui/images/icon/head_3_1.png) no-repeat"
			si1.style.background="";
			si3.style.background="";
		};
		si3.onclick=function(){
			si3.style.background="url(/public/static/libs/amazeui/images/icon/head_4_1.png) no-repeat"
			si1.style.background="";
			si2.style.background="";
		};
	};
	function b(){
		var text = document.getElementById('input_box');
		var chat = document.getElementById('chatbox');
		var btn = document.getElementById('send');
		var talk = document.getElementById('talkbox');
		btn.onclick=function(){
			if(text.value ==''){
				alert('不能发送空消息');
			}else{
				chat.innerHTML += '<li class="me"><img src="'+'/public/static/libs/amazeui/images/own_head.jpg'+'"><span>'+text.value+'</span></li>';
				text.value = '';
				chat.scrollTop=chat.scrollHeight;
				talk.style.background="#fff";
				text.style.background="#fff";
			};
		};
	};
	//a();
	//b(); 
};

//检查框架宽度与高度是否足够
$(function(){
	try{
		var obj = window.parent.$("iframe");
		for(var i=0;i<obj.length;i++){	 
			var url = obj.eq(i).attr('src');
			if(url!=undefined && url.indexOf('member/msg/index')>-1){
				if(obj.eq(i).css('height').replace('px','')<680){
					obj.eq(i).parent().parent().css({height:'750px'});
					obj.eq(i).css({height:'707px'});
				}

				if(obj.eq(i).parent().parent().hasClass('layui-layer-iframe') && obj.eq(i).parent().parent().css('width').replace('px','')<950){
					obj.eq(i).parent().parent().css({width:'950px',top:'0px'});
				} 
			}
		}
	}catch(e){
		console.error(e);
	}	
});

//PC圈子风格里边框架聊天界面时使用的
function load_chat_iframe(url,callback){
	if( window.parent.$("#iframe_play").length==0 ){
		return ;
	}
	if(url==''){	//关闭窗口
		window.parent.$("#iframe_play").attr("src","about:blank");
		window.parent.$(".iframe_play").hide(500);
		if(typeof(window.parent.close_play)=='function'){
			window.parent.close_play();
		}
		return ;
	}
	
	//这里绕个弯是解决JQ的BUG.不然重复执行的话,下面的load方法会执行多次.
	var obj = window.parent.$("#iframe_play").parent();
	var str = window.parent.$("#iframe_play")[0].outerHTML;
	window.parent.$("#iframe_play").remove();	
	obj.append(str);
	
	window.parent.$("#iframe_play").show();
	window.parent.$("#iframe_play").attr("src",url);	
	window.parent.$("#iframe_play").load(function(){
		var body = $(this).contents();	//body.find("body").html(); 获取页面元素
		var win = window.parent.$("#iframe_play")[0].contentWindow;	//win.test() 执行页面方法		
		if(typeof(callback)=='function'){
			callback(win,body);
		}
		var b_height = body.find("body").height();
		if(b_height>0){
			window.parent.$("#iframe_play").height( body.find("body").height() );
		}
		if(typeof(window.parent.open_play)=='function'){
			window.parent.open_play(url);
		}
	});
}


//异步加载被调用的函数  务必注意,这个函数名必须要跟标签名一样
function pc_msg_user_list(res){	
	$.each(res.ext.s_data,function(i,rs){		
		//console.log(rs.uid);		
		uid_array[rs.f_uid] = rs.id;
		if(uid==0){
			//console.log(rs.uid);
			uid = rs.f_uid;
			showMoreMsg(uid);
			set_user_name(uid);
		}
	});
	add_click_user();
}

var visit_uids = [];	//考虑到不同的圈子及圈子与私聊之间功能菜单不一样,所以要跳转才更好
//信息用户列表添加点击事件
function add_click_user(){

	//未读群消息排在前面
	var that = $(".pc_msg_user_list");
	var obj = $(".pc_msg_user_list .ck");
	for(var i=(obj.length-1);i>=0;i--){
		var o = obj.eq(i).parent();
		that.prepend( o.get(0).outerHTML);
		o.remove();
	}

	$(".pc_msg_user_list li").off('click');
	$(".pc_msg_user_list li").click(function(){
		uid = $(this).data('uid');
		visit_uids.push(uid);
		visit_uids.forEach(v=>{
			if(v<0){
				//window.location.href="/member.php/member/msg/index.html?uid="+uid;
				//return ;//考虑到不同的圈子及圈子与私聊之间功能菜单不一样,所以要跳转才更好
			}
		});		
		w_s.close();
		$(this).find(".shownum").removeClass("ck");
		$(".pc_msg_user_list li").removeClass('user_active');
		$(this).addClass('user_active');		
		console.log(uid);
		show_msg_page = 1; //重新恢复第一页
		msg_scroll = true; //恢复可以使用滚动条
		showMoreMsg(uid);	//加载相应用户的聊天记录
		set_user_name(uid); //设置当前会话的用户名

		//$(".live-player-warp").hide();
		//$("#players").html('<span>视频直播即将开始...</span>');
		have_load_live_player = false;
	});
	
}

//显示更多用户列表
function showMore_User(){
  ListMsgUserPage++;
  user_scroll = false;
  $.get(ListMsgUserUrl+ListMsgUserPage,function(res){  
    //console.log(res);
    //console.log(res.data);
    if(res.code==0){
      if(res.data==''){
        layer.msg("已经显示完了！",{time:500});
      }else{
        $('.pc_msg_user_list').append(res.data);
		pc_msg_user_list(res);
        user_scroll = true;
      }
    }else{
      layer.msg(res.msg,{time:2500});
    }
  });
}

//设置当前聊天的用户名
function set_user_name(uid){
	if(uid>0){
		$.get(get_user_info_url+"?uid="+uid,function(res){
			if(res.code==0){
				$("#send_user_name").html(res.data.username);
			}
		});
	}else if(uid<0){
		$.get("/index.php/qun/wxapp.qun/getbyid.html?id="+Math.abs(uid),function(res){
			if(res.code==0){
				$("#send_user_name").html(res.data.title);
			}
		});
	}else{
		$("#send_user_name").html("系统消息");
	}
}


//将对话内容的数组转成HTML字符串
function format_chatmsg_tohtml(array){
	if(typeof(array)=='string'){
		return array;
	}
		var str = '';
		var str_name = '';
		var str_del = '';
		var old_html = $(".pc_show_all_msg").html();
		var old_num = $(".pc_show_all_msg li").length,new_num = array.length;	//解决重复点击没有内容的BUG
		array.forEach((rs)=>{
			if(old_html.indexOf( '<li data-id="'+rs.id )>-1 && new_num<old_num){
				console.log('有重复的消息'+rs.id);
				return true;
			}
			str_name = (rs.qun_id && rs.uid!=my_uid)?`<div class="name" data-uid="${rs.uid}" onclick="$('#input_box').val('@${rs.from_username} ').focus()">@${rs.from_username}</div>`:'';
			str_del = ((rs.uid==my_uid||rs.touid==my_uid)||(uid<0&&my_uid==quninfo.uid)) ? `<i data-id="${rs.id}" class="del glyphicon glyphicon-remove-circle"></i>` : '';
			str += `<li data-id="${rs.id}" class="` + ( rs.uid==my_uid ? 'me' : 'other' ) + `">
						<dd class="time" data-time="${rs.full_time}"><a>${rs.create_time}</a></dd>
						${str_name}
						<a href="/member.php/home/${rs.uid}.html" class="user_icon" target="_blank"><img src="${rs.from_icon}" onerror="this.src='/public/static/images/noface.png'" title="${rs.from_username}"></a><span class="content">${rs.content}</span>
						${str_del}		
						</li>`;
		});
		return str;
}


//刷新最近的消息用户
function check_list_new_msgnum(){
		$.get(ListMsgUserUrl+"1",function(res){
			if(res.code==0){
				var remind = true;
				$.each(res.ext.s_data,function(i,rs){
					//出现新的消息新用户，或者是原来新消息的用户又发来了新消息
					if(typeof(uid_array[rs.f_uid])=='undefined'||rs.id>uid_array[rs.f_uid]){
						console.log('有新的消息来了');
						$('.pc_msg_user_list').html(res.data);
						add_click_user();
						if(remind && window.Notification){	//消息提醒
							remind = false;
							if(Notification.permission=="granted"){
								pushNotice();
							}else{
								Notification.requestPermission(function(status) {                  
									if (status === "granted") {
										pushNotice();
									}
								});
							}
						}
					}
					//新消息已读
					if(rs.new_num<1){
						$('.pc_msg_user_list .list_'+rs.f_uid+' .shownum').removeClass('ck');
						$('.pc_msg_user_list .list_'+rs.f_uid+' .shownum').html(rs.num>999?'99+':rs.num);
					}
					//console.log(rs.f_uid+'='+rs.id+'='+uid_array[rs.f_uid]);
					uid_array[rs.f_uid] = rs.id;
				});
			}
		});
}

//右下角弹信息提示,有新消息来了
function pushNotice(){
		console.log('你有新消息');
		var m = new Notification('新消息提醒', {body: '你收到一条新消息,请注意查收',});
			m.onclick = function () { window.focus();}
}

//滚动到底部   old_height为pc_show_all_msg之前的高度
function goto_bottom(old_height){
		var iCount = setInterval(function() {
			var obj = $(".pc_show_all_msg");	//反复的刷新.pc_show_all_msg渲染完毕没有.用setTimeout的话,数值小又担心还没渲染完毕,数字大,又让用户等太久.
			var now_height = obj.height();
			if(now_height>old_height){
				clearInterval(iCount);
				show_msg_top = now_height+20-$(".windows_body").height();
				obj.css({top:(-show_msg_top)+"px"});
			}
		}, 200);
}

//显示超长内容
function get_longmsg(id){
	ws_send({type:'user_ask_longmsg',msgid:id});
}

var online_members = []; //所有在线用户
var roll_user_obj = null;

//建立WebSocket长连接
var chat_timer,clientId = '';
var pushIdArray = [];
var connect_handle;
function ws_connect(){
	if(typeof(w_s)=='object'){	//强制重新建立新的连接
		console.log("#########被强制断开了,重新发起连接!!!!!!!!!!"+Math.random());
		$("#remind_online").hide();
		if(w_s.readyState==1||w_s.readyState==0){	//0是连接中,1是已连接上
			w_s.close();
		}
		if(typeof(connect_handle)!='undefined'){
			clearInterval(connect_handle); //避免重复发起定时器,而导致重复连接,把之前的连接任务全清掉
		}
		connect_handle = setInterval(function(){	//w_s.close()执行后,并不能马上关闭链接的
			if(w_s.readyState==3){	//0是连接中,1是已连接上,2是断开中,3是已完全断开
				clearInterval(connect_handle);
				ws_link();
			}
		}, 500 );
	}else{
		ws_link();
	}
}

//发送WebSocket信息
function ws_send(o,getcid){

	if(typeof(w_s)=='undefined'){ //解决还没开始连接就请求发送的情况.
		wait_connect(o,getcid);
	}else if(w_s.readyState==1){		
		w_s.send( get_msg(o,getcid) );
	}else{
		ws_connect(); //已断开,重新发起一次链接
		var index = layer.msg('连接已断开,正在重新发起链接,请稍候...');
		wait_connect(o,getcid);
	}
	function wait_connect(o,getcid){
		var k = 'require_senmsg';
		if(typeof(o)=='object'){
			if(o.type!=undefined){
				k+=o.type;
			}
			if(o.tag!=undefined){
				k+=o.tag;
			}
		}
		ws_onmsg[k] = function(obj){	//收到消息时候的回调
			ws_onmsg[k] = null;		//这一行不能缺少,不然会进入死循环
			setTimeout(function(){	//避免跟注册信息同时发送
				w_s.send( get_msg(o,getcid) );
			},200);
		}
	}
	function get_msg(o,getcid){
		if(typeof(getcid)=='string' && typeof(o)=='object'){
			o[getcid] = clientId;
		}
		return typeof(o)=='object' ? JSON.stringify( o ) : o;
	}
}

//WebSocket下发消息的回调接口,当前页面可以这样使用 ws_onmsg.xxxx=function(o){} 子窗口可以这样使用 parent.ws_onmsg.xxxx=function(o){}
var ws_onmsg = {};

function ws_link(){
	clientId = '';
	w_s = new WebSocket(ws_url);

	w_s.onmessage = function(e){
			var obj = {};
			try {
				obj = JSON.parse(e.data);
			}catch(err){
				console.log(err);
			}
			
			if(obj.type=='newmsg'){	//其它地方推送消息过来,非在线群聊
				if( (obj.data[0].qun_id>0 && uid==-obj.data[0].qun_id) || (obj.data[0].uid==uid||obj.data[0].touid==uid) ){
					check_new_showmsg(obj);	//推数据
					$.get("/index.php/index/wxapp.msg/update_user.html?uid="+uid+"&id="+obj.data[0].id,function(res){//更新记录
						console.log(res.msg);
					});	
				}
			}else if(obj.type=='new_msg_id'){	//圈子直播文字最后得到的真实ID
				pushIdArray[obj.data.push_id] = obj.data.id; //删除内容的时候要用到
				$.get("/index.php/index/wxapp.msg/update_user.html?uid="+uid+"&id="+obj.data.id,function(res){//更新记录
					console.log(res.msg);
				});
			}else if(obj.type=='qun_sync_msg'){	//圈子直播文字  
				check_new_showmsg(obj);
			}else if(obj.type=='delete_msg'){	//删除或撤回消息
				$(".pc_show_all_msg>li[data-id='"+obj.data.id+"']").hide();
			}else if(obj.type=='connect'){	//建立链接时得到客户的ID
				clientId = obj.client_id;
				if(uid==0){
					return ;
				}				
				$.get("/index.php/index/wxapp.msg/bind_group.html?uid="+uid+"&client_id="+clientId,function(res){	//绑定用户
					if(res.code==0){
						//layer.msg('欢迎到来!',{time:500});
					}else{
						layer.msg(res.msg);
					}
				});
				var username = my_uid>0?userinfo.username:'';
				var icon = my_uid>0?userinfo.icon:'';
				var is_quner = my_uid==quninfo.uid ? 1 : 0;	//圈主
				w_s.send('{"type":"connect","url":"'+window.location.href+'","uid":"'+uid+'","my_uid":"'+my_uid+'","is_quner":"'+is_quner+'","userAgent":"'+navigator.userAgent+'","my_username":"'+username+'","my_icon":"'+icon+'"}');
			}else if(obj.type=='count'){  //用户连接成功后,算出当前在线数据统计
				show_online(obj,'goin');
			}else if(obj.type=='leave'){	//某个用户离开了
				show_online(obj,'getout')
				//console.log(obj);
			}else if(obj.type=='give_online_user'){  //服务器给出在线用户数据
				show_online(obj,'show')
			}else if(obj.type=='msglist'){	//需要更新列表信息
				//console.log("消息列表,有新消息来了..........");
				//console.log(e.data);
				//obj.uid==uid即本圈子提交数据(或者自己正处于跟他人私聊),不用更新列表, obj.uid它人私信自己,就要更新,obj.uid是其它圈子也要更新
				//if( (obj.uid<0 && obj.uid!=uid) || (obj.uid==my_uid && obj.from_uid!=uid ) ){
				if( obj.uid==my_uid && obj.from_uid!=uid ){
					check_list_new_msgnum();
				}
			}else{
				//console.log(e);
			}


			//当前页面可以这样使用 ws_onmsg.xxxx=function(o){} 子窗口可以这样使用 parent.ws_onmsg.xxxx=function(o){}
			for(var index in ws_onmsg){
				if(typeof(ws_onmsg[index])=='function'){
					ws_onmsg[index](obj);
				}				
			}

	};
		
	w_s.onopen = function(e) {	//w_s.readyState CONNECTING: 0 OPEN: 1 CLOSING: 2 CLOSED: 3
		setTimeout(function() {
			if(clientId==''){
				console.log('clientId获取失败,WebSocket连接不顺畅',w_s.readyState);
				if(w_s.readyState==1||w_s.readyState==0){	////0是连接中,1是已连接上
					w_s.close();
				}
				ws_connect();
			}else{
				console.log('WebSocket成功连接上了 '+clientId,w_s.readyState);
			}		
		}, 1500 );
	};
	
	w_s.onerror = function(e){
		console.log("#########连接异常中断了.........."+Math.random(),e.code + ' ' + e.reason + ' ' + e.wasClean);
	};
	
	w_s.onclose = function(e){
		console.log("########连接被关闭了.........."+Math.random(),e.code + ' ' + e.reason + ' ' + e.wasClean);
	};
		
	if(typeof(chat_timer)!='undefined')clearInterval(chat_timer);
	chat_timer = setInterval(function() {
		if(w_s.readyState!=1){	//不处于正常连接状态
			ws_connect();
		}else{
			w_s.send('{"type":"refreshServer"}');
		}			
	}, 1000*50);	//50秒发送一次心跳

	var show_online = function(obj,type){
			var total = obj.total; //在线窗口,同一个人可能有多个窗口				 
			var data = obj.data;
			//online_members = data;
			//var usernum = obj.data.length;  //在线会员人数,已注册的会员
			if(type=='show'){
				view_online_user(data);
			}else if(total>1){
				if(typeof(parent.show_qun_online)=='function'){
					parent.show_qun_online(obj,type);
				}
				if(type=='goin'){
					layer.msg("有新用户："+data[0].username+" 进来了",{offset: 't'});
					//统计最近来访的用户开始
					online_members.forEach(function(rs,i){
						if(rs.username==data[0].username){
							online_members.splice(i,1);
						}						
					});
					online_members.push(data[0]);
					if(online_members.length>1){
						var str = '';
						for(var i=online_members.length-1;i>=0;i--){
							str += '<a href="/member.php/home/'+(online_members[i].uid>0?online_members[i].uid:0)+'.html" target="_blank">'+online_members[i].username + (i==0?'</a>':'</a>、');
						}
						if(roll_user_obj==null){
							$('.welcome_user').show();
							roll_user_obj = $('.welcome_user').html($('.welcome_user i')[0].outerHTML+str).liMarquee({loop:-1,direction:'left',scrollamount:30,circular:true});
						}else{
							roll_user_obj.liMarquee('destroy');
							$('.welcome_user').html($('.welcome_user i')[0].outerHTML+str);
							roll_user_obj.liMarquee('update');
						}
					}					
					//统计最近来访的用户结束
				}else if(type=='getout'){
					layer.msg(obj.msg,{offset: 't'});
				}
				$("#remind_online").show();
				if(uid>0){
					$("#remind_online").html('对方在线,请不要离开!');
				}else{
					$("#remind_online").html('共有 '+total+' 个访客,会员有 '+ obj.total_login +' 人! 查看详情');
					$("#remind_online").off('click');
					$("#remind_online").click(function(){
						layer.msg('请稍候,正在拉取数据!',{time:800});
						ws_send({type:'get_online_user',});
						//view_online_user(data);
					});
				}
			}else if( !$("#remind_online").is(':hidden') ){
				if(uid>0){
					layer.msg('对方已离开!',{offset: 't'});
				}else{
					layer.msg('人全走光了!'+obj.msg,{offset: 't'});
				}
				$("#remind_online").hide();
			}
	}

	var view_online_user = function(data){
			var str = '';
			data.forEach((rs)=>{
				str += '<a href="/member.php/home/'+rs.uid+'.html" target="_blank">'+rs.username+'</a>、';
			});
			layer.open({
					type: 1,
					anim: 5,
					shade: 0.1,
					shadeClose: true,
					title: '仅列出已注册的在线会员数，不含游客',
					area: $('body').width()<500?['95%', '300px']:['400px', '300px'],
					content: '<div style="padding:20px;line-height:180%;">'+str+'</div>',
			});
	}
}

var load_data = {};	//接口
var mod_class = {}; //模块间互相执行方法函数要用到的接口类
var iframe_body_class = {}; //框架文件接口
var is_repeat = 0; //判断是不是重复加载接口
var qun_link_handle,first_page_data;

//初次加载成功
function load_first_page(res){
	maxid = res.ext.maxid;

	quninfo = res.ext.qun_info;	//圈子信息

	ws_url = res.ext.ws_url;

	visit_uids.push(uid);

	//建立链接 延时执行,避免用户反复切换圈子
	if(typeof(qun_link_handle)!='undefined'){
		clearTimeout(qun_link_handle);
	}
	qun_link_handle = setTimeout(function(){
		ws_connect();
	},typeof(w_s)=='object'?5000:0);			

	//set_live_player(res);	//检查是否有视频直播


	//first_page_data = res; //这个要放在load_data下面
	
	if(!is_repeat){
		set_chatmod(res);	//只允许执行一次 , 加载不同的圈子,不会重复再执行
	}else{	//页面初次加载的时候,mod_class还没有全部加载完毕,所以这里不执行,放在set_chatmod执行
		for(var index in mod_class){
			if(typeof(mod_class[index].logic_init)=='function'){
				mod_class[index].logic_init(res);	//logic_init()方法或函数,每次加载不同的圈子,都会执行 ，init() 就不要再执行,不然会重复渲染界面
			}
		}
		//初次加载不能批量执行,JS文件模块可以这样使用 load_data.xxxx=function(o){} 框架网页可以这样使用 parent.load_data.xxxx=function(o){}
		for(var index in load_data){
			load_data[index](res);
		}
	}
	is_repeat++;
}

//设置模块,只允许执行一次 , 加载不同的圈子,不会重复再执行
function set_chatmod(res){
	var arr = res.ext.chatmod;
	var btn_str = '',iframe_str = '';
	var total_need_load = 0,total_have_load = 0;
	arr.forEach((rs)=>{
		if(rs.icon!=''){
			btn_str += `<a href="javascript:;" title="${rs.name}" id="btn_${rs.keywords}" class="set-chatmod-btn ${rs.icon}"></a>`;
		}
		if(rs.init_iframe!=''){
			total_need_load++;
			iframe_str += `<iframe style="display:none;" class="chat_iframe_hack" data-keyword="${rs.keywords}" name="iframe_${rs.keywords}" id="iframe_${rs.keywords}" src="${rs.init_iframe}"></iframe>`;
		}
		if(rs.init_jsfile!=''){
			total_need_load++;
			jQuery.getScript(rs.init_jsfile).done(function() {
				fisrt_load(res,rs.keywords);	//首次加载的时候,单独执行
				total_have_load++;				
				if(total_have_load>=total_need_load){
					run_mod_finsih(res)
				}
			}).fail(function(e) {
				total_have_load++;
				console.log("此文件加载失败或者是代码有错误!",rs.init_jsfile);
			});
		}
		if(rs.init_jscode!=''){
			eval(rs.init_jscode);
		}
	});
	$("#chat_model_btn").append(btn_str);
	$("body").append(iframe_str);

	//框架插件接口
	$(".chat_iframe_hack").each(function(){
		var k = $(this).data('keyword');
		$(this).load(function(){			
			iframe_body_class[k] = $(this).contents();	//body.find("body").html(); 获取页面元素
			mod_class[k] = $(this)[0].contentWindow;	//win.test() 执行页面方法
			//mod_class[k].init(res);
			fisrt_load(res,k);	//首次加载的时候,单独执行
			total_have_load++;
			if(total_have_load>=total_need_load){
				run_mod_finsih(res)
			}
		});
	});

	//首次加载的时候,单独执行
	function fisrt_load(res,keywords){
		if(typeof(mod_class[keywords])=='object'){
			if( typeof(mod_class[keywords].init)=='function' ){
				mod_class[keywords].init(res);
			}
			if( typeof(mod_class[keywords].logic_init)=='function' ){
				mod_class[keywords].logic_init(res);
			}
			if( typeof(mod_class[keywords].once)=='function' ){
				mod_class[keywords].once(res);
			}				
		} 
		if( typeof(load_data[keywords])=='function' ){
			load_data[keywords](res);
		}
		if(typeof(format_content[keywords])=='function'){
			format_content[keywords](res);
		}
	}

	//所有模块加载完毕后,检查有没有需要执行的回调方法
	function run_mod_finsih(res){
		for(var index in mod_class){
			if(typeof(mod_class[index].finish)!='undefined'){
				mod_class[index].finish(res);
			}
		}
	}
}



//加载更多的会话记录
function showMoreMsg(uid){
	if(show_msg_page==1){
		maxid = -1;
		get_qunuser_list(uid);	//获取圈内成员列表
		layer.msg("数据加载中,请稍候...",{time:300});
	}
	msg_scroll = false;
	var loadIndex = null;
	if(show_msg_page>1){
		loadIndex = layer.load(3,{shade: [0.1,'#333'],time:9000});
	}
	$.get(getShowMsgUrl+show_msg_page+"&uid="+uid+"&my_uid="+my_uid,function(res){
		if(res.code==0){
			if(show_msg_page==1){
				load_first_page(res);
			}else if(res.data.length>0){				
				$(".pc_show_all_msg").append("<div style='border-top:1px solid #ddd;border-bottom:1px solid #ddd;text-align:center;padding:5px;'>第"+show_msg_page+"页</div>");
				var old_height = $(".pc_show_all_msg").height();
			}
			set_main_win_content(res);	//这个函数里,做了show_msg_page++
			
			if(show_msg_page>2 && res.data.length>0){		
				setTimeout(function(){
					layer.close(loadIndex);
					var new_height = $(".pc_show_all_msg").height();
					var top = new_height - old_height - $(".windows_body").height();
					$(".pc_show_all_msg").css("top",((top>0?-top:0)-$(".windows_body").height())+'px');
					if( (typeof(res.data)=='string'&&res.data!='') || (typeof(res.data)!='string'&&res.data.length>0) ){
						msg_scroll = true;
					}
				},500);
			}else{
				if(loadIndex!=null)layer.close(loadIndex);
				if(res.data.length>0)msg_scroll = true;
			}
						
		}else{
			layer.close(loadIndex);
			layer.msg(res.msg,{time:2500});
		}
	});
}

//刷新会话用户中有没有新消息
var num = ck_num = 0;
function check_new_showmsg(obj){
    if(ws_url==''){	//没有设置WS的话,就用AJAX轮询
        if(ck_num>num){
            console.log("服务器还没反馈数据过来");
            //layer.msg("服务器反馈超时",{time:500});
            return ;
        }
    }
    
    if( typeof(obj)=='object' && typeof(obj.data)=='object' && obj.data.length>0 ){		//服务端推数据, 即被动获取数据
        var res = obj;
        
        var that = $('.pc_show_all_msg');
		
        var str = format_chatmsg_tohtml(res.data);
        if(str!=""){	//有新的聊天内容

            var refresh_newmsg = true;	//显示最新消息

			if(res.data[0].uid!=my_uid && show_msg_page>2 && $(".windows_body").height()+300-(that.height()-Math.abs(that.css('top').replace('px','')))<0 ){

				refresh_newmsg = false;	//不在第一屏的时候,就不滚动刷新,自己发布的话,就强制滚动刷新
			
			}else if(that.find('li').length>20){	//大于20条就自动清屏

				$('.pc_show_all_msg>li').each(function(i){
					if(i>10){
						$(this).remove();
						console.log('清除了第'+i+'条');
					}
				});
				show_msg_page = 2;
			}

			var old_height = that.height();

            that.prepend(str);
            format_show_time(that)			//隐藏相邻的时间
			
			
			if( refresh_newmsg ){
				goto_bottom(old_height);		//消息滚到最底部
			}
			
            content_add_btn(obj,'cknew');
            need_scroll = true;
            if(window.Notification){	//消息提醒
                if(Notification.permission=="granted"){
                    pushNotice();
                }else{
                    Notification.requestPermission(function(status) {
                        if (status === "granted") {
                            pushNotice();
                        }
                    });
                }
            }
        }
        //set_live_player(res,'cknew');	//设置视频直播的播放器 
        //add_msg_data(res,'new');
        if(typeof(res.ext)!='undefined')maxid = res.ext.maxid;	//不主动获取数据的话,这个用不到

		//当前页面可以这样使用 load_data.xxxx=function(o){} 子窗口可以这样使用 parent.load_data.xxxx=function(o){}
		for(var index in load_data){
			load_data[index](res,'cknew');
		}
        
    }else{	//客户端拉数据, 主动获取数据  ******已经弃用********
        
        $.get(getShowMsgUrl+"1&maxid="+maxid+"&uid="+uid+"&my_uid="+my_uid+"&num="+num,function(res){
            if(res.code!=0){
                layer.alert('页面加载失败,请刷新当前网页');
                return ;
            }
            //set_live_player(res,'cknew');	//检查是否有视频直播
            num++;
            ck_num = num;
            var that = $('.pc_show_all_msg');
            var str = format_chatmsg_tohtml(res.data);
            if(str!=""){	//有新的聊天内容
                var old_height = that.height();
                //console.log( '原来的高度='+old_height);
                that.prepend(str);
                format_show_time(that)	//隐藏相邻的时间
                goto_bottom(old_height);
                content_add_btn(res,'cknew');
                need_scroll = true;
                if(window.Notification){	//消息提醒
                    if(Notification.permission=="granted"){
                        pushNotice();
                    }else{
                        Notification.requestPermission(function(status) {
                            if (status === "granted") {
                                pushNotice();
                            }
                        });
                    }
                }
            }
            //console.log( '='+res.ext.lasttime);
            maxid = res.ext.maxid;
            if(res.ext.lasttime<3){	//3秒内对方还在当前页面的话,就提示当前用户不要关闭当前窗口
                if(uid>0){
                    $("#remind_online").html("对方正在输入中，请稍候...");
                }else{
                    $("#remind_online").html("有用户在线");
                }
                $("#remind_online").show();
            }else{
                $("#remind_online").hide();
            }

			//当前页面可以这样使用 load_data.xxxx=function(o){} 子窗口可以这样使用 parent.load_data.xxxx=function(o){}
			for(var index in load_data){
				load_data[index](res,'cknew');
			}

        });
            ck_num++;
    }
}

//往主窗口里边加入显示的数据, 聊天用到, 统计数据也用到
function set_main_win_content(res){
	//layer.closeAll();
	var that = $('.pc_show_all_msg');
	res.data = format_chatmsg_tohtml(res.data);
	if(res.data==''){
		if(show_msg_page==1){
			that.html("");
			layer.msg("没有任何聊天记录！",{time:1000});
		}else{
			layer.msg("已经显示完了！",{time:500});
		}		
	}else{
		
		//console.log("ddddddddddddddddd-"+show_msg_page);
		//need_scroll$('.pc_show_all_msg').css('top',(453-that.height())+'px');
		if(show_msg_page==1){
			that.html(res.data);
			format_show_time(that);			
			setTimeout(function(){
				var ckh = $(".windows_body").height()-473;
				that.css('top',(453-that.height()+ckh)+'px');
			},500);
		}else{
			var old_h = that.height();

			that.append(res.data);
			format_show_time(that);
			setTimeout(function(){
				var new_h = $(".pc_show_all_msg").height();				
				$(".pc_show_all_msg").css('top',(old_h-new_h)+'px');
			},500);
		}		

		content_add_btn(res);
		format_nickname();
		show_msg_page++;
		//msg_scroll = true;
	}
}

//隐藏相邻的时间
function format_show_time(that){
	that.children('li').each(function(i){
		var this_time = $(this).find('.time').data('time');
		var next_time = $(this).next().find('.time').data('time');
		//console.log(i+" "+this_time+" "+next_time);
		if(next_time!=undefined && this_time-next_time<60){
			$(this).find('.time').hide();
		}
	})
}

//加载统计动态的详细内容数据
function get_tongji_msg(type){
	if(show_msg_page==1){
		$.get(tongjiCountUrl+"?set_read=1&type="+tj_type,function(res){});//把新数据标志为已读
		layer.msg("数据加载中,请稍候...");
	}
	msg_scroll = false;
	$.get(tongjiMsgUrl + show_msg_page + "&type="+type,function(res){
		if(res.code==0){
			set_main_win_content(res);
			if( (typeof(res.data)=='string'&&res.data!='') || (typeof(res.data)!='string'&&res.data.length>0) ){
				msg_scroll = true;
			}
		}
	});
}

var msg_id=0,msg_sys=0;
var uid = 0;	//当前聊天用户的UID

//URL中指定的用户,同步WAP模板
var str = window.location.href;
if (str.indexOf('?uid=')>-1 || str.indexOf('&uid=')>-1) {
	var uid_array = str.indexOf('?uid=')>-1 ? str.split('?uid=') : str.split('&uid=');
    uid = parseInt(uid_array[1].split('&')[0]);
	$(function(){
		showMoreMsg(uid);
		set_user_name(uid);
	});
}
if (my_uid<1 && str.indexOf('my_uid=')>-1) {
	my_uid = str.split('my_uid=')[1].split('&')[0];
}
if (str.indexOf('msg_sys=')>-1) {
	msg_sys = str.split('msg_sys=')[1].split('&')[0];
}
if (str.indexOf('msg_id=')>-1) {
	msg_id = str.split('msg_id=')[1].split('&')[0];
}
getShowMsgUrl = getShowMsgUrl.replace('?','?msg_sys='+msg_sys+'&msg_id='+msg_id+'&');


var chat_type = 'chat';   //主窗口当前应该加载哪类内容,执行哪个函数

var tj_type = '';  //当前选择了哪种统计数据

var uid_array = [];   //每个用户的最新消息ID

var quninfo = [];   //圈子信息
var ListMsgUserPage = 1;	//所有信息用户列表
var show_msg_page = 1;	//会话记录分页
var msg_scroll = true;  //做个标志,不要反反复复的加载会话内容
var user_scroll = true;  //做个标志,不要反反复复的加载用户列表
var user_div_top = 0;	//当前信息用户列表滚动条坐标top系数
var show_msg_top = 0;  //当前对话框滚动条坐标top系数
var maxid = -1;
var need_scroll = false;
var user_num = 0;		//圈内成员数
var user_list = {};	//圈内成员列表
var have_load_live_player=false;
var check_new;
var w_s,ws_url;
var in_pc = true;
//var list_i=0,list_time=30;	//每隔30秒获取一次列表数据

$(function(){
	
	var pc_show_all_msg_obj = $(".pc_show_all_msg");
	var pc_msg_user_list_obj = $(".pc_msg_user_list");

	$(document).on("mousewheel DOMMouseScroll", function (e) {
			var delta = (e.originalEvent.wheelDelta && (e.originalEvent.wheelDelta > 0 ? 1 : -1)) ||  // chrome & ie
					(e.originalEvent.detail && (e.originalEvent.detail > 0 ? -1 : 1));              // firefox
			if (delta > 0) {
				
				//监听会话内容的滚动条
				var msg_top = pc_show_all_msg_obj.css('top');		
				msg_top = Math.abs(msg_top.replace('px',''));	
				//console.log("向上滚"+msg_top);
				//console.log("高_"+$(".pc_show_all_msg").height()) 
				if( msg_top<100 && msg_scroll==true){
					if(show_msg_top>0||show_msg_page>1){
						if(chat_type=='tongji'){
							get_tongji_msg(tj_type);
						}else{
							console.log("加载内容了");
							showMoreMsg(uid);
						}				
					}			
				}
			} else if (delta < 0) {
				 if(pc_msg_user_list_obj.length<1){
					 return ;
				 }
				//监听用户列表的滚动条
				var user_top = pc_msg_user_list_obj.css('top');
				user_top = Math.abs(user_top.replace('px',''));	
				//console.log("向下滚"+user_top);
				if(user_top-user_div_top>300 && user_scroll==true){
					//console.log(user_div_top);
					user_div_top = user_top;		
					showMore_User();
				}
			}
	});
	
	setInterval(function() {

		//监听会话内容的滚动条
		var msg_top = pc_show_all_msg_obj.css('top');		
		msg_top = Math.abs(msg_top.replace('px',''));	
		//console.log("高_"+$(".pc_show_all_msg").height()) 
		if( msg_top<100 && msg_scroll==true){
			if(show_msg_top>0||show_msg_page>1){
				if(chat_type=='tongji'){
					get_tongji_msg(tj_type);
				}else{
					showMoreMsg(uid);
				}				
			}			
		}
		
		//监听用户列表的滚动条
		var user_top = pc_msg_user_list_obj.css('top');
		user_top = Math.abs(user_top.replace('px',''));	
		if(user_top-user_div_top>300 && user_scroll==true){
			//console.log(user_div_top);
			user_div_top = user_top;		
			showMore_User();
		}

		//setInterval(function() {
		//	if(user_scroll==true)showMore_User();	//定时把他们全加载出来,方便做搜索使用.其实上面的滚动可删除了
		//}, 4000);

		//if(maxid>=0)check_new_showmsg();				

	}, 1000*10000);//永远不执行


	
	$(".friends_list li > p").click(function(){
		if($(this).find('i').is('.fa-chevron-up')){
			$(this).find('i').removeClass('fa-chevron-up');
			$(this).find('i').addClass('fa-chevron-down');
			$(this).parent().children('div').show();
		}else{
			$(this).find('i').removeClass('fa-chevron-down');
			$(this).find('i').addClass('fa-chevron-up');
			$(this).parent().children('div').hide()
		}
	});

	goto_bottom(500)

	//统计数据的类型选择
	var tongji_num = 0;//parseInt($("#tongji_num").html());
	$("#tongji li").each(function(i){
		var that = $(this);
		var type = that.data('type');
		that.click(function(){
			$("#send_user_name").html(that.find('span').html());
			$("#tongji li").removeClass('icon_active');
			that.addClass('icon_active');
			tj_type = type;
			show_msg_page = 1;
			tongji_num = tongji_num-parseInt(that.find('em').html());
			if(tongji_num<1){
				$("#tongji_num").hide();
			}else{
				$("#tongji_num").html(tongji_num);
			}
			that.find('em').hide();
			get_tongji_msg(type)
		});
		setTimeout(function(){
			//各种动态的新数据统计		
			$.get(tongjiCountUrl+'?type='+type,function(res){
				if(res.code==0 && res.data>0){
					that.find('em').html(res.data>999?'99+':res.data);
					that.find('em').addClass('ck');
					tongji_num = tongji_num+res.data;
					$("#tongji_num").html(tongji_num>999?'99+':tongji_num);
					$("#tongji_num").css('display','block');
				}else{
					that.find('em').hide();
				}
			});
		},2000*i+2000);
	})





	$("#input_box").focus(function(){
		if(my_uid==""){
			layer.alert("请先登录!!");
			return false;
		}
      // $('.windows_input').css('background','#fff');
       $('#input_box').css('border','1px solid #ececec');
	   $('#input_box').css('background','#F9F7F7');
	   
	});

    $("#input_box").blur(function(){
       $('.windows_input').css('background','');
       $('#input_box').css('background','');
    });

	$("#send").click(function(){
		postmsg();
	});

	$("#input_box").unbind('keydown').bind('keydown', function(e){
		//console.log(e.ctrlKey +'  '+e.keyCode);
		//if(e.ctrlKey && e.keyCode==13){
		if( e.keyCode==13 && e.shiftKey==false ){
			//layer.msg('正在发送消息');
			postmsg();
			return false;
		}
	});	
})


//发送消息
var allowsend = true;
function postmsg(cnt,callback){
	if(my_uid==""){
		layer.alert("请先登录!!");
		return false;
	}
	var content_obj = {};
	if(typeof(cnt)=='object'){
		content_obj = cnt;		
	}else{
		var content = (typeof(cnt)=='string' && cnt!='') ? cnt : $(".msgcontent").val();
		if(content==''){
			layer.alert('消息内容不能为空');
			return ;
		}
		content_obj.content = content;
	}
	content_obj.ext_id = msg_id;
	content_obj.ext_sys = msg_sys;
	
	if(allowsend == false){
		layer.alert('请不要重复发送信息');
		allowsend = true;
		return ;
	}
	$(".msgcontent").val('');
	allowsend = false;
	content_obj.push_id = (Math.random()+'').substring(2);
	ws_send({
		type:'qun_sync_msg',
		data:content_obj,
	});
	content_obj.uid = uid;
	content_obj.my_uid = my_uid;
	$.post(postMsgUrl,content_obj,function(res){
		allowsend = true;
		if(res.code==0){				
			//layer.msg('发送成功',{time:500});
			$("#hack_wrap").hide(100);
		}else{
			//$(".msgcontent").val(content);
			layer.msg('本条信息已发出，在线会员都能看，但后面来的人看不到，因为没有入库，<br>原因：'+res.msg,{time:5000});
		}
		if(typeof(callback)=='function'){
			callback(res);
		}
	});
}

var severUrl = "/index.php/index/attachment/upload/dir/images/from/base64/module/bbs.html";

var format_content = {};

//添加删除信息的功能按钮
function content_add_btn(res,type){
	$("#chatbox .del").off('click');
	$("#chatbox .del").click(function(){
		var id = $(this).data("id");
		var that = $(this);
		//通知其它人也要一起删除
		ws_send({
			type:'qun_to_alluser',
			tag:'delete_msg',
			data:{
				id:id,
			},
		});
		if(pushIdArray[id]!=undefined){
			id = pushIdArray[id];
		}
		$.get("/member.php/member/wxapp.msg/delete.html?id="+id,function(res){
			if(res.code==0){
				layer.msg("删除成功");
				that.parent().hide();
			}else{
				layer.alert(res.msg);
			}
		});
	});
	$("#chatbox .big").off('click');
	$("#chatbox .big").click(function(){
		window.open($(this).attr('src'));
	});

	
	if(show_msg_page>1 || is_repeat>1 || type=='cknew'){	//第一页不一定能执行得到,所以就放在加载脚本那里单独处理
		for(var index in format_content){
			if(typeof(format_content[index])=='function'){
				format_content[index](res,type);
			}
		}
	}
}

function pc_qun_hot(){	//异步加载执行的函数
	$("#hot_qunzi").append( $(".pc_qun_hot").html() );
	add_friend_click_fun();
}
function pc_qun_myjoin(){	//异步加载执行的函数
	$("#my_join_qunzi").append( $(".pc_qun_myjoin").html() );
	add_friend_click_fun();
}
function pc_qun_myvisit(){	//异步加载执行的函数
	$("#my_visit_qunzi").append( $(".pc_qun_myvisit").html() );
	add_friend_click_fun();
}

function pc_myfriend(){	//异步加载执行的函数
	$("#my_friend").find('.friends_box').remove();
	$("#my_friend").append( $("#friends_tag").html() );
	add_friend_click_fun();

	get_friend_data('my_idol');
	get_friend_data('my_fans');
	get_friend_data('my_blacklist');
}

//添加好友
function friend_act_add(uid){
	$.get(FriendActUrl+"?type=add&uid="+uid,function(res){
		if(res.code==0){
			layer.msg(res.msg);			
			get_friend_data('my_friend');
			get_friend_data('my_idol');
			get_friend_data('my_fans');
			get_friend_data('my_blacklist');
		}else{
			layer.alert(res.msg);
		}
	});
}

//删除好友
function friend_act_del(uid){
	$.get(FriendActUrl+"?type=del&uid="+uid,function(res){
		if(res.code==0){
			layer.msg(res.msg);
			get_friend_data('my_friend');
			get_friend_data('my_idol');
			get_friend_data('my_fans');
			get_friend_data('my_blacklist');
		}else{
			layer.alert(res.msg);
		}
	});
}

//加黑名单
function friend_act_bad(uid){
	$.get(FriendActUrl+"?type=bad&uid="+uid,function(res){
		if(res.code==0){
			layer.msg(res.msg);
			get_friend_data('my_friend');
			get_friend_data('my_idol');
			get_friend_data('my_fans');
			get_friend_data('my_blacklist');
		}else{
			layer.alert(res.msg);
		}
	});
}

//给好友列表添加点击事件
function add_friend_click_fun(){	
	$(".friends_list .friends_box").each(function(){
		var that = $(this);
		var btn = that.find(".friends_text");
		var btn_add = that.find(".add");
		var btn_del = that.find(".del");
		var btn_bad = that.find(".bad");
		
		//添加好友
		btn_add.off("click");
		btn_add.click(function(){
			friend_act_add( that.data("uid") );
		});
		
		//移除好友
		btn_del.off("click");
		btn_del.click(function(){
			friend_act_del( that.data("uid") );
		});
		
		//加黑名单
		btn_bad.off("click");
		btn_bad.click(function(){
			friend_act_bad( that.data("uid") );
		});


		btn.off("click");
		that.mouseout(function(){
			that.find("i").hide();
		});
		that.mouseover(function(){			
			that.find("i").show();
			$("#my_friend .friends_box i.add").hide();
			$("#my_blacklist .friends_box i.bad").hide();
			//$("#my_idol .friends_box i.bad").hide();
			//$("#my_idol .friends_box i.add").hide();
		});
		btn.click(function(){
			$(".friends_list .friends_box").removeClass('user_active');
			//$(".friends_list .friends_box i").hide();
			that.addClass('user_active');
			//that.find("i").show();
			uid = that.data("uid");
			set_user_name(uid);
			show_msg_page = 1;
			showMoreMsg(uid);
		})
	});
}

//获取我的好友或粉丝列表
function get_friend_data(ty){
	var page = 1;
	var url = MyFriendUrl + page + "&type=";
	if(ty=='my_idol'){	//我的偶像,我所关注的人
		url += "1&suid=&uid="+my_uid;
	}else if(ty=='my_fans'){	//我的粉丝
		url += "1&uid=&suid="+my_uid;
	}else if(ty=='my_blacklist'){	//黑名单
		url += "-1&suid=&uid="+my_uid;
	}else if(ty=='my_friend'){	//我的好友
		url += "2&uid=&suid="+my_uid;
	}
	$.get(url,function(res){
		if(res.code==0){
			if(page==1)$('#'+ty).find('.friends_box').remove();
			if(res.data!=''){				
				$('#'+ty).append(res.data);
				add_friend_click_fun();
			}
		}
	})
}


function format_nickname(){
	if(uid>0 || user_num<1){
		return ;
	}
	$('.pc_show_all_msg .name').each(function(){
		var _uid = $(this).data('uid');
		if(typeof(user_list[_uid]) == 'object' && typeof(user_list[_uid].nickname)!='undefined' && user_list[_uid].nickname!=''){
			$(this).html(user_list[_uid].nickname);
		}
	});
}

//获取圈子所有成员
function get_qunuser_list(uid){
	if(uid<0){
		var id = -uid;
		$.get("/index.php/qun/wxapp.member/get_member.html?id="+id+"&rows=1000&order=update_time&get_username=0",function(res){
			if(res.code==0){
				res.data.forEach((rs)=>{
					user_list[rs.uid] = rs;
				});
				user_num = res.data.length;
				format_nickname();				
			}
		});
	}		
}




