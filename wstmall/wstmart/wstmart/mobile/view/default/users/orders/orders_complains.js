jQuery.noConflict();
$(function(){
  getOrderDetail();
  userComplainInit();
})
function getOrderDetail(){
  var oid = $('#oId').val();
  $.post(WST.U('mobile/orders/getDetail'),{id:oid},function(data){
      var json = WST.toJson(data);
      if(json.status!=-1){
        var gettpl1 = document.getElementById('detailBox').innerHTML;
          laytpl(gettpl1).render(json, function(html){
            $('#orderDetail').html(html);
          });
      }else{
        WST.msg(json.msg,'info');
      }
      WST.imgAdapt('j-imgAdapt');
  });

}

/*********************** 投诉类型 ****************************/
//弹框
function dataShow(){
    jQuery('#cover').attr("onclick","javascript:dataHide(0);").show();
    jQuery('#frame').animate({"bottom": 0}, 500);
}
function dataHide(type){
	if(type==1){
	    var flag=false,chk;
	    $('.active').each(function(k,v){
	      if($(this).prop('checked')){
	        flag = true
	        $('#complainType').val($(this).val());
	        chk = $(this).parent().parent().find('.name').html();
	      }
	    });
	    if(!flag){
	      WST.msg('请选择投诉类型');
	      return;
	    }
	    $('#complainText span').html(chk);
	}
    jQuery('#frame').animate({'bottom': '-100%'}, 500);
    jQuery('#cover').hide();
}

function uploadFile(){
  $('#filePicker').trigger("click");
}


function saveCom(oId){
  // 验证投诉类型
  var type = $('#complainType').val();
  if(type==''){
    dataShow();
    WST.msg('请选择投诉类型','info');
    return;
  }
  var complainContent = $.trim($('#complain').val());
  if(complainContent==''){
    WST.msg('投诉内容不能为空','info');
    return;
  }
  var param = {};
  param.orderId = oId;
  param.complainType = type;
  param.complainContent = complainContent;

  var imgs = [];
  //  是否有上传附件
  $('.imgSrc').each(function(k,v){
    imgs.push($(this).attr('v'));
  })
  imgs = imgs.join(',');
  if(imgs!='')
    param.complainAnnex = imgs;

  $.post(WST.U('mobile/ordercomplains/saveComplain'),param,function(data){
    var json = WST.toJson(data);
    if(data.status){
      WST.msg('投诉成功请留意商城消息','success');
      setTimeout(function(){location.href=WST.U('mobile/ordercomplains/index')},1000);
    }else{
      WST.msg(json.msg,'info');
    }
  });

}
/*************** 上传图片 *****************/
function userComplainInit(){
   var uploader =WST.upload({
        pick:'#filePicker',
        formData: {dir:'complains',isThumb:1},
        fileNumLimit:5,
        accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
        callback:function(f,file){
          var json = WST.toJson(f);
          if(json.status==1){
          var tdiv = $("<div style='position: relative'>"+
                       "<img class='imgSrc' src='"+WST.conf.RESOURCE_PATH+"/"+json.savePath+json.thumb+"' v='"+json.savePath+json.name+"'></div>");
          var btn = $('<div class="del-btn"><span class="upload-icon-delete"></span></div>');
          tdiv.append(btn);
          $('#filePicker').before(tdiv);
          btn.on('click','span',function(){
            uploader.removeFile(file);
            $(this).parent().parent().remove();
            uploader.refresh();
          });
          }else{
            WST.msg(json.msg,{icon:2});
          }
      },
      progress:function(rate){
          $('#uploadMsg').show().html('已上传'+rate+"%");
      }
    });
}
