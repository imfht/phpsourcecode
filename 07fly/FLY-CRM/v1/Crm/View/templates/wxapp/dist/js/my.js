$(function () {
	var ACT='http://192.168.0.107:5002/Crm/index.php';
/*	
//客户列表无滚动加载数据
  $(document).on("pageInit", "#customer_show_list", function(e, id, page) {
  //加载flag
  var loading = false;
    // 最多可加载的条目
    function addItems(pageNum, numPerPage) {
      $.ajax({
          type: "POST",//方法类型
          dataType: "json",//预期服务器返回的数据类型
          data: {pageNum: pageNum, numPerPage: numPerPage},  //异步返回给data
          url: ACT+'/wxapp/WxCustomer/customer/',  //接口地址
          timeout: 300,
          success: function (data) {
          console.log(data);
           var datalist=data.list;
            var html = '';
          for(var i=0;i<datalist.length;i++){
             html += '<li> <a href="'+ACT+'/wxapp/WxCustomer/customer_show_one/cusID/'+datalist[i].id+'/" class="item-link item-content external">';
             html += ' <div class="item-inner">';
             html += '   <div class="item-title-row">';
             html += '     <div class="item-title">'+datalist[i].name+'</div>';
             html += '     <div class="item-after">'+datalist[i].adt+'</div>';
             html += '   </div>';
             html += '   <div class="item-subtitle">'+datalist[i].linkman+'</div>';
             html += '   <div class="item-text">电话：'+datalist[i].mobile+'</div>';
             html += ' </div>';
             html += ' </a> ';
             html += '</li>';
          }
          // 添加新条目
               $('.infinite-scroll .list-container').append(html);
               $("#data-currentPage").val(parseInt(data.currentPage));
               $("#data-totalCount").val(parseInt(data.totalCount));
               $("#data-numPerPage").val(parseInt(data.numPerPage));
          }
        });
     }
      //预先加载第一页内容
      addItems(1, 10);
      // 注册'infinite'事件处理函数
      $(document).on('infinite', '.infinite-scroll',function() {
        // 如果正在加载，则退出
        if (loading) return;
         // 设置flag
         loading = true;
         // 模拟1s的加载过程
         setTimeout(function() {
              // 重置加载flag
            loading = false;
            var currentPage	= Number($("#data-currentPage").val());
            var totalCount	 = Number($("#data-totalCount").val());
            var numPerPage	 = Number($("#data-numPerPage").val());
            var maxPage = Math.ceil(totalCount/numPerPage);
            if (currentPage >= maxPage) {
              // 加载完毕，则注销无限加载事件，以防不必要的加载
              $.detachInfiniteScroll($('.infinite-scroll'));
              // 删除加载提示符
              $('.infinite-scroll-preloader').remove();
              return;
            }
            // 添加新条目
            addItems(currentPage+1, numPerPage);
            // 更新最后加载的序号
            //lastIndex = $('.list-container li').length;
             //容器发生改变,如果是js滚动，需要刷新滚动
            //$.refreshScroller();
          }, 1000);
      });//end infinite 
  });	//end customer_show_list
	*/
 /* $(document).on("pageInit", "#customer_show_one", function(e, id, page) {
    $(page).find(".pull-to-refresh-content").on('refresh', function(e) {
      // 2s timeout
      var $this = $(this);
      setTimeout(function() {

        $this.find('.content-block').prepend("<p>New Content......</p>");
        // Done
        $.pullToRefreshDone($this);
      }, 2000);
    });
    $(page).find(".infinite-scroll").on('infinite', function(e) {
      // 2s timeout
      var $this = $(this);
      if($this.data("loading")) return;
      $this.data("loading", 1);
      setTimeout(function() {
        $this.find('.content-block').append("<p>New Content......</p><p>New Content......</p><p>New Content......</p>");
        $this.data("loading", 0);
      }, 2000);
    });
  });*/
  
  
  
  
  
  
	
	 $.init();
});