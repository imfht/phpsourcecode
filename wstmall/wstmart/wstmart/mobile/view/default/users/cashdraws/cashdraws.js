jQuery.noConflict();
// 获取提现记录
function getCashDraws(){
  $('#Load').show();
    loading = true;
    var param = {};
    param.pagesize = 10;
    param.page = Number( $('#currPage').val() ) + 1;
    $.post(WST.U('mobile/cashdraws/pageQuery'), param, function(data){
        var json = WST.toJson(data.data);
        var html = '';
        if(json && json.data && json.data.length>0){
          var gettpl = document.getElementById('scoreList').innerHTML;
          laytpl(gettpl).render(json.data, function(html){
            $('#score-list').append(html);
          });

          $('#currPage').val(json.current_page);
          $('#totalPage').val(json.last_page);
        }else{
           html += '<div class="wst-prompt-icon"><img src="'+ window.conf.MOBILE +'/img/no_data.png"></div>';
  	       html += '<div class="wst-prompt-info">';
  	       html += '<p>暂无数据</p>';
  	       html += '</div>';
          $('#score-list').html(html);
        }
        loading = false;
        $('#Load').hide();
        echo.init();//图片懒加载
    });
}
var currPage = totalPage = 0;
var loading = false;
$(document).ready(function(){
  getCashDraws();
  WST.initFooter('user');
    $(window).scroll(function(){  
        if (loading) return;
        if ((5 + $(window).scrollTop()) >= ($(document).height() - screen.height)) {
            currPage = Number( $('#currPage').val() );
            totalPage = Number( $('#totalPage').val() );
            if( totalPage > 0 && currPage < totalPage ){
            	getCashDraws();
            }
        }
    });
});
