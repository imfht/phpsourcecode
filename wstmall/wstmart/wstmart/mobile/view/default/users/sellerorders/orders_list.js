jQuery.noConflict();
// 获取订单列表
function getOrderList(){
  $('#Load').show();
    loading = true;
    var param = {};
    param.type = $('#type').val();
    param.pagesize = 10;
    param.page = Number( $('#currPage').val() ) + 1;
    param.deliverType = -1;
    param.payType = -1;
    $.post(WST.U('mobile/orders/getSellerOrderList'), param, function(data){
        var json = WST.toJson(data);
        if(json.status>0){
          var html = '';
          json = json.data;
          if(json && json.data && json.data.length>0){
            var gettpl = document.getElementById('shopList').innerHTML;
            laytpl(gettpl).render(json.data, function(html){
              $('#order-box').append(html);
            });

            $('#currPage').val(json.current_page);
            $('#totalPage').val(json.last_page);
          }else{
  	        html += '<div class="wst-prompt-icon"><img src="'+ window.conf.MOBILE +'/img/nothing-order.png"></div>';
	        html += '<div class="wst-prompt-info">';
	        html += '<p>暂无订单</p>';
	        html += '</div>';
            $('#order-box').html(html);
          }
          WST.imgAdapt('j-imgAdapt');
          loading = false;
          $('#Load').hide();
          echo.init();//图片懒加载
        }else{
          WST.msg(json.msg,'info');
        }
    });
}
var currPage = totalPage = 0;
var loading = false;
$(document).ready(function(){
  
  $('#type').val(jQuery("#shopBox li:first").attr("type"));
  jQuery("#shopBox li:first").addClass("tab-curr");
  getOrderList();

  WST.initFooter('user');
  // Tab切换卡
  $('.tab-item').click(function(){
      $(".tab-item").removeClass("tab-curr");
      $(this).addClass('tab-curr');
      var type = $(this).attr('type');
      $('#type').val(type);
      reFlashList();
  });
  // 弹出层
   $("#frame").css('top',0);

    $(window).scroll(function(){  
        if (loading) return;
        if ((5 + $(window).scrollTop()) >= ($(document).height() - screen.height)) {
            currPage = Number( $('#currPage').val() );
            totalPage = Number( $('#totalPage').val() );
            if( totalPage > 0 && currPage < totalPage ){
            	getOrderList();
            }
        }
    });
});

// 刷新列表页
function reFlashList(){
  $('#currPage').val('0');
  $('#order-box').html(' ');
  getOrderList();
}

// 全选
function checkAll(obj){
    var chk = $(obj).attr('checked');
    $('.active').each(function(k,v){
        if(!$(this).prop('disabled')){
            $(this).prop('checked',chk);
        }
    });
}

function getSelectIds(obj){
    var ids = [];
    $(obj).each(function(){
        if($(this)[0].checked)ids.push($(this).val());
    });
    return ids;
}


// 拒收
function showEditMoneyBox(event){
    $("#wst-event3").attr("onclick","javascript:"+event);
    $("#editMoneyBox").dialog("show");
}

function editOrderMoney(oid){
  var newOrderMoney = $('#newOrderMoney').val();
  $.post(WST.U('mobile/orders/editOrderMoney'),{id:oid,orderMoney:newOrderMoney},function(data){
              hideDialog('#editMoneyBox');
              var json = WST.toJson(data);
              if(json.status>0){
                $('#newOrderMoney').val(' ');
            	WST.msg(json.msg,'success');
                setTimeout(function(){
                	reFlashList();
                },2000);
              }else{
                WST.msg(json.msg,'info');
              }
            });
}

