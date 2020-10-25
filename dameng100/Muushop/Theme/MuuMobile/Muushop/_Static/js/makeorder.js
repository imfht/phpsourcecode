
$(function(){

    //初始化地址列表
    $.loadAddressData(onloadAddress);

    function onloadAddress(data){
        if(data.status){
            var list = data.data;
            var _html='';
            $.each(list,function(index,value){
                _html +='<div class="address-box" data-id="'+value.id+'">'+
                '<div class="address-info" data-type="address">'+
                    '<span class="title" data-id="'+value.id+'">'+
                        '<span class="address-user">'+value.name+'</span>'+' '+value.phone+'<i></i>'+
                    '</span>'+
                    '<span class="info">'+value.province+' '+value.city+' '+value.district+' '+value.address+'</span>'+
                    '<span class="edit">'+
                        '<a class="edit-address-btn" data-toggle="modal" data-remote="/muushop/user/address/action/edit/id/'+value.id+'">编辑</a>'+ 
                        ' <a class="del-address-btn" data-toggle="modal" data-remote="/muushop/user/address/action/del/id/'+value.id+'">删除</a></span>'+
                    '</div>'+
                '</div>';
            });

            $('.address-main .address-list').html(_html);
            $('.address-box .title:first').trigger("click");
        }else{
            toast.error(data.info, '温馨提示');

        }
    }
  
    //鼠标经过显示编辑删除按钮
    $('.address-list').on('mouseover','.address-box',function(){
        $(this).find('.edit').show();
    });
    $('.address-list').on('mouseout','.address-box',function(){
        $(this).find('.edit').hide();
    });

});

$(function(){

    //收货地址选择
    var delivery_id = $('input[data-type="delivery_id"]').data('value');
    var real_quantity = $('input[data-type="real_quantity"]').data('value');
    $('.address-list').on('click','.address-box',function(){
        $('.address-box .title').removeClass('selected');
        $(this).find('.title').addClass('selected');
        var areaid = $(this).data('id');
        confirmAddress('.address-box','.makeorder_address_box');
        delivery_fee(delivery_id,areaid,real_quantity,'express',function(){
            //总价格修改
            var tPrice = totalPrice();
            $('span[data-type="total_price"]').attr('data-value',tPrice).html(tPrice);

            ableScoreMore();//执行积分抵用默认值
        });
    });
    //获取运费价格
    function delivery_fee(id,areaid,quantity,express,callback){
        var data={
            "id":id,
            "areaid":areaid,
            "quantity":quantity,
            "express":express
        }

        $.get('/Muushop/api/delivery',data,function(ret){
            if(ret.status==1){
                var delivery_fee = ret['data']['delivery_fee'];
                $('#freightPriceId i').text(delivery_fee);
                callback();
            }
        });
    }

    //最终确认地址显示
    function confirmAddress(oldEle,newEle){
        var oBox = $(oldEle).find('.selected').parents('.address-box');
        //获取寄送地址信息
        var address = $.trim($(oBox).find('.info').text());
        var user = $.trim($(oBox).find('.title').text());
        var confirmAddress = '<span>寄送至：'+address+'</span><span>收件人：'+user+'</span>';
        $(newEle).html(confirmAddress);
    }

    //收起展开收货地址
    $('.more-address').on('click',function(){
        if($('.address-list').hasClass('in')){
            $('.address-list').removeClass('in');
            $(this).find('span:first').text('更多地址');
            $(this).find('span:eq(1)').html('<i class="icon icon-double-angle-down"></i>');
        }else{
            $('.address-list').addClass('in');
            $(this).find('span:first').text('收起地址');
            $(this).find('span:eq(1)').html('<i class="icon icon-double-angle-up"></i>');
        };
    })
});


$(function(){
    /*支付方式选择*/
    $('.pay-type').click(function(){
        var _this = $(this);
        $('.pay-type').removeClass('selected');
        _this.addClass('selected');
        if(_this.data('value')=='onlinepay'){
            $('.inline-pay-type').removeClass('hidden');
            $('.inline-pay-type').addClass('display');
        }else{
            $('.inline-pay-type').removeClass('display');
            $('.inline-pay-type').addClass('hidden');
        }
    });
    //模拟一次点击
    $('.pay-section .pay-type:first').trigger("click");
});

