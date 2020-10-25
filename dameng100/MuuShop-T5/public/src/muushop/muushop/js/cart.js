$(function () {
    /**
     * 购物车JS
     */
    /*全选按钮*/
    $('[data-role="all-check"]').click(function(){
        var goodSection = $('.good-section');
        for(var i=0;i<goodSection.length;i++){
            $(goodSection[i]).toggleClass('checked-section');
        }
        check_price();
    });//模拟一次全选点击
    $('[data-role="all-check"]:first').click();

    /*批量删除商品*/
    $('[data-role="del-cart"]').click(function(){
        var id = new Array();
        $('.checked-section').each(function(){
            id.push($(this).data('id'));
        });
        id = id.join(",");
        delGoodCart(id);  
    });
    //删除商品单独按钮
    $('[data-role="delete-cart"]').click(function(event){
        event.stopPropagation();
        var id = $(this).parents('.good-section').attr('data-id');
        delGoodCart(id);
    });

    /*结算按钮*/
    $('[data-rule="buy-immediately"]').click(function () {
        var products_count = $(this).find('#products-count').text();
        var id = $(this).data('id');
        if(products_count>0){
            var url = $('[data-rule="buy-immediately"]').data('url');
            window.location.href = url +'?cart_id='+id;
        }else{
            toast.error('请先选择商品', '温馨提示');
        }
    });

    function delGoodCart(id){
        if(id&&id!=''){
            //if(confirm('您确定要删除吗？')){
                var url = $('[data-role="delete-cart"]').data('url');
                id = id.split(',');
                $.post(url,{ids:id}, function (ret) {
                    //console.log(id);
                    if(ret.code==1){
                        toast.success(ret.msg, '温馨提示');
                        //重新加载购物车数量
                        $.muushop.get_cart_count();
                        //动态移除元素
                        for(var i = 0; i < id.length; i++){
                            $('.good-section[data-id="'+id[i]+'"]').fadeOut(500,function(){                             
                                $(this).remove();
                                //更新结算价格
                                check_price();
                                //数据为空，显示空购物车
                                if($('.good-section').length<=0) {
                                    var html_str = '<div class="empty-cart-item">'+
                                                        '<p class="empty-cart-p tips-font">'+
                                                            '购物车还是空的噢<br><a href="/muushop/index/cats.html">去挑几件商品吧!</a>'+
                                                        '</p>'+
                                                    '</div>';
                                    $('.cart-main').html(html_str);
                                }    
                            });
                        }
                    }else{
                        toast.error(ret.msg, '温馨提示');
                    }
                })
            //}
        }else{
            toast.error('请先选择要移除的商品', '温馨提示');
        }
    }
    /*
    调整商品数
     */ 
    $('.count-box .add-btn').click(function (event) {
        var box = $(this).parents('.count-box');
        var maxNum = box.parents('.good-section-quantity').find('.quantity').text();
        event.stopPropagation();
        count.add(box,maxNum);
        tsum_price(box);
    });
    $('.count-box .cut-btn').click(function (event) {
        var box = $(this).parents('.count-box');
        event.stopPropagation();
        count.cut(box);
        tsum_price(box)
    })
    /*
    选择购物车里的商品
    */
    $('.good-section').click(function () {
        $(this).toggleClass('checked-section');
        check_price();
    });

    /*更改计算价格*/
    function tsum_price(box){
        var num = parseFloat(box.find('.count-input').val());
        var tprice = parseFloat(box.parents('.good-section').find('.good-section-tprice span').text());
        var tsumBox = box.parents('.good-section').find('.good-section-tsum span');
        tsumBox.html(formatCurrency(num*tprice));
        //如果是选中商品就更改一次结算价格
        var sectionBox = box.parents('.good-section');
        if(sectionBox.hasClass('checked-section')){
            check_price()
        }
    }
    /*更新结算价格*/
    function check_price(){
        var price_all = 0;
        var checked_section = $('.checked-section');
        var card_id = '';/*随手记录购物车id*/
        checked_section.each(function () {
            price_all+=parseFloat($(this).find('.product-price').text(),10);
            /**/
            var id = $(this).data('id');
            card_id += ','+id
        });
        card_id = card_id.substr(1);
        //console.log('all',card_id,price_all);
        $('.buyImmediately').data('id',card_id);
        $('.editing-cart').data('id',card_id)
        $('#products-price-all').text(price_all.toFixed(2));
        $('#products-count').text(checked_section.length)
    }
    /**  
     * 将数值四舍五入(保留2位小数)后格式化成金额形式  
     * @param num 数值(Number或者String)  
     * @return 金额格式的字符串,如'1,234,567.45'  
     * @type String  
     */    
    function formatCurrency(num) {    
        num = num.toString().replace(/\$|\,/g,'');
        
        if(isNaN(num)) {
            num = "0";
        }   
        var sign = (num == (num = Math.abs(num)));    
            num = Math.floor(num*100+0.50000000001);    
        var cents = num%100;    
            num = Math.floor(num/100).toString();
            
        if(cents<10)    
        cents = "0" + cents;    
        return (((sign)?'':'-') + num + '.' + cents);    
    }    
    /*商品数量增减类*/
    var count = {
        add:function(box,maxNum){
            var maxNum = parseInt(maxNum);
            var oNum = parseInt(box.find('.count-input').val());
            if(oNum>=maxNum){
                return;
            }else{
                box.find('.count-input').val(oNum+1);
            }
            var id = box.parents('.good-section').attr('data-id');
            editQuantity(id,oNum+1);
        },
        cut:function(box){
            var oNum = parseInt(box.find('.count-input').val());
            if(oNum<=1){
                return;
            }else{
                box.find('.count-input').val(oNum-1);
            }
            var id = box.parents('.good-section').attr('data-id');
            editQuantity(id,oNum-1);
        }
    }
    //调整购物车选中商品数量
    function editQuantity(id,quantity){
        var url = $('[data-role="edit-cart"]').data('url');
        var data = {
            id:id,
            quantity:quantity
        };
        $.post(url,data, function (ret) {
            if(ret.code==1){
                //toast.success(ret.info, '温馨提示');
            }
            else{
                toast.error(ret.info, '温馨提示');
            }
        })
    }
})
