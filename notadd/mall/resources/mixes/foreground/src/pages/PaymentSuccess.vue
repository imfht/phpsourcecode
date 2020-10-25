<script>
    import SplinLine from '../components/SplinLine.vue';
    import FooterBar from '../layouts/FooterBar.vue';
    import FooterContent from '../layouts/FooterContent.vue';
    import HeaderBar from '../layouts/HeaderBar.vue';
    import order from '../assets/images/details/order.png';

    export default {
        components: {
            FooterBar,
            FooterContent,
            HeaderBar,
            SplinLine,
        },
        computed: {
            total_price() {
                let totalPrice = 0;
                this.submitOrder.productList.forEach(item => {
                    totalPrice += item.price * item.num;
                });
                return totalPrice.toFixed(2);
            },
        },
        data() {
            return {
                logisticsInfo: {
                    company: '顺丰速运',
                    list: [
                        {
                            address: '北京海淀区xx大道',
                            current: true,
                            time: '2016-12-29  13:06:03',
                            status: '已收入',
                        },
                        {
                            address: '北京海淀区xx大道',
                            current: false,
                            time: '2016-12-29  13:06:03',
                            status: '已收入',
                        },
                        {
                            address: '北京海淀区xx大道',
                            current: false,
                            time: '2016-12-29  13:06:03',
                            status: '已收入',
                        },
                        {
                            address: '北京海淀区xx大道',
                            current: false,
                            time: '2016-12-29  13:06:03',
                            status: '已收入',
                        },
                    ],
                    number: '2017020615400000710016792',
                },
                status: 5,
                steps: [
                    {
                        icon: 'icon-icon',
                        name: '提交订单',
                        time: '2017-05-10 12:30:10',
                    },
                    {
                        icon: 'icon-fukuan',
                        name: '付款成功',
                    },
                    {
                        icon: 'icon-feiji',
                        name: '商家发货',
                    },
                    {
                        icon: 'icon-shouhuo',
                        name: '确认收货',
                    },
                    {
                        icon: 'icon-pingjia',
                        name: '评价',
                    },
                ],
                submitOrder: {
                    integral_num: 1660,
                    integral_price: 16.6,
                    freight: 20,
                    productList: [
                        {
                            img: order,
                            name: 'Purrfect diary 咕噜日记1-7岁儿童可爱短袜5双装儿童可爱短袜5双装儿童可爱短袜5双装',
                            num: 2,
                            offer: 6,
                            price: 39.9,
                            status: '待付款',
                            size: 'L',
                            shop: 'XXX母婴用品店',
                        },
                    ],
                },
            };
        },
        mounted() {
        },
    };
