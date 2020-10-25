  // 售后类型
  var goodsServiceType = 0;
  // 改变数量
  function changeNum(diffNum,iptId,btnId,id,func){
      var suffix = (id)?"_"+id:"";
      var iptElem = $(iptId+suffix);
      var minVal = parseInt(iptElem.attr('data-min'),10);
      var maxVal = parseInt(iptElem.attr('data-max'),10);
      var tmp = 0;
      if(maxVal<minVal){
          tmp = maxVal;
          maxVal = minVal;
          minVal = tmp;
      }
      var num = parseInt(iptElem.val(),10);
      num = num?num:1;
      num = num + diffNum;
      btnId = btnId.split(',');
      $(btnId[0]+suffix).css('color','#333');
      $(btnId[1]+suffix).css('color','#333');
      if(minVal>=num){
          num=minVal;
          $(btnId[0]+suffix).css('color','#333');
      }
      if(maxVal<=num){
          num=maxVal;
          $(btnId[1]+suffix).css('color','#333');
      }
    iptElem.val(num);
    switchChk();
  }
  function switchChk(){
    if(goodsServiceType!=2){
      getCanRefundMoney();
    }
  }
  
  function changeType(target,val){
     goodsServiceType = val;
     $(target).addClass('oti-selected').siblings().removeClass('oti-selected');
     if(val!=2){
      getCanRefundMoney();
       $('#refundMoneyBox').show();
      }else{
       $('#refundMoneyBox').hide();
     }
  }
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
      return WST.msg('请选择提交原因');
    }
    if(postData.serviceRemark.length==0){
      return WST.msg('请输入问题描述');
    }
    if(postData.goodsServiceType!=2 && postData.refundMoney<0){
      return WST.msg('请输入退款金额');
    }
  
    // 图片
    var imgs = [];
    $('.appraise_pic').each(function(k,item){
      imgs.push($(item).attr('v'));
    });
    if(imgs.length>0){
      postData.serviceAnnex = imgs.join(',');
    }
  
    var _gids = []; // 被勾选中的商品id
    var _glist = {}; // {"goodsNum_商品id":"数量"} {goodsNum_59:1}
    $('.os-chk').each(function(key,item){
      var _chked = $(item).prop('checked');
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
    $.post(WST.U('home/orderservices/commit'), postData, function(res){
      var json = WST.toJson(res);
      if(json.status==1){
        history.go(-1);
      }
      WST.msg(json.msg);
    });
  }
  // 获取可退款金额
  function getCanRefundMoney(){
    var _gids = []; // 被勾选中的ogId
    var _glist = {}; // {"num_ogId":"数量"} {num_59:1}
    $('.os-chk').each(function(key,item){
      var _chked = $(item).prop('checked');
      if(_chked){
        var _id = $(item).attr('gid');
        var _key = 'num_'+_id;
        _gids.push(_id);
        _glist[_key] = $('#goodsNum_'+_id).val();
      }
    })
    _glist.ids = _gids.join(',');
    _glist.orderId = $('#orderId').val();
    $.post(WST.U('home/orderservices/getRefundableMoney'),_glist,function(res){
      var json = WST.toJson(res);
      if(json.status==1){
        $('#maxRefundMoney').html(json.data.totalMoney);
      }
    });
  }
  
  $(function(){
    var uploader =WST.upload({
          pick:'#filePicker',
          formData: {dir:'appraises',isThumb:1},
          fileNumLimit:5,
          accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
          callback:function(f,file){
            var json = WST.toJson(f);
            if(json.status==1){
            var tdiv = $("<div style='width:75px;float:left;margin-right:5px;'>"+
                         "<img class='appraise_pic' width='75' height='75' src='"+WST.conf.RESOURCE_PATH+"/"+json.savePath+json.thumb+"' v='"+json.savePath+json.name+"'></div>");
            var btn = $('<div style="position:relative;top:-80px;left:60px;cursor:pointer;" ><img src="'+WST.conf.ROOT+'/wstmart/home/view/default/img/seller_icon_error.png"></div>');
            tdiv.append(btn);
            $('#picBox').append(tdiv);
            btn.on('click','img',function(){
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