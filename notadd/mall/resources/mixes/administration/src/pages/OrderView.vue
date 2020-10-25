<script>
    import goods from '../assets/images/adv.jpg';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                buyerData: {
                    address: '某人，陕西 西安 雁塔区 高新二路与光泰路口陕西国土资源大厦2304',
                    buyerMessage: '包装仔细',
                    buyerName: '昵称',
                    buyerPhone: '029-66458124',
                    invoiceData: {
                        content: '闽西',
                        title: 'sgs',
                        type: '普通发票',
                    },
                },
                columns: [
                    {
                        align: 'center',
                        key: 'orderName',
                        render(h, data) {
                            return h('div', [
                                h('img', {
                                    props: {
                                        class: 'orderImg',
                                        src: data.row.img,
                                    },
                                }),
                                h('span', {
                                    props: {
                                        class: 'orderName',
                                    },
                                }, data.row.orderName),
                            ]);
                        },
                        title: '商品',
                        width: 650,
                    },
                    {
                        align: 'center',
                        key: 'orderPrice',
                        title: '单价',
                    },
                    {
                        align: 'center',
                        key: 'orderNumber',
                        title: '数量',
                    },
                    {
                        align: 'center',
                        key: 'totalAmount',
                        title: '总金额',
                    },
                ],
                orderData: {
                    orderNumber: '565454424',
                    orderSource: 'PC端',
                    orderTime: '2015-06-12 10:42:40',
                    payMethod: '在线支付',
                    payNumber: '541544524124245',
                    payTime: '2015-06-12 10:42:40',
                },
                list: [
                    {
                        freight: 10.00,
                        img: goods,
                        orderName: 'AIM 商务男包牛皮单肩包男手提包横款公文包休闲皮包斜挎男士包包',
                        orderNumber: 5,
                        orderPrice: 19.99,
                        totalAmount: '99.99',
                    },
                ],
                sellersData: {
                    address: '某人，陕西 西安 雁塔区 高新二路与光泰路口陕西国土资源大厦2304',
                    expressDelivery: '顺丰速递',
                    owner: 'fwef',
                    phone: '1234546546',
                    shipmentNumber: '54654654545454786',
                    shipTime: '2016-12-24   03:06:47',
                    shopName: '以瓷换米',
                },
                steps: [
                    {
                        content: '2016-12-23  13:41:59',
                        title: '生成订单',
                    },
                    {
                        content: '2016-12-23  13:41:59',
                        title: '完成付款',
                    },
                    {
                        content: '2016-12-23  13:41:59',
                        title: '商家发货',
                    },
                    {
                        content: '2016-12-23  13:41:59',
                        title: '收货确认',
                    },
                    {
                        content: '2016-12-23  13:41:59',
                        title: '完成评价',
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
    <div class="mall-wrap">
        <div class="order-view">
            <div class="view-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>商品订单—查看</span>
            </div>
            <card class="view-body" :bordered="false">
                <div class="view-step">
                    <steps :current="1">
                        <step v-for="step in steps" :title="step.title" :content="step.content"></step>
                    </steps>
                </div>
                <i-button class="order-detail-btn" type="ghost">订单详情</i-button>
                <i-form :label-width="110">
                    <h4>下单/支付</h4>
                    <row>
                        <i-col span="8">
                            <form-item label="订单号：">
                                {{ 　orderData.orderNumber　 }}
                            </form-item>
                        </i-col>
                        <i-col span="8">
                            <form-item label="订单来源：">
                                {{ 　orderData.orderSource　 }}
                            </form-item>
                        </i-col>
                        <i-col span="8">
                            <form-item label="下单时间：">
                                {{ 　orderData.orderTime　 }}
                            </form-item>
                        </i-col>
                        <i-col span="8">
                            <form-item label="支付单号：">
                                {{ 　orderData.payNumber　 }}
                            </form-item>
                        </i-col>
                        <i-col span="8">
                            <form-item label="支付方式：">
                                {{ 　orderData.payMethod　 }}
                            </form-item>
                        </i-col>
                        <i-col span="8">
                            <form-item label="支付时间：">
                                {{ 　orderData.payTime　 }}
                            </form-item>
                        </i-col>
                    </row>
                </i-form>
                <i-form :label-width="110">
                    <h4>购买/收货方信息</h4>
                    <row>
                        <i-col span="8">
                            <form-item label="买家：">
                                {{ 　buyerData.buyerName　 }}
                            </form-item>
                        </i-col>
                        <i-col span="8">
                            <form-item label="联系方式：">
                                {{ 　buyerData.buyerPhone　 }}
                            </form-item>
                        </i-col>
                    </row>
                    <form-item label="收货地址：">
                        {{ 　buyerData.address　 }}
                    </form-item>
                    <form-item label="发票信息：">
                        <p>类型：{{ 　buyerData.invoiceData.type　 }}</p>
                        <p>抬头：{{ 　buyerData.invoiceData.title　 }}</p>
                        <p>内容：{{ 　buyerData.invoiceData.content　 }}</p>
                    </form-item>
                    <form-item label="买家留言：">
                        {{ 　buyerData.buyerMessage　 }}
                    </form-item>
                </i-form>
                <i-form :label-width="110">
                    <h4>销售/发货方信息</h4>
                    <row>
                        <i-col span="8">
                            <form-item label="店铺：">
                                {{ 　sellersData.shopName　 }}
                            </form-item>
                        </i-col>
                        <i-col span="8">
                            <form-item label="店主名称：">
                                {{ 　sellersData.owner　 }}
                            </form-item>
                        </i-col>
                        <i-col span="8">
                            <form-item label="联系电话：">
                                {{ 　sellersData.phone　 }}
                            </form-item>
                        </i-col>
                    </row>
                    <form-item label="发货地址：">
                        {{ 　sellersData.address　 }}
                    </form-item>
                    <row>
                        <i-col span="8">
                            <form-item label="发货时间：">
                                {{ 　sellersData.shipTime　 }}
                            </form-item>
                        </i-col>
                        <i-col span="8">
                            <form-item label="快递公司：">
                                {{ 　sellersData.expressDelivery　 }}
                            </form-item>
                        </i-col>
                        <i-col span="8">
                            <form-item label="物流单号：">
                                {{ 　sellersData.shipmentNumber　 }}
                            </form-item>
                        </i-col>
                    </row>
                </i-form>
                <i-form>
                    <h4>商品信息</h4>
                    <i-table highlight-row :columns="columns" :data="list"></i-table>
                    <p class="price">订单总额：
                        <span class="totalAmount">￥{{ list[0].totalAmount }}</span>
                    </p>
                    <p class="price">(含运费：￥{{ list[0].freight }})</p>
                </i-form>
            </card>
        </div>
    </div>
</template>