$(function(){
    /*优惠券展开收起*/
    $('.coupon-section h3 span').on('click',function(e){
        e.stopPropagation();
        var couponMain = $('.coupon-main');
        if(couponMain.hasClass('hidden')){
            couponMain.removeClass('hidden');
            couponMain.addClass('display');
        }else{
            couponMain.addClass('hidden');
            couponMain.removeClass('display');
        }
    });
    /*优惠卷选择*/
    $('.coupon-list-box').click(function(){
        var _this = $(this);
        $('.coupon-list-box.selected').removeClass('selected');
        _this.addClass('selected');
        cachBack();
        var tPrice = totalPrice();
        $('span[data-type="total_price"]').attr('data-value',tPrice).text(tPrice);
        //var data = {
        //    id:$(this).data('id')
        //};
        //$.get('{:U("wshop/api/coupon")}',data,function(ret){
        //    if(ret.status==1){
        //        $('#cachBackId i').text(ret.data.info.rule.discount);
        //        var tPrice = totalPrice();
        //        $('span[data-type="total_price"]').attr('data-value',tPrice).text(tPrice);
        //    }
        //});
    });
});


//默认载入可用积分数量
var ableScoreMore = function(){
    var scoreEle = $('[data-type="score"]');
    scoreEle.each(function(){
        var _this = $(this);
        //积分总数量
        var totalQuantity = _this.find('[data-type="quantity"]').text();
        var exchange = _this.find('[data-exchange]').data('exchange');
        
        //初始载入时可用的积分数量
        var initializeNum;
        var ableScoreNum = initializeNum = totalPrice()*exchange;
        //已输入的积分数量
        var num = parseInt(_this.find('input').val());
        if(num=='' || isNaN(num)){
            num = 0;
        }
        //求最终可以使用的积分数
        if(totalQuantity-num>ableScoreNum){
            ableScoreNum=ableScoreNum;
        }else{
            ableScoreNum = totalQuantity-num;
        }
        //写入还可以使用的积分数
        _this.find('.ableScoreNum').text(parseInt(ableScoreNum));
        //console.log('score:'+ableScoreNum);
    });
};


$(function(){
    //写入积分数量事件
    $(".panel-body input").bind('input propertychange',function(){
        var _this = $(this);
        var productPrice = $('#warePriceId i').text();
        var deliveryPrice = $('#freightPriceId i').text();
        var trueTotalPrice = parseInt(productPrice)+parseInt(deliveryPrice);
        var exchange = parseInt($(this).data('exchange'));
        var num = parseInt($(this).val());
        if(num<=0){
            $(this).val(0);
            num=0;
        }
        if(isNaN(num)){
            num=0;
        }
        //if(totalPrice()==0){
        //    $(this).val(0);
         //   num=0;
        //}
        //超过该积分总数设置为总数
        var totalQuantity = parseInt($(this).parent().parent().find('[data-type="quantity"]').text());
        if(num>parseFloat(totalQuantity)){
            $(this).val(totalQuantity);
            //num = totalQuantity;
        }
        console.log('总积分：'+totalQuantity+'|输入值:'+num);
        //总金额减去同级元素抵用金额乘以兑换比例之和
        //获取同级元素之和即已抵用的金额
        //计算该积分还可用的总数
        var otherInput = $(this).parents().siblings('[data-type="score"]');
        var otherScoreTotal = 0;
        otherInput.each(function(){
            var _tmpNum = $(this).find('input').val();
            if(_tmpNum==''){
                _tmpNum=0;
            }
            //var _tmpPrice = _tmpNum/_tmpExchange;
            var _tmpExchange = $(this).find('input').data('exchange');
            var _tmpThisScoreTotal = _tmpNum/_tmpExchange;//该积分已抵用的金额
            otherScoreTotal += _tmpThisScoreTotal;
        });
        
        var thisAbleScore = trueTotalPrice-otherScoreTotal;//该积分可使用的积分抵用总金额数
        thisAbleScore = thisAbleScore*exchange;//转成可使用的总积分数
        if(thisAbleScore>totalQuantity){
            thisAbleScore=totalQuantity;
        }

        if(parseFloat(num)>parseFloat(thisAbleScore)){
            //输入值超出可用积分数后设置为可用积分数
            $(this).val(parseInt(thisAbleScore));
            num = thisAbleScore;
        }
        console.log(thisAbleScore);
        //抵用金额
        var queryprice = num/exchange;
        $(this).parent().parent().find('.return').text(queryprice.toFixed(2));
        cachBack();//计算返现总金额

        //重载各积分可使用数量
        ableScoreMore();
        //cachBack();//计算返现总金额
        //重新计算并写入订单总金额
        $('span[data-type="total_price"]').attr('data-value',totalPrice()).text(totalPrice());
    });
});



