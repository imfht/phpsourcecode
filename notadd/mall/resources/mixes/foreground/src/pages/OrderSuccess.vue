<script>
    import SplinLine from '../components/SplinLine.vue';
    import HeaderBar from '../layouts/HeaderBar.vue';
    import FooterBar from '../layouts/FooterBar.vue';
    import FooterContent from '../layouts/FooterContent.vue';

    export default {
        components: {
            FooterBar,
            FooterContent,
            HeaderBar,
            SplinLine,
        },
        data() {
            return {
                orderDetail: {
                    address: '北京市朝阳区群众',
                    money: 1034.88,
                    name: 'Purrfectdiary 咕噜日记 1-8岁儿童中筒袜1双袜子随机发货中筒袜1双袜子随机发货',
                    num: 3,
                    number: 1122154857863,
                    phone: 12345654321,
                    user: '王茂',
                    size: 'M',
                },
            };
        },
        mounted() {
            const self = this;
            self.$nextTick(() => {
                setTimeout(() => {
                    self.loading = false;
                }, 1000);
            });
        },
    };
</script>
<template>
    <div class="order-success">
        <header-bar></header-bar>
        <div class="header-bar-logo header-bar-line">
            <div class="container">
                <router-link to="/mall">
                    <img src="../assets/images/logo.png" alt="">
                </router-link>
            </div>
        </div>
        <splin-line v-if="loading"></splin-line>
        <div v-if="!loading" class="padding-attribute container">
            <div class="submit-success" v-if="orderDetail">
                <div class="submit-title text-center">
                    订单提交成功，请于<span>30分00秒</span>内完成支付~
                </div>
                <p>订单编号：{{ orderDetail.number }}</p>
                <p>收货地址：{{ orderDetail.address }}，{{ orderDetail.user }}，{{ orderDetail.phone }} </p>
                <p>{{ orderDetail.name }}
                    <span>数量：{{ orderDetail.num }}</span> <span>号码: {{ orderDetail.size }}</span></p>
            </div>
            <div class="order-pay">
                <div class="need-pay">
                    应付金额: <span> ￥{{ orderDetail.money }}</span>
                </div>
                <p class="alipay">
                    <label class="form-control-radio">
                        <input type="radio" name="pay">
                        <span></span>
                        <img src="../assets/images/alipay.png" alt="alipay">
                    </label>
                </p>
                <p class="weixin">
                    <label class="form-control-radio">
                        <input type="radio" name="pay">
                        <span></span>
                        <img src="../assets/images/weixin.png" alt="weixin">
                    </label>
                </p>
                <router-link to="/mall/scan-pay" class="order-btn">立即支付</router-link>
            </div>
        </div>
        <footer-content></footer-content>
        <footer-bar></footer-bar>
    </div>
</template>