/**
 * 聊天对话模板
 * 默认模块名: pages/chat/chat
 * @return {[object]}  [ 返回一个对象 ]
 */
loader.define({
	 beforeCreate: function() {
        // 只在创建脚本前执行,缓存的时候不执行
        console.log(this.moduleName + " before create")
    },
    created: function() {
        // 只在创建后执行,缓存的时候不执行
        console.log(this.moduleName + " createed")
    },
    beforeLoad: function() {
        // 页面每次跳转前都会执行
        console.log(this.moduleName + " before load")
    },
    loaded: function() {
        // 页面每次跳转后都会执行
        console.log(this.moduleName + " loaded")
    },
    hide: function(e) {
        // 页面每次跳转后退都会执行当前模块的触发
        console.log(this.moduleName + " hide ="+e.type)
    },
    show: function(e) {
        // 页面每次跳转后退都会执行当前模块的触发
        console.log(this.moduleName + " show ="+e.type)

		var pageview = {};
		// 模块初始化定义
		pageview.init = function () {
			router.$(".btn-back").click(function(){
				if($(".bui-router-item[data-page='/public/static/libs/bui/pages/frame/show']").length>1){
					bui.back();
				}				
			});
			this.bind();
		}
		pageview.bind = function () {
		}

		var getParams = bui.getPageParams();
		getParams.done(function(result){
			var url = result.url;
			console.log("url地址是",url);
			var title='',picurl='';
			if( typeof(result.title)!="undefined" ){
				title = result.title;
				router.$("#title_name").html(title);
			}
			if( typeof(result.picurl)!="undefined" ){
				picurl = result.picurl;
			}
			router.$("#bui_win").attr("src",url);
			router.$("#bui_win").load(function(){
				var that = $(this).contents().find(".qb_wap_header");
				if(that.length>0){
					that.hide();
				}
				//console.log("height="+$(this).height());
				//console.log("DIVnum="+$(this).contents().find("body").height())
				if(router.$(this).height()<500){
					//router.refresh();
					layer.msg('err');
					//window.location.reload();
				}
			});
			weixin_share({
				title:title!=''?title:'这是分享标题',
				about:title!=''?title:'这是分享描述',
				picurl:picurl!=''?picurl:'',
				url:url,
			});
		})

		// 初始化
		pageview.init();
    },
    beforeDestroy: function() {
        // 页面每次后退前执行
        console.log(this.moduleName + " before destroy")
    },
    destroyed: function() {
        // 页面每次后退后执行
        console.log(this.moduleName + " destroyed")
    }
}
/*
function(require,exports,module) {

    var pageview = {};
	

    // 模块初始化定义
    pageview.init = function () {
        this.bind();
    }
    pageview.bind = function () {
    }

	var getParams = bui.getPageParams();
    getParams.done(function(result){
		var url = result.url;
		$("#bui_win").attr("src",url);
    })

    // 初始化
    pageview.init();

    // 输出模块
    return pageview;
}*/
)
