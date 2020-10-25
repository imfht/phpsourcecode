//公共js文件
//api连接前缀
var APP_DOMAIN = 'http://erp.07fly.com/index.php';
//为true输出日志
var debug = true;
var PAY_DOMAIN = ''
	/**
	 * 打印日志
	 */

function log(data) {
	if (debug) {
		if (typeof (data) == "object") {
			console.log(JSON.stringify(data)); //console.log(JSON.stringify(data, null, 4));
		} else {
			console.log(data);
		}
	}
}

/**
 * @description 调试用的时间戳
 * @author suwill
 * @param {none} 不需要参数
 * @example mklog()
 */
function mklog() {
	var date = new Date(); //新建一个事件对象
	var seperator1 = "/"; //日期分隔符
	var seperator2 = ":"; //事件分隔符
	var month = date.getMonth() + 1; //获取月份
	var strDate = date.getDate(); //获取日期
	var ss = date.getSeconds(); //获取秒
	if (month >= 1 && month <= 9) { //判断月份
		month = "0" + month;
	}
	if (strDate >= 0 && strDate <= 9) {
		strDate = "0" + strDate;
	}
	if (ss >= 0 && ss <= 9) {
		ss = "0" + ss;
	}
	var ms = date.getMilliseconds();
	if (ms >= 10 && ms <= 100) {
		ms = '0' + ms;
	} else if (ms >= 0 & ms <= 9) {
		ms = '00' + ms;
	}
	var currentdate = ('2' + date.getYear() - 100) + seperator1 + month + seperator1 + strDate + " " + date.getHours() + seperator2 + date.getMinutes() + ":" + ss + "'" + ms;
	//	var currentdate = date.getHours() + seperator2 + date.getMinutes() + ":" + ss + "'" + ms;

	return currentdate + '|';
}
/**
 * @description 返回所有窗口的艾迪
 * @author suwill
 * @param {none} 不需要参数
 * @example mkwv();
 */
function mkwv() {
	var wvs = plus.webview.all(); //循环显示当前webv
	var t1 = "|debug:当前共有" + wvs.length + "个webview\n";
	var t2 = "";
	for (var i = 0; i < wvs.length; i++) {
		t2 += "|webview" + i + "|id:" + wvs[i].id + "|@url:" + wvs[i].getURL().substr(82) + '\n';
	}
	return t1 + t2;
}

var waitingStyle = {
	style: "black",
	color: "#FF0000",
	background: "rgba(0,0,0,0)",
	loading: {
		icon: "../../images/loading.png",
		display: "inline"
	}
}


/**
 * @description 新开窗口
 * @param {URIString} target  需要打开的页面的地址
 * @param {Object} parm 传递的对象
 * @param {Boolean} autoShow 是否自动显示
 * @example openNew({URIString});
 * */
function openNew(target, parm, autoShow) {
	if (mui.os.plus) {
		var currPageId = plus.webview.currentWebview().id;
	} else {
		var currPageId = target;
	}
	var id = "main.html"; //除了一级目录，其它目录id组成结构为：二级文件夹/页面.html
	if (currPageId != undefined) {
		var sp_xg = target.split("/");
		if (sp_xg.length == 3) //target结构为 ../二级文件夹/页面.html,表示跨文件夹打开页面
		{
			id = sp_xg[1] + "/" + sp_xg[2];
		} else if (sp_xg.length == 2) { //target结构为 二级文件夹/页面.html，表示html下一级目录打开页面
			id = target;
		} else { //同级打开页面，需从currpageid中拿取二级文件夹名
			var curr_sp_xg = currPageId.split("/");
			id = curr_sp_xg[0] + "/" + sp_xg[0];
		}
	}
	var isAutoShow = autoShow || true;
	console.log("currPageId=" + currPageId + " target=" + target + " id=" + id + " parm=" + JSON.stringify(parm) + " isAutoShow=" + isAutoShow);
	mui.openWindow({
		url: target,
		id: id,
		show: {
			autoShow: isAutoShow, //页面loaded事件发生后自动显示，默认为true
			aniShow: 'pop-in',
			duration: 200
		},
		waiting: {
			autoShow: true,
			options: waitingStyle
		},
		extras: parm
	})
}

