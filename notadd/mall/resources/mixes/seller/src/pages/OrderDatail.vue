<script>
    import image from '../assets/images/img_logo.png';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                goods: {
                    amount: 2,
                    color: '黑色',
                    commission: '39.9',
                    freight: '10.00',
                    name: 'MIUI /小米小米手机4小米4代MI4智能4G手机包邮黑色D-LTE（4G）/ TD-SCD',
                    picture: image,
                    price: '20.00',
                    priceAll: '167.00',
                    promotions: '',
                },
                order: {
                    address: '陕西省西安市雁塔区科技四路中段陕西国土资源大厦公寓楼2304',
                    code: '3333',
                    company: '顺丰速运',
                    invoice: '普通发票',
                    number: 22222222222,
                    orderNum: 3333333333333,
                    payStyle: '在线支付',
                    phone: 122222222,
                    replayMessage: 'sssssssss',
                    status: '待自提',
                    user: '旺旺',
                },
                step: [
                    {
                        title: '提交订单',
                        createTime: '2016-12-21 13:11:20',
                    },
                    {
                        title: '支付订单',
                        createTime: '2016-12-21 13:11:20',
                    },
                    {
                        title: '门店备货',
                        createTime: '2016-12-21 13:11:20',
                    },
                    {
                        title: '确认收货',
                        createTime: '2016-12-21 13:11:20',
                    },
                    {
                        title: '评价',
                        createTime: '2016-12-21 13:11:20',
                    },
                ],
            };
        },
        methods: {
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="order-detail">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>所有订单-订单详情</span>
            </div>
            <card :bordered="false">
                <div class="logistics-detail">
                    <i-form ref="order" :model="order" :rules="ruleValidate" :label-width="160">
                        <row>
                            <i-col span="12">
                                <h5>订单详情</h5>
                                <row>
                                    <i-col>
                                        <form-item label="收货人">
                                            {{ order.user }} {{ order.phone }} {{ order.address }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="支付方式">
                                            {{ order.payStyle }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="发票">
                                            {{ order.invoice }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="买家留言">
                                            {{ order.replayMessage }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="订单编号">
                                            {{ order.number }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col>
                                        <form-item label="提货码">
                                            {{ order.code }}
                                        </form-item>
                                    </i-col>
                                </row>
                            </i-col>
                            <i-col span="12" class="goods-intro-content">
                                <h4>
                                    <icon type="android-alert"></icon>
                                    订单状态: {{ order.status }}</h4>
                                <p>1.买家已使用“微信支付[扫码]”法师成功对订单进行支付，支付单号“2759758495793”</p>
                                <p>2.订单已提交商家进行备货发货准备</p>
                            </i-col>
                        </row>
                    </i-form>
                </div>
                <div class="step-content">
                    <steps :current="1">
                        <step :title="item.title" :content="item.createTime"
                              v-for="item in step"></step>
                    </steps>
                </div>
                <table>
                    <thead>
                    <tr>
                        <th class="th-name">商品</th>
                        <th>单价(元)</th>
                        <th>数量</th>
                        <th>优惠活动</th>
                        <th>实付*佣金比=应付佣金(元)</th>
                        <th>交易操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <div class="img-text">
                                <div class="img">
                                    <img :src="goods.picture" alt="">
                                </div>
                                <div class="goods-name">
                                    <p>{{ goods.name }}</p>
                                    <p>颜色:{{ goods.color }}, 选择版本{{ goods.version }}</p>
                                </div>
                            </div>
                        </td>
                        <td>{{ goods.price }}</td>
                        <td>{{ goods.amount }}</td>
                        <td>{{ goods.promotions }}</td>
                        <td>&yen;{{ goods.commission }}</td>
                        <td>
                            <i-button type="error">取消订单</i-button>
                        </td>
                    </tr>
                    <tr class="action-module">
                        <td colspan="6">
                            运费: {{ goods.freight }}&nbsp;&nbsp;&nbsp;&nbsp;
                            订单金额: <span>{{ goods.priceAll }}</span> 元
                        </td>
                    </tr>
                    </tbody>
                </table>
            </card>
        </div>
    </div>
</template>