/**
 * 朋友圈
 * 默认模块名: pages/blog/blog
 * @return {[object]}  [ 返回一个对象 ]
 */
 loader.define(function(require, exports, module) {

     var pageview = {};
	 var uiDropdown,topic_id=0,pid,scroll_s=true,showpage=1,id_array=[];
	 var map_x = 0;
	 var map_y = 0;
	
	var bs = store.compile(".bui-bar");	//重新加载全局变量数据

	// 模块初始化定义
     pageview.init = function(opt) {

		 bui.init()

		 router.$("main").scroll(function () {
			var h = router.$(".comment-box").height() - router.$("main").height()-$(this).scrollTop();
			if(h<100 && scroll_s==true){console.log(h);
				layer.msg('数据加载中,请稍候...',{time:2000});
				scroll_s = false;
				get_list();
			}
			//console.log(h);
		});

		layer.msg('数据加载中,请稍候...',{time:2000});
		
		router.$("#post_btn").click(function(){
			post_reply();
		});
		
		router.$("#close_post").click(function(){
			router.$(".comment-post").hide();
			$(".bui-page-index").children('footer').show();
		});
		
		get_list();


		loader.import(["/public/static/libs/bui/js/map.js"],function(){
			get_gps_location(function(x,y){
				map_x = x;
				map_y = y;
				$.get("/member.php/member/wxapp.user/edit_map.html?point="+x+","+y);
				$.get("/index.php/index/wxapp.map/get_address.html?xy="+x+","+y,function(res){
					if(res.code==0){
						router.$(".my_address span").html( res.data.address );
					}
				});
				show_distance(map_x,map_y); //显示距离					
			});
		});

         
     }
	
	function TopicAgree(){	
		$.get("/index.php/bbs/wxapp.post/agree.html?id="+topic_id,function(res){
			if(res.code==0){
				var that = $(".comment-box .cmtbtn-"+topic_id+" .span1").eq(0).find('a');
				var num =  that.html();
				num++;
				that.html(num);
				bui.hint("点赞成功")
			}else{
				layer.msg("点赞失败:"+res.msg,{time:2500});
			}	
		});
	}

	 function reply_data_html(res,obj,id){
		 var str = "";
		if(res.code==0){				
			res.data.forEach((rs)=>{
				str += `<li>
                        <em>${rs.username}</em>：${rs.content}
                    </li>`;
			});				
		}
		if(str!=""){
			obj.html(str);
			if(topic_id>0){
				$(".comment-box .comment-"+topic_id).show();				
			}
			$(".comment-box .cmtbtn-"+id+" .span1").eq(1).find('a').html(res.paginate.total);
			obj.show();
		}
	 }

	 function get_reply(id,obj){
		 $.get("/index.php/bbs/wxapp.reply/index.html?rows=10&id="+id,function(res){
			reply_data_html(res,obj,id);
		});
	 }

	 var vues = new Vue({
				el: '.bbs_near_page',
				data: {
					listdb: [],
					userinfo: {},
				},
				watch:{
					listdb: function() {
						this.$nextTick(function(){	//数据渲染完毕才执行
							router.$(".comment-box").show(1000);
							this.userinfo = Object.assign({}, this.userinfo,bs.get().userinfo);
							//console.log(bs.get().userinfo);
							$('.comment-box .comment-line .comment-reply').each(function(i){
								var that = $(this);
								var id = that.data('id');
								setTimeout(function(){	//延时加载减轻服务器的并发负载
									//console.log(id);
									if(typeof(id_array[id])=='undefined'){
										get_reply(id,that)	//获取每一个主题的所有评论
									}									
									id_array[id] = true;
								},i*500);
							});
							$('.comment-box .comment-content').each(function(){
								if($(this).height()>500){
									$(this).next().show();
								}
							});
							show_reply();	//显示评论框与点赞功能
							showMore();		//显示更多文字
							
							if(map_x!=0)show_distance(map_x,map_y); //显示距离
							$('.comment-box .viewinfo').off("click");
							$('.comment-box .viewinfo').click(function(){
								var url = "/index.php/bbs/show.html?id="+$(this).data("id");
								bui.load({ 
									url: "/public/static/libs/bui/pages/frame/show.html",
									param:{
										url:url,
										title:$(this).data("title"),
										picurl:$(this).data("picurl"),
									}
								});
							})
					   })
				  }
				},
				methods: {
					set_data:function(array){
						array.forEach((rs)=>{
							var pic = [];
							rs.pics = [];
							rs.picurls.forEach((qs,index)=>{
								pic.push({picurl:qs.picurl}); 
								if((index+1)%3==0){
									rs.pics.push(pic);
									pic = [];
								}
							});
							if(pic.length>0){
								rs.pics.push(pic);
							}
							//console.log(rs.pics);
							this.listdb.push(rs);
						});			
					},
				},
			});


	function get_list(){
		$.get("/index.php/bbs/wxapp.near/index.html?rows=10&range=200&page="+showpage,function(res){
			if(res.code==0 && res.data.length>0){
				vues.set_data(res.data);
				layer.closeAll();
				showpage++;
				scroll_s = true;
			}else{
				layer.msg("没有了!");
			}
		});
	}


	//计算距离当前位置的公里数
	function show_distance(map_lat,map_lon){
		$('.comment-line .distance').each(function(){
			var this_map=$(this).data('map');
			var thismap=this_map.split(",");
			var this_lon = thismap[0];
			var this_lat = thismap[1];
			var show_word='距离';
			if(this_lon!=0 &&this_lat!=0){
				var show_map_str = GPS.distance(map_lat,map_lon,this_lon,this_lat);
				console.log(map_lat+"="+map_lon+"="+this_lon+"="+this_lat);
				var kilometres = Math.floor(show_map_str/1000);  
				var metres=Math.floor(show_map_str%1000);				
				if(kilometres>0){
					show_word+='<font style="color:blue;">'+kilometres+'</font>公里';
				}
				show_word += isNaN(metres)?'未知':'<font style="color:blue;">'+metres+'</font>米';
			}else{
				show_word = '未标定位';
			}			
			$(this).html(show_word);
		});	
	}
	
	//展开评论框与添加点赞事件
	 function show_reply(){
		 $(".comment-box .dropdown-comment").each(function(){
			var that = $(this);
			that.find(".span1").off('click');
			that.find(".span1").eq(0).click(function(){
				topic_id = that.data("id");
				TopicAgree();
			});
			that.find(".span1").eq(1).click(function(){
				topic_id = that.data("id");
				router.$(".comment-post").show();
				router.$("#comment_cnt").focus()
				$(".bui-page-index").children('footer').hide();
				bui.init();
			});
		 });
		/*uiDropdown = bui.dropdown({
             id: ".dropdown-comment",
             data: [{
                 name: "点赞",
                 value: "点赞"
             }, {
                 name: "更多",
                 value: "评论"
             }],
             position: "left",
             change: false,
             relative: false,
             callback: function(e) {
				 topic_id = $(e.target).parent().parent().data("id");
                 var index = $(e.target).index();
                 // 打分
                 switch (index) {
                   case 0:
                   bui.hint("点赞成功")
                   break;
                   case 1:
					router.$(".comment-post").show();
                   //pageview.showPost();
                   bui.init();
                   // 评论
                   break;
                 }
             }
         });*/
	 }

	 function post_reply(){
		var url = "/index.php/bbs/wxapp.reply/add.html?rows=10&id="+topic_id;
		var pid = 0;
		if(pid>0){
			url += "&pid="+pid;
		}
		havepost = false;
		var contents = $('#comment_cnt').val();			
		if(contents==''){
			layer.msg("请输入评论内容！",{time:1500});		
		}else{
			if(contents.replace(/\[(face\d+)\]/g,"")==""){
				layer.alert('不允许只发表情!');
				return false;			
			}
			if(havepost==true){
				layer.msg('请不要重复提交');
				return false;
			}
			layer.msg('内容提交中,请稍候');
			havepost = true;		
			contents = contents.replace(new RegExp('<',"g"),'&lt;');
			contents = contents.replace(new RegExp('>',"g"),'&gt;');
			contents = contents.replace(new RegExp('\n',"g"),'<br>');
			contents = contents.replace(new RegExp(' ',"g"),'&nbsp;');
			$.post(
				url,
				{'content':contents},
				function(res,status){
					havepost = false;
					if(res.code==0){
						if(pid>0){							
						}else{
							reply_data_html(res,$(".comment-box .comment-"+topic_id),topic_id);
						}
						layer.closeAll(); //关闭所有层
						layer.msg("发表成功！",{time:1500});	
						$('#comment_cnt').val('');
						router.$(".comment-post").hide();
						$(".bui-page-index").children('footer').show();
						bui.init();
					}else{
						layer.msg("评论发表失败:"+res.msg,{time:1500});
					}
				}
			);
		}
	}

     // 展开更多
     function showMore() {
		 router.$(".bui-btn-toggle").off("click");
         router.$(".bui-btn-toggle").on("click", function() {
             var $target = $(this).prev(".comment-content");
             if ($target.hasClass("active")) {
                 $(this).text("展开")
             } else {
                 $(this).text("收起")
             }
             $target.toggleClass("active")
         })
     }

     pageview.hidePost = function () {
       router.$(".comment-post").hide();
     }

     // 为图片绑定点击事件,触发对话框展示
     router.$(".container-full").on("click",".span1",function () {
       var index = $(this).index();
       pageview.dialog.open();
       pageview.slide.to(index,"none");
     })
     pageview.dialog = bui.dialog({
         id: "#uiDialog",
         fullscreen: true,
         mask: false,
     });

     // 初始化焦点图
     pageview.slide =  bui.slide({
         id:"#slide",
         height:380,
         autopage: true,
         fullscreen: true
     })
     pageview.dialog.on("open",function () {
       pageview.slide.init();
     });


	 

     pageview.init();
     // 输出模块
     return pageview;
 })
