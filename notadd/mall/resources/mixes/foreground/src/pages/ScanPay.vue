<script>
    import saoyisao from '../assets/images/sao.png';
    import payImg from '../assets/images/s1.jpg';

    export default{
        data() {
            return {
                img: saoyisao,
                pay: payImg,
            };
        },
    };
</script>
<template>
    <div class="scan-pay container">
        <div></div>
        <div class="scan-pay-title">
            <span>微信支付</span>
            二维码已过期，刷新页面重新获取二维码
        </div>
        <div class="scan clearfix">
            <div class="scan-left">
                <div class="two-bar-codes">
                    <img :src="pay" alt="">
                </div>
                <p>请使用微信扫一扫，扫描二维码支付</p>
            </div>
            <div class="scan-right">
                <img :src="img" alt="">
            </div>
        </div>
        <div class="chose-pay clearfix">
            <router-link to="/mall/order-success">
                <span><&nbsp;选择其他支付方式</span>
            </router-link>
        </div>
    </div>
</template>