/**
 * @description 获取数据
 * @param {URIString} method  需要请求数据的接口地址
 * @param {Object} parm 提交的参数
 * */
function request(method, parm, callback, showwait, errcallback, shownetmsg) {
	showwait = showwait == undefined ? false : showwait; //若需要显示等到，传递true
	shownetmsg = shownetmsg == undefined ? true : shownetmsg;
	if (showwait)
		appUI.showWaiting();
	//parm.hmac = md5sign(parm);
	mui.ajax(APP_DOMAIN + method, {
		data: parm,
		dataType: 'json', //要求服务器返回json格式数据
		type: 'POST', //HTTP请求类型，要和服务端对应，要么GET,要么POST
		timeout: 60000, //超时时间设置为6秒；
		beforeSend: function () {
			log(mklog() + '【AJAX:-->】【' + method + '】【P=' + JSON.stringify(parm) + '】');
			setRequestMsg("玩命加载中......");
		},
		success: function (data) {
			//alert(method+data)
			//log(mklog() + '【AJAX:OK!】' + method + '】【响应：' + JSON.stringify(data) + '】');
			if (data && data.statusCode && data.statusCode != undefined) {
				log(mklog() + '【AJAX:OK!】【' + method + '】【合法数据：' + JSON.stringify(data) + '】');
				callback(data);
			} else {
				setRequestMsg("服务器繁忙,请稍后再试");
			}
		},
		error: function (xhr, type, errorThrown) { //失败，打一下失败的类型，主要用于调试和用户体验
			log(mklog() + '【AJAX:ERR!】【' + method + '】错误');
			log(xhr.responseText + " " + xhr.status + " " + xhr.statusText)
			if (showwait)
				appUI.closeWaiting();
			log(xhr.status)
			log(mklog() + '【AJAX:ERR】【' + method + '】错误T:' + type + '|H:' + errorThrown);
			if (type == 'timeout' || type == 'abort') {
				setRequestMsg("请求超时：请检查网络");
				if (shownetmsg)
					mui.toast("请求超时：请检查网络：" + type)
			} else {
				setRequestMsg("服务器累了");
				if (shownetmsg)
					mui.toast("服务器累了：" + type)
			}
			if (errcallback) {
				errcallback();
			}
		},
		complete: function () {
			//setRequestMsg("");
			log(mklog() + '【AJAX:END】【' + method + '】【命令执行完成】');
			if (showwait)
				appUI.closeWaiting();
		}
	}); //ajax end
} //获取数据结束

function setRequestMsg(msg) {
	var arr = mui(".mui-loading-msg");
	if (arr) {
		for (var i = 0; i < arr.length; i++) {
			arr[i].innerText = msg;
		}
	}
}


/**
 * @description 根据模板渲染指定节点
 * @param {NodeSelector} selector 要插入的节点选择器
 * @param {String} tpl 需要渲染模板的名称
 * @param {Object} data 传入的阿贾克斯回来的数据
 * @param {Boolean} type 仅在上拉时为真
 * */
function render(selector, tpl_view, data, type) {
	type = arguments[3] || false;
	log('Render:[D:' + selector + '|M:' + tpl_view + '|T:' + type + '|D:' + JSON.stringify(data).length);
	//log(JSON.stringify(data));
	var elem = document.querySelector(selector);
	//var html = template(tpl, data);
	//使用
	var bt = baidu.template;
	var html = bt(tpl_view, data);
	if (type) {
		elem.innerHTML += html;
	} else {
		elem.innerHTML = html;
	}
}


//获取cookie值
function getCookie(name) {
	//document.cookie.setPath("/");
	var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");   
	if (arr = document.cookie.match(reg)) {     
		return unescape(arr[2]);
	}   
	else {      
		return null;
	}
}
//设置cookie值
function setCookie(name, value) {
	//document.cookie.setPath("/");
	   
	var hour = 8;   
	var exp = new Date();   
	exp.setTime(exp.getTime() + hour * 60 * 60 * 1000);   
	document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString() + ";path=/";
}
