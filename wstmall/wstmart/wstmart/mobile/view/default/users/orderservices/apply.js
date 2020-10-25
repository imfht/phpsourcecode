var goodsServiceType = 0;

 //只能輸入數字和小數點
 isNumberdoteKey = function(evt){
  var e = evt || window.event; 
  var srcElement = e.srcElement || e.target;
  
  var charCode = (evt.which) ? evt.which : event.keyCode;			
  if (charCode > 31 && ((charCode < 48 || charCode > 57) && charCode!=46)){
    return false;
  }else{
    if(charCode==46){
      var s = srcElement.value;			
      if(s.length==0 || s.indexOf(".")!=-1){
        return false;
      }			
    }		
    return true;
  }
}


$(function(){
    //选中商品
    $('.ui-icon-chooseg').click(function(){
      if( $(this).attr('class').indexOf('wst-active') == -1 ){
          WST.changeIconStatus($(this), 1);//选中
      }else{
          WST.changeIconStatus($(this), 2);//取消选中
      }
    });
    // 上传图片
    var uploader =WST.upload({
        pick:'#filePicker',
        formData: {dir:'appraises',isThumb:1},
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
})

function changeNum(diffNum,iptId,id){
	var suffix = (id)?"_"+id:"";
	var iptElem = $(iptId+suffix);
	var minVal = parseInt(iptElem.attr('data-min'),10);
	var maxVal = parseInt(iptElem.attr('data-max'),10);
	var num = parseInt(iptElem.val(),10);
	num = num?num:1;
	num = num + diffNum;
	if(maxVal<=num)num=maxVal;
	if(num<=minVal)num=minVal;
	if(num==0)num=1;
    iptElem.val(num);
    switchChk();
}


var pageW = WST.pageWidth();
//弹框
function dataShow(n){
	jQuery('#cover').attr("onclick","javascript:dataHide('"+n+"');").show();
	jQuery('#'+n).animate({"bottom": 0}, 500);
}

function dataHide(n){
	jQuery('#'+n).animate({ 'bottom': '-100%' }, 500);
	jQuery('#cover').hide();
}
function onSwitch(obj,n){
	$(obj).children('.ui-icon-push').removeClass('ui-icon-unchecked-s').addClass('ui-icon-checked-s wst-active');
	$(obj).siblings().children('.ui-icon-push').removeClass('ui-icon-checked-s wst-active').addClass('ui-icon-unchecked-s');
}
// 售后理由
function choseReason(n){
    var _text = '', _val;
    $('.active').each(function(k,v){
      if($(this).prop('checked')){
        _val = $(this).val();
        _text = $(this).parent().parent().find('.name').html();
      }
    });
    if(_val==undefined)return dataHide(n);;
    $('#serviceType').val(_val);
    $('#sTText').html(_text);
    dataHide(n);
}
// 售后类型
function choseType(n){
  var _text = '', _val;
  $('.active').each(function(k,v){
    if($(this).prop('checked')){
      _val = $(this).val();
      _text = $(this).parent().parent().find('.name').html();
    }
  });
  if(_val==undefined)return dataHide(n);;

  goodsServiceType = _val;
  var _rmObj =$('#refundBox');
  if(_val==2){
      _rmObj.hide();
  }else{
      getCanRefundMoney();
      _rmObj.show();
  }
  $('#sTypeText').html(_text);
  dataHide(n);
}

function switchChk(){
    if(goodsServiceType!=2){
      getCanRefundMoney();
    }
}
// 获取可退款金额
function getCanRefundMoney(){
    var _gids = []; // 被勾选中的ogId
    var _glist = {}; // {"num_ogId":"数量"} {num_59:1}
    $('.os-chk').each(function(key,item){
      var _chked = $(item).hasClass('wst-active');
      if(_chked){
        var _id = $(item).attr('gid');
        var _key = 'num_'+_id;
        _gids.push(_id);
        _glist[_key] = $('#goodsNum_'+_id).val();
      }
    })
    _glist.ids = _gids.join(',');
    _glist.orderId = $('#orderId').val();
    $.post(WST.U('mobile/orderservices/getRefundableMoney'),_glist,function(res){
      var json = WST.toJson(res);
      if(json.status==1){
        $('#maxRefundMoney').html('￥'+json.data.totalMoney);
      }
    });
}
  

document.addEventListener('touchmove', function(event) {
    //阻止背景页面滚动,
    if(!jQuery("#cover").is(":hidden")){
        event.preventDefault();
    }
})
// 提交售后申请
function commitOrderService(){
    var postData = {
      orderId:$('#orderId').val(),
      serviceType:$('#serviceType').val(),
      serviceRemark:$('#serviceRemark').val(),
      goodsServiceType:goodsServiceType,
      refundMoney:$('#refundMoney').val()
    };
    if(postData.serviceType==-1){
      return WST.msg('请选择申请原因');
    }
    if(postData.serviceRemark.length==0){
      return WST.msg('请输入问题描述');
    }
    if(postData.goodsServiceType!=2 && postData.refundMoney<0){
      return WST.msg('请输入退款金额');
    }
  
    // 图片
    var imgs = [];
    $('.imgSrc').each(function(k,item){
      imgs.push($(item).attr('v'));
    });
    if(imgs.length>0){
      postData.serviceAnnex = imgs.join(',');
    }
  
    var _gids = []; // 被勾选中的商品id
    var _glist = {}; // {"goodsNum_商品id":"数量"} {goodsNum_59:1}
    $('.os-chk').each(function(key,item){
      var _chked = $(item).hasClass('wst-active');
      if(_chked){
        var _id = $(item).attr('gid');
        var _key = 'goodsNum_'+_id;
        _gids.push(_id);
        _glist[_key] = $('#'+_key).val();
      }
    })
    if(_gids.length==0){
      return WST.msg('请至少勾选一件商品');
    }
    postData.ids = _gids.join(',');
    $.extend(postData,_glist);
    $.post(WST.U('mobile/orderservices/commit'), postData, function(res){
      var json = WST.toJson(res);
      if(json.status==1){
        history.go(-1);
      }
      WST.msg(json.msg);
    });
  }