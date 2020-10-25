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
                returnsColumns: [
                    {
                        align: 'left',
                        key: 'returnsTotal',
                        render(h, data) {
                            if (data.row.isReturns) {
                                return h('div', {
                                    class: {
                                        'returns-total': true,
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
                            }
                            return '';
                        },
                        title: '商品/订单号/退货编号',
                    },
                    {
                        align: 'center',
                        key: 'returnsMoney',
                        title: '退款金额',
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'returnsName',
                        title: '买家会员名',
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'returnsTime',
                        title: '申请时间',
                        width: 160,
                    },
                    {
                        align: 'center',
                        key: 'returnsStatus',
                        title: '处理状态',
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'returnsPlatform',
                        title: '平台确认',
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'complaintAction',
                        render(h, data) {
                            if (data.row.status === 1) {
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
                            }
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
                        },
                        title: '操作',
                        width: 120,
                    },
                ],
                returnsData: [
                    {
                        goodsName: 'MIUI /小米小米手机4小米4代MI4智能4G手机包邮黑色D-LTE（4G）/ TD-SCD',
                        img: image,
                        isReturns: true,
                        orderNum: '3232656564988',
                        refundNum: 341236512,
                        returnsMoney: '1999.00',
                        returnsName: 'ibenchu',
                        returnsPlatform: '无',
                        returnsStatus: '待审核',
                        returnsTime: '2017-05-17  22:50:22',
                        status: 1,
                    },
                    {
                        goodsName: 'MIUI /小米小米手机4小米4代MI4智能4G手机包邮黑色D-LTE（4G）/ TD-SCD',
                        img: image,
                        isReturns: true,
                        orderNum: '3232656564988',
                        refundNum: 341236512,
                        returnsMoney: '1999.00',
                        returnsName: 'ibenchu',
                        returnsPlatform: '待审核',
                        returnsStatus: '同意',
                        returnsTime: '2017-04-01  16:30:31',
                        status: 2,
                    },
                    {
                        goodsName: 'MIUI /小米小米手机4小米4代MI4智能4G手机包邮黑色D-LTE（4G）/ TD-SCD',
                        img: image,
                        isReturns: true,
                        orderNum: '3232656564988',
                        refundNum: 341236512,
                        returnsMoney: '1999.00',
                        returnsName: 'ibenchu',
                        returnsPlatform: '无',
                        returnsStatus: '同意',
                        returnsTime: '2017-04-01  16:30:31',
                        status: 3,
                    },
                    {
                        goodsName: 'MIUI /小米小米手机4小米4代MI4智能4G手机包邮黑色D-LTE（4G）/ TD-SCD',
                        img: image,
                        isReturns: true,
                        orderNum: '3232656564988',
                        refundNum: 341236512,
                        returnsMoney: '1999.00',
                        returnsName: 'ibenchu',
                        returnsPlatform: '无',
                        returnsStatus: '同意',
                        returnsTime: '2017-04-01  16:30:31',
                        status: 4,
                    },
                    {
                        goodsName: 'MIUI /小米小米手机4小米4代MI4智能4G手机包邮黑色D-LTE（4G）/ TD-SCD',
                        img: image,
                        isReturns: true,
                        orderNum: '3232656564988',
                        refundNum: 341236512,
                        returnsMoney: '1999.00',
                        returnsName: 'ibenchu',
                        returnsPlatform: '已完成',
                        returnsStatus: '同意',
                        returnsTime: '2017-04-01  16:30:31',
                        status: 5,
                    },
                    {
                        goodsName: 'MIUI /小米小米手机4小米4代MI4智能4G手机包邮黑色D-LTE（4G）/ TD-SCD',
                        img: image,
                        isReturns: true,
                        orderNum: '3232656564988',
                        refundNum: 341236512,
                        returnsMoney: '1999.00',
                        returnsName: 'ibenchu',
                        returnsPlatform: '无',
                        returnsStatus: '不同意',
                        returnsTime: '2017-04-01  16:30:31',
                        status: 6,
                    },
                ],
                dealReturns: [
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
                returnsSearch: [
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
                        path: 'returns/check',
                    },
                );
            },
            deal() {
                this.$router.push(
                    {
                        path: 'returns/deal',
                    },
                );
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="service-returns">
            <tabs value="name1">
                <tab-pane label="退货记录" name="name1">
                    <card :bordered="false">
                        <div class="returns-content">
                            <div class="order-money-content">
                                <div class="returns-select">
                                    <ul class="clearfix">
                                        <li class="store-body-header-right">
                                            <i-input  placeholder="请输入关键词进行搜索">
                                                <i-select slot="prepend" style="width: 100px;" placeholder="请输入关键词进行搜索">
                                                    <i-option :value="item.value" v-for="item in returnsSearch">{{ item.label }}</i-option>
                                                </i-select>
                                                <i-button slot="append" type="primary">搜索</i-button>
                                            </i-input>
                                        </li>
                                        <li>
                                            处理状态
                                            <i-select style="width:124px">
                                                <i-option :key="item" :value="item.value" v-for="item in dealReturns">{{ item.label }}</i-option>
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
                                <i-table :columns="returnsColumns"
                                         :context="self"
                                         :data="returnsData">
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