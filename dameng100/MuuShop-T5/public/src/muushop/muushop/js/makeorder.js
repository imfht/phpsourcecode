
$(function(){

    //初始化地址列表
    var url = $('[data-role="address_list"]').data('url');
    $.get(url,'', onloadAddress, "json");

    function onloadAddress(res){
        var _html='';
        if(res.code){
            var list = res.data;
            $.each(list,function(index,value){
                _html +=    '<div class="col-md-3">'+
                                '<div class="address-box" data-id="'+value.id+'">'+
                                    '<div class="address-info" data-type="address">'+
                                        '<div class="title clearfix">'+
                                            '<h3 class="address-user">'+value.name+'</h3>'+
                                            '<p class="address-phone">'+value.phone+'</p>'+
                                        '</div>'+

                                        '<div class="info">'+value.province+' '+value.city+' '+value.district+'</div>'+
                                        '<div class="info">'+value.address+'</div>'+
                                        '<div class="edit">'+
                                            '<a class="edit-address-btn" data-toggle="modal" data-remote="/muushop/user/address/action/edit/id/'+value.id+'">编辑</a>'+ 
                                            '<a class="del-address-btn" data-toggle="modal" data-remote="/muushop/user/address/action/delete/id/'+value.id+'">删除</a>'+
                                        '</div>'+
                                    '</div>'+
                                    '<i></i>'+
                                '</div>'+
                            '</div>';
            });

            $('.address-main .address-list').prepend(_html);
            $('.address-box .title:first').trigger("click");
            if(list.length>=3){
                $('.more-address').removeClass('hidden');
            }
            
        }else{
            //_html +='<div class="empty-address">还没有设置收货地址</div>';
            //$('.address-main .address-list').html(_html);
            //toast.error(res.msg, '温馨提示');
        }
    }
});

//收货地址选择
$(function(){

    var delivery_id = $('input[data-type="delivery_id"]').data('value');
    var real_quantity = $('input[data-type="real_quantity"]').data('value');

    $('.address-list').on('click','.address-box',function(){
        $('.address-box').removeClass('selected');
        $('.address-box').find('.edit a').hide();
        $(this).addClass('selected');
        $(this).find('.edit a').show();
        var address_id = $(this).data('id');
        //确认地址区域显示
        confirmAddress('.address-box.selected','.makeorder_address_box');
        delivery_fee(delivery_id,address_id,real_quantity,'express',function(){
        //总价格修改
        var tPrice = totalPrice();
        $('span[data-type="total_price"]').attr('data-value',tPrice).html(tPrice);

        ableScoreMore();//执行积分抵用默认值
        });
    });


    //最终确认地址显示
    function confirmAddress(oldEle,newEle){
        var oBox = $(oldEle);
        //获取寄送地址信息
        var address = $.trim($(oBox).find('.info').text());
        var user = $.trim($(oBox).find('.title').text());
        var confirmAddress = '<span>寄送至：'+address+'</span><span>收件人：'+user+'</span>';
        $(newEle).html(confirmAddress);
    }
    //获取运费价格
    function delivery_fee(id,address_id,quantity,express,callback){
        var data={
            "id":id,
            "address_id":address_id,
            "quantity":quantity,
            "express":express
        }
        var url = $('[data-role="delivery"]').data('url');
        $.get(url,data,function(ret){
            if(ret.code==1){
                var delivery_fee = ret['data'];
                $('#freightPriceId i').text(delivery_fee);
                callback();
            }
        });
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
    /**
     * 支付方式选择
     */
    $('[data-role="pay_type"]').on('click',function(){
        $('[data-role="pay_type"]').removeClass("selected");
        $(this).addClass("selected");
    })
    $('[data-role="pay_type"]:first').trigger("click");//执行一次支付方式选择点击

});

$(function(){
    
    /*优惠卷选择*/
    $('.coupon-section .item').click(function(){
        var _this = $(this);
        $('.item.selected').removeClass('selected');
        $('.coupon-section .item').find('.receive a').text('立即使用>>');
        _this.addClass('selected');
        _this.find('.receive a').text('使用中');
        cachBack();
        var tPrice = totalPrice();
        $('span[data-type="total_price"]').attr('data-value',tPrice).text(tPrice);
    });
});


//默认载入可用积分数量
var ableScoreMore = function(){
    var scoreEle = $('[data-type="score"]');
    scoreEle.each(function(){
        var _this = $(this);
        //积分总数量
        var totalQuantity = _this.find('[data-type="quantity"]').text();
        var exchange = _this.find('[data-prop]').data('prop');
        
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
        var trueTotalPrice = parseInt(productPrice)+parseInt(deliveryPrice);//总金额
        var exchange = parseInt($(this).data('prop'));//兑换比例
        var num = parseInt($(this).val());
        if(num<=0){
            $(this).val(0);
            num=0;
        }
        if(isNaN(num)){
            num=0;
        }
        
        //超过该积分总数设置为总数
        var totalQuantity = parseInt($(this).parent().parent().find('[data-type="quantity"]').text());
        if(num>=parseFloat(totalQuantity)){
            $(this).val(totalQuantity);
            num = totalQuantity;
        }
        //console.log('总积分：'+totalQuantity+'|输入值:'+num);
        //抵用金额
        var queryprice = parseFloat(num/exchange);
        //console.log('抵用金额：'+queryprice+'|兑换比例:'+exchange);
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
    if($('.coupon-section .item.selected').data('value')){
        couponPrice = parseFloat($('.coupon-section .item.selected').data('value'));
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
    $('[data-role="make-order"]').click(function () {
        var address_id = $('.address-section .address-box.selected').data('id');//
        if(!address_id){
            toast.error('收货地址未选择', '温馨提示');
            return
        }
        //获取支付方式
        var pay_type = $('[data-role="pay_type"].selected').data('value');
        //获取支付渠道
        //var channel = $('[data-toggle="paychannel"].selected').data('channel');
        //获取组装积分使用数据
        var use_point=0;
        var scoreele = $('[data-type="score"] input');
        scoreele.each(function(){
            if($(this).val()) {
                use_point = $(this).val();
            } 
        });
        //优惠券
        var coupon_id = $('.coupon-section .item.selected').data('id');
        
        //提交的通用数据
        var data = {
                address_id:address_id,
                pay_type:pay_type,
                //channel:channel,
                delivery_id:delivery_id,
                use_point:use_point,
                coupon_id:coupon_id,
                info:{
                    remark:$('.remark-input').val(),
                    invoice_title:$('.invoice-title').val(),
                },
        }

        var cart_id = $('[data-type="cart_id"]').data('value');
        if(cart_id){
            /*购物车*/
            data.cart_id = cart_id;
        }
        
        var url = $(this).data('url');
        $.post(url,data,function (ret) {
            
            if(ret.code==1){

                toast.success(ret.msg, '温馨提示');
                //console.log(ret);
                setTimeout(function () {
                    window.location.href = ret.url;
                }, 1000);
                
            }else{
                toast.error(ret.msg, '温馨提示');
            }
        })
    });
});

