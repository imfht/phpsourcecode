if (navigator.userAgent.indexOf("MSIE") >= 0) {
	alert("请使用谷歌或火狐访问.其它浏览器无法操作与使用");
}

function send_user_msg(obj){
	layer.open({
	  type: 2,
	  title: '给 '+obj.data('name')+' 私信',
	  shade: [0.3,'#333'], 
	  area: ['600px', '500px'],
	  anim: 1,
	  content: 'https://x1.php168.com/index.php/index/msg/index.html#/public/static/libs/bui/pages/chat/chat?uid='+obj.data('uid'),
	  end: function(){ //关闭事件	
	  }
	});
	parent.document.body.scrollTop = parent.document.documentElement.scrollTop = 0;	//必须要返回顶部,不然外部框架有可能会显示不了
}





var page = 1,loading=1,order_by='appupdate_time',keyword='',category=0;
var appUrl = 'https://x1.php168.com/appstore/wxapp.index/index.html?rows=12&status=2';



//设置安装菜单
function init_btn(){
	$(".choose_btn button").click(function(){		 
		$(".choose_btn button").each(function(){
			$(this).addClass('layui-btn-primary');
			$(this).removeClass('layui-btn-normal');
		});
		$(this).removeClass('layui-btn-primary');
		$(this).addClass('layui-btn-normal');
		var type = $(this).data('type');
		if(type=='good'){
			appUrl = 'https://x1.php168.com/appstore/wxapp.index/index.html?rows=8&status=2';
		}else if(type=='other'){
			appUrl = 'https://x1.php168.com/appstore/wxapp.index/listbyuid.html?rows=8&uid='+$(this).data('uid');
		}else if(type=='must'){
			appUrl = 'https://x1.php168.com/appstore/wxapp.index/index.html?rows=8&keyword='+$(this).data('ids');
		}
		keyword = '';
		page = 1;
		$('.Markercontents').html('');
		showlist(); 
	});
}


$(window).scroll(function(){
	// 当滚动到最底部以上100像素时， 加载新内容
	if (loading==1 &&  (400 + $(window).scrollTop())>($(document).height() - $(window).height())){
		loading = 0;
		showlist();
	}	
});

var appInfo = {};


var vues = new Vue({
	el: '.vueId',
	data: {
		listdb: [],
		info:{},
		uid:0,
	},
	watch:{
      listdb: function() {
			this.$nextTick(function(){	//数据渲染完毕才执行
				check_setup();
				hide_demo();
			})
      },
	  info: function() {
			
			this.$nextTick(function(){	//数据渲染完毕才执行
				init_btn();
				check_setup();
				if($(".choose_btn .must").data('ids')!=''){
					$(".choose_btn .must").trigger('click');
				}else{
					$(".choose_btn .must").hide();
					$(".choose_btn .other").trigger('click');
				}				
				$(".setup_btn a").click(function(){
					setup_app(appInfo.id,appInfo.fid,appInfo.app_keywords,appInfo.price);
				});
			})
		},
    },
	methods: {
		setup: function (id,fid,keywords,price,must_view_about) {
			//setup_app(id,fid,keywords,price);	//当前界面安装
			
			if(must_view_about){
				var index = parent.layer.open({
					type: 2,
					title: '安装当前应用，必须先阅读注意事项，以避免安装后产生不愉快的后果！',
					shadeClose: true,
					shade: [0.9, '#393D49'],
					maxmin: false, //开启最大化最小化按钮
					area: ['80%', '80%'],
					content: "https://x1.php168.com/appstore/content/about/id/" + id + ".html",
					btn: ['确定安装', '放弃安装'],
					yes: function() {
						parent.layer.close(index);
						open_win(id);
					},
					btn2: function() {
						parent.layer.close(index);
					},
				});
			}else{
				open_win(id);
			}

			function open_win(id){
				parent.layer.open({
					type: 2,
					title: '安装应用',
					shadeClose: true,
					shade:  [0.9, '#393D49'],
					maxmin: false, //开启最大化最小化按钮
					area: ['80%', '98%'],
					content: market_url+"?id="+id,
					end: function(){
						//setup_app(id,keywords,price,1);
					}
				});
			}			
		},
		add_data:function(array){
			array.forEach((rs)=>{
				this.listdb.push(rs);
			});			
		},
		add_info:function(o){
			this.uid = o.uid;
			appInfo = o;
			if(o.depend_app!=''){
			}else{
			}
			o.content = o.content.replace(/<.*?>/g,"").substring(0,300)
			this.info = Object.assign({}, this.info, o);
		}
	}		  
});




//vues.$watch('listdb',function(val){ 
//	vues.$nextTick(function() {	//渲染完毕
//	}); 
//})

function showlist(){
	layer.load(3,{shade: [0.1,'#333']});
	var url = appUrl + '&order_by='+order_by+'&page='+page;
	$.get(url,function(res){
		if(res.code==0){
			layer.closeAll();
			page++;
			if(res.data==''){
				layer.msg("已经显示完了！",{time:500});
				$('.ShowMoreInfo span').attr('onclick','');
				$('.ShowMoreInfo span').html('显示完了');
				$('.ShowMoreInfo span').css({'background':'#CCC'});
			}else{
				vues.add_data(res.data);			
				loading = 1;				
			}
		}else{
			layer.msg(res.msg,{time:2500});
		}
	});
}

if(Id>0){
	$.get("https://x1.php168.com/appstore/wxapp.show?id="+Id,function(res){
		if(res.code==0){
			vues.add_info(res.data);
		}else{
			layer.alert(res.msg);
		}
	});
}else{
	$(".mainshop,.setup_btn,.choose_btn button").hide();
	$(".choose_btn button.good").show();

	//$(".choose_btn .good").trigger('click');
	//init_btn();
	showlist();
}


