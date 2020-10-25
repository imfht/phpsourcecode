<script>
    import image1 from '../assets/images/img_logo.png';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                cancelModal: false,
                cancelOrder: {
                    num: 458789990006645,
                    reason: '',
                    reasonList: ['爱拍数码', '不是有效订单', '买家主动要求', '其他原因'],
                    select: [],
                },
                delivery: [
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 1,
                    },
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        image: image1,
                        isGifts: false,
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 1,
                    },
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        image: image1,
                        isGifts: false,
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 3,
                    },
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        image: image1,
                        isGifts: false,
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 4,
                    },
                ],
                loading: false,
                managementSearch: '',
                order: [
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 1,
                    },
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 2,
                    },
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 3,
                    },
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 4,
                    },
                ],
                orderCancel: [
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 1,
                    },
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        image: image1,
                        isGifts: false,
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 2,
                    },
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        image: image1,
                        isGifts: false,
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 3,
                    },
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        image: image1,
                        isGifts: false,
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 4,
                    },
                ],
                orderComputed: [
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 1,
                    },
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        image: image1,
                        isGifts: false,
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 2,
                    },
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        image: image1,
                        isGifts: false,
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 3,
                    },
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        image: image1,
                        isGifts: false,
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 4,
                    },
                ],
                orderDelivery: [
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 2,
                    },
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        image: image1,
                        isGifts: false,
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 2,
                    },
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        image: image1,
                        isGifts: false,
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 2,
                    },
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        image: image1,
                        isGifts: false,
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 2,
                    },
                ],
                orderPrePay: [
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 1,
                    },
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        image: image1,
                        isGifts: false,
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 1,
                    },
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        image: image1,
                        isGifts: false,
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 3,
                    },
                    {
                        buyer: 'maijiaming',
                        createTime: '2017-02-09',
                        freight: '10.00',
                        goods: [
                            {
                                amount: 2,
                                color: '黑色',
                                image: image1,
                                name: 'MIUI/小米 小米手机4 小米4 MI4智能4G手机包邮 黑色 D-LT（4G）/TD-SCD',
                                price: '100.00',
                                version: 'D-LTE（4G）/TD-SCDMA',
                            },
                        ],
                        image: image1,
                        isGifts: false,
                        num: 666666666666688,
                        payStyle: '在线支付',
                        priceAmount: '55.00',
                        status: 4,
                    },
                ],
                self: this,
            };
        },
        methods: {
            cancelOrders() {
                this.cancelModal = true;
            },
            cancelOrderPay() {
                this.cancelModal = true;
            },
            lookLogistics() {
                const self = this;
                self.$router.push({
                    path: 'order/logistics',
                });
            },
            lookOrderDetail() {
                const self = this;
                self.$router.push({
                    path: 'order/detail',
                });
            },
            orderSetting() {
                const self = this;
                self.$router.push({
                    path: 'order/setting/ship',
                });
            },
            submitCancelOrder() {
                const self = this;
                self.loading = true;
                self.$refs.cancelOrder.validate(valid => {
                    if (valid) {
                        window.console.log(valid);
                    } else {
                        self.loading = false;
                        self.$notice.error({
                            title: '请正确填写设置信息！',
                        });
                    }
                });
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="order-wrap">
            <tabs value="name1">
                <tab-pane label="所有订单" name="name1">
                    <card :bordered="false">
                        <div class="analysis-content clearfix">
                            <div class="order-money-content">
                                <div class="select-content">
                                    <ul class="clearfix">
                                        <li>
                                            下单时间
                                            <date-picker type="date" placeholder="选择日期"
                                                         style="width: 124px"></date-picker>
                                            -
                                            <date-picker type="date" placeholder="选择日期"
                                                         style="width: 124px"></date-picker>
                                        </li>
                                        <li class="store-body-header-right">
                                            <i-input v-model="applicationWord" placeholder="请输入关键词进行搜索">
                                                <span slot="prepend">订单编号</span>
                                                <i-button slot="append" type="primary">搜索</i-button>
                                            </i-input>
                                        </li>
                                    </ul>
                                </div>
                               <!-- <div class="page">
                                    <page :total="100" show-elevator></page>
                                </div>-->
                            </div>
                        </div>
                        <table class="order-table">
                            <thead>
                            <tr class="thead-border">
                            <th class="th-name">
                                <span>商品名称</span>
                                <span>单价</span>
                                <span>数量</span>
                            </th>
                            <th>买家</th>
                            <th>订单金额</th>
                            <th>交易状态</th>
                            <th>操作</th>
                            </tr>
                            <tr class="space-bg">
                                <th colspan="5"></th>
                            </tr>
                            </thead>
                            <tbody v-for="(item, index) in order">
                            <tr class="space">
                                <td colspan="8">
                                    <span>订单号:{{ item.num }}</span>
                                    <span>下单时间:{{ item.createTime }}</span>
                                    <button class="print-btn">打印发货单</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <div class="goods-name-row" v-for="item in item.goods">
                                           <div class="img-text">
                                               <div class="img">
                                                   <img :src="item.image" alt="">
                                               </div>
                                               <div class="goods-name">
                                                   <p>{{ item.name }}</p>
                                                   <p>颜色:{{ item.color }}, 选择版本{{ item.version }}</p>
                                               </div>
                                           </div>
                                            <div class="goods-td-border">&yen;{{ item.price }}</div>
                                            <div class="goods-td-border">{{ item.amount }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ item.buyer }}</td>
                                <td>
                                    <p>&yen;{{ item.priceAmount }}</p>
                                    <p class="tip">(含运费{{ item.freight }})</p>
                                    <p class="tip">{{ item.payStyle }}</p>
                                </td>
                                <td>
                                    <p v-if="item.status === 1">等待买家付款</p>
                                    <p v-if="item.status === 2">待发货</p>
                                    <p v-if="item.status === 3">已取消</p>
                                    <p v-if="item.status === 4">交易完成</p>
                                    <p><a @click="lookOrderDetail">订单详情</a></p>
                                </td>
                                <td>
                                    <i-button type="error" v-if="item.status === 1"
                                              @click.native="cancelOrders(index)">取消订单</i-button>
                                    <i-button type="primary" v-if="item.status === 2"
                                              @click.native="orderSetting">设置发货</i-button>
                                </td>
                            </tr>
                            <tr class="space-bg">
                                <td colspan="5"></td>
                            </tr>
                            </tbody>
                        </table>
                    </card>
                    <modal
                            v-model="cancelModal"
                            title="取消订单" class="upload-picture-modal seller-order-modal">
                        <div>
                            <i-form ref="cancelOrder" :model="cancelOrder" :rules="cancelValidate" :label-width="100">
                                <row>
                                    <i-col span="12">
                                        <form-item label="订单编号">
                                            {{ cancelOrder.num }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="20">
                                        <form-item label="取消原因">
                                            <checkbox-group v-model="cancelOrder.select">
                                                <checkbox :label="item" v-for="item in cancelOrder.reasonList">
                                                    <span>{{ item }}</span>
                                                </checkbox>
                                                </checkbox-group>
                                            <i-input type="textarea" v-model="cancelOrder.reason"
                                                     :autosize="{minRows: 3,maxRows: 5}"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="20">
                                        <form-item>
                                            <i-button :loading="loading" type="primary" @click.native="submitCancelOrder">
                                                <span v-if="!loading">确认提交</span>
                                                <span v-else>正在提交…</span>
                                            </i-button>
                                        </form-item>
                                    </i-col>
                                </row>
                            </i-form>
                        </div>
                    </modal>
                </tab-pane>
                <tab-pane label="待付款" name="name2">
                    <card :bordered="false">
                        <div class="analysis-content clearfix">
                            <div class="order-money-content">
                                <div class="select-content">
                                    <ul class="clearfix">
                                        <li>
                                            下单时间
                                            <date-picker type="date" placeholder="选择日期"
                                                         style="width: 124px"></date-picker>
                                            -
                                            <date-picker type="date" placeholder="选择日期"
                                                         style="width: 124px"></date-picker>
                                        </li>
                                        <li class="store-body-header-right">
                                            <i-input v-model="applicationWord" placeholder="请输入关键词进行搜索">
                                                <span slot="prepend">订单编号</span>
                                                <i-button slot="append" type="primary">搜索</i-button>
                                            </i-input>
                                        </li>
                                    </ul>
                                </div>
                                <!-- <div class="page">
                                     <page :total="100" show-elevator></page>
                                 </div>-->
                            </div>
                        </div>
                        <table class="order-table">
                            <thead>
                            <tr class="thead-border">
                                <th class="th-name">
                                    <span>商品名称</span>
                                    <span>单价</span>
                                    <span>数量</span>
                                </th>
                                <th>买家</th>
                                <th>订单金额</th>
                                <th>交易状态</th>
                                <th>操作</th>
                            </tr>
                            <tr class="space-bg">
                                <th colspan="5"></th>
                            </tr>
                            </thead>
                            <tbody v-for="(item, index) in orderPrePay">
                            <tr class="space">
                                <td colspan="8">
                                    <span>订单号:{{ item.num }}</span>
                                    <span>下单时间:{{ item.createTime }}</span>
                                    <button class="print-btn">打印发货单</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <div class="goods-name-row" v-for="item in item.goods">
                                            <div class="img-text">
                                                <div class="img">
                                                    <img :src="item.image" alt="">
                                                </div>
                                                <div class="goods-name">
                                                    <p>{{ item.name }}</p>
                                                    <p>颜色:{{ item.color }}, 选择版本{{ item.version }}</p>
                                                </div>
                                            </div>
                                            <div class="goods-td-border">&yen;{{ item.price }}</div>
                                            <div class="goods-td-border">{{ item.amount }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ item.buyer }}</td>
                                <td>
                                    <p>&yen;{{ item.priceAmount }}</p>
                                    <p class="tip">(含运费{{ item.freight }})</p>
                                    <p class="tip">{{ item.payStyle }}</p>
                                </td>
                                <td>
                                    <p>代付款</p>
                                    <p><a @click="lookOrderDetail">订单详情</a></p>
                                </td>
                                <td>
                                    <i-button type="error" @click.native="cancelOrderPay(index)">取消订单</i-button>
                                </td>
                            </tr>
                            <tr class="space-bg">
                                <td colspan="5"></td>
                            </tr>
                            </tbody>
                        </table>
                    </card>
                </tab-pane>
                <tab-pane label="待发货" name="name3">
                    <card :bordered="false">
                        <div class="analysis-content clearfix">
                            <div class="order-money-content">
                                <div class="select-content">
                                    <ul class="clearfix">
                                        <li>
                                            下单时间
                                            <date-picker type="date" placeholder="选择日期"
                                                         style="width: 124px"></date-picker>
                                            -
                                            <date-picker type="date" placeholder="选择日期"
                                                         style="width: 124px"></date-picker>
                                        </li>
                                        <li class="store-body-header-right">
                                            <i-input v-model="applicationWord" placeholder="请输入关键词进行搜索">
                                                <span slot="prepend">订单编号</span>
                                                <i-button slot="append" type="primary">搜索</i-button>
                                            </i-input>
                                        </li>
                                    </ul>
                                </div>
                                <!-- <div class="page">
                                     <page :total="100" show-elevator></page>
                                 </div>-->
                            </div>
                        </div>
                        <table class="order-table">
                            <thead>
                            <tr class="thead-border">
                                <th class="th-name">
                                    <span>商品名称</span>
                                    <span>单价</span>
                                    <span>数量</span>
                                </th>
                                <th>买家</th>
                                <th>订单金额</th>
                                <th>交易状态</th>
                                <th>操作</th>
                            </tr>
                            <tr class="space-bg">
                                <th colspan="5"></th>
                            </tr>
                            </thead>
                            <tbody v-for="(item, index) in orderDelivery">
                            <tr class="space">
                                <td colspan="8">
                                    <span>订单号:{{ item.num }}</span>
                                    <span>下单时间:{{ item.createTime }}</span>
                                    <button class="print-btn">打印发货单</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <div class="goods-name-row" v-for="item in item.goods">
                                            <div class="img-text">
                                                <div class="img">
                                                    <img :src="item.image" alt="">
                                                </div>
                                                <div class="goods-name">
                                                    <p>{{ item.name }}</p>
                                                    <p>颜色:{{ item.color }}, 选择版本{{ item.version }}</p>
                                                </div>
                                            </div>
                                            <div class="goods-td-border">&yen;{{ item.price }}</div>
                                            <div class="goods-td-border">{{ item.amount }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ item.buyer }}</td>
                                <td>
                                    <p>&yen;{{ item.priceAmount }}</p>
                                    <p class="tip">(含运费{{ item.freight }})</p>
                                    <p class="tip">{{ item.payStyle }}</p>
                                </td>
                                <td>
                                    <p>待发货</p>
                                    <p><a>订单详情</a></p>
                                </td>
                                <td>
                                    <i-button type="primary" v-if="item.status === 2"
                                              @click.native="orderSetting">设置发货</i-button>
                                </td>
                            </tr>
                            <tr class="space-bg">
                                <td colspan="5"></td>
                            </tr>
                            </tbody>
                        </table>
                    </card>
                </tab-pane>
                <tab-pane label="已发货" name="name4">
                    <card :bordered="false">
                        <div class="analysis-content clearfix">
                            <div class="order-money-content">
                                <div class="select-content">
                                    <ul class="clearfix">
                                        <li>
                                            下单时间
                                            <date-picker type="date" placeholder="选择日期"
                                                         style="width: 124px"></date-picker>
                                            -
                                            <date-picker type="date" placeholder="选择日期"
                                                         style="width: 124px"></date-picker>
                                        </li>
                                        <li class="store-body-header-right">
                                            <i-input v-model="applicationWord" placeholder="请输入关键词进行搜索">
                                                <span slot="prepend">订单编号</span>
                                                <i-button slot="append" type="primary">搜索</i-button>
                                            </i-input>
                                        </li>
                                    </ul>
                                </div>
                                <!-- <div class="page">
                                     <page :total="100" show-elevator></page>
                                 </div>-->
                            </div>
                        </div>
                        <table class="order-table">
                            <thead>
                            <tr class="thead-border">
                                <th class="th-name">
                                    <span>商品名称</span>
                                    <span>单价</span>
                                    <span>数量</span>
                                </th>
                                <th>买家</th>
                                <th>订单金额</th>
                                <th>交易状态</th>
                                <th>操作</th>
                            </tr>
                            <tr class="space-bg">
                                <th colspan="5"></th>
                            </tr>
                            </thead>
                            <tbody v-for="(item, index) in delivery">
                            <tr class="space">
                                <td colspan="8">
                                    <span>订单号:{{ item.num }}</span>
                                    <span>下单时间:{{ item.createTime }}</span>
                                    <button class="print-btn">打印发货单</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <div class="goods-name-row" v-for="item in item.goods">
                                            <div class="img-text">
                                                <div class="img">
                                                    <img :src="item.image" alt="">
                                                </div>
                                                <div class="goods-name">
                                                    <p>{{ item.name }}</p>
                                                    <p>颜色:{{ item.color }}, 选择版本{{ item.version }}</p>
                                                </div>
                                            </div>
                                            <div class="goods-td-border">&yen;{{ item.price }}</div>
                                            <div class="goods-td-border">{{ item.amount }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ item.buyer }}</td>
                                <td>
                                    <p>&yen;{{ item.priceAmount }}</p>
                                    <p class="tip">(含运费{{ item.freight }})</p>
                                    <p class="tip">{{ item.payStyle }}</p>
                                </td>
                                <td>
                                    <p v-if="item.status === 1">待收货</p>
                                    <p v-if="item.status === 2">待自提</p>
                                    <p><a>订单详情</a></p>
                                    <p><a @click="lookLogistics(item)">查看物流</a></p>
                                </td>
                                <td></td>
                            </tr>
                            <tr class="space-bg">
                                <td colspan="5"></td>
                            </tr>
                            </tbody>
                        </table>
                    </card>
                </tab-pane>
                <tab-pane label="已完成" name="name5">
                    <card :bordered="false">
                        <div class="analysis-content clearfix">
                            <div class="order-money-content">
                                <div class="select-content">
                                    <ul class="clearfix">
                                        <li>
                                            下单时间
                                            <date-picker type="date" placeholder="选择日期"
                                                         style="width: 124px"></date-picker>
                                            -
                                            <date-picker type="date" placeholder="选择日期"
                                                         style="width: 124px"></date-picker>
                                        </li>
                                        <li class="store-body-header-right">
                                            <i-input v-model="applicationWord" placeholder="请输入关键词进行搜索">
                                                <span slot="prepend">订单编号</span>
                                                <i-button slot="append" type="primary">搜索</i-button>
                                            </i-input>
                                        </li>
                                    </ul>
                                </div>
                                <!-- <div class="page">
                                     <page :total="100" show-elevator></page>
                                 </div>-->
                            </div>
                        </div>
                        <table class="order-table">
                            <thead>
                            <tr class="thead-border">
                                <th class="th-name">
                                    <span>商品名称</span>
                                    <span>单价</span>
                                    <span>数量</span>
                                </th>
                                <th>买家</th>
                                <th>订单金额</th>
                                <th>交易状态</th>
                                <th>操作</th>
                            </tr>
                            <tr class="space-bg">
                                <th colspan="5"></th>
                            </tr>
                            </thead>
                            <tbody v-for="(item, index) in orderComputed">
                            <tr class="space">
                                <td colspan="8">
                                    <span>订单号:{{ item.num }}</span>
                                    <span>下单时间:{{ item.createTime }}</span>
                                    <button class="print-btn">打印发货单</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <div class="goods-name-row" v-for="item in item.goods">
                                            <div class="img-text">
                                                <div class="img">
                                                    <img :src="item.image" alt="">
                                                </div>
                                                <div class="goods-name">
                                                    <p>{{ item.name }}</p>
                                                    <p>颜色:{{ item.color }}, 选择版本{{ item.version }}</p>
                                                </div>
                                            </div>
                                            <div class="goods-td-border">&yen;{{ item.price }}</div>
                                            <div class="goods-td-border">{{ item.amount }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ item.buyer }}</td>
                                <td>
                                    <p>&yen;{{ item.priceAmount }}</p>
                                    <p class="tip">(含运费{{ item.freight }})</p>
                                    <p class="tip">{{ item.payStyle }}</p>
                                </td>
                                <td>
                                    <p>交易完成</p>
                                    <p><a>订单详情</a></p>
                                </td>
                                <td></td>
                            </tr>
                            <tr class="space-bg">
                                <td colspan="5"></td>
                            </tr>
                            </tbody>
                        </table>
                    </card>
                </tab-pane>
                <tab-pane label="已取消" name="name6">
                    <card :bordered="false">
                        <div class="analysis-content clearfix">
                            <div class="order-money-content">
                                <div class="select-content">
                                    <ul class="clearfix">
                                        <li>
                                            下单时间
                                            <date-picker type="date" placeholder="选择日期"
                                                         style="width: 124px"></date-picker>
                                            -
                                            <date-picker type="date" placeholder="选择日期"
                                                         style="width: 124px"></date-picker>
                                        </li>
                                        <li class="store-body-header-right">
                                            <i-input v-model="applicationWord" placeholder="请输入关键词进行搜索">
                                                <span slot="prepend">订单编号</span>
                                                <i-button slot="append" type="primary">搜索</i-button>
                                            </i-input>
                                        </li>
                                    </ul>
                                </div>
                                <!-- <div class="page">
                                     <page :total="100" show-elevator></page>
                                 </div>-->
                            </div>
                        </div>
                        <table class="order-table">
                            <thead>
                            <tr class="thead-border">
                                <th class="th-name">
                                    <span>商品名称</span>
                                    <span>单价</span>
                                    <span>数量</span>
                                </th>
                                <th>买家</th>
                                <th>订单金额</th>
                                <th>交易状态</th>
                                <th>操作</th>
                            </tr>
                            <tr class="space-bg">
                                <th colspan="5"></th>
                            </tr>
                            </thead>
                            <tbody v-for="(item, index) in orderCancel">
                            <tr class="space">
                                <td colspan="8">
                                    <span>订单号:{{ item.num }}</span>
                                    <span>下单时间:{{ item.createTime }}</span>
                                    <button class="print-btn">打印发货单</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <div class="goods-name-row" v-for="item in item.goods">
                                            <div class="img-text">
                                                <div class="img">
                                                    <img :src="item.image" alt="">
                                                </div>
                                                <div class="goods-name">
                                                    <p>{{ item.name }}</p>
                                                    <p>颜色:{{ item.color }}, 选择版本{{ item.version }}</p>
                                                </div>
                                            </div>
                                            <div class="goods-td-border">&yen;{{ item.price }}</div>
                                            <div class="goods-td-border">{{ item.amount }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ item.buyer }}</td>
                                <td>
                                    <p>&yen;{{ item.priceAmount }}</p>
                                    <p class="tip">(含运费{{ item.freight }})</p>
                                    <p class="tip">{{ item.payStyle }}</p>
                                </td>
                                <td>
                                    <p>已取消</p>
                                    <p><a>订单详情</a></p>
                                </td>
                                <td></td>
                            </tr>
                            <tr class="space-bg">
                                <td colspan="5"></td>
                            </tr>
                            </tbody>
                        </table>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>