// 默认已经定义了main模块
loader.define(function() {

    var pageview = {};
	var id = 0;

	var vues = new Vue({
				el: '#page_share',
				data: {
					id:0,
					//title:'二维码',
					imgurl:'',
					listdb:[],
					quninfo:window.store.get("quninfo"),
				},
				watch:{
					listdb: function() {
						this.$nextTick(function(){	//数据渲染完毕才执行
						})
					},
				},
				methods: {
					set_id:function(id){
						this.id = id;
					},
					set_img:function(url){
						this.imgurl = url;
					},
				}		  
			});

    // 主要业务初始化
    pageview.init = function() {// 这里写main模块的业务
          console.log('点击我了dddddd');
    };


	pageview.setImg = function(url) {
        console.log('设置了图片');
		vues.set_img(url);
		app_page('page_share', function(){div_toimg("share_html", "share_img");});
    };


    // 事件绑定
    pageview.bind = function() {
    }


	var getParams = bui.getPageParams();
    getParams.done(function(result){
		console.log(result);
		if(result.id!=undefined){
			id = result.id;
			vues.set_id(id);
		}
    })



    // 初始化
    pageview.init();
    // 绑定事件
    pageview.bind();
    
    return pageview;
})

