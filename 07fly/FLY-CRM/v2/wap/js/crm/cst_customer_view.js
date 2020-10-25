var customer_id;
var pageNum = 1;
var pageTotal = 1;
var orderField = "";
var orderDirection = "";
var searchKeyword = "";
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
	customer_id = extras.customer_id;
	loadData();

}

//阻尼系数
var deceleration = mui.os.ios?0.003:0.0009;
mui('.mui-scroll-wrapper').scroll({
    bounce: false,
    indicators: true, //是否显示滚动条
    deceleration:deceleration
});
 //因为是四个不同的区块，每一个页数不一样，所以要存不同的页数
var pageNum=null;
var pageNum1=1;
var pageNum2=1;
var pageNum3=1;
var pageNum4=1;

mui('.mui-slider').slider().stopped = true;

(function($) {
	$.ready(function() {
		//循环初始化所有下拉刷新，上拉加载。
		$.each(document.querySelectorAll('.mui-slider-group .mui-scroll'), function(index, pullRefreshEl) {
			$(pullRefreshEl).pullToRefresh({
				up: {
					auto: true, //自动执行一次上拉加载，可选；
					show: false, //显示底部上拉加载提示信息，可选；      
					contentrefresh: '正在加载...', //上拉进行中提示信息
					contentnomore: '没有更多数据了', //上拉无更多信息时提示信息
					callback:function () {
						var self = this;                  
						setTimeout(function () {
						//获取四个不同的区块显示的位置
							var ul 	=self.element.querySelector('.mui-table-view');
							var tpl	=ul.getAttribute('data-tplid');
							var pageNum=ul.getAttribute('data-pagenum');

							var bar	=document.querySelectorAll(".mui-slider .mui-control-item")[index];
							var url	=bar.getAttribute('data-url');
							loadDataList(pageNum, url,ul,tpl,self);

						}, 1000);
					}
				}
			});
		}); 
	}); 
})(mui);



/**
 * @description 异步加载配合 baidu template.js共用
 * 这是一个方法描述
 * @param {String} url 请求地址(不带域名)
 * @param {Object} ul 关联的容器
 * @param {String} temid template.js对应的ID
 * @param {String} catid 请求参数
 * @param {String} page 请求的起始页
 * @param {String} pageNum加载页数
 * @example 
 * ajax_load('/index.php?g=home&m=index',ul,'index_host_list','1,2',1,10);
 */
function loadDataList(pageNum, url,ul,temid,self) {
	if (pageNum > pageTotal) {
		self.endPullUpToRefresh(true);
		return;
	}
	request(url, {
		pageNum: pageNum,
		orderField: orderField,
		orderDirection: orderDirection,
		searchKeyword: searchKeyword
	}, function (data) {
		if (data.list != '') {
			pageTotal = data.totalCount % data.pageSize != 0 ? parseInt(data.totalCount / data.pageSize) + 1 : data.totalCount / data.pageSize;
			//使用
			var bt = baidu.template;
			var html = bt(temid, data);
			if(pageNum>1){
			   ul.innerHTML += html;
			}else{
				ul.innerHTML =html;
			}
			if(ul && ul.getAttribute('data-pagenum') != '') {
        ul.setAttribute('data-pagenum', parseInt(pageNum) + 1);
      }				
			self.endPullUpToRefresh();
		}else{
			self.endPullUpToRefresh(true);
		}
	}, false, function () {
		self.endPullUpToRefresh(true);
	});
}

//加载客户信息
function loadData() {
	request("/wap/crm/CstCustomer/cst_customer_detail/", {
		customer_id: customer_id
	}, function (json) {
		if (json.statusCode == 200) {
			render("#detail_wrap", "detail_view", json);
		} else {
			var arr = document.getElementsByClassName("mui-loading-msg");
			for (var i = 0; i < arr.length; i++) {
				arr[i].innerText = "记录不存在";
			}
		}
	});

}
