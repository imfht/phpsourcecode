<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                loading: false,
                salesColumns: [
                    {
                        key: 'num',
                        title: '结算单号',
                    },
                    {
                        key: 'startTime',
                        title: '起止时间',
                    },
                    {
                        key: 'amount',
                        title: '本期应收',
                    },
                    {
                        key: 'status',
                        title: '结算状态',
                    },
                    {
                        key: 'payTime',
                        title: '付款日期',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('i-button', {
                                on: {
                                    click() {
                                        self.look(data.index);
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
                salesData: [
                    {
                        amount: '99.00',
                        num: '326569562656',
                        payTime: '2016-12-20 13:31:54',
                        startTime: '2016-12-20 13:31:54',
                        status: '已出帐',
                    },
                    {
                        amount: '99.00',
                        num: '326569562656',
                        payTime: '2016-12-20 13:31:54',
                        startTime: '2016-12-20 13:31:54',
                        status: '商家已确认',
                    },
                    {
                        amount: '99.00',
                        num: '326569562656',
                        payTime: '2016-12-20 13:31:54',
                        startTime: '2016-12-20 13:31:54',
                        status: '平台已审核',
                    },
                    {
                        amount: '99.00',
                        num: '326569562656',
                        payTime: '2016-12-20 13:31:54',
                        startTime: '2016-12-20 13:31:54',
                        status: '结算完成',
                    },
                    {
                        amount: '99.00',
                        num: '326569562656',
                        payTime: '2016-12-20 13:31:54',
                        startTime: '2016-12-20 13:31:54',
                        status: '已出帐',
                    },
                ],
                self: this,
                style: 'height: 400px',
                timeList: [
                    {
                        label: '已出账',
                        value: '1',
                    },
                    {
                        label: '商家已确认',
                        value: '2',
                    },
                    {
                        label: '平台已审核',
                        value: '3',
                    },
                    {
                        label: '结算完成',
                        value: '4',
                    },
                ],
            };
        },
        methods: {
            exportSalesData() {
                this.$refs.salesList.exportCsv({
                    filename: '商品销售明细数据',
                });
            },
            look() {
                const self = this;
                self.$router.push(
                    {
                        path: 'settlement/look',
                    },
                );
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="statistics-settlement">
            <tabs value="name1">
                <tab-pane label="订单结算" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>当前与平台结算周期为：1个月</p>
                        </div>
                        <div class="analysis-content">
                            <div class="order-money-content">
                                <div class="select-content">
                                    <ul>
                                        <li>
                                            账单状态
                                            <i-select v-model="model2" style="width:124px">
                                                <i-option v-for="item in timeList" :value="item.value"
                                                          :key="item">{{ item.label }}</i-option>
                                            </i-select>
                                        </li>
                                        <li class="store-body-header-right">
                                            <i-input v-model="applicationWord" placeholder="请输入关键词进行搜索">
                                                <i-button slot="append" type="primary">搜索</i-button>
                                            </i-input>
                                        </li>
                                    </ul>
                                </div>
                                <i-table :columns="salesColumns" :context="self"
                                         :data="salesData" ref="salesList"></i-table>
                                <div class="page">
                                    <page :total="100" show-elevator></page>
                                </div>
                            </div>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>