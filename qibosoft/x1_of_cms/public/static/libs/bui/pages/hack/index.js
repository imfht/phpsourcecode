loader.define(function(require,exports,module) {


	var pageview = {};      // 页面的模块, 包含( init,bind )
	var type = 'bbs';
    var module = 'module';
	var uid = 0;
	var mod_array = {};
	var page = 1;
	var scroll_get = true;
	
    /**
     * [init 页面初始化]
     * @return {[type]} [description]
     */
    pageview.init = function () {
		get_list_data();
		get_mod();

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
					str += `<li class="bui-btn ${ck}" data-type="${rs.keywords}" data-module="${rs.module}">${rs.name}</li>`;
				});
				router.$("#choose_mod .bui-nav").html(str);
			}
			router.$("#choose_mod .bui-nav li").click(function(){
				router.$("#choose_mod .bui-nav li").removeClass("active");
				$(this).addClass("active");
				type = $(this).data("type");
                 module = $(this).data("module");
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
		 if(module=='plugin'){
       var url = "/index.php/index/plugin/execute/plugin_name/"+type+"/plugin_controller/quote/plugin_action/listbyuid.html?page="+page+"&rows=20";
       }else{
      var url = "/index.php/"+type+"/wxapp.index/listbyuid.html?page="+page+"&rows=20";
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
	
	//群聊直接发送到短消息数据表
	function send_msg(id,m_title,m_content,m_picurl){
		var content = `<ul class="model-list model-${type}" data-id="${id}" data-type="${type}" data-imgurl="${m_picurl}"><li class="model-title">${m_title}</li><li class="model-more"><div class="model-content">${m_content}</div><div class="model-picurl"><img src="${m_picurl}" onerror="$(this).parent().hide()"/></div></li></ul>`;
		$.post("/member.php/member/wxapp.msg/add.html",{
				'uid':uid,
				'content':content,
				'ext_id':id,
				'ext_sys':type,
				},function(res){
					refresh_timenum = 1;	//加快刷新时间
					if(res.code==0){
						layer.msg('添加成功');
						bui.back();
					}else{
						layer.alert('添加失败:'+res.msg);
					}
		});
	}

	//赋值到表单那里
	function send_form(id,m_title,m_content,m_picurl,m_url,mid,path){
		m_title = m_title.replace('"',"'");
		if(m_content==''||m_content==null){
			m_content = '暂无介绍';
		}
		var labelpath = path==undefined ? '' : `data-labelpath="${path},${type},${id},${mid}"`;
		//var content = `[topic type=${type} id=${id} picurl=${m_picurl}]${m_title}##@@##${m_content}[/topic]`;
		var content = `
			   <section ${labelpath} class='topic-box topic-type-${type}' data-id="${id}" data-type="${type}">
                    <div class='topic-img'><a href='${m_url}' target='_blank'><img width='100' src='${m_picurl}' onerror="this.src='/public/static/images/nopic.png';" /></a></div>
                    <div class='topic-text'>
                        <div class='topic-title'><a href='${m_url}' target='_blank'>${m_title}</a></div>
                        <div class='topic-content'><a href='${m_url}' target='_blank'>${m_content}</a></div>
                    </div>
                </section>
		`;
		window.parent.layer.closeAll();
		window.parent.insert_topic(content);
	}
	
	//绑定按钮事件
	function add_action(){

		router.$("#add_model").click(function(){
			bui.load({ 
				url: "/public/static/libs/bui/pages/frame/show.html",
				param:{
					url:"/member.php/"+type+"/content/postnew.html?job=bui",
				}
			});
		});
		
		router.$('.list-hack .add').off("click");
		router.$('.list-hack .add').click(function(){
			var id = $(this).data("id");
			var m_title = $(this).data("title").toString();
			var m_content = $(this).data("content");
			var m_picurl = $(this).data("picurl");
			var m_url = $(this).data("url");
			var mid = $(this).data("mid");
			if(m_picurl==null) m_picurl = '';
			if(uid!=0){
				send_msg(id,m_title,m_content,m_picurl,m_url);
			}else{
				//send_form(id,m_title,m_content,m_picurl,m_url,$(this).data("mid"));
				layer.msg('请稍候...');
				$.get("/member.php/member/quote/get_template.html?type="+type+"&mid="+mid,function(res){
					if(res.code==0){
						var btn_title = ['默认设置'];
						var o = {btn1:function(){
							layer.close(index);
							send_form(id,m_title,m_content,m_picurl,m_url);
						}};
						res.data.forEach(function(rs,i){
							btn_title.push(rs.title);
							var key = 'btn'+(i+2);
							o[key] = function(){
								send_form(id,m_title,m_content,m_picurl,m_url,mid,rs.path);
							}
						});
						o.btn = btn_title;
						var index = layer.confirm('你可以选择一种风格',o);
					}else{
						send_form(id,m_title,m_content,m_picurl,m_url);
					}
				});
			}			
		});

		//添加侧滑菜单
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
	}

	pageview.format_list_data = function(array){
		var str = "";
		array.forEach((rs)=>{
			var content = rs.content.substring(0,20);
			var title = rs.title.substring(0,15);
			if(rs.url==undefined){
				rs.url=`/index.php/${type}/content/show/id/${rs.id}.html`;
			}
			if(rs.mid==undefined){
				rs.mid = '';
			}
			str +=`
			<li class="list-item" data-uid="${rs.id}">
				<div class="bui-btn bui-box">
					<a href="/index.php/${type}/content/show/id/${rs.id}.html" class="iframe"><img class="ring ring-group" src="${rs.picurl}" onerror="this.src='/public/static/images/nopic.png'"/></a>                                
					<div class="span1">
						<h3 class="item-title">
							${title}
						</h3>
						<p class="item-text">${content}</p>
					</div>
					<i class="icon- primary add" data-id="${rs.id}" data-mid="${rs.mid}" data-url="${rs.url}" data-title="${rs.title}" data-picurl="${rs.picurl}" data-content="${rs.content}"><i class="fa fa-plus-circle"></i></i>
				</div>
			</li>
			`;
		});
		return str;
	}

	var getParams = bui.getPageParams();
		getParams.done(function(result){
			if(result.uid==undefined){
				layer.alert('uid参数不存在');
			}else if(result.type==undefined){
				layer.alert('type参数不存在');
			}
			uid = result.uid;
			type = result.type;
		})


    // 控件初始化
    pageview.init();

    // 输出模块
    module.exports = pageview;
})
