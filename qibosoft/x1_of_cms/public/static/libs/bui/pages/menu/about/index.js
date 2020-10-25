loader.define(function(){
	
	function show(id,vid){
		var url = vid>0 ? "/index.php/cms/wxapp.show/index.html?id="+vid : "/index.php/qun/wxapp.show/index.html?id="+id;
		$.get(url,function(res){
			if(res.code==0){
				router.$(".article-title").html(res.data.title);
				router.$(".article-content").html(res.data.content);
				router.$(".icon-time").html(res.data.time);
				router.$(".icon-comment").html(res.data.replynum);
				router.$(".icon-eye").html(res.data.view);
				if(res.data.start_time>0){
					router.$(".article-from").html('直播开始时间：'+format_time(res.data.start_time));
				}else{
					router.$(".article-from").parent().hide();
				}
			}else{
				layer.alert('内容不存在');
			}
		});
	}

	function format_time(timestamp){
		var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
		Y = date.getFullYear() + '-';
		M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
		D = date.getDate() + ' ';
		h = date.getHours() + ':';
		m = date.getMinutes() + ':';
		s = date.getSeconds();
		return Y+M+D+h+m+s;
	}

	var getParams = bui.getPageParams();
    getParams.done(function(result){
		if(result.id==undefined){
			alert('id参数不存在');
			return ;
		}else if(result.vid==undefined){
			alert('vid参数不存在');
			return ;
		}
		show(result.id,result.vid);
	})
})