//退款
function showRefundBox(id){
    $.post(WST.U('mobile/orders/toShopRefund'),{id:id},function(data){
           var json = WST.toJson(data);
           $('#refundOid').html(json.orderNo);
           $('#realTotalMoney').html('¥ '+json.realTotalMoney);
           $('#refundMoney').html('¥ '+json.backMoney);
           $('#useScore').html(json.useScore);
           $('#scoreMoney').html('¥ '+json.scoreMoney);
           // 弹出层滚动条
           var clientH = WST.pageHeight();// 屏幕高度
           var boxheadH = $('#refund-boxTitle').height();// 弹出层标题高度
           var contentH = $('#refund-content').height(); // 弹出层内容高度
           $('#refund-content').css('height',clientH-boxheadH+'px');
           $("#wst-event8").attr("onclick","javascript:refund("+id+")");
           reFundDataShow();
    })
}
//弹框
function reFundDataHide(){
    $('#shopBox').show();
    jQuery('#refundFrame').animate({'right': '-100%'}, 500);
    jQuery('#cover').hide();
}
function reFundDataShow(){
    jQuery('#cover').attr("onclick","javascript:reFundDataHide();").show();
    jQuery('#refundFrame').animate({"right": 0}, 500);
    setTimeout(function(){$('#shopBox').hide();},600)
}
// 退款
function refund(id){

  var params = {};
      params.refundStatus = $('#refundStatus')[0].checked?1:-1;
      params.content = $.trim($('#shopRejectReason').val());
      params.id = id;
      if(params.refundStatus==-1 && params.content==''){
        WST.msg('请输入原因','info');
        return;
      }
      $.post(WST.U('mobile/orderrefunds/shoprefund'),params,function(data){
        var json = WST.toJson(data);
        if(json.status==1){
            WST.msg('操作成功','success');
            setTimeout(function(){
            	reFundDataHide();
            	reFlashList();
            },2000);
          }else{
            WST.msg(json.msg,'info');
          }
     });

}

//  隐藏对话框
function hideDialog(id){
  $(id).dialog("hide");
}

// 确认收货
function receive(oid){
  hideDialog('#wst-di-prompt');
  $.post(WST.U('mobile/orders/receive'),{id:oid},function(data){
      var json = WST.toJson(data);
      if(json.status==1){
      	WST.msg(json.msg,'success');
        setTimeout(function(){
        	reFlashList();
        },2000);
      }else{
        WST.msg(json.msg,'info');
      }
  });
}



/*********************** 订单详情 ****************************/
//弹框
function dataShow(){
    jQuery('#cover').attr("onclick","javascript:dataHide();").show();
    jQuery('#frame').animate({"right": 0}, 500);
    setTimeout(function(){$('#shopBox').hide();},600)
    
}
function dataHide(){
    $('#shopBox').show();
    jQuery('#frame').animate({'right': '-100%'}, 500);
    jQuery('#cover').hide();
}



function getOrderDetail(oid){
  $.post(WST.U('mobile/orders/getDetail'),{id:oid},function(data){
      var json = WST.toJson(data);
      if(json.status!=-1){
        var gettpl1 = document.getElementById('detailBox').innerHTML;
          laytpl(gettpl1).render(json, function(html){
            $('#content').html(html);
            // 弹出层滚动条
            var clientH = WST.pageHeight();// 屏幕高度
            var boxheadH = $('#boxTitle').height();// 弹出层标题高度
            var contentH = $('#content').height(); // 弹出层内容高度
            if((clientH-boxheadH) < contentH){
              $('#content').css('height',clientH-boxheadH+'px');
            }
            dataShow();
          });
      }else{
        WST.msg(json.msg,'info');
      }
  });
}
// 跳转到评价页
function toAppr(oid){
  location.href=WST.U('mobile/orders/orderappraise',{'oId':oid});
}
// 投诉
function complain(oid){
  location.href=WST.U('mobile/ordercomplains/complain',{'oId':oid});
}

//修改价格
function editPrice(orderNo){
  alert('修改价格');
}

