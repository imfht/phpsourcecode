<template>
    <div class="personnal-center padding-attribute">
        <div class="container">
            <div class="dashboard-sidebar">
                <div class="dashboard-brand">个人中心</div>
                <ul class="dashboard-sidebar-nav">
                    <li v-for="item in list">
                        <router-link :to="item.to">{{ item.text }}</router-link>
                    </li>
                </ul>
            </div>
            <div class="dashboard-view">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                list: [
                    {
                        to: '/mall/user/order',
                        text: '我的订单',
                    },
                    {
                        to: '/mall/user/evaluation',
                        text: '交易评价',
                    },
                    {
                        to: '/mall/user/notice',
                        text: '预约/到货通知',
                    },
                    {
                        to: '/mall/user/selaes-return',
                        text: '退款退货',
                    },
                    {
                        to: '/mall/user/my-collect',
                        text: '我的收藏',
                    },
                    {
                        to: '/mall/user/collect-store',
                        text: '店铺关注',
                    },
                    {
                        to: '/mall/user/footprint',
                        text: '我的足迹',
                    },
                    {
                        to: '/mall/user/my-account',
                        text: '账户信息',
                    },
                    {
                        to: '/mall/user/account-security',
                        text: '账户安全',
                    },
                    {
                        to: '/mall/user/address',
                        text: '收货地址',
                    },
                    {
                        to: '/mall/user/integral',
                        text: '我的积分',
                    },
                    {
                        to: '/mall/user/coupon',
                        text: '优惠券',
                    },
                ],
            };
        },
    };
</script>