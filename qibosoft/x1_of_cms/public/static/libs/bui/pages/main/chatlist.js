loader.define(function(require,exports,module) {


	var pageview = {},      // 页面的模块, 包含( init,bind )
		uiPullrefresh,      // 消息,电话公用的下拉刷新控件
		mainHeight,
		uiMask,             // 公共遮罩
		uiListviewMessage,  // 消息侧滑菜单
		uiDropdownMore,     // 下拉菜单更多
		user_scroll=true,	//滚动条
		ListMsgUserPage=1,	//用户列表分页
		uiSlideTabMessage;  // 顶部tab
	


    /**
     * [init 页面初始化]
     * @return {[type]} [description]
     */
    pageview.init = function () {

		try {	//地址栏目中有UID就会自动跳转
			if ((navigator.userAgent.match(/(iPhone|iPod|Android|ios|iPad)/i))){
				var str = window.location.href;
				if (str.indexOf('?uid=')>-1 || str.indexOf('&uid=')>-1) {	//replace(/[^\d|^\-]/g,"");
					//str_uid = parseInt(str.split('uid=')[1].split('&')[0]);	//parseInt(str.substring(str.indexOf('uid=')+4));
					var uid_array = str.indexOf('?uid=')>-1 ? str.split('?uid=') : str.split('&uid=');
					var str_uid = parseInt(uid_array[1].split('&')[0]);

					var msg_sys = str.indexOf('msg_sys=')>0?str.split('msg_sys=')[1].split('&')[0]:'';
					var msg_id = str.indexOf('msg_id=')>0?str.split('msg_id=')[1].split('&')[0]:'';
					var my_uid = str.indexOf('my_uid=')>0?str.split('my_uid=')[1].split('&')[0]:'';

					if (/^[-]?[0-9]+$/.test(str_uid)) {
						bui.load({url: "/public/static/libs/bui/pages/chat/chat.html",param: {
							"uid":str_uid,
							"msg_sys":msg_sys,
							"msg_id":msg_id,
							"my_uid":my_uid,
						}});
					}
				}
			}
		}catch(err){}
		
		pageview.msg_listuser();	//加载信息用户列表

        
		var that = $("#listview");
		that.parent().scroll(function () {	//滚动加载更多好友列表示
			var h = that.height()-that.parent().height()-that.parent().scrollTop();			
			if(h<300 && user_scroll==true){
				console.log(h);
				layer.msg('数据加载中,请稍候...',{time:3000});
				pageview.msg_listuser();	//显示更多用户列表
			}
		});

		this.bind();

    }

    pageview.bind = function (argument) {

		mainHeight = $(window).height() - $("#tab-home-header").height()- $("#tabDynamicNav").height();
        var slideHeight = parseInt(mainHeight) - $(".bui-searchbar").height();

		//setInterval(function() {
		//	if(user_scroll==true)showMore_User();	//定时把他们全加载出来,方便做搜索使用.其实上面的滚动可删除了
		//}, 5000);


		//setTimeout(function(){order_list();},3000);	//把未读消息强制排在前面
		
		//初次这里有可能会加载晚一步
		//if(MsgUserList!=''){
		//	$("#listview").html(MsgUserList);
		//	$("#listview .span1").click(function(){$(this).find('.bui-badges').removeClass('badges-ck')});
		//}
		
		//var btn_chat = $("#tabDynamicNav .bui-box-vertical").eq(0);
		//setInterval(function() {
		//	var url = window.location.href;
		//	if(url.indexOf('#/')==-1 && btn_chat.hasClass('active')==true ){	//跳转到了其它页面,就不要再执行
		//		check_list_new_msgnum(); //刷新有没有新用户发消息 过来
		//	}			
		//}, 5);
		
		
        //顶部好友与消息的切换菜单
		var have_load_friend = false;
        uiSlideTabMessage = bui.tab({
            id       : "#tabMessage",
            menu     : "#tabMessageNav",
            height   : slideHeight,
			scroll   : true,
            swipe    : true,   //不允许通过滑动触发
            animate  : true,    //点击跳转时不要动画
			onBeforeTo: function(e) {//   目标索引  e.currentIndex e.prevIndex
				if(have_load_friend==false){
					console.log("加载好友成功");
					have_load_friend = true;
					get_friend_data('my_friend');
					get_friend_data('my_idol');
					get_friend_data('my_fans');
					get_friend_data('my_blacklist');
				}
           }
        });


        //侧滑菜单
		/*
        uiListviewMessage = bui.listview({
                id: "#listview",
                data: [{ "text": "置顶", "classname":"primary"},{ "text": "删除", "classname":"danger"}],
                callback: function (e) {
                    // this 为滑动出来的操作按钮
                    var $this = $(e.target);

                    var text = $this.text();
                        if( text == '删除' ){
                            bui.confirm("确定要删除吗",function (e) {
                                //this 是指点击的按钮
                                var text2 = $(e.target).text();
                                if( text2 == "确定"){
                                    // 执行删除整行操作
                                    $this.parents(".list-item").fadeOut(300,function (e) {
                                        $(this).remove();
                                    });
                                }
                            })
                        }
                    // 不管做什么操作,先关闭按钮,不然会导致第一次点击无效.
                    this.close();
                }
            });
		*/
        

        // 初始化下拉刷新
        //uiPullrefresh = bui.pullrefresh({
         //   id        : "#messageScroll",
         //   height: mainHeight,
         //   onRefresh : getData
        //});

/*------------消息 电话 end --------------*/

/*------------右上角更多菜单 start --------------*/

        // 初始化下拉更多操作
        uiDropdownMore = bui.dropdown({
          id: "#more",
          showArrow: true,
          width: 160
        });
        // 为下拉菜单添加一个遮罩
        uiMask = bui.mask({
          appendTo:"#main",
          opacity: 0.5,
          zIndex:9,
          callback: function (argument) {
            // 隐藏下拉菜单
            uiDropdownMore.hide();
          }
        });
        // 通过监听事件绑定
        uiDropdownMore.on("show",function () {
          uiMask.show();
        })
        uiDropdownMore.on("hide",function () {
          uiMask.hide();
        });

/*------------右上角更多菜单 end --------------*/
    }


	//获取我的好友或粉丝列表
	function get_friend_data(ty){
		var url = "/index.php/index/wxapp.friend/get_list.html?page=1&row=100&type=";
		if(ty=='my_idol'){	//我的偶像,我所关注的人
			url += "1&suid=&uid="+my_uid;
		}else if(ty=='my_fans'){	//我的粉丝
			url += "1&uid=&suid="+my_uid;
		}else if(ty=='my_blacklist'){	//黑名单
			url += "-1&suid=&uid="+my_uid;
		}else if(ty=='my_friend'){	//我的好友
			url += "2&suid=&uid="+my_uid;
		}
		$.get(url,function(res){
			if(res.code==0){
				if(res.data.length>0){				
					$('#'+ty).append( pageview.format_friend_data(res.data) );
					if(res.paginate.total>0)$('#'+ty).prev().find("em").html(res.paginate.total); //有几位好友
					//添加侧滑菜单
					bui.listview({
							id   : '#'+ty,
							data : [{text : "删除",classname : "danger"},{text : "拉黑",classname : "warning"},{text : "+好友",classname : "primary"}],
							callback : function(e,ui) {
								$(e.target).parents(".list-item").fadeOut(300,function () {
									$(this).remove();
								});
								var urls = "/member.php/member/wxapp.friend/act.html?uid=" + $(e.target).parents(".list-item").data('uid') + "&type=";
								var text = $(e.target).text().trim();
								if( text == '删除' ){
									urls+='del';
								}else if( text == '拉黑' ){
									urls+='bad';
								}else if( text == '+好友' ){
									urls+='add';
								}
								$.get(urls,function(res){
									if(res.code==0){
										layer.msg(res.msg);
									}else{
										layer.alert(res.msg);
									}
								});
								// 关闭侧滑
								ui.close();
							}
						});
				}
			}
		})
	};

	pageview.format_friend_data = function(array){
		var str = "";
		array.forEach((rs)=>{
			str +=`
			<li class="list-item" data-uid="${rs.he_id}">
				<div class="bui-btn bui-box">
					<a href="/member.php/home/${rs.he_id}.html"  class="iframe"><img class="ring ring-group" src="${rs.he_icon}" onerror="this.src='/public/static/images/noface.png'"/></a>                                
					<div class="span1 a" href="/public/static/libs/bui/pages/chat/chat.html?uid=${rs.he_id}">
						<h3 class="item-title">
							${rs.he_username}
						</h3>
						<p class="item-text">[近况] ${rs.he_lastvist}登录过</p>
					</div>
					<i class="icon- primary"><i class="si si-logout"></i></i>
				</div>
			</li>
			`;
		});
		return str;
	}


	
	//var uid_array = [];   //每个用户的最新消息ID
	//刷新有没有新用户发消息 过来
	function check_list_new_msgnum(){
		if(typeof(uid_array)=='undefined'){
			return ;
		}
		$.get(ListMsgUserUrl+"1",function(res){
			if(res.code==0){			
				$.each(res.ext.s_data,function(i,rs){
					//出现新的消息新用户，或者是原来新消息的用户又发来了新消息
					if(typeof(uid_array[rs.f_uid])=='undefined'||rs.id>uid_array[rs.f_uid]){
						$('#listview').html(res.data);
						//上面重新清空了,这里要重新加载
						user_scroll=true;
						ListMsgUserPage=1;
					}
					//新消息已读
					if(rs.new_num<1){
						$('#listview  .list_'+rs.f_uid+' .bui-badges').removeClass('badges-ck');
						$('#listview  .list_'+rs.f_uid+' .bui-badges').html(rs.num>999?'99+':rs.num);
					}
					//console.log(rs.f_uid+'='+rs.id+'='+uid_array[rs.f_uid]);
					uid_array[rs.f_uid] = rs.id;
				});
			}
		});
	}


	pageview.msg_listuser = function(){		
		user_scroll = false;
		$.get("/member.php/member/wxapp.msg/get_listuser.html?rows=20&page="+ListMsgUserPage,function(res){
			if(res.code==0){
				if(res.data.length<1){
					if(ListMsgUserPage==1){
						layer.msg('没有记录');
					}else{
						layer.msg('加载完了');
					}
				}else{					
					$('#listview').append( pageview.format_listuser(res.data) );	//加载回来的好友数据显示
					$.each(res.data,function(i,rs){
						uid_array[rs.f_uid] = rs.id;
					});
					user_scroll = true;
					if(ListMsgUserPage>1)$("#listview").parent().scrollTop( $("#listview").parent().scrollTop()-200 );
					$("#listview .span1").click(function(){
						var th = $(this).find('.bui-badges');
						if(th.hasClass("badges-ck")){
							th.removeClass('badges-ck');
							if( th.parent().parent().parent().parent().hasClass("list-user") ){	//圈子的没统计,就不要处理
								var num = $("#chat_num").html()-th.html();
								if(num<1){
									$("#chat_num").hide();
								}else{
									$("#chat_num").html(num);
								}								
							}							
						}
					});
					//order_list();	//新消息要排在前面
					ListMsgUserPage++;
				}				
			}else{
				layer.msg(res.msg,{time:2500});
			}
		});
	}

	pageview.format_listuser = function(array){
		var str = "";
		var obj = {};
		array.forEach((rs)=>{
			obj = {};
			obj.c = rs.f_uid<0 ? 'list-qun' : 'list-user';
			obj.url = rs.f_uid>0 ? '/member.php/home/'+rs.f_uid+'.html' : '/index.php/qun/show-'+(-rs.f_uid)+'.html';
			obj.content = (typeof(rs.qun)=='object'&&typeof(rs.qun.content)!='undefined' ? rs.qun.username +'说:'+ rs.qun.content : rs.title).substr(0,25);
			
			obj.num_icon = rs.new_num>0 ? 'badges-ck' : ''
			obj.show_num = rs.num>999 ? '99+' : rs.num;
			if(rs.new_num>0){
				obj.show_num = rs.new_num;
			}
			if( typeof(rs.f_icon)=='undefined'||rs.f_icon==null)rs.f_icon='';
			str += `
				<li class="list-item list_${rs.f_uid} ${obj.c}">
					<div class="bui-btn bui-box">
						<a href="${obj.url}" class="iframe"><img class="ring ring-group" src="${rs.f_icon}" onerror="this.src='/public/static/images/noface.png'"></a>
						<div class="span1 a" href="/public/static/libs/bui/pages/chat/chat.html?uid=${rs.f_uid}">
							<h3 class="item-title">
								${rs.f_name}
								<span class="item-time bui-right">${rs.create_time}</span>
							</h3>
							<p class="item-text">
								${obj.content}
								<span class="bui-badges bui-right ${obj.num_icon}">
									${obj.show_num}
								</span>
							</p>
						</div>
					</div>
               </li> 
			`;
		});
		return str;
	};

	//把未读消息强制排在前面
	function order_list(){
		var that = $("#listview");
		var obj = $("#listview .badges-ck");
		for(var i=(obj.length-1);i>=0;i--){
			var o = obj.eq(i).parent().parent().parent().parent();
			that.prepend( o.get(0).outerHTML);
			o.remove();
		}
	}

    // 下拉刷新以后执行数据请求
    function getData () {

        bui.ajax({
            url : "/public/static/libs/bui/userlist.json",
            data: {
                pageindex:1,
                pagesize:4
            }
        }).done(function(res) {

            //还原刷新前状态
            uiPullrefresh.reverse();

        }).fail(function (res) {
            //请求失败变成点击刷新
            uiPullrefresh.fail();
        })
    }

    // 控件初始化
    pageview.init();

    // 输出模块
    module.exports = pageview;
})