// 发货
var deliverType;
function toDeliver(id,type){
	deliverType = type;
    $.post(WST.U('mobile/orders/waitdeliverbyid'),{id:id},function(json){
        json = WST.toJson(json);
        if(json.status > 0){
            json = json.data;
            $('#goods_info').empty();
            $('.user_name').empty();
            $('.user_address').empty();
            $('.user_phone').empty();
            $.each(json.list, function(idx, obj) {
                $('#goods_info').append("<tr align='center'><td class='delivery_select'><label class='ui-checkbox msg-chk'><input class='active' "+(obj.hasDeliver?'disabled':'')+" type='checkbox' value='"+obj.id+"'/></label></td><td class='delivery_good'><img src='"+WST.conf.RESOURCE_PATH+'/'+obj.goodsImg+"'/></td><td ><div class='delivery_goodsname' >"+obj.goodsName+"</div></td><td class='delivery_num'>"+obj.goodsNum+"</td><td class='delivery_status'>"+(obj.hasDeliver?'已发货':'')+"</td></tr>");
            });
            $('.user_name').append(json.userName);
            $('.user_address').append(json.userAddress);
            $('.user_phone').append(json.userPhone);
        }
    });
    if(type==0){
        delivery('orderDelivery('+id+','+type+')');
    }else{
        WST.dialog('确定发货吗？','orderDelivery('+id+','+type+')');
    }
}
function delivery(event){
    $("#wst-event0").attr("onclick","javascript:"+event);
    $("#deliveryBox").dialog("show");
}
function orderDelivery(oid,type){
    var params = {};
    var expressId = $('#expressId').val();
    var expressNo = $('#expressNo').val();
    var delivery_type = $("input[name='delivery_type']:checked").val();
    if(type==0){
        if(delivery_type == 1){
            if(expressId == '' || expressNo == ''){
                WST.msg('请填写快递信息',{icon:2});
                return;
            }
        }
        var selectIds = getSelectIds('.active');
        if(selectIds.length==0){
            WST.msg('请选择要发货的商品',{icon:2});
            return;
        }
        params.selectOrderGoodsIds = selectIds.join(',');
        params.deliverType = type;
    }
    params.id = oid;
    params.expressId = expressId ? expressId : 0;
    params.expressNo = expressNo ? expressNo : '';
    if(deliverType==0){
    	hideDialog('#deliveryBox');
    }else{
    	 WST.dialogHide('prompt');
    }
  $.post(WST.U('mobile/orders/deliver'),params,function(data){
        var json = WST.toJson(data);
        if(json.status>0){
            $('#expressNo').val('');
	      	WST.msg(json.msg,'success');
	        setTimeout(function(){
	        	reFlashList();
	        },2000);
        }else{
          WST.msg(json.msg,'info');
        }
      });
}

//加入购物车
function addCart(goodsId, goodsSpecId, goodsType){
    $.post(WST.U('mobile/carts/addCart'),{goodsId:goodsId,goodsSpecId:goodsSpecId,buyNum:1,rnd:Math.random()},function(data,textStatus){
        var json = WST.toJson(data);
        if(json.status==1){
            WST.msg(json.msg,'success');
            if(type==1){
                setTimeout(function(){
                    if(goodsType==1){
                        location.href=WST.U('mobile/carts/'+json.data.forward);
                    }else{
                        location.href=WST.U('mobile/carts/settlement');
                    }
                },1000);
            }
        }else{
            WST.msg(json.msg,'info');
        }
    });
}

function showExpressBox(){
    jQuery('#cover3').attr("onclick","javascript:hideExpressBox();").show();
    jQuery('#expressBox').animate({"bottom": 0}, 500);
}
function hideExpressBox(){
    jQuery('#expressBox').animate({'bottom': '-100%'}, 500);
    jQuery('#cover3').hide();
}
function chooseExpress(){
    $('.express-active').each(function(k,v){
        if($(this).prop('checked')){
            $('#expressText').html($(this).parent().parent().find('.name').html());
            $('#expressId').val($(this).val());
        }
    });
    jQuery('#expressBox').animate({'bottom': '-100%'}, 500);
    jQuery('#cover3').hide();
}