//计算返现总金额
function cachBack(){
    var cachBackPrice = 0; //初始化总优惠价
    var couponPrice=0; //选中的优惠卷的价格
    //优惠券使用的价格
    if($('.coupon-list-box.selected').data('value')){
        couponPrice = parseFloat($('.coupon-list-box.selected').data('value'));
    }
    //积分抵用的价格
    var scoreReturn = $(".panel-body .return");
    var scoreTotal=0; //积分抵用总额初始化
    scoreReturn.each(function(){
        scoreTotal += parseFloat($(this).text());
    });

    cachBackPrice=couponPrice+parseFloat(scoreTotal);
    
    $('#cachBackId i').text(cachBackPrice.toFixed(2));
}
/*总付款数整合计算*/
function totalPrice(){
    var totalPrice = parseFloat($('#warePriceId i').text());
    var cachBackId = parseFloat($('#cachBackId i').text());
    var freightPriceId = parseFloat($('#freightPriceId i').text());

    totalPrice = totalPrice-cachBackId+freightPriceId;
    if(totalPrice<=0){
        return 0.00;
    }else{
        return totalPrice.toFixed(2);
    }
}


//确认下单
$(function(){
    var delivery_id = $('input[data-type="delivery_id"]').data('value');
    //$('#real-pay').text(countPrice($('#real-pay')).toFixed(2));
    /*确认下单,下单完清除优惠券信息*/
    $(".makeorderBtn").click(function () {
        var address_id = $('.address-section .title.selected').data('id');//
        if(!address_id){
            toast.error('收货地址未选择', '温馨提示');
            return
        }
        var payType = $('.pay-type.selected').data('value');
        //获取支付方式
        if(payType=='onlinepay'){
            //支付方式为在线支付时，获取支付方式
            var channel = $('.paychannel.selected input').val();
        }else{
            var channel = '';
        }
        var coupon_id = $('.coupon-list-box.selected').data('id');
        //获取组装积分使用数据
        var use_point={};
        var scoreele = $('[data-type="score"] input');
        scoreele.each(function(){
            var scoreId = $(this).data('id');
            if(scoreId){
                use_point['score'+scoreId] = $(this).val();
            }
        });
        
        //提交的通用数据
        var data = {
                address_id:address_id,
                pay_type:payType,
                channel:channel,
                delivery_id:delivery_id,
                use_point:use_point,
                coupon_id:coupon_id,
                info:{
                    remark:$('.remark-input').val()
                }
        }

        var cart_id = $('[data-type="cart_id"]').data('value');
        if(cart_id){
            /*购物车*/
            data.cart_id = cart_id;

        }else{
            /*立即购买*/
            var sku_id = $('[data-type="product_skuid"]').data('value');
            var quantity = $('[data-type="product_quantity"]').data('value');
            var products = {
                "0":{
                    sku_id:sku_id,
                    quantity:quantity,
                }
            };
            data.products = products;
        }
        /*优惠券*/
        
        $.post('/Muushop/order/makeorder',data,function (ret) {
                //ret = JSON.parse(ret);
            if(ret.status==1){
                toast.success(ret.info, '温馨提示');
                setTimeout(function () {
                    window.location.href = ret.url;
                }, 1000);
            }else{
                toast.error(ret.info, '温馨提示');
            }
        })
    });
});

