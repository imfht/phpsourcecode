//mui('.mui-bar-tab').on('tap', 'a', function(e) {
//	var tabId = this.getAttribute('id');
//	var title = document.getElementById("title");
//	title.innerHTML = this.querySelector('.mui-tab-label').innerHTML;
//});
if (mui.os.plus) {
	mui.plusReady(function () {
		
	});
} else {
	mui.ready(function () {
		
		//读取上次打开的选项卡
		navActive=getCookie('navActive');
		navTitle =getCookie('navTitle');
		tarActive=getCookie('tarActive');
		//初始化清除选中样式式
		mui(".mui-bar-tab a").each(function () {
			this.classList.remove('mui-active');
			barTab = this.getAttribute('id');
			if(navActive==barTab){
			   targetTab = this.getAttribute('href');
				this.classList.add('mui-active');
				document.getElementById("title").innerHTML=navTitle;
			}
		});
		mui("mui-content .mui-control-content").each(function () {
			this.classList.remove('mui-active');	
		});
		if(navActive==null){
		   document.getElementById('crm').classList.add('mui-active');
		   document.getElementById('tabbar-with-crm').classList.add('mui-active');
			document.getElementById("title").innerHTML='CRM';
		}else{
			document.getElementById('tabbar-with-'+navActive).classList.add('mui-active');
		}
		
		//
		chatLoadData();
		
	});
}
mui('.mui-bar-tab').on('tap', 'a', function (e) {
	//更换标题
	var title = document.getElementById("title");
	console.log(this.querySelector('.mui-tab-label').innerHTML);
	title.innerHTML = this.querySelector('.mui-tab-label').innerHTML;
	
	var targetTab = this.getAttribute('href');
	var barTab   = this.getAttribute('id');

	setCookie('navActive',barTab);
	setCookie('tarActive',targetTab);
	setCookie('navTitle',title.innerHTML);

});

mui('#tabbar-with-chat').on('tap', '.notice', function () {
	openNew("crm/notice.html", {
		id: 'ss'
	});
});
mui('#tabbar-with-chat').on('tap', '.message', function () {
	mui.openWindow({
		url: "crm/message.html",
		id: "notice.html"
	});
});

//加载读取消息信息
function chatLoadData() {
	var tab_chat = document.getElementById("chat");
	var bar_chat = document.getElementById("tabbar-with-chat");
	//消息通知
	request("/wap/crm/Message/message_noread_cnt/", {
		
	}, function(json) {
		if(json.statusCode == 200) {
			var msg_cnt=json.one.cnt;	
			bar_chat.querySelector('.message .mui-badge').innerHTML=msg_cnt;
			tab_chat.querySelector('.mui-badge').innerHTML=msg_cnt;
		} 
	});
	//公告
	request("/wap/crm/Notice/notice_noread_cnt/", {
		
	}, function(json) {
		if(json.statusCode == 200) {
			var notice_cnt =json.one.cnt;
			var msg_cnt	  =tab_chat.querySelector('.mui-badge').innerHTML;
			bar_chat.querySelector('.notice .mui-badge').innerHTML=notice_cnt;
			tab_chat.querySelector('.mui-badge').innerHTML=parseInt(notice_cnt)+parseInt(msg_cnt);
		} 
	});

}
