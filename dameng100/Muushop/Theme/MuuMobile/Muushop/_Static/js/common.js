/*公共JS库*/
(function($){
    $.extend({
        //AJAX获取购物车产品数
        get_cart_count : function(){
            var url = $('[data-toggle="cart_count"]').val();
            $.get(url,'',function(ret){
                if(ret.status == 1){
                    $('[data-type="cart_count"] .cart_count').text(ret.data)
                }
            });
        },

        /**  
         * 将数值四舍五入(保留2位小数)后格式化成金额形式  
         * @param num 数值(Number或者String)  
         * @return 金额格式的字符串,如'1,234,567.45'  
         * @type String  
         */
        formatCurrency : function(num) {    
            num = num.toString().replace(/\$|\,/g,'');    
            if(isNaN(num))    
            num = "0";    
            sign = (num == (num = Math.abs(num)));    
            num = Math.floor(num*100+0.50000000001);    
            cents = num%100;    
            num = Math.floor(num/100).toString();    
            if(cents<10)    
            cents = "0" + cents;
            //for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)    
            //num = num.substring(0,num.length-(4*i+3))+','+    
            //num.substring(num.length-(4*i+3));    
            return (((sign)?'':'-') + num + '.' + cents);    
        },

          //重新加载用户收货地址列表
          loadAddressData : function(callback) {
            //toast.showLoading();
            $.get('/Muushop/api/address','', callback, "json");
        },
    });
})(jQuery);

$(function(){
    //获取购物车产品数
    $.get_cart_count();
    //非首页顶部导航处理
    var location = $('input[data-location="home"]').data('location');
    if(!location || location!=='home'){
        $('.navbar-muushop').addClass('white-nav');
    }
    //
});