</script>
<template>
    <div class="payment-success">
        <header-bar></header-bar>
        <div class="header-bar-logo header-bar-line">
            <div class="container">
                <router-link to="/mall">
                    <img src="../assets/images/logo.png" alt="">
                </router-link>
            </div>
        </div>
        <div class="container">
            <div class="pay-success-model">
                <div class="top-status bottom-line">
                    <ul class="clearfix">
                        <li class="clearfix" v-for="(step, index) in steps" :class="{ already:status>index }">
                            <ul class="clearfix cricle-box pull-left">
                                <li class="cricle" v-for="item in 13"></li>
                            </ul>
                            <div class="step pull-left">
                                <i class="icon iconfont" :class="step.icon"> </i>
                                <p>{{ step.name }}</p>
                                <p>{{ step.time }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="wait-pay bottom-line step-status" v-if="status===1">
                    <h4>当前状态：等待买家付款</h4>
                    <p>请于<span class="time"> 0小时30分07秒 </span>内进行付款，若未及时付款，系统将自动取消订单</p>
                    <p class="remind-btn">
                        <router-link to="/mall/scan-pay">
                            <button class="order-btn">立即付款</button>
                        </router-link>
                        <a href="">取消订单</a>
                    </p>
                </div>
                <div class="current-status bottom-line step-status"  v-if="status===2">
                    <h4>当前状态：待发货</h4>
                    <p>订单已提交，商家进行备货发货准备。若您想取消购买，可与商家沟通后申请退款。</p>
                    <p class="remind-btn">
                        <router-link to="/mall/refund">
                            <button class="order-btn">申请退款</button>
                        </router-link>
                    </p>
                </div>
                <div class="bottom-line step-status wait-receipt"  v-if="status===3">
                    <h4>当前状态：商家已发货</h4>
                    <p>订单已提交，商家进行备货发货准备。若您想取消购买，可与商家沟通后申请退款。</p>
                    <button class="remind-btn order-btn">确认收货</button>
                    <p>物流公司： {{ logisticsInfo.company }}</p>
                    <p>运单号码： {{ logisticsInfo.number }}</p>
                    <div class="logistics-information clearfix">
                        <span>物流信息： </span>
                        <ul class="right-content">
                            <li class="address" v-for="logic in logisticsInfo.list" :class="{active:logic.current}">
                                <span class="point" :class="{active:logic.current}"></span>
                                <span>{{ logic.time }}&nbsp;&nbsp;&nbsp;{{ logic.address }}  {{ logic.status }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="wait-receipt bottom-line transaction step-status" v-if="status===4">
                    <h4>当前状态：买家确认收货</h4>
                    <p>如订单有问题，可联系商家协商解决。若商家协商失败，您可申请投诉商家。</p>
                    <router-link to="/mall/user/evaluation">
                        <button class="remind-btn order-btn">评价</button>
                    </router-link>
                    <p>物流公司： {{ logisticsInfo.company }}</p>
                    <p>运单号码： {{ logisticsInfo.number }}</p>
                    <div class="logistics-information clearfix">
                        <span>物流信息： </span>
                        <ul class="right-content">
                            <li class="address" v-for="logic in logisticsInfo.list" :class="{active:logic.current}">
                                <span class="point" :class="{active:logic.current}"></span>
                                <span>{{ logic.time }}&nbsp;&nbsp;&nbsp;{{ logic.address }}  {{ logic.status }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="wait-receipt bottom-line transaction step-status">
                    <h4>当前状态：交易成功</h4>
                    <p>如订单有问题，可联系商家协商解决。若商家协商失败，您可申请投诉商家。</p>
                    <p>物流公司： {{ logisticsInfo.company }}</p>
                    <p>运单号码： {{ logisticsInfo.number }}</p>
                    <div class="logistics-information clearfix">
                        <span>物流信息： </span>
                        <ul class="right-content">
                            <li class="address" v-for="logic in logisticsInfo.list" :class="{active:logic.current}">
                                <span class="point" :class="{active:logic.current}"></span>
                                <span>{{ logic.time }}&nbsp;&nbsp;&nbsp;{{ logic.address }}  {{ logic.status }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="receipt-address bottom-line">
                    <p>收货地址:<span>&nbsp王茂&nbsp;&nbsp;176****000&nbsp;&nbsp;陕西省西安市雁塔区高新二路36号xx大厦</span></p>
                    <p>支付方式：在线支付</p>
                    <p> 发票：普通发票 &nbsp;&nbsp;陕西本初网络有限公司</p>
                    <p> 买家留言：请尽快发货，务必包装完好</p>
                    <p>订单号：2017020615400000710016792</p>
                </div>
                <div class="ensure-information">
                    <ul class="product-head clearfix">
                        <li class="pull-left">商品信息</li>
                        <li class="pull-left text-center">单价</li>
                        <li class="pull-left text-center">数量</li>
                        <li class="pull-left text-center">优惠活动</li>
                        <li class="pull-left text-center">金额</li>
                        <li class="pull-left text-center">交易状态</li>
                    </ul>
                    <ul class="product-list">
                        <li v-for="order in submitOrder.productList">
                            <ul class="order-detail clearfix">
                                <li class="pull-left clearfix">
                                    <img class="pull-left" :src="order.img" alt="">
                                    <div class="pull-left">
                                        <p>{{ order.name }}</p>
                                        <p>尺码: {{ order.size }}</p>
                                    </div>
                                </li>
                                <li class="pull-left text-center">￥{{ order.price }}</li>
                                <li class="pull-left text-center">{{ order.num }}</li>
                                <li class="pull-left text-center">￥{{ order.price * order.num }}</li>
                                <li class="pull-left text-center">-{{ order.offer }}</li>
                                <li class="pull-left text-center">{{ order.status }}</li>
                            </ul>
                        </li>
                    </ul>
                    <div class="order-submit submit-btn">
                        <div class="order-submit-content clearfix">
                            <span class="order-price">-&yen;{{ submitOrder.freight }}</span>
                            <span class="name">运费：</span>
                        </div>
                        <div class="order-submit-content clearfix">
                            <span class="order-price price">&yen;{{ total_price}}</span>
                            <span class="name">金额(不含运费)：</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer-content></footer-content>
        <footer-bar></footer-bar>
    </div>
</template>