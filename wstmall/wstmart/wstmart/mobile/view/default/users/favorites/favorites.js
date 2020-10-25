//点击编辑框
function showRadio(num) {
  if (num == '1') {
    $('.edit-radio').show();
    $('.edit-not').hide();    
    $('.good-item').css('padding-left', '0.3rem');
  } else {
    $('.edit-radio').hide();
    $('.edit-not').show();
    $('.good-item').css('padding-left', '0rem');
  }

}

// 获取关注的商品
function getFavorites(){
  $('#Load').show();
    loading = true;
    var param = {};
    param.id = $('#catId').val();
    param.condition = $('#condition').val();
    param.desc = $('#desc').val();
    param.keyword = $('#searchKey').val();
    param.pagesize = 10;
    param.page = Number( $('#currPage').val() ) + 1;
    $.post(WST.U('mobile/favorites/listGoodsQuery'), param, function(data){
        var json = WST.toJson(data.data);
        var html = '';
        if(json && json.data && json.data.length>0){
           var gettpl = document.getElementById('fGoods').innerHTML;
          laytpl(gettpl).render(json.data, function(html){
            $('#goods-list').html(html);
          });
          $('#currPage').val(data.current_page);
          $('#totalPage').val(data.last_page);
        }else{

          $('body').css('background', '#FFFFFF')
          html += '<div style="text-align:center"><div class="wst-prompt-icon"><img style="width:1.62rem;height:1.27rem" src="'+ window.conf.MOBILE +'/img/nothing-follow.png"></div><div class="wst-prompt-info"><p>暂无关注</p></div></div>';
          $('#goods-list').html(html);
          
        }
        WST.imgAdapt('j-imgAdapt');
        loading = false;
        $('#Load').hide();
        echo.init();//图片懒加载
    });
}
// 全选
function checkAll(obj){
  var chk = $(obj).attr('checked');
  $('.active').each(function(k,v){
    $(this).prop('checked',chk);
  });
}
function toCancel(){
  var gids = new Array();
  $('.active').each(function(k,v){
    if($(this).attr('checked')){
      gids.push($(this).attr('gid'));
    }
  });
  gids = gids.join(',');
  if(gids==''){
    WST.msg('请先选择商品','info');
    return;
  }
  WST.dialog('确认要取消关注吗','cancelFavorite()');
}
// 取消关注
function cancelFavorite(){
  WST.dialogHide('prompt');
  var gids = new Array();
  $('.active').each(function(k,v){
    if($(this).attr('checked')){
      gids.push($(this).attr('gid'));
    }
  });
  gids = gids.join(',');
  if(gids==''){
    WST.msg('请先选择商品','info');
    return;
  }
  $.post(WST.U('mobile/favorites/cancel'),{id:gids,type:0},function(data){
    var json = WST.toJson(data);
    if(json.status==1){
      $('#currPage').val('0')
      getFavorites();
    }else{
      WST.msg(json.msg,'info');
    }
  });

}

var currPage = totalPage = 0;
var loading = false;
$(document).ready(function(){
	getFavorites();
    $(window).scroll(function(){  
        if (loading) return;
        if ((5 + $(window).scrollTop()) >= ($(document).height() - screen.height)) {
            currPage = Number( $('#currPage').val() );
            totalPage = Number( $('#totalPage').val() );
            if( totalPage > 0 && currPage < totalPage ){
            	getFavorites();
            }
        }
    });
});



function addCart(goodsId){
  $.post(WST.U('mobile/carts/addCart'),{goodsId:goodsId,buyNum:1},function(data,textStatus){
       var json = WST.toJson(data);
       if(json.status==1){
         WST.msg(json.msg,'success');
       }else{
         WST.msg(json.msg,'info');
       }
  });
}