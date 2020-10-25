//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.wap_top_right_menu = {

	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
	},
	finish:function(res){  //所有模块加载完才执行
	},
	logic_init:function(res){ //init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
	},
}


load_data.wap_top_right_menu = function(res,type){

	if(type!='cknew'){
		menu(uid,quninfo,qun_userinfo,userinfo);
	}

	function menu(to_uid,_quninfo,_quser,_user){
		var str = '';
		if(to_uid>0){
			str = `<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/frame/show.html?url=/member.php/home/${to_uid}.html&title=个人主页">
                        <i class="icon-jiahao">&#xe660;</i>
                        <div class="span1">TA的主页</div>
                    </li>
					`;
		}else{
			to_id = -to_uid;
			var jifen_str = home_str = listmember_str = more_str = join_str = nickname_str = '';
			if(_quninfo.uid==_user.uid){
				jifen_str = `<li class="bui-btn bui-box">
                        <i class="icon-jiahao"><dd class="fa fa-calendar"></dd></i>
                        <div class="span1 a"  href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/p/signin-index-index/id/${to_id}.html&title=签到领积分">签到</div>
						<div class="span1 a" href="/public/static/libs/bui/pages/frame/show.html?url=/member.php/member/plugin/execute/plugin_name/signin/plugin_controller/manage/plugin_action/set/ext_id/${to_id}.html&title=签到设置">设置</div>
                    </li>`;
				
				home_str = `<li class="bui-btn bui-box">
                        <i class="icon-jiahao"><i class="si si-support"></i></i>
                        <div class="span1 a" href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/qun/show-${to_id}.html&title=圈子主页">主页</div>
						<div class="span1 a" href="/public/static/libs/bui/pages/frame/show.html?url=/member.php/qun/content/edit/id/${to_id}.html&title=签到设置">设置</div>
                    </li>`;

				listmember_str = `<li class="bui-btn bui-box">
                        <i class="icon-jiahao"><i class="si si-users"></i></i>
                        <div class="span1 a" href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/qun/member/index/id/${to_id}.html&title=成员列表">成员</div>
						<div class="span1 a" href="/public/static/libs/bui/pages/frame/show.html?url=/member.php/qun/member/index/id/${to_id}.html&title=成员管理">管理</div>
                    </li>`;

				more_str = `<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/frame/show.html?url=/member.php/qun/msgtask/add/id/${to_id}.html&title=群发消息">
							<i class="icon-jiahao"><i class="si si-speech"></i></i>
							<div class="span1">群发消息</div>
						</li>
						<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/qun/content/my/type/0.html&title=我加入的圈子">
							<i class="icon-jiahao"><i class="fa fa-connectdevelop"></i></i>
							<div class="span1">我加入的圈子</div>
						</li>
					`;
			}else{
				if(_quser==''){
					join_str = `<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/qun/content/apply/id/${to_id}.html&title=加入圈子">
                        <i class="icon-jiahao"><i class="fa fa-child"></i></i>
                        <div class="span1">加入圈子</div>
                    </li>`;
				}
				jifen_str = `<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/p/signin-index-index/id/${to_id}.html&title=签到领积分">
                        <i class="icon-jiahao"><dd class="fa fa-calendar"></dd></i>
                        <div class="span1">签到领积分</div>
                    </li>`;

				home_str = `<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/qun/show-${to_id}.html&title=圈子主页">
                        <i class="icon-jiahao"><i class="si si-support"></i></i>
                        <div class="span1">圈子主页</div>
                    </li>`;

				listmember_str = `<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/qun/member/index/id/${to_id}.html&title=成员列表">
                        <i class="icon-jiahao"><i class="si si-users"></i></i>
                        <div class="span1">圈子成员列表</div>
                    </li>`;
				listmember_str = `<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/qun/member/index/id/${to_id}.html&title=成员列表">
                        <i class="icon-jiahao"><i class="si si-users"></i></i>
                        <div class="span1">圈子成员列表</div>
                    </li>`;

				more_str = `<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/chat/chat?uid=${_quninfo.uid}">
                        <i class="icon-jiahao"><i class="si si-speech"></i></i>
                        <div class="span1">与群主私聊</div>
                    </li>
					<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/frame/show.html?url=/index.php/qun/content/my/type/1.html&title=我的圈子">
							<i class="icon-jiahao"><i class="fa fa-connectdevelop"></i></i>
							<div class="span1">我的圈子(创建)</div>
						</li>`;
			}

			if(_quser!=''){
				nickname_str = `<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/chat/nickname?id=${to_id}">
                        <i class="icon-jiahao"><i class="fa fa-pencil-square-o"></i></i>
                        <div class="span1">修改群内昵称</div>
                    </li>`;
			}

			str = `${home_str} 
					${join_str}
                    <li class="bui-btn bui-box" href="/public/static/libs/bui/pages/chat/codeimg.html?type=msg&id=${to_id}">
                        <i class="icon-jiahao">&#xe657;</i>
                        <div class="span1">直播群聊二维码</div>
                    </li>
					<li class="bui-btn bui-box" href="/public/static/libs/bui/pages/chat/codeimg.html?type=home&id=${to_id}">
                        <i class="icon-jiahao">&#xe657;</i>
                        <div class="span1">圈子主页二维码</div>
                    </li>
					${listmember_str}
					${jifen_str}
					${more_str}
					${nickname_str}
					`;
		}
		if(_user.uid>0){
			str += `<li class="bui-btn bui-box">
                        <i class="icon-jiahao"><i class="fa fa-user-circle-o"></i></i>
                        <div class="span2 a" href="/public/static/libs/bui/pages/frame/show.html?url=/member.php/home/${_user.uid}.html&title=我的主页">我的主页</div>
						<div class="span1 a" href="/public/static/libs/bui/pages/frame/show.html?url=/member.php/member/user/edit.html&title=资料修改">设置</div>
                    </li>`;
		}
		
		router.$("#TopRightBtn").html(str);
	}
}