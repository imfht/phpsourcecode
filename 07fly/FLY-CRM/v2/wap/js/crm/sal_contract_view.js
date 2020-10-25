var trace_id;
mui.init();
// 同步调用
if (mui.os.plus) {
	mui.plusReady(function () {
		var extras = mui.getExtras();
		log(extras);
	});
} else {
	var extras = mui.getExtras();
	log("接收参数：" + JSON.stringify(extras));
	trace_id=extras.trace_id;
	loadData();
	
}
////// 异步调用
//mui.getExtras(function(extras){
//    log(extras);
//});


function loadData() {
	log("加载新闻")
	request("/wap/crm/CstTrace/cst_trace_view/", {
		trace_id: trace_id
	}, function(json) {
		if(json.statusCode == 200) {
			//json.data.ReadCount = json.data.ReadCount || 0;
			render("#detail_wrap", "detail_view", json);
			//document.getElementById("body").innerHTML = HTMLDecode(json.data.Body);
			var imgs = document.getElementsByTagName("img");
			for(var i = 0; i < imgs.length; i++) {
				imgs[i].setAttribute("data-preview-src", "");
				imgs[i].setAttribute("data-preview-group", "1");
			}
			//mui.previewImage();
		} else {
			var arr = document.getElementsByClassName("mui-loading-msg");
			for(var i = 0; i < arr.length; i++) {
				arr[i].innerText = "记录不存在";
			}
		}
	});

}