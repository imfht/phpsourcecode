<script>
    import head from '../assets/images/head.png';
    import computer from '../assets/images/computer.png';

    export default {
        data() {
            return {
                user: {
                    collects: [
                        {
                            img: computer,
                            name: 'Purrfect diary 咕噜日',
                            price: 6533.00,
                        },
                        {
                            img: computer,
                            name: 'Purrfect diary 咕噜日',
                            price: 6533.00,
                        },
                        {
                            img: computer,
                            name: 'Purrfect diary 咕噜日',
                            price: 6533.00,
                        },
                        {
                            img: computer,
                            name: 'Purrfect diary 咕噜日',
                            price: 6533.00,
                        },
                    ],
                    cart: [
                        {
                            color: '白色',
                            img: computer,
                            name: 'Purrfect diary 咕噜日记1-7岁儿童可爱',
                            price: 39.9,
                        },
                        {
                            color: '白色',
                            img: computer,
                            name: 'Purrfect diary 咕噜日记1-7岁儿童可爱',
                            price: 39.9,
                        },
                        {
                            color: '白色',
                            img: computer,
                            name: 'Purrfect diary 咕噜日记1-7岁儿童可爱',
                            price: 39.9,
                        },
                    ],
                    footprint: [
                        {
                            img: computer,
                            price: 39.9,
                        },
                        {
                            img: computer,
                            price: 39.9,
                        },
                        {
                            img: computer,
                            price: 39.9,
                        },
                        {
                            img: computer,
                            price: 39.9,
                        },
                    ],
                    headImg: head,
                    integral: 3264,
                    name: 'yonghuming',
                    level: '金牌会员',
                    order: {
                        pendingPayment: 1,
                        unShip: 5,
                        unReceipt: 4,
                        unEvaluate: 10,
                    },
                    transactions: [
                        {
                            color: '白色',
                            created_at: '2017.06.11 12:30:51',
                            img: computer,
                            name: 'Purrfect diary 咕噜日记1-7岁儿童可爱',
                            price: 39.9,
                        },
                        {
                            color: '白色',
                            created_at: '2017.06.11 12:30:51',
                            img: computer,
                            name: 'Purrfect diary 咕噜日记1-7岁儿童可爱',
                            price: 39.9,
                        },
                        {
                            color: '白色',
                            created_at: '2017.06.11 12:30:51',
                            img: computer,
                            name: 'Purrfect diary 咕噜日记1-7岁儿童可爱',
                            price: 39.9,
                        },
                    ],
                },
            };
        },
    };
</script>
<template>
    <div class="user">
        <div class="userInfo clearfix">
            <img class="pull-left" :src="user.headImg" alt="">
            <div class="pull-left">
                <p><b>{{ user.name }}</b></p>
                <p>会员等级： {{ user.level }}</p>
                <p>积分： {{ user.integral }}</p>
            </div>
            <ul class="pull-right clearfix">
                <li class="pull-left">
                    <p>待付款</p>
                    <p>{{ user.order.pendingPayment }}</p>
                </li>
                <li class="pull-left">
                    <p>待发货</p>
                    <p>{{ user.order.unShip }}</p>
                </li>
                <li class="pull-left">
                    <p>待收货</p>
                    <p>{{ user.order.unReceipt }}</p>
                </li>
                <li class="pull-left">
                    <p>待评价</p>
                    <p>{{ user.order.unEvaluate }}</p>
                </li>
            </ul>
        </div>
        <div class="content clearfix">
            <div class="content-left pull-left">
                <div class="content-block">
                    <div class="block-head clearfix">
                        <h5 class="pull-left">交易提醒</h5>
                        <router-link class="pull-right" to="/mall/user/order">
                            查看更多>
                        </router-link>
                    </div>
                    <ul class="transactions">
                        <li class="clearfix" v-for="(transaction,index) in user.transactions" :key="index">
                            <img class="pull-left" :src="transaction.img" alt="">
                            <div class="pull-left">
                                <p>{{ transaction.name }}&nbsp;颜色：{{ transaction.color }}</p>
                                <p>{{ transaction.created_at }}</p>
                            </div>
                            <div class="price pull-left">￥{{ transaction.price }}</div>
                            <router-link class="pull-left" to="/mall/search/product-details">查看</router-link>
                        </li>
                    </ul>
                </div>
                <div class="content-block">
                    <div class="block-head clearfix">
                        <h5 class="pull-left">我的收藏</h5>
                        <router-link class="pull-right" to="/mall/user/my-collect">
                            查看更多>
                        </router-link>
                    </div>
                    <ul class="collects clearfix">
                        <li class="pull-left" v-for="(collect, index) in user.collects" :key="index">
                            <router-link to="/mall/search/product-details">
                                <img :src="collect.img" alt="">
                                <p>{{ collect.name }}</p>
                                <p>￥{{ collect.price.toFixed(2) }}</p>
                            </router-link>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="content-right pull-right">
                <div class="content-block">
                    <div class="block-head clearfix">
                        <h5 class="pull-left">购物车</h5>
                        <router-link class="pull-right" to="/mall/cart-settlement">
                            查看更多>
                        </router-link>
                    </div>
                    <ul class="shopping-cart">
                        <li class="clearfix" v-for="(item, index) in user.cart" :key="index">
                            <router-link to="/mall/search/product-details">
                                <img class="pull-left" :src="item.img" alt="">
                                <div class="pull-left">
                                    <p>{{ item.name }}&nbsp;颜色：{{ item.color }}</p>
                                    <p>价格 <i class="price"> ￥{{ item.price }}</i></p>
                                </div>
                            </router-link>
                        </li>
                    </ul>
                </div>
                <div class="content-block">
                    <div class="block-head clearfix">
                        <h5 class="pull-left">我的足迹</h5>
                        <router-link class="pull-right" to="/mall/user/footprint">
                            查看更多>
                        </router-link>
                    </div>
                    <ul class="clearfix footprint">
                        <li class="pull-left" v-for="(item,index) in user.footprint">
                            <router-link to="/mall/search/product-details">
                                <img :src="item.img" alt="">
                                <p>￥{{ item.price }}</p>
                            </router-link>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>