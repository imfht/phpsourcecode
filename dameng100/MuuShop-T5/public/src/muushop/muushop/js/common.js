/*公共JS库*/
(function($){
    $.muushop = {
        //AJAX获取购物车产品数
        get_cart_count : function(url,ele){
            if(!url) url = $('[data-rule="cart_count"]').data('url');
            
            $.get(url,'',function(ret){
                if(ret.code){
                    if(ele){
                        $(ele).text(ret.data);
                    }else{
                        if($('[data-rule="cart_count"] .cart span').length>0) {
                            $('[data-rule="cart_count"] .cart span').text(ret.data);
                        }else{
                            var e = '<span class="label label-danger cart_count">'+ret.data+'</span>';
                            $('[data-rule="cart_count"] .cart').append(e);
                        }
                    }
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
    };
})(jQuery);

$(function(){
    //获取购物车产品数
    $.muushop.get_cart_count();
});
