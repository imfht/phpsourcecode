// 默认已经定义了main模块
loader.define(function() {

    var pageview = {};
	var id = 0;
	var imgs = {};
	var type = 'msg';

	var vues = new Vue({
				el: '.codeimg-page',
				data: {
					id:0,
					wxapp_img:'',
					url_img:'',
					listdb:[],
					title:'直播群聊二维码',
					quninfo:window.store.get("quninfo")?window.store.get("quninfo"):{},
				},
				watch:{
					listdb: function() {
						this.$nextTick(function(){	//数据渲染完毕才执行
						})
					},
				},
				methods: {
					set_quninfo:function(o){
						this.quninfo = Object.assign({}, this.quninfo, o);
					},
					set_id:function(id,type){
						this.id = id;
						var _url = '/index.php/index/msg/index.html#/public/static/libs/bui/pages/chat/chat?uid=-'+id;
						var _wxurl = '/index.php/index/msg/index.html#/public/static/libs/bui/pages/chat/chat?uid=-'+id;
						console.log("类型"+type);
						if(type=='home'){
							this.title = '圈子主页二维码';
							_url = '/index.php/qun/show-'+id+'.html';
							_wxurl = '/index.php/qun/show-'+id+'.html';
						}
						imgs.url = (typeof(web_url)!='undefined'?web_url:'')+'/index.php/index/qrcode/index.html?url='+encodeURIComponent(_url)+"&logo="+this.quninfo.picurl;
						imgs.wxapp = (typeof(web_url)!='undefined'?web_url:'')+'/index.php/index/wxapp/img.html?url='+encodeURIComponent(_wxurl);
						this.url_img = imgs.url;
						this.wxapp_img = imgs.wxapp;
					},
				}		  
			});

    // 主要业务初始化
    pageview.init = function() {// 这里写main模块的业务

         //按钮在tab外层,需要传id
		var tab = bui.tab({
			id: "#tabDynamic",
			// 1: 声明是动态加载的tab
			autoload: true,
		})

		// 2: 监听加载后的事件, load 只加载一次
		tab.on("to", function(index) {
			switch (index) {
				case 2:
					load_iframe('wap');
					//load_haibao(imgs.url)
					//loader.require(["pages/ui_controls/bui.tab_dynamic_page1"])
					break;
				case 3:
					load_iframe('wxapp')
					//load_haibao(imgs.wxapp)
					//loader.require(["pages/ui_controls/bui.tab_dynamic_page1"])
					break;
				default:
					$("#showhaibao").hide();
			}
		}).to(0)
    };

	function load_iframe(imgtype){
		if(router.$("#imgcode_"+imgtype).attr("src")!='about:blank'){
			return ;
		}
		var url = (typeof(web_url)!='undefined'?web_url:'')+'/index.php/qun/content/haibaoiframe/id/'+id+'.html?imgtype='+imgtype+'&pagetype='+type;
		router.$("#imgcode_"+imgtype).attr("src",url);
		router.$("#imgcode_"+imgtype).load(function(){
			var that = $(this).contents().find("body");
		});
	}
	/*
	function load_haibao(url){
		$("#showhaibao").show();
		router.loadPart({
					id: "#showhaibao",
					url: "/public/static/libs/bui/pages/haibao/default.html?dfd",
				}).then(function (module) {
						console.log('点击我了');
						module.setImg(url);
					//loader.require("/public/static/libs/bui/pages/haibao/default",function (voice) {})
				});
	}*/



    // 事件绑定
    pageview.bind = function() {
    }


	var getParams = bui.getPageParams();
    getParams.done(function(result){
		console.log(result);
		if(result.id!=undefined){
			id = result.id;			
			if(typeof(result.type)!='undefined'&& result.type=='home'){
				type = 'home'
			}
			vues.set_id(id,type);
			if(window.store.get("quninfo")==''){
				$.get("/index.php/qun/wxapp.qun/getbyid.html?id="+id,function(res){
					if(res.code==0){
						window.store.set("quninfo",res.data);
						vues.set_quninfo(res.data);
						//console.log('quninfo=',res.data);
					}
				});
			}			
		}		
    })



    // 初始化
    pageview.init();
    // 绑定事件
    pageview.bind();
    
    return pageview;
})