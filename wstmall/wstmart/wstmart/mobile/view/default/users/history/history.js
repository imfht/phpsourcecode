// 获取浏览记录
function getHistory(){
  $('#Load').show();
    loading = true;
    var param = {};
    param.pagesize = 10;
    param.page = Number( $('#currPage').val() ) + 1;
    $.post(WST.U('mobile/goods/historyQuery'), param, function(data){
        var json = WST.toJson(data);
        var html = '';
        if(json && json.data && json.data.length>0){
           var gettpl = document.getElementById('list').innerHTML;
          laytpl(gettpl).render(json.data, function(html){
            $('#listBox').append(html);
          });
          $('#currPage').val(data.current_page);
          $('#totalPage').val(data.last_page);
        }else{
          html += '<div class="apply" style="background: #FFFFFF" >';
          html += '<div class="apply-box"><img class="apply-after" src="'+ window.conf.MOBILE +'/img/nothing-history.png"></div>';
          html += '<div class="apply-title2">暂无浏览历史</div>';
          html += '<div onclick="javascript:WST.intoIndex();" class="apply-btn">去逛逛</div></div>';
          $('body').css('background', '#FFFFFF')
          $('#listBox').html(html);
        }
        WST.imgAdapt('j-imgAdapt');
        loading = false;
        $('#Load').hide();
        echo.init();//图片懒加载
    });
}
var currPage = totalPage = 0;
var loading = false;
$(document).ready(function(){
    // WST.initFooter();
    getHistory();
    $(window).scroll(function(){  
        if (loading) return;
        if ((5 + $(window).scrollTop()) >= ($(document).height() - screen.height)) {
            currPage = Number( $('#currPage').val() );
            totalPage = Number( $('#totalPage').val() );
            if( totalPage > 0 && currPage < totalPage ){
            	getHistory();
            }
        }
    });
});

