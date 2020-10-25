<script type="text/javascript">
var order_sn = "<?php echo $order['order_sn']; ?>";
        //调用微信JS api 支付
        function jsApiCall()
        {
                WeixinJSBridge.invoke(
                        'getBrandWCPayRequest',
                        <?php echo $jsApiParameters; ?>,
                        function(res){
                                WeixinJSBridge.log(res.err_msg);
                        }
                );
        }

        function callpay()
        {
                if (typeof WeixinJSBridge == "undefined"){
                    if( document.addEventListener ){
                        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                    }else if (document.attachEvent){
                        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
                        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                    }
                }else{
                    jsApiCall();
                }
        }
        
        callpay();
        
        // 微信公众号 jsapi支付，前端页面在此处监听，一旦支付状态异步调整后则进行相关前端JS处理

</script>