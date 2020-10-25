var pageNum = 1;
var pageTotal = 1;
var orderField = "";
var orderDirection = "";
var searchKeyword = "";
mui.init({
	pullRefresh: {
		container: '#refreshContainer',
		down: { //下拉刷新
			callback: pulldownRefresh,
			style: mui.os.android ? "circle" : "default"
				//auto: true
		},
		up: { //上拉加载
			contentinit: '',
			contentrefresh: '正在加载...',
			contentnomore: '没有更多了',
			callback: pullupRefresh,
		}
	},
});


loadData();

//下拉刷新具体业务实现
function pulldownRefresh() {
	//重置页码
	pageNum = 1;
	loadData();
}

// 上拉加载具体业务实现
function pullupRefresh() {
	loadData(true);
}

//数据加载
function loadData(isnextpage, isreload) {
	if (isnextpage) { //加载下一页
		pageNum++;
	} else if (isreload) { //重新加载当前页
		pageNum = curr_pageNum;
	} else if (pageNum == 0) {
		pageNum = 1; //未加载过
	} else {
		pageNum = 1; //默认加载第一页
	}
	var showload = pageNum == 1;
	var isappend = pageNum > 1 ? true : false;
	log(pageNum + "," + pageTotal + "," + showload + "," + isappend)
	if (pageNum > pageTotal) {
		mui('#refreshContainer').pullRefresh().endPullupToRefresh(true);
		return;
	}

	request("/wap/crm/CstService/cst_service_json/", {
		pageNum: pageNum,
		orderField: orderField,
		orderDirection: orderDirection,
		searchKeyword: searchKeyword
	}, function (data) {
		mui('#refreshContainer').pullRefresh().endPullupToRefresh(true);
		if (data.list != '') {
			pageTotal = data.totalCount % data.pageSize != 0 ? parseInt(data.totalCount / data.pageSize) + 1 : data.totalCount / data.pageSize;
			render("#list_wrap", "list_view", data, isappend);
			mui('#refreshContainer').pullRefresh().refresh(true);
		} else {
			mui('#refreshContainer').pullRefresh().endPullupToRefresh(true);
		}
	}, false, function () {
		//mui('#refreshContainer').pullRefresh().endPullupToRefresh(true);
		mui('#refreshContainer').pullRefresh().endPulldownToRefresh(true);;
	});

}

//点击弹出详细页
mui('#list_wrap').on('tap', '.mui-table-view-cell', function () {
	var service_id = this.getAttribute('data-id');
	openNew("cst_service_view.html", {
		service_id: service_id
	});
});
//点击添加事件
mui('.mui-bar-nav').on('tap', '.mui-icon-plus', function () {
	var customer_id = this.getAttribute('data-id');
	openNew("cst_service_view.html", {
		customer_id: customer_id
	});
});

//点击弹出详细页
mui('#topPopover').on('tap', '.mui-table-view-cell', function () {
	mui("#topPopover .mui-table-view-cell").each(function () {
		this.classList.remove('on');
	});
	orderField = this.getAttribute('data-orderField');
	orderDirection = this.getAttribute('data-orderDirection');
	log('orderField=' + orderField + ';orderDirection=' + orderDirection + '');
	this.classList.add('on');
	mui('#topPopover').popover('hide');
	//重置页码
	pageNum = 1;
	loadData();
	//	openNew("customer_view.html", {
	//		customer_id: customer_id
	//	});
});
//搜索功能
document.getElementById("searchInput").addEventListener("keypress", function (event) {
	if (event.keyCode == "13") {
		document.activeElement.blur(); //收起虚拟键盘
		searchKeyword = document.getElementById('searchInput').value;
		//重置页码
		pageNum = 1;
		loadData();
		event.preventDefault(); // 阻止默认事件---阻止页面刷新
	}
});	
