/**
 * 聊天对话模板
 * 默认模块名: pages/chat/chat
 * @return {[object]}  [ 返回一个对象 ]
 */
loader.define(function(require,exports,module) {

    var pageview = {};
	var tj_type;
	var msg_scroll = true;
	var show_msg_page  = 1;
	var tongjiMsgUrl = "/index.php/tongji/wxapp.feed/index.html?rows=20&uid="+my_uid+"page=";
	

    // 模块初始化定义
    pageview.init = function () {
        this.bind();
		var that = $("#tongji_win");
		that.parent().scroll(function () {
			var h = that.height()-that.parent().height()-that.parent().scrollTop();
			//console.log(h);
			if( h<300 && msg_scroll==true){
				msg_scroll = false;
				get_tongji_msg(tj_type);
			}
		})
		console.log(chat_timer);
    }

    pageview.bind = function () {            
    }
	
	//加载统计动态的详细内容数据
	function get_tongji_msg(type){
		if(show_msg_page==1){
			$.get(tongjiCountUrl+"?set_read=1&type="+type,function(res){});//把新数据标志为已读
			layer.msg("数据加载中,请稍候...");
		}		
		$.get(tongjiMsgUrl + show_msg_page + "&type="+type,function(res){
			if(res.code==0){
				layer.closeAll();
				var that = $('#tongji_win');
				if(res.data.length<1){
					if(show_msg_page==1){
						//that.parent().scrollTop(0)
						layer.msg("没有记录！",{time:1000});
					}else{
						layer.msg("已经显示完了！",{time:500});
					}		
				}else{
					if(show_msg_page==1){
						that.html( format_data(res.data) );
						//that.parent().scrollTop(1000)
					}else{
						that.append( format_data(res.data) );
						//that.parent().scrollTop(50);
					}     
					show_msg_page++;
					msg_scroll = true;
				}
			}
		});
	}
	
	function format_data(array){
		var str_o = {};
		var str = '';
		array.forEach((rs)=>{
			if( rs.type=='visit'||rs.type=='fans' ){
				str_o.type = rs.time;
				str_o.c = ' chat-content-topic ';
				if(rs.type=='visit'){
					str_o.content = `${rs.time} 访问了你的空间 `;
				}else{
					str_o.content = `${rs.time} 关注了你 `;
				}
			}else{
				str_o.type = rs.create_time;
				str_o.c = ' ';
				var alt = rs.topic.title.replace(/<.*?>/g,"").substring(0,10);
				str_o.content = `<i>${rs.type_name}：</i> ${rs.topic.title}
							 <a href="${rs.topic.url}" class="more iframe" title="${alt}...">详情</a>`;
			}
			str += `
			<div class="bui-box-center">
                <div class="time">${str_o.type}</div>
            </div>
			<div class="bui-box-align-top chat-target">
                <div class="chat-icon"><a href="/member.php/home/${rs.from_uid}.html"  class="iframe"><img src="${rs.from_icon}"  onerror="this.src='/public/static/images/noface.png'"></a></div>
                <div class="span1">
                    <div class="chat-content bui-arrow-left ${str_o.c}">
						<span class="name bui-btn" href="/public/static/libs/bui/pages/chat/chat.html?uid=${rs.from_uid}">${rs.from_username}</span>
						${str_o.content}
					</div>
                </div>
            </div>
			`;
		});
		return str;
	}



	var getParams = bui.getPageParams();
    getParams.done(function(result){
		tj_type = result.type;
        console.log(tj_type);
		show_msg_page = 1;			//重新恢复第一页
		msg_scroll = true;			//恢复可以使用滚动条
		get_tongji_msg(tj_type);	//加载相应用户的记录
    })
	

    // 初始化
    pageview.init();

    // 输出模块
    return pageview;
})