var ids_ck_msg = new Array();
var app_have_setup = [];


//演示地址为空的,就隐藏掉
function hide_demo(){
	$('.Markercontents a.demo_url').each(function(){
		if($(this).attr('href')=='')$(this).hide();
	});
}

var have_pay = [];	//是否已购买过可以直接安装.不提示购买
function setup_app(id,fid,keywords,price,have_open_layer){
	if(typeof(app_have_setup[id])!='undefined'){
		layer.alert('当前应用,已经安装过了!');
		return ;
	}
	if(have_pay[id]==undefined){
		have_pay[id]=0;
	}
	var baseurl = "?id=" + id + "&domain="+domain+"&appkey="+appkey+"&";
	if(price>0 && have_pay[id]<1){	//收费模块,先要做权限判断
		//安装权限检查
		$.get("https://x1.php168.com/appstore/getapp/client_check.html"+baseurl+'&'+Math.random(),function(res){
			if(res.code==0){	//已经购买过,有权限安装
				have_pay[id] = 1;
				setup_app(id,fid,keywords,price);
			}else if(res.code==1){	//还没购买,没权限安装
				if(have_open_layer==1){	//没有成功付款购买
					layer.msg('你放弃了安装!');
					return ;
				}
				var msg = '当前模块需要付费，安装后费用不退还，你还要安装吗?';
				if( typeof(ids_ck_msg[id])!="undefined" ){
					msg = "当前应用处于“"+ids_ck_msg[id]+"”状态,你确认要付费授权吗?";
				}
				layer.confirm(msg, {
					title:'重要提醒',
					btn : [ '继续安装', '取消安装' ]
				}, function(index) {
					layer.close(index);
					//此处请求后台程序，下方是成功后的前台处理……
					//var index = layer.load(1,{shade: [0.7, '#393D49']}, {shadeClose: true}); //0代表加载的风格，支持0-2
					var server_url = "https://x1.php168.com/appstore/getapp/index.html" + baseurl;
					layer_buy_iframe(server_url,id,fid,keywords,price);
				});
				//layer.alert(res.data.money);
			}else{
				layer.alert('网络故障',{time:5500});
			}
		}).fail(function (res) {
			//layer.alert('网络故障,请晚点再偿试安装!!');
			layer.close(index);
			layer.open({title: '安装失败,请晚点再偿试!',area:['90%','90%'],content: res.responseText});
		});
		return ;
	}	
	
	layer.alert('安装需要一点时间,请耐心等候...');
	var index = layer.load(1,{shade: [0.7, '#393D49']}, {shadeClose: true}); //0代表加载的风格，支持0-2
	//模块下载安装
	var mtype = '';
	if(fid==1){
		mtype = 'module';
	}else if(fid==2){
		mtype = 'plugin';
	}else if(fid==3){
		mtype = 'hook_plugin';
	}else if(fid==4 || fid==7 || fid==9 || fid==11){
		mtype = 'style';
	}else{
		layer.alert('APP分类有误!');
		return ;
	}
	var url = window.location.href.split('/market/')[0]+"/"+mtype+"/market.html" + baseurl + "keywords=" + keywords + "&type=down";
	if( typeof(ids_ck_msg[id])!="undefined" ){
		url +="&upvip=1"
	}
	$.get(url+'&'+Math.random(),function(res){
		layer.close(index);
		if(res.code==0){
			layer.confirm(res.msg, {
					btn : [ '设置权限', '不设置' ]
				}, function(index) {
					layer.close(index);
					//parent.window.location.href = res.data.url;
					parent.layer.open({
						type: 2,
						title: '设置权限',
						shadeClose: true,
						shade:  [0.9, '#393D49'],
						maxmin: true, //开启最大化最小化按钮
						area: ['85%', '95%'],
						content: res.data.url,
						end: function(){},
					});
				}
			);
		}else{
			layer.alert(res.msg);
		}
	}).fail(function (res) {
		layer.close(index);
		layer.open({title: '安装失败,请晚点再偿试!你若已付费,下次安装不会重复扣费',area:['90%','90%'],content: res.responseText});
    });
}

var setup_index;
function layer_buy_iframe(url,id,fid,keywords,price){
	setup_index = layer.open({
		type: 2,
		title: false,
		shadeClose: true,
		shade:  [0.9, '#393D49'],
		maxmin: true, //开启最大化最小化按钮
		area: ['700px', '95%'],
		content: url,
		end: function(){
			if(count_handle != null){
				clearInterval(count_handle);
				setup_app(id,fid,keywords,price,1);
			}			
		}
	});
	check_buy(id,fid,keywords,price);
	parent.document.body.scrollTop = parent.document.documentElement.scrollTop = 0;	//必须要返回顶部,不然外部框架有可能会显示不了
}

var count_handle = null;

function check_buy(id,fid,keywords,price){
	if(count_handle != null){
		clearInterval(count_handle);
	}
	count_handle = setInterval(function(){
		$.get("https://x1.php168.com/appstore/getapp/check_buy.html?id="+id+"&domain="+domain,function(res){
			if(res.code==0){				
				clearInterval(count_handle);
				count_handle = null;
				layer.close(setup_index);
				setup_app(id,fid,keywords,price,1);
			}
		});
	}, 1000 );
}

$(document).ready(function () {
	$(window).scroll(function () {
		 if($(window).scrollTop()>800){
			parent.document.body.scrollTop = parent.document.documentElement.scrollTop = 0;	//必须要返回顶部,不然外部框架有可能会显示不了
		 }
	});
});