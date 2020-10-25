loader.define(function(require,exports,module) {
    
	var pageview = {},      // 页面的模块, 包含( init,bind )
		vues,				// 要用vue渲染数据
		uiPullrefresh,      // 消息,电话公用的下拉刷新控件
		uiAccordionDevice,  // 我的设备折叠菜单
		uiAccordionFriend={};  // 我的好友折叠菜单
    
	store.compile(".bui-bar");	//重新加载全局变量数据


    /**
     * [init 页面初始化]
     * @return {[type]} [description]
     */
    pageview.init = function () {
        
        // 页面动态加载,需要重新初始化
        bui.init({
            id: "#tab-contact"
        })
        var mainHeight = $(window).height() - $("#tab-contact-header").height()- $("#tabDynamicNav").height();

		//loader.import("/public/static/js/core/vue.min.js",function(){
		//	load_menu(); //必须要确保加载成功后,才能执行下面的
		//});
		load_menu();

        // 初始化下拉刷新
        //uiPullrefresh = bui.pullrefresh({
        //    id        : "#contactScroll",
        //    height: mainHeight,
        //    onRefresh : getData
       // });

        // 初始化设备折叠菜单
       // uiAccordionDevice = bui.accordion({
       //     id:"#device"
       // });

    }

	function load_menu(){
		vues = new Vue({
				el: '#more_menu',
				data: {
					listdb: [],
				},
				watch:{
				  listdb: function() {
					this.$nextTick(function(){	//数据渲染完毕才执行
						$("#more_menu .module_or_plugin").each(function(){
							$(this).get(0).outerHTML = $(this).html();	//去除DIV包皮
						});
						$("#more_menu .modules_or_plugins").each(function(){
							$(this).get(0).outerHTML = $(this).html();	//去除DIV包皮
						});
						this.listdb.forEach((rs,i)=>{
							//console.log(i);
							uiAccordionFriend.i = bui.accordion({id:"#model_"+rs.model_type});
							uiAccordionFriend.i.showFirst(); //
						});
						$("#more_menu .friend-list .bui-box").on("click",function(e){
							bui.load({ url: "/public/static/libs/bui/pages/frame/show.html",param:{url:$(this).attr('link')}});
						});
					})
				  }
				},
				methods: {
					add_data:function(obj){ //对加载回来的菜单数据动态塞入到VUE
						for(var key in obj){
							this.listdb.push({
										model_type:key,
										title:obj[key].title,
										icon:obj[key].icon,
										sons:obj[key].sons
									});
						}	
					}
				}		  
			});
		if(typeof(member_menu)=='object'){
			layer.msg('页面渲染中...',{time:800});
			vues.add_data(member_menu);
		}else{
			layer.msg('数据加载中...',{time:800});
			$.get("/member.php/member/wxapp.menu/get.html",function(res){	//加载菜单数据
				if(res.code==0){
					vues.add_data(res.data);
				}
			});
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