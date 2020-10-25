jQuery.noConflict();
// 查看大图
function gViewImg(index,obj){
  var pswpElement = document.querySelectorAll('.pswp')[0];
  var gallery = $(obj).parent().data("gallery");
  if(gallery!=''){
      gallery = gallery.split(',').map(function(imgUrl,i){
        imgUrl = WST.conf.RESOURCE_PATH+"/"+imgUrl;
        var _obj = { src:imgUrl, w:0, h:0 };
        return _obj;
      })
    }
  // build items array
  if(!gallery || gallery.length==0)return;
  // define options (if needed)
  var options = {
    index: index
  };
  // Initializes and opens PhotoSwipe
  var gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, gallery, options);
  gallery.init();
  gallery.listen('imageLoadComplete', function(index, item) { 
        if (item.h < 1 || item.w < 1) {
      var img = new Image();
      img.onload = function(){
      item.w = img.width;
      item.h = img.height;
      gallery.invalidateCurrItems()
      gallery.updateSize(true);
      }
      img.src = item.src
    }


    });  

  }
// 获取订单列表
function getComplainList(){
  $('#Load').show();
    loading = true;
    var param = {};
    param.pagesize = 10;
    param.page = Number( $('#currPage').val() ) + 1;
    $.post(WST.U('mobile/orderComplains/complainByPage'), param, function(data){
        var json = WST.toJson(data.data);
        var html = '';
        if(json && json.data && json.data.length>0){
          var gettpl = document.getElementById('complainList').innerHTML;
          laytpl(gettpl).render(json.data, function(html){
            $('#complain-list').append(html);
          });

          $('#currPage').val(json.current_page);
          $('#totalPage').val(json.last_page);
        }else{
          html += '<div class="wst-prompt-icon"><img src="'+ window.conf.MOBILE +'/img/no_data.png"></div>';
          html += '<div class="wst-prompt-info">';
          html += '<p>暂无投诉信息</p>';
          html += '</div>';
          $('#complain-list').html(html);
        }
        loading = false;
        $('#Load').hide();
        echo.init();//图片懒加载
    });
}
var currPage = totalPage = 0;
var loading = false;
$(document).ready(function(){
  getComplainList();
  WST.initFooter('user');
  // 弹出层
   $("#frame").css('top',0);

    $(window).scroll(function(){  
        if (loading) return;
        if ((5 + $(window).scrollTop()) >= ($(document).height() - screen.height)) {
            currPage = Number( $('#currPage').val() );
            totalPage = Number( $('#totalPage').val() );
            if( totalPage > 0 && currPage < totalPage ){
            	getComplainList();
            }
        }
    });
});


//弹框
function dataShow(){
    jQuery('#cover').attr("onclick","javascript:dataHide();").show();
    jQuery('#frame').animate({"right": 0}, 500);
    $('#com-list').hide();
}
function dataHide(){
    $('#com-list').show();
    jQuery('#frame').animate({'right': '-100%'}, 500);
    jQuery('#cover').hide();
}

function complainDetail(cId){
  $.post(WST.U('mobile/orderComplains/getComplainDetail'),{'id':cId},function(data){
    var json = WST.toJson(data);
    if(json){
      var gettpl = document.getElementById('complainDetail').innerHTML;
          laytpl(gettpl).render(json, function(html){
            // 写入数据
            $('#complainDetailBox').html(html);
            // 设置滚动条
            var screenH = WST.pageHeight();
            var titleH = $('#frame').find('.title').height();
            var contentH = $('#complainDetailBox').height();
            if(screenH-titleH < contentH){
              $('#complainDetailBox').css('height',screenH-titleH);
            }
            // 展示弹出层
            dataShow();
          });
    }

  })
    
}