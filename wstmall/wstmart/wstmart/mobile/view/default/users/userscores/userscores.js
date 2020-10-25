jQuery.noConflict();
// 获取订单列表
function getScoreList(){
  $('#Load').show();
    loading = true;
    var param = {};
    param.type = $('#type').val() || -1;
    param.pagesize = 10;
    param.page = Number( $('#currPage').val() ) + 1;
    $.post(WST.U('mobile/userscores/pageQuery'), param, function(data){
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
  getScoreList();
  WST.initFooter('user');
  // 弹出层
  var h = WST.pageHeight();
  $('#frame .content').css('overflow-y','scroll').css('height',h-48);
  $("#frame").css('right','-100%');

    $(window).scroll(function(){  
        if (loading) return;
        if ((5 + $(window).scrollTop()) >= ($(document).height() - screen.height)) {
            currPage = Number( $('#currPage').val() );
            totalPage = Number( $('#totalPage').val() );
            if( totalPage > 0 && currPage < totalPage ){
            	getScoreList();
            }
        }
    });
});


//弹框
function dataShow(){
    jQuery('#cover').attr("onclick","javascript:dataHide();").show();
    jQuery('#frame').animate({"right": 0}, 500);
}
function dataHide(){
    var dataHeight = $("#frame").css('height');
    var dataWidth = $("#frame").css('width');
    jQuery('#frame').animate({'right': '-'+dataWidth}, 500);
    jQuery('#cover').hide();
}