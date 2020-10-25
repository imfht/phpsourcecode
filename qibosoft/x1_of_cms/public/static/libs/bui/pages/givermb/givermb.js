loader.define(function() {
	var pageview = {};
	var uid='';
	var userdb = window.store.get('userinfo');

	pageview.init = function () {
		
		loader.require( (typeof(web_url)=='undefined'?'/':'')+'public/static/js/pay.js',function (o) {});

		if(userdb.rmb==undefined){	//充值刷新过网页的情况
			setTimeout(function(){				
				userdb = window.store.get('userinfo');
				router.$("#user_rmb").html(userdb.rmb);
			},1000);
		}
		bui.input({
            id: ".user-input",
            callback: function (e) {
                // 清空数据
                this.empty();
            }
        });
		
		router.$("#user_rmb").html(userdb.rmb);
		
		//确认打赏
		router.$("#giveBtn").click(function(){
			var rmb = router.$("#give_rmb").val();
			if(rmb>userdb.rmb){
				var layer_index = layer.alert("你的可用余额只有"+userdb.rmb+"元,请先充值");
			}
			$.get("/member.php/member/wxapp.rmb/give.html?uid="+uid+"&money="+rmb,function(res){
				if(res.code==0){
					$.post("/member.php/member/wxapp.msg/add.html",{
						content:'<div class="give_qun_money"><img src="/public/static/libs/bui/givemoney.jpg" /><span>打赏圈主 <b>'+rmb+'</b> 元</span></div>',
						uid:uid,
						},function(res){
							//if(typeof(refresh_timenum)!='undefined')refresh_timenum = 1;	//加快刷新时间
							if(res.code==0){
								layer.msg(res.msg);
								if( typeof(refresh_timenum)=='undefined' ){ //可能是充值过来时刷新过网页
									bui.load({ 
										url: "/public/static/libs/bui/pages/chat/chat.html",
										param:{
											uid:uid,
										}
									});
								}else{
									bui.back();
								}
							}else{
								layer.alert(res.msg);
							}
					});
				}else if(res.code==2){
					var money = parseFloat( parseFloat(res.data.pay_rmb) - parseFloat(res.data.have_rmb) ).toFixed(2);
					Pay.mobpay( money ,'打赏充值',function(type,index){
						layer.close(index);
						layer.close(layer_index);
						if(type=='ok'){
							userdb.rmb = parseFloat( parseFloat(userdb.rmb) + parseFloat(money) ).toFixed(2);													
							router.$("#user_rmb").html(userdb.rmb);
							layer.msg('充值成功,你可以继续打赏充了');
						}else{
							layer.alert('充值失败');
						}
					});
				}else{
					layer.alert(res.msg);
				}
			});
		});
		
		//充值
		router.$("#addrmb").click(function(){
			bui.load({ 
				url: "/public/static/libs/bui/pages/frame/show.html",
				param:{
					url:"/member.php/member/plugin/execute/plugin_name/marketing/plugin_controller/rmb/plugin_action/add.html?callback_url="+encodeURIComponent(window.location.href),
				}
			});
		});
	}

	var getParams = bui.getPageParams();
	getParams.done(function(result){
		uid = result.uid;
	});

	pageview.init();
})