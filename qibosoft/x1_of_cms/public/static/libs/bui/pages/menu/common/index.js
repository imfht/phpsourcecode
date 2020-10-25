loader.define(function(){

	 function list_content(sys,mid,uid){
		var uiList = bui.list({
			id: "#scrollList",
			url: "/index.php/"+sys+"/wxapp.index/listbyuid.html?uid="+uid+"&mid="+mid,
			pageSize: 5,
			data: {},
			//如果分页的字段名不一样,通过field重新定义
			field: {
				page: "page",
				size: "rows",
				data: "data"
			},
			callback: function(e) {
				// e.target 为你当前点击的元素
				// $(e.target).closest(".bui-btn") 可以找到你当前点击的一整行,可以把一些属性放这里
				var url = $(e.target).closest(".bui-btn").data("url");
				window.parent.bui.load({ 
					url: "/public/static/libs/bui/pages/frame/show.html",
					param:{
						url:url,
					}
				});
			},
			template: function(data) {
				var html = "";
				data.map(function(el, index) {
					// 演示传参,标准JSON才能转换
					var param = {"id":index,"title":el.title};
					var paramStr = JSON.stringify(param);

					// 处理角标状态
					var sub = '',
						subClass = '';
					switch (el.status) {
						case 1:
							sub = '已审';
							subClass = sys=='bbs'?'primary-reverse':'bui-sub';
							break;
						case 2:
							sub = '推荐';
							subClass = sys=='bbs'?'warning-reverse':'bui-sub danger';
							break;
						default:
							sub = '';
							subClass = '';
							break;
					}
					el.title = (el.title).substring(0,34);
					var url = "/index.php/"+sys+"/content/show/id/"+el.id+".html";
					if(sys=='bbs'){
						html += `<li class="bui-btn" data-url="${url}">
								<h3 class="item-title" style="font-size:.30rem;"><span class="${subClass}">【${sub}】</span>${el.title}</h3>
								<p class="item-text">${el.time}</p>
							</li>`
					}else{
						el.content = (el.content).substring(0,15);
						html += `<li class="bui-btn bui-box" data-url="${url}">
							<div class="bui-thumbnail ${subClass}" data-sub="${sub}" ><img src="${el.picurl}" onerror="this.src='/public/static/images/nopic.png'"></div>
							<div class="span1">
								<h3 class="item-title" style="font-size:.30rem;">${el.title}</h3>
								<p class="item-text">${el.time}</p>
								<p class="item-text">${el.content}</p>
							</div>
							<span class="price">`+(el.price!=undefined?`<i>￥</i>${el.price}`:`<i>浏览</i> ${el.view}`)+`</span>
						</li>`
					}					
				});
				return html;
			}
		});
	 }


	var getParams = bui.getPageParams();
    getParams.done(function(result){
		if(result.sys==undefined){
			alert('sys参数不存在');
			return ;
		}else if(result.mid==undefined){
			alert('mid参数不存在');
			return ;
		}else if(result.q_uid==undefined){
			alert('q_uid参数不存在');
			return ;
		}
		list_content(result.sys,result.mid,result.q_uid);
	})
})
