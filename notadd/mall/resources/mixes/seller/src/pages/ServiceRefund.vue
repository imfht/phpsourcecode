<script>
    import image from '../assets/images/img_logo.png';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                refundColumns: [
                    {
                        align: 'left',
                        key: 'refundTotal',
                        render(h, data) {
                            return h('div', {
                                class: {
                                    'refund-total': true,
                                },
                            }, [
                                h('img', {
                                    domProps: {
                                        src: data.row.img,
                                    },
                                }),
                                h('div', [
                                    h('p', data.row.goodsName),
                                    h('p', `订单编号：${data.row.orderNum}`),
                                    h('p', `退款编号：${data.row.refundNum}`),
                                ]),
                            ]);
                        },
                        title: '商品/订单号/退款号',
                    },
                    {
                        align: 'center',
                        key: 'refundMoney',
                        title: '退款金额',
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'refundName',
                        title: '买家会员名',
                        width: 120,
                    },
                    {
                        align: 'center',
                        key: 'refundTime',
                        title: '申请时间',
                        width: 160,
                    },
                    {
                        align: 'center',
                        key: 'refundStatus',
                        title: '处理状态',
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'refundPlatform',
                        title: '平台确认',
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'complaintAction',
                        render(h, data) {
                            if (data.row.refundStatus !== '待审核') {
                                return h('i-button', {
                                    on: {
                                        click() {
                                            self.check(data.index);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '查看');
                            }
                            return h('i-button', {
                                on: {
                                    click() {
                                        self.deal(data.index);
                                    },
                                },
                                props: {
                                    size: 'small',
                                    type: 'ghost',
                                },
                            }, '处理');
                        },
                        title: '操作',
                        width: 120,
                    },
                ],
                refundData: [
                    {
                        goodsName: 'MIUI /小米小米手机4小米4代MI4智能4G手机包邮黑色D-LTE（4G）/ TD-SCD',
                        img: image,
                        orderNum: '3232656564988',
                        refundMoney: '300.00',
                        refundName: 'huiyuanming',
                        refundNum: 341236512,
                        refundPlatform: '已完成',
                        refundStatus: '同意',
                        refundTime: '2017-04-01  16:30:31',
                    },
                    {
                        goodsName: 'MIUI /小米小米手机4小米4代MI4智能4G手机包邮黑色D-LTE（4G）/ TD-SCD',
                        img: image,
                        orderNum: '3232656564988',
                        refundMoney: '300.00',
                        refundName: 'huiyuanming',
                        refundNum: 341236512,
                        refundPlatform: '已完成',
                        refundStatus: '同意',
                        refundTime: '2017-04-01  16:30:31',
                    },
                    {
                        goodsName: 'MIUI /小米小米手机4小米4代MI4智能4G手机包邮黑色D-LTE（4G）/ TD-SCD',
                        img: image,
                        orderNum: '3232656564988',
                        refundMoney: '300.00',
                        refundName: 'huiyuanming',
                        refundNum: 341236512,
                        refundPlatform: '已完成',
                        refundStatus: '不同意',
                        refundTime: '2017-04-01  16:30:31',
                    },
                    {
                        goodsName: 'MIUI /小米小米手机4小米4代MI4智能4G手机包邮黑色D-LTE（4G）/ TD-SCD',
                        img: image,
                        orderNum: '3232656564988',
                        refundMoney: '300.00',
                        refundName: 'huiyuanming',
                        refundNum: 341236512,
                        refundPlatform: '无',
                        refundStatus: '待审核',
                        refundTime: '2017-04-01  16:30:31',
                    },
                    {
                        goodsName: 'MIUI /小米小米手机4小米4代MI4智能4G手机包邮黑色D-LTE（4G）/ TD-SCD',
                        img: image,
                        orderNum: '3232656564988',
                        refundMoney: '300.00',
                        refundName: 'huiyuanming',
                        refundNum: 341236512,
                        refundPlatform: '无',
                        refundStatus: '待审核',
                        refundTime: '2017-04-01  16:30:31',
                    },
                    {
                        goodsName: 'MIUI /小米小米手机4小米4代MI4智能4G手机包邮黑色D-LTE（4G）/ TD-SCD',
                        img: image,
                        orderNum: '3232656564988',
                        refundMoney: '300.00',
                        refundName: 'huiyuanming',
                        refundNum: 341236512,
                        refundPlatform: '无',
                        refundStatus: '待审核',
                        refundTime: '2017-04-01  16:30:31',
                    },
                    {
                        goodsName: 'MIUI /小米小米手机4小米4代MI4智能4G手机包邮黑色D-LTE（4G）/ TD-SCD',
                        img: image,
                        orderNum: '3232656564988',
                        refundMoney: '300.00',
                        refundName: 'huiyuanming',
                        refundNum: 341236512,
                        refundPlatform: '无',
                        refundStatus: '待审核',
                        refundTime: '2017-04-01  16:30:31',
                    },
                ],
                dealRefund: [
                    {
                        label: '待审核',
                        value: '1',
                    },
                    {
                        label: '同意',
                        value: '2',
                    },
                    {
                        label: '不同意',
                        value: '2',
                    },
                ],
                refundSearch: [
                    {
                        label: '订单编号',
                        value: '1',
                    },
                    {
                        label: '退货编号',
                        value: '2',
                    },
                    {
                        label: '买家会员名',
                        value: '3',
                    },
                ],
                self: this,
            };
        },
        methods: {
            check() {
                this.$router.push(
                    {
                        path: 'refund/check',
                    },
                );
            },
            deal() {
                this.$router.push(
                    {
                        path: 'refund/deal',
                    },
                );
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="service-refund">
            <tabs value="name1">
                <tab-pane label="退款记录" name="name1">
                    <card :bordered="false">
                        <div class="analysis-content">
                            <div class="order-money-content">
                                <div class="select-content">
                                    <ul class="clearfix">
                                        <li class="store-body-header-right">
                                            <i-input  placeholder="请输入关键词进行搜索">
                                                <i-select slot="prepend" style="width: 100px;" placeholder="请输入关键词进行搜索">
                                                    <i-option :value="item.value" v-for="item in refundSearch">{{ item.label }}</i-option>
                                                </i-select>
                                                <i-button slot="append" type="primary">搜索</i-button>
                                            </i-input>
                                        </li>
                                        <li>
                                            处理状态
                                            <i-select style="width:124px">
                                                <i-option :key="item" :value="item.value" v-for="item in dealRefund">{{ item.label }}</i-option>
                                            </i-select>
                                        </li>
                                        <li>
                                            申请时间
                                            <date-picker placeholder="选择日期" style="width: 124px" type="date"></date-picker>
                                            -
                                            <date-picker placeholder="选择日期" style="width: 124px" type="date"></date-picker>
                                        </li>
                                    </ul>
                                </div>
                                <i-table :columns="refundColumns"
                                         :context="self"
                                         :data="refundData"
                                         ref="refundList">
                                </i-table>
                                <div class="page">
                                    <page  show-elevator :total="100"></page>
                                </div>
                            </div>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>