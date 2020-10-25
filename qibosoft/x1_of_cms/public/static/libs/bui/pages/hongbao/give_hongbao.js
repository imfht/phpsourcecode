function end_choose_map(map){
	$(".hongbao_warp input[name='map']").val(map);
}
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
		
		//确认提交
		router.$("#giveBtn").click(function(){			
			if( router.$(".hongbao_warp input[name='money']").val()<0.01 ){
				layer.alert("单个最小红包不能小于0.01元");
				return ;
			}else if( router.$(".hongbao_warp input[name='num']").val()<1 ){
				layer.alert("红包个数必须要1个起");
				return ;
			}else if( router.$(".hongbao_warp input[name='onlymember']:checked").val()==2 ){
				if(router.$(".hongbao_warp input[name='map']").val()==''){
					layer.alert("地图坐标不能为空!");
					return ;
				}else if(router.$(".hongbao_warp input[name='distance']").val()<1){
					layer.alert("距离范围必须1公里以上");
					return ;
				}
			}else if( router.$(".hongbao_warp input[name='onlymember']:checked").val()=='bbsreply' && router.$(".hongbao_warp input[name='bbsnum']").val()<1 ){
				layer.alert("最低发贴数不能小于1篇");
				return ;
			}
			var total = router.$(".hongbao_warp input[name='num']").val() * router.$(".hongbao_warp input[name='money']").val();
			if( router.$(".hongbao_warp input[name='ifrand']:checked").val()==1 ){
				if(  router.$(".hongbao_warp input[name='totalmoney']").val()=='' ){
					layer.alert("红包总金额不能为空!");
					return ;
				}				
				if(router.$(".hongbao_warp input[name='totalmoney']").val()<total){
					layer.alert("红包总金额不能小于"+total+"元!");
					return ;
				}
				total = router.$(".hongbao_warp input[name='totalmoney']").val();
			}			
			if( router.$(".hongbao_warp input[name='title']").val()=='' ){
				router.$(".hongbao_warp input[name='title']").val('恭喜发大财')
			}
			if(total>userdb.rmb){
				//layer.alert("你的可用余额只有"+userdb.rmb+"元,请先充值");
			}
			$.post('/index.php/p/hongbao-wxapp.post-add.html?ext_id='+(-uid),$('.hongbao_warp').serialize(),function(res){
				if(res.code==0){
					//if(typeof(refresh_timenum)!='undefined')refresh_timenum = 1;	//加快刷新时间
					if(res.code==0){
						layer.msg(res.msg);
						if( typeof(mod_class)=='undefined' ){ //充值过来时刷新过网页,所以这个变量是不存在的
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
				}else{
					var str = res.msg;
					var msgindex = layer.alert(res.msg);
					if( str.indexOf('余额不足')>0 ){
						var money = str.replace(/[^\d|^\.]/g,"");
						Pay.mobpay( money ,'红包充值',function(type,index){
							layer.close(index);
							layer.close(msgindex);
							if(type=='ok'){
								router.$("#user_rmb").html( router.$("#user_rmb").html()+value );
								layer.msg('充值成功,你可以继续发红包了');
							}else{
								layer.alert('充值失败');
							}
						});
					}				
				}
			});
		});

		router.$(".hongbao_warp input[name='ifrand']").click(function(){
			choose_type();
		});

		router.$(".hongbao_warp input[name='onlymember']").click(function(){
			choose_limit();
		});

		router.$(".hongbao_warp input[name='map']").click(function(){
			layer.open({
			  type: 2,
			  title: false,
			  closeBtn: 0, //不显示关闭按钮
			  shadeClose: true, //点击遮罩区域是否关闭页面 
			  shade: 0.5,  //遮罩透明度
			  area: ['100%', '480px'],
			  offset: 'b', //右下角弹出
			  anim: 2,
			  content: ['/index.php/index/map/point.html?xy='+router.$(".hongbao_warp input[name='map']").val(), 'no'], //iframe的url，no代表不显示滚动条
			});
		});
		
		//充值
		router.$("#addrmb").click(function(){
			layer.prompt({
				formType: 0,
				value: '3',
				title: '请输入要充值的金额数,单位元',
				//area: ['100px', '20px'] //formType:2 自定义文本域宽高
			}, function(value, index, elem){
				if(value<0.01){
					layer.alert('充值金额不能小于0.01元');
					return ;
				}
				layer.close(index);
				Pay.mobpay( value,'红包充值',function(type,index){
					layer.close(index);
					if(type=='ok'){
						router.$("#user_rmb").html( router.$("#user_rmb").html()+value );
						layer.msg('充值成功,你可以发红包了');
					}else{
						layer.alert('充值失败');
					}
				});
			});
		});
	}

	function choose_limit(){
		var v = router.$(".hongbao_warp input[name='onlymember']:checked").val();
		if(v==2){
			router.$(".bbs_warp").hide();
			router.$(".map_warp").show();
			router.$(".hongbao_warp input[name='bbsnum']").val('');
		}else if(v=='bbsreply'){
			router.$(".map_warp").hide();
			router.$(".bbs_warp").show();
			router.$(".hongbao_warp input[name='map']").val('');
			router.$(".hongbao_warp input[name='distance']").val('');
		}else{
			router.$(".map_warp").hide();
			router.$(".bbs_warp").hide();
			router.$(".hongbao_warp input[name='map']").val('');
			router.$(".hongbao_warp input[name='distance']").val('');
			router.$(".hongbao_warp input[name='bbsnum']").val('');
		}
	}

	function choose_type(){
		var ifrand = router.$(".hongbao_warp input[name='ifrand']:checked").val();
		if(ifrand==0){
			router.$("#totalmoney_warp").hide();
			router.$(".hongbao_warp input[name='totalmoney']").val('');
		}else{
			router.$("#totalmoney_warp").show();
		}
	}

	var getParams = bui.getPageParams();
	getParams.done(function(result){
		uid = result.uid;
	});

	pageview.init();
})