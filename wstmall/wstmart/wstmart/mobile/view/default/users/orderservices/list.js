$(function(){
    pagequery();
})
function goBack(){
    location.href = WST.U('mobile/users/index');
}
function pagequery(){
    $('#Load').show();
      loading = true;
      var param = {};
      param.pagesize = 10;
      param.page = Number( $('#currPage').val() ) + 1;
      $.post(WST.U('mobile/orderservices/pagequery'), param, function(data){
          var json = WST.toJson(data);
          var html = '';
          if(json && json.data && json.data.length>0){
            var gettpl = document.getElementById('oslist').innerHTML;
            laytpl(gettpl).render(json.data, function(html){
              $('#os-main').append(html);
            });
            $('#currPage').val(json.current_page);
            $('#totalPage').val(json.last_page);
          }else{
              html += '<div class="wst-prompt-icon"><img src="'+ window.conf.MOBILE +'/img/no_data.png"></div>';
              html += '<div class="wst-prompt-info">';
              html += '<p>暂无数据</p>';
              html += '</div>';
              $('#os-main').html(html);
          }
          loading = false;
          $('#Load').hide();
          echo.init();//图片懒加载
      });
  }
function viewDetail(id){
    var url = WST.U('mobile/orderservices/detail',{id:id});
    location.href = url;
}
//  隐藏对话框
function hideDialog(id){
    $(id).dialog("hide");
  }
  
function receive(id){
    hideDialog('#wst-di-prompt');
    doConfirm({
        id:id,
        isUserAccept:1,
        userRejectType:0
    });
}

function doConfirm(postData){
    $.post(WST.U('mobile/orderservices/userReceive'),postData,function(res){
        var json = WST.toJson(res);
        WST.msg(json.msg);
        if(json.status==1){
            // 返回列表页
            location.href = WST.U('mobile/orderservices/oslist');
        }
    })
}


// 拒收
function showRejectBox(event){
    $("#wst-event3").attr("onclick","javascript:"+event);
    $("#rejectBox").dialog("show");
}
function changeRejectType(v){
    if (v == 10000) {
        $('#rejectTr').show();
    } else {
        $('#rejectTr').hide();
    }
}
function reject(id){
    var postData = {
          id:id,
          isUserAccept:-1,
          userRejectType:$('#reject').val(),
          userRejectOther:$.trim($('#rejectContent').val())
      }
      if(postData.userRejectType=='10000' && postData.userRejectOther.length==0){
          return WST.msg('请输入拒收原因');
      }
      doConfirm(postData);
}