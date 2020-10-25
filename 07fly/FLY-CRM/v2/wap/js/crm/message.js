var pageNum = 1;
var pageTotal = 1;
var appPage=mui('#refreshContainer').pullRefresh();
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

	request("/wap/crm/Message/message_show_json/", {
		pageNum: pageNum
	}, function (data) {
		mui('#refreshContainer').pullRefresh().endPullupToRefresh(true);
		if(data.list != '') {
			pageTotal = data.totalCount%data.pageSize!=0?parseInt(data.totalCount/data.pageSize)+1:data.totalCount/data.pageSize;		
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
mui('.mui-content').on('tap', '.mui-table-view-cell', function () {
	var message_id = this.getAttribute('data-id');
	openNew("message_view.html", {
		message_id: message_id
	});
});
