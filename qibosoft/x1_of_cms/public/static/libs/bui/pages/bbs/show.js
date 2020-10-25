loader.define(function(require,exports,module) {


	var pageview = {};
	var scroll_s = true,showpage=1,id=0;
	var vues = new Vue({
				el: '.bbs_show',
				data: {
					info: {},
					quninfo: {},
					userinfo: {},
					listdb:[],
					time:0,
					user:{},
					admin:false,
				},
				watch:{
					listdb: function() {
						this.$nextTick(function(){	//数据渲染完毕才执行
							router.$(".reply_comnent").off('click');
							router.$(".reply_comnent").click(function(){
								var touser;
								if(typeof($(this).data("to"))!='undefined'){
									touser = "@" + $(this).data("to") + " ";
								}
								replyuser($(this).parent().data("cid"),touser)
							});
							$(".del-reply").off('click');
							$(".del-reply").click(function(){	//注意,这里使用$(this).data()有BUG,先删前面的,紧接着删除下一个会获取值失败
								delreply( $(this).parent().attr('data-cid') , $(this).parent().attr('data-sonid') );
							});
							set_lou();
							$(".upnum").off('click');
							$(".upnum").click(function(){	//注意,这里使用$(this).data()有BUG,先删前面的,紧接着删除下一个会获取值失败
								reply_agree( $(this).parent().attr('data-cid') , $(this) );
							});
							
					   })
					},
					info: function() {
						this.$nextTick(function(){	//数据渲染完毕才执行
							get_reply(id);	//获取评论数据
							this.time = window.store.get('time');
							this.admin = window.store.get('admin');
							this.user = Object.assign({}, this.user, window.store.get('userinfo'));
							gz_qunzi();
					   })
					},
					user:function() {
						this.$nextTick(function(){	//数据渲染完毕才执行
							set_menu();		//设置操作菜单
					   })
					},   
				},
				methods: {
					set_data:function(o){
						if(o.mvurl){
							o.content += `<br><video src="${o.mvurl}" controls="controls" style="width:100%;" class="video_player">您的浏览器不支持播放该视频！</video>`;
						}						
						this.info = Object.assign({}, this.info, o);
					},
					set_reply:function(array){
						var ck_array = [];
						this.listdb.forEach((rs)=>{
							ck_array[rs.id] = true;
						});
						array.forEach((rs)=>{
							if(typeof(ck_array[rs.id])=='undefined'){
								this.listdb.push(rs);
							}							
						});			
					},
					add_reply:function(ar,pid){						
						if(pid==0){	//非引用回复,在开头新增加评论
							this.listdb.unshift(ar);
						}else{
							var array = this.listdb;
							this.listdb = [];													
							array.forEach((rs,i)=>{
								if(pid>0 && rs.id==pid){	//引用回复
									rs.sons.push(ar);
								}
								this.listdb.push(rs);
							});
						}
					},
					del_reply:function(cid,sonid){						
						var array = this.listdb;
						this.listdb = [];													
						array.forEach((rs)=>{
							if(rs.id==cid && typeof(sonid)!='undefined'){	//引用回复
								var son = rs.sons;
								rs.sons = [];
								son.forEach((ps)=>{
									if(ps.id!=sonid){
										rs.sons.push(ps);
									}
								});
								this.listdb.push(rs);
							}else if(rs.id!=cid){
								this.listdb.push(rs);
							}							
						});
						console.log('删除内容'+sonid);
					},
				},
			});

	function set_menu(){
		router.$(".del-topic").click(function(){
			layer.confirm('确认要删除吗？', {
					btn : [ '确定', '取消' ]
				}, function(index) {
					delinfo(id);
				});
		});

		router.$(".editinfo").click(function(){
			location.href = "/index.php/bbs/content/edit.html?id="+id;
		});

		router.$(".bottom-info").click(function(){
			api_get('/index.php/bbs/wxapp.api/bottom/id/'+id+'.html','你要设置为沉底吗?',this);
		});

		router.$(".unbottom-info").click(function(){
			api_get('/index.php/bbs/wxapp.api/recover/id/'+id+'.html','你要取消沉底吗?',this);
		});
		
		router.$(".unbottom-info").click(function(){
			api_get('/index.php/bbs/wxapp.api/recover/id/'+id+'.html','你要取消沉底吗?',this);
		});

		router.$(".untop-info").click(function(){
			api_get('/index.php/bbs/wxapp.api/recover/id/'+id+'.html','你要取消置顶吗?',this);
		});

		router.$(".top-info").click(function(){
			set_topic_top('/index.php/bbs/wxapp.api/top/id/'+id+'.html',this);
		});

		router.$(".unstar-info").click(function(){
			api_get('/index.php/bbs/wxapp.api/unstar/id/'+id+'.html','你要取消推荐吗?',this);
		});

		router.$(".star-info").click(function(){
			api_get('/index.php/bbs/wxapp.api/star/id/'+id+'.html','你要设置为推荐吗?',this);
		});
		
		router.$(".unlock-info").click(function(){
			api_get('/index.php/bbs/wxapp.api/unlock/id/'+id+'.html','你要取消锁定吗?',this);
		});

		router.$(".lock-info").click(function(){
			api_get('/index.php/bbs/wxapp.api/lock/id/'+id+'.html','你要锁定不给回复吗?',this);
		});

		router.$(".unfonttype-info").click(function(){
			api_get('/index.php/bbs/wxapp.api/unfonttype/id/'+id+'.html','要取消标题加粗吗?',this);
		});

		router.$(".fonttype-info").click(function(){
			api_get('/index.php/bbs/wxapp.api/fonttype/id/'+id+'.html','你要给标题加粗吗?',this);
		});

		router.$(".unfontcolor-info").click(function(){
			api_get('/index.php/bbs/wxapp.api/unfontcolor/id/'+id+'.html','你要取消颜色标题吗?',this);
		});

		router.$(".fontcolor-info").click(function(){
			api_get('/index.php/bbs/wxapp.api/fontcolor/id/'+id+'.html','你要给标题变红色吗?',this);
		});

		router.$(".topic-agree").click(function(){
			$.get("/index.php/bbs/wxapp.post/agree/id/"+id+".html?"+Math.random(),function(res){
				if(res.code==0){
					var num =  $('.topic_agree').html();
					num++;
					$('.topic_agree').html(num);
					layer.msg("点赞成功！",{time:500});
				}else{
					layer.msg("点赞失败:"+res.msg,{time:2500});
				}	
			});
		});		

		router.$(".add-fav").click(function(){
			$.get("/index.php/p/fav-api-add.html?type=bbs&id="+id,function(res){
				if(res.code==0){
					layer.msg('收藏成功');
				}else{
					layer.alert(res.msg);
				}
			})
		});
	}

	function post_act_menu(url,obj){
		layer.load(1);
		$.get(url,function(res){
			layer.closeAll();
				if(res.code==0){
					layer.msg('操作成功');
					if($(obj).hasClass('menu_highlight')){
						$(obj).removeClass('menu_highlight');
					}else{
						$(obj).addClass('menu_highlight');
					}
				}else if(res.code==1){
					layer.alert(res.msg);
				}else{
					layer.alert('未知错误');
				}
		}).fail(function(){
				layer.alert('页面出错');
		});
	}

	function api_get(url,msg,obj,type){
		if(type=='color'){
			layer.confirm(msg,{title:false,btn:['加红色','加蓝色','取消'],btn2:function(){
				post_act_menu(url+"?type=blue",obj);
			}},function(index){
				post_act_menu(url,obj);
			});
		}else{
			layer.confirm(msg,{title:false,},function(index){
				post_act_menu(url,obj);
			});
		}
	}

	function set_topic_top(url,obj){
		layer.prompt({
			  formType: 0,
			  value: '3',
			  title: '请输入要置顶多少天?',
			  //area: ['100px', '20px'] //formType:2 自定义文本域宽高
			}, function(value, index, elem){
				layer.close(index);
				var time = value * 24;	//单位小时
				post_act_menu(url+'?time='+time,obj);
		});
	}
	
	//获取主题内容 
	function get_info(id){		
		$.get("/index.php/bbs/wxapp.show/index.html?id="+id,function(res){
			if(res.code==0){
				vues.set_data(res.data);
				layer.closeAll();
			}else{
				layer.msg("没有了!");
			}
		});
	}
	
	//获取评论
	function get_reply(id){		
		$.get("/index.php/bbs/wxapp.reply/index.html?rows=2&id="+id+"&page="+showpage,function(res){
			if(res.code==0 && res.data.length>0){
				vues.set_reply(res.data);
				layer.closeAll();
				showpage++;
				scroll_s = true;				
			}else if(showpage>1){
				layer.msg("没有了!");
			}
		});
	}

	pageview.init = function () {
		router.$(".bbsContainer").parent().scroll(function () {			
			var h = router.$(".bbsContainer").height() - $(this).height() - $(this).scrollTop();			
			if(h<200 && scroll_s==true){//
				console.log(h);
				if(showpage>1)layer.msg('数据加载中,请稍候...',{time:2000});
				scroll_s = false;
				get_reply(id);
			}
		});
		
		router.$(".post_topic_comment").click(function(){
			postcomment();
		});
    }

    pageview.bind = function (argument) {	
		$('.OpenAction ul .close').click(function(){
			$('.OpenAction').animate({'height':'0px'},300,function(){
				$(this).css({'display':'none'});
			});
		});
    }

	var getParams = bui.getPageParams();
		getParams.done(function(result){
			if( typeof(result.id)=="undefined" ){
				layer.alert('ID不存在');
			}else{
				id = result.id;
				get_info(id);				
			}
		})



    // 控件初始化
    pageview.init();

    // 输出模块
    module.exports = pageview;

	//打赏积分
	var sys_dirname = "bbs";
	var give_money_url = "/index.php/p/givemoney-api-give.html";
	var count_money_url = "/index.php/p/givemoney-api-count.html";
	var getlist_money_url = "/index.php/p/givemoney-api-getlist.html";

	function give_jifen(){
		$(".give-money").each(function(){
			var that = $(this);
			var id = that.data('id');
			var rid = typeof(that.data('rid'))=='undefined' ? 0 : that.data('rid');
			var cid = typeof(that.data('cid'))=='undefined' ? 0 : that.data('cid');
			
			that.each(function(){
				var obj = $(this);
				$.post(count_money_url,{'sysname':sys_dirname,'id':id,'rid':rid,'cid':cid},function(res){
					if(res.code==0){	//有人打赏过了
						obj.html(' '+res.data);
						obj.click(function(){
							layer.confirm('请问你是要打赏？还是要查看打赏的用户列表',{btn:['我要打赏','查看用户']},function(){
								layer.closeAll();
								putnum();
							},function(){
								getlist();
							});						
						});
					}else{	//还没人打赏
						obj.click(function(){						
							putnum();
						});					
					}
				});
			});
			
			//显示打赏用户
			var getlist = function(){
				layer.closeAll();
				layer.load(1);
				$.post(getlist_money_url,{'sysname':sys_dirname,'id':id,'rid':rid,'cid':cid},function(res){
					layer.closeAll();
					if(res.code==0){
						var str = '';
						res.data.forEach(function(rs){
							str += '<div style="padding:5px;"><span style="color:blue;">' + rs.username + '</span> 打赏积分: ' + rs.money + ' 个 <span style="color:#666;">['+rs.create_time+']</span></div>';
						});
						layer.open({
						  title:'打赏用户列表',
						  type: 1,
						  area: '98%',
						  content: '<div style="padding:15px;">' + str + '</div>',
						});
					}else{
						layer.alert(res.msg);
					}
				}).fail(function(){layer.closeAll();layer.alert('页面出错了!')});
			}
			
			//打赏输入积分个数
			var putnum = function(){
				layer.prompt({
					  formType: 0,
					  value: '3',
					  title: '请输入要打赏的积分个数',
					  //area: ['100px', '20px'] //formType:2 自定义文本域宽高
					}, function(value, index, elem){
						layer.close(index);
						postdata(value);
					}
				);
			};
			
			//打赏提交数据
			var postdata = function(num){			
				layer.load(1);
				$.post(give_money_url,{'sysname':sys_dirname,'money':num,'id':id,'rid':rid,'cid':cid,'about':''},function(res){
					layer.closeAll();
					if(res.code==0){
						layer.msg('谢谢你的打赏!');
					}else{
						layer.alert(res.msg);
					}
				}).fail(function(){layer.closeAll();layer.alert('页面出错了!')});
			}		
		});
	}

	function set_lou(){
		var lou = 0;
		router.$(".CommentBox .lou").each(function(){
			lou++;
			$(this).html(lou+'楼')
		});
	}
	

	//删除主题
	function delinfo(aid){
		var url="/index.php/bbs/wxapp.post/delete.html?id="+aid;
		$.get(url,function(res){
			if(res.code==0){
				layer.msg("删除成功！",{time:500});
				location.href="/public/static/h5/"
			}else{
				layer.msg("删除失败:"+res.msg,{time:2500});
			}	
		});
	}
	
	//删除回复
	function delreply(cid,sonid){
		var id;
		if(typeof(sonid)=='undefined'){
			id = cid;
		}else{
			id = sonid;
		}
		var url = "/index.php/bbs/wxapp.reply/delete.html?id="+id;
		$.get(url,function(res){
			if(res.code==0){
				layer.msg("删除成功！",{time:500});
				vues.del_reply(cid,sonid);
				router.$("#total_comment").html( parseInt(router.$("#total_comment").html())-1);
			}else{
				layer.alert("删除失败:"+res.msg);
			}	
		});
	}

	//回复点赞
	function reply_agree(cid,obj){
		$.get("/index.php/bbs/wxapp.reply/agree.html?id=" + cid + "&" + Math.random(),function(res){
			if(res.code==0){
				var num =  obj.html();
				num++;
				obj.html(num);
				layer.msg("点赞成功！",{time:500});
			}else{
				layer.msg("点赞失败:"+res.msg,{time:2500});
			}	
		});
	}

	//打赏RMB
	function gave_rmb(aid,rid){
		layer.open({
		  type: 1,
		  title:'我要打赏',
		  area: ['300'], //宽高，高参数忽略
		  content: '<ul class="replayBox"><ol><input class="gavemoney" type="number" step="0.01"  min="0.3" placeholder="请输入打赏金额" />单位:元</ol><li><button onclick="post_rmb('+aid+','+rid+');">确定</button><span onclick="layer.closeAll();">取消</span></li></ul>'
		});
	}
	function post_rmb(aid,rid){
		var money = $('.replayBox .gavemoney').val();
		money = parseFloat(money).toFixed(2);
		if(isNaN(money)){
			layer.msg('请输入正确的金额',{time:800});
			return ;
		}else if(money<0.3){
			layer.msg('打赏金额不能小于0.3元',{time:800});
			return ;
		}

		$.get("/index.php/bbs/wxapp.post/reward.html?id=" + aid + '&rid=' + rid + '&money=' + money + '&' + Math.random(),function(res){
			if(res.code==0){
				layer.closeAll(); //关闭所有层
				layer.msg(res.msg);
			}else if(res.code==2){
				layer.msg('你的余额只有'+res.data.money+',请先充值',{time:1000});
				setTimeout(function(){
					callpay();
				},1000);
			}else{
				layer.alert(res.msg);
			}
		});
		
	}

	jQuery(document).ready(function() {
		router.$(".contentHtml img").each(function(){
			$(this).click(function(){
				location.href=($(this).attr('src'));
			});
		});
		router.$(".CommentBox .replycontent img").each(function(){
			$(this).click(function(){
				location.href=($(this).attr('src'));
			});
			$(this).css({"max-width":'100%',});
		});
	})

	//微信充值
    var wxpay = {};
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			wxpay,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				//alert(res.err_code+res.err_desc+res.err_msg);
				if(res.err_msg=='get_brand_wcpay_request:ok'){
					layer.msg('充值成功,请现在可以打赏了!');
				}
				//if(res.err_msg=='get_brand_wcpay_request:cancel')window.location.href="https://x1.php168.com/index/pay/index/banktype/weixin/action/pay_end_return.html?ispay=0&numcode=6e22a6621d";
			}
		);
	}

	function callpay()
	{
		var money = $('.replayBox .gavemoney').val();
			money = parseFloat(money).toFixed(2);
		if(isNaN(money)){
			money = 0.3;
		}
		$.get("/index.php/index/wxapp.pay/index.html" + '?type=mp&title=打赏充值&money=' + money + '&' + Math.random(),function(res){
			if(res.code==0){
				wxpay = eval("("+res.data.json+")");
				if (typeof WeixinJSBridge == "undefined"){
					if( document.addEventListener ){
						document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
					}else if (document.attachEvent){
						document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
						document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
					}
				}else{
					jsApiCall();
				}


			}else{
				layer.alert(res.msg);
			}
		});
	}

	//关注圈子
	function gz_qunzi(){//console.log(vues.$data.user.uid);
		$(".gzqun").each(function(){
			var that = this;
			if( typeof(vues.$data.user.uid)=='undefined' ){	//游客点关注,就直接进入对应的商圈
				$(this).click(function(){
					window.location.href = "/index.php/qun/show.html?id="+id;
				});
			}else{
				//检查是否已关注
				$.get("/index.php/qun/wxapp.member/ckjoin.html?id="+id,function(res){
					if(res.code==1){	//还没关注
						$(that).click(function(){	//添加关注点击事件
							$.get("/index.php/qun/wxapp.member/join.html?id="+id,function(res){
								if(res.code==1){	//关注失败
									layer.alert(res.msg);
								}else if(res.code==0){	//关注成功
									layer.msg('已关注,'+res.msg);
									$(that).html('已关注');
									$(that).click(function(){
										window.location.href = "/index.php/qun/show.html?id="+id;
									});
								}
							});						
						});
					}else if(res.code==0){	//已关注
						$(that).html('已关注');
						$(that).click(function(){
							window.location.href = "/index.php/qun/show.html?id="+id;
						});
					}
				});
				
			}		
		});
	}

	
	//检查登录与否
	function check_login(){
		if("1"==""){
			layer.confirm('你还没登录,你确认要登录吗？', {
				btn : [ '确定', '取消' ]
			}, function(index) {
				location.href="/index.php/index/login/index.html?fromurl=http%3A%2F%2Fqb.net%2Findex.php%2Fbbs%2Fshow.html%3Fid%3D7";
			});		
		}else{
			return true;
		}
	}

	//对主题发表评论
	function postcomment(){
		if(check_login()!=true) return ;
		layer.open({
		  type: 1,
		  title:'评论主题',
		  area: ['90%'], //宽高，高参数忽略
		  content: router.$(".comment-box").html()
		});
		$(".replayBox .comment-btn").last().click(function(){
			ajax_post();
		});
	}

	//引用回复
	function replyuser(pid,touser){
		if(check_login()!=true) return ;
		layer.open({
		  type: 1,
		  title:'给TA回复',
		  area: ['90%'], //宽高，高参数忽略
		  content: router.$(".comment-box").html(),
		});
		if(typeof(touser)!='undefined'){
			$(".replayBox textarea").last().val(touser);	//特别要注意,这里不能加 router.			
		}
		$(".replayBox textarea").last().focus();
		$(".replayBox .comment-btn").last().click(function(){
			ajax_post(pid);
		});
	}

	//提交回复信息
	var havepost = false;
	function ajax_post(pid){	//提交回复表单
		var url = "/index.php/bbs/wxapp.reply/add.html?id="+id+"&rows=1&";
		if(typeof(pid)!="undefined"){
			url += "pid="+pid;
		}
		var contents = $('.replayBox textarea').last().val();			
		if(contents==''){
			layer.msg("请输入评论内容！",{time:1500});		
		}else{
			if(contents.replace(/\[(face\d+)\]/g,"")==""){
				layer.alert('不允许只发表情!');
				return false;			
			}
			if(havepost==true){
				layer.msg('请不要重复提交');
				return false;
			}
			layer.msg('内容提交中,请稍候');
			havepost = true;		
			contents = contents.replace(new RegExp('<',"g"),'&lt;');
			contents = contents.replace(new RegExp('>',"g"),'&gt;');
			contents = contents.replace(new RegExp('\n',"g"),'<br>');
			contents = contents.replace(new RegExp(' ',"g"),'&nbsp;');
			$.post(
				url,
				{'content':contents,'picurl':$(".atc_picurl").last().val()},
				function(res,status){
					havepost = false;
					if(res.code==0){
						if(typeof(pid)!="undefined"){
							var son = res.data[0].sons;
							vues.add_reply(son[(son.length-1)],pid);
						}else{
							vues.add_reply(res.data[0],0);
						}
						router.$("#total_comment").html( parseInt(router.$("#total_comment").html())+1);
						//give_jifen();	//重置打赏积分事件
						layer.closeAll(); //关闭所有层
						layer.msg("发表成功！",{time:1500});
						//HiddenShowMoreComment();
						//隐藏的内容需要刷新才可见
						if(($(".contentHtml").html()).indexOf('需要刷新网页才可见')>0){
							//window.location.reload();
						}
					}else{
						layer.msg("评论发表失败:"+res.msg,{time:1500});
					}
				}
			);				
		}
	}

})
