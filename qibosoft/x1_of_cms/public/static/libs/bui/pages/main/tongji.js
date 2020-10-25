loader.define(function(require,exports,module) {
    
	var pageview = {},      // 页面的模块, 包含( init,bind )
		uiPullrefresh;      // 消息,电话公用的下拉刷新控件
    
	store.compile(".bui-bar");	//重新加载全局变量数据

    /**
     * [init 页面初始化]
     * @return {[type]} [description]
     */
    pageview.init = function () {
        
        // 页面动态加载,需要重新初始化
        bui.init({
            id: "#tab-minespace"
        })
        var mainHeight = $(window).height() - $("#tab-minespace-header").height()- $("#tabDynamicNav").height();

        // 初始化下拉刷新
        //uiPullrefresh = bui.pullrefresh({
        //    id        : "#minespaceScroll",
        //    height: mainHeight,
        //    onRefresh : getData
       // });
    }

	//统计数据的类型选择
	var tongji_num = 0;//parseInt($("#tongji_num").html());
	router.$(".tongji li").each(function(){
		var that = $(this);		
		var type = that.data('type');
		var obj_num = that.find('i').last();

		that.click(function(){
			that.attr('href','/public/static/libs/bui/pages/tongji/show.html?type='+type);			
			obj_num.removeClass('bui-badges');
			obj_num.addClass('icon-listright');
			tongji_num = tongji_num-parseInt(obj_num.html()!=''?obj_num.html():0);
			obj_num.html('');
			if(tongji_num<1){
				router.$(".tongji_num").hide();
			}else{
				router.$(".tongji_num").html(tongji_num);
				router.$(".tongji_num").show();
			}
		});
		
		//各种动态的新数据统计		
		$.get(tongjiCountUrl+'?type='+type,function(res){
			if(res.code==0 && res.data>0){
				obj_num.html(res.data>999?'99+':res.data);
				obj_num.removeClass('icon-listright');
				obj_num.addClass('bui-badges');
				tongji_num = tongji_num+res.data;
				//$("#tongji_num").html(tongji_num>999?'99+':tongji_num);
				//$("#tongji_num").show();
			}else{
				obj_num.removeClass('bui-badges');
				obj_num.addClass('icon-listright');
			}
		});
	})

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