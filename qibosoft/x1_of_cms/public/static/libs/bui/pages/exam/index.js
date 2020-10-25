loader.define(function(require,exports,module) {


	var pageview = {};      // 页面的模块, 包含( init,bind )
	var type = 'paper';  //paper question
	var uid = 0; //圈主UID
	var mid = 1;
	var aid = 0; //圈子ID
	var mod_array = {paper:'试卷',question:'试题'};
	var page = 1;
	var scroll_get = true;
	
    /**
     * [init 页面初始化]
     * @return {[type]} [description]
     */
    pageview.init = function () {
		get_list_data();
		//get_mod();
		router.$("#choose_mod .bui-nav li").click(function(){
				router.$("#choose_mod .bui-nav li").removeClass("active");
				$(this).addClass("active");
				type = $(this).data("type");
				page = 1;
				router.$(".bui-bar-main").html(mod_array[type]);
				get_list_data();
		});

		var that = router.$("#choose_mod");
		that.parent().scroll(function () {
			
			var h = $(".bui-listview").height()-that.parent().height()-that.parent().scrollTop();
			if( h<300 && scroll_get==true){
				scroll_get = false;
				//console.log(h);
				get_list_data();
			}
		});
    }
	
	function get_mod(){
		var url = "/index.php/index/wxapp.index/topic_mod.html";
		$.get(url,function(res){
			if(res.code==0){
				var str = '';
				res.data.forEach((rs)=>{
					mod_array[rs.keywords] = rs;
					var ck = rs.keywords == type?"active":"";
					str += `<li class="bui-btn ${ck}" data-type="${rs.keywords}">${rs.name}</li>`;
				});
				router.$("#choose_mod .bui-nav").html(str);
			}
			router.$("#choose_mod .bui-nav li").click(function(){
				router.$("#choose_mod .bui-nav li").removeClass("active");
				$(this).addClass("active");
				type = $(this).data("type");
				page = 1;
				router.$(".bui-bar-main").html(mod_array[type].name);
				get_list_data();
			});
			router.$(".bui-bar-main").html(mod_array[type].name);
		});
	}

	//获取相应的频道数据列表
	function get_list_data(){
		layer.msg("加载中,请稍候...");
		var url = "/index.php/exam/wxapp.paper/index.html?uid="+uid+"&page="+page+"&rows=10";	//列出试卷
		if(type=='question'){
			url = "/index.php/exam/wxapp.index/listbyuid.html?uid="+uid+"&page="+page+"&rows=10";  //列出试题
		}		
		$.get(url,function(res){
			layer.closeAll();
			if(res.code==0){
				if(res.data.length>0){
					if(page==1){
						$('.list-hack').html( pageview.format_list_data(res.data) );
					}else{
						$('.list-hack').append( pageview.format_list_data(res.data) );
					}					
					add_action();
					page++;
					scroll_get = true;
				}else{
					layer.msg("没有了!");
				}
			}
		});		
	};
	
	//绑定按钮事件
	function add_action(){

		router.$("#add_model").click(function(){
			//Qibo.open(web_url + "/member.php/exam/paper/add.html");
			bui.load({ 
				url: "/public/static/libs/bui/pages/frame/show.html",
				param:{
					url:"/member.php/exam/paper/add.html",
				}
			});
		});

		router.$('.list-hack .add').click(function(){
			var id = $(this).data("id");
			$(".chat_mod_btn").hide();
			var url = "/index.php/exam/wxapp.paper/setlive.html?type=add&aid="+aid+"&id="+id; //获取试卷信息
			if(type=='question'){
				url = "/index.php/exam/wxapp.show/index.html?aid="+aid+"&id="+id;  //获取试题信息
			}
			$.get(url,function(res){
				if(res.code==0){
					console.log('信息',res.data);
					var arr = {
						info:res.data,
					}
					layer.msg("操作成功");
					//通知所有用户打开播放器,或者同步音乐信息					
					setTimeout(function(){
						ws_send({
							type:"qun_to_alluser",
							tag:type=='question'?"give_question_state":"give_exam_state",
							data: arr,
						});
						bui.back();
					},100);
				}else{
					layer.alert(res.msg);
				}
			});
		});

		//添加侧滑菜单
		/*
		bui.listview({
				id   : '.list-hack',
				data : [{text : "删除",classname : "danger"},{text : "修改",classname : "warning"}],
				callback : function(e,ui) {
					var text = $(e.target).text().trim();
					if( text == '删除' ){
						$(e.target).parents(".list-item").fadeOut(300,function () {
							$(this).remove();
						});
					}
						// 关闭侧滑
					ui.close();
				}
		});
		*/
	}

	pageview.format_list_data = function(array){
		var str = "";
		var d_url = typeof(web_url)!='undefined'?'':'/';
		array.forEach((rs)=>{
			var about = '';
			if(rs.content == undefined){	//试卷
				rs.content = '';
				about = '暂无介绍';
			}else{
				about = '暂无介绍';
			}
			var content = rs.content.substring(0,20);
			
			if(rs.title == undefined){
				rs.title = rs.name;
			}
			var title = rs.title.substring(0,15);
			if(rs.picurl == undefined){
				rs.picurl = '';
			}
			
			str +=`
			<li class="list-item" data-uid="${rs.id}">
				<div class="bui-btn bui-box">
					<a href="/index.php/${type}/content/show/id/${rs.id}.html" class="iframe"><img class="ring ring-group" src="${rs.picurl}" onerror="this.src='${d_url}public/static/images/nopic.png'"/></a>                                
					<div class="span1">
						<h3 class="item-title">
							${title}
						</h3>
						<p class="item-text">${about}</p>
					</div>
					<i class="icon- primary add" data-id="${rs.id}" data-url="${rs.url}" data-title="${rs.title}" data-picurl="${rs.picurl}" data-content="${rs.content}"><i class="fa fa-plus-circle"></i></i>
				</div>
			</li>
			`;
		});
		return str;
	}

	var getParams = bui.getPageParams();
		getParams.done(function(result){
			uid = result.uid;
			aid = result.aid;
			//if(result.type!=undefined)type = result.type;
		})


    // 控件初始化
    pageview.init();

    // 输出模块
    module.exports = pageview;
})
