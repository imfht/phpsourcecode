/**
 * 底部导航TAB模板
 * 默认模块名: main
 * @return {[object]}  [ 返回一个对象 ]
 */
var from_main = true;	//主要是为微信的wx.config做考虑的
loader.define(function(require,exports,module) {

    var bs = bui.store({
				scope: "page",
				data: {
					list: {},
				},
				mounted: function () {
						var i=0;
						setInterval(()=>{
								i++; 
								// 告诉使用obj的模板,数据变更需要重新渲染
								//bs.set("list",{title:"我的"+i});
							},2000)	
					}
			})
 

    var pageview = {},
        uiNavtab,          // 侧边栏
        uiSidebar,          // 侧边栏
        uiDialogSearchbar,  // 弹出搜索栏
        uiListSearchbar,    // 搜索输入控件
        uiListSearch;       // 搜索结果列表控件

    // 模块初始化定义
    pageview.init = function () {
        //bui.init({
        //  id: ".bui-page-index"
        //})
        // 先加载页面
        pageview.navTab();

        // 绑定侧滑栏
        pageview.sidebar();

        // 初始化搜索栏
        pageview.searchbar();

    }

    pageview.sidebar = function () {

        /*初始化侧边栏 放在这里会影响到滚动按钮往上滑
        uiSidebar = bui.sidebar({
            id      : "#sidebar",
            handle: ".bui-page-index",
            width   : 610
        });*/

        // 绑定侧滑栏点击, TAB动态加载,所以这里需要在父级绑定
        $("#sidebar").on("click",".sidemenu",function () {
			uiSidebar = bui.sidebar({
				id      : "#sidebar",
				//handle: ".bui-page-index",
				opacity:0.5,
				width   : 610
			});
            uiSidebar.open();
        })

		$("#sidebar").on("click","#close_side",function () {
            uiSidebar.close();
			$(".bui-mask").remove();
        })
    }

    pageview.searchbar = function () {

        // 初始化搜索弹出框
        uiDialogSearchbar = bui.dialog({
            id         : "#search-dialog",
            fullscreen : true,
            mask: false,
            position   : "right",
            effect     : "fadeInRight"
        });

        // 计算列表的高度,传固定高度不会导致聚焦时,列表动态计算高度,导致多余空白
        var listHeight = $(window).height() - $("#uiSearchbar").height() - $("#tab-home-header").height() - $("#tabDynamicNav").height();

        // 搜索条的初始化
        uiListSearchbar = bui.searchbar({
              id      :"#uiSearchbar",
              onInput : function(ui,keyword) {

                  //清空列表数据
                  $("#uiScroll .bui-list").empty();
					console.log('fd',keyword);
                  //实时搜索
                  if( uiListSearch ){

                    // 重新初始化数据
                      uiListSearch.init({
                        page: 1,
                        data: {
                          "name":keyword
                        }
                      });
                  }else{

                    // 搜索结果的列表初始化
                    uiListSearch = bui.list({
                          id: "#uiScroll",
                          url: "/index.php/index/wxapp.member/get_list.html?rows=10",
                          data: {"name":keyword},
                          field: {
                            data:"data"
                          },
                          page:1,
                          pageSize:10,
                          height: listHeight,
                          template: uiListSearchTemplate,
						  callback: function (e) {
							  var uid = $(e.target).data("uid");
							  bui.load({url: "/public/static/libs/bui/pages/chat/chat.html",param: {
								"uid":uid,}
							  });
						  },
                    });
                  }
              },
              onRemove: function(ui,keyword) {
                  //删除关键词需要做什么其它处理
                  $("#uiScroll .bui-list").empty();
              },
              callback: function (ui,keyword) {}
          });

        // 打开搜索弹窗页
        $("#bui-router").on("click",".search",function () {
            uiDialogSearchbar.open();
        });
        // 关闭搜索弹窗页
        $("#bui-router").on("click",".btn-cancel",function () {			
            uiDialogSearchbar.close();			
        })
    }

    // 底部导航
    pageview.navTab= function() {

        //按钮在tab外层,需要传id
        uiNavtab = bui.tab({
            id:"#tabDynamic",
            menu:"#tabDynamicNav",
            scroll: false,
            swipe: false,
            animate: true,
            // 1: 声明是动态加载的tab
            autoload: true,
			index:typeof(member_menu)=='object'?2:0,
        })

        // 2: 监听加载后的事件, loader 只加载一次
        uiNavtab.on("to",function (index) {
            var index = index || 0;
            switch(index){
                case 0:					
                loader.require(["/public/static/libs/bui/pages/main/chatlist"])
                break;
                case 1:
                loader.require(["/public/static/libs/bui/pages/main/qun"])
                break;
                case 2:
                loader.require(["/public/static/libs/bui/pages/main/tongji"])
                break;
				case 3:
                loader.require(["/public/static/libs/bui/pages/bbs/index"])
                break;
            }
        }).to(typeof(member_menu)=='object'?2:0)
    }

    // 列表生成模板
    function uiListSearchTemplate (data) {
        var html = "";

        $.each(data,function(index, el) {

            html += '<li class="bui-btn" data-uid="'+el.uid+'"><i class="icon-facefill" style="font-size:.4rem;margin-right:.1rem;"></i>'+el.username+'</li>';
        });

        return html;
    }


    // 初始化
    pageview.init();

    // 输出模块
    return pageview;
})
