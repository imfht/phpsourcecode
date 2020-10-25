<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                columns: [
                    {
                        align: 'center',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        key: 'orderID',
                        title: '订单编号',
                    },
                    {
                        align: 'center',
                        key: 'orderSource',
                        title: '订单来源',
                    },
                    {
                        key: 'ownerTime',
                        title: '下单时间',
                    },
                    {
                        key: 'orderAmount',
                        title: '订单金额',
                    },
                    {
                        align: 'center',
                        key: 'orderStatus',
                        title: '订单状态',
                    },
                    {
                        key: 'payNumber',
                        title: '支付单号',
                    },
                    {
                        align: 'left',
                        key: 'paymentMethod',
                        title: '支付方式',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            const items = [];
                            items.push(h('i-button', {
                                on: {
                                    click() {
                                        self.toView(data.index);
                                    },
                                },
                                props: {
                                    size: 'small',
                                    type: 'ghost',
                                },
                            }, '查看'));
                            if (data.row.orderStatus === '待付款') {
                                items.push(h('dropdown', {
                                    scopedSlots: {
                                        list() {
                                            const menus = [];
                                            menus.push(h('dropdown-item', {
                                                nativeOn: {
                                                    click() {
                                                        self.cancelOrder(data.row);
                                                    },
                                                },
                                            }, '取消订单'));
                                            if (data.row.status === 1) {
                                                menus.push(h('dropdown-item', {
                                                    nativeOn: {
                                                        click() {
                                                            self.receiveGoods(data.row);
                                                        },
                                                    },
                                                }, '收到货款'));
                                            }
                                            return h('dropdown-menu', menus);
                                        },
                                    },
                                }, [
                                    h('i-button', {
                                        props: {
                                            size: 'small',
                                            type: 'ghost',
                                        },
                                        style: {
                                            marginLeft: '10px',
                                        },
                                    }, [
                                        '设置',
                                        h('icon', {
                                            props: {
                                                type: 'arrow-down-b',
                                            },
                                        }),
                                    ]),
                                ]));
                            }
                            return h('div', items);
                        },
                        title: '操作',
                        width: 180,
                    },
                ],
                list: [
                    {
                        orderAmount: '899（含运费10.00）',
                        orderID: '65454654546',
                        orderSource: '移动端',
                        orderStatus: '待付款',
                        ownerTime: '2017-03-21 16:45:45',
                        paymentMethod: '钻石店铺',
                        payNumber: '45654646',
                        status: 1,
                    },
                    {
                        orderAmount: '899（含运费10.00）',
                        orderID: '65454654546',
                        orderSource: '移动端',
                        orderStatus: '待发货',
                        ownerTime: '2017-03-21 16:45:45',
                        paymentMethod: '钻石店铺',
                        payNumber: '45654646',
                    },
                    {
                        orderAmount: '899（含运费10.00）',
                        orderID: '65454654546',
                        orderSource: '移动端',
                        orderStatus: '待收货',
                        ownerTime: '2017-03-21 16:45:45',
                        paymentMethod: '钻石店铺',
                        payNumber: '45654646',
                    },
                    {
                        orderAmount: '899（含运费10.00）',
                        orderID: '65454654546',
                        orderSource: '移动端',
                        orderStatus: '交易完成',
                        ownerTime: '2017-03-21 16:45:45',
                        paymentMethod: '钻石店铺',
                        payNumber: '45654646',
                    },
                    {
                        orderAmount: '899（含运费10.00）',
                        orderID: '65454654546',
                        orderSource: '移动端',
                        orderStatus: '已取消',
                        ownerTime: '2017-03-21 16:45:45',
                        paymentMethod: '钻石店铺',
                        payNumber: '45654646',
                    },
                    {
                        orderAmount: '899（含运费10.00）',
                        orderID: '65454654546',
                        orderSource: '移动端',
                        orderStatus: '交易完成',
                        ownerTime: '2017-03-21 16:45:45',
                        paymentMethod: '钻石店铺',
                        payNumber: '45654646',
                    },
                    {
                        orderAmount: '899（含运费10.00）',
                        orderID: '65454654546',
                        orderSource: '移动端',
                        orderStatus: '待付款',
                        ownerTime: '2017-03-21 16:45:45',
                        paymentMethod: '钻石店铺',
                        payNumber: '45654646',
                        status: 2,
                    },
                    {
                        orderAmount: '899（含运费10.00）',
                        orderID: '65454654546',
                        orderSource: '移动端',
                        orderStatus: '交易完成',
                        ownerTime: '2017-03-21 16:45:45',
                        paymentMethod: '钻石店铺',
                        payNumber: '45654646',
                    },
                    {
                        orderAmount: '899（含运费10.00）',
                        orderID: '65454654546',
                        orderSource: '移动端',
                        orderStatus: '交易完成',
                        ownerTime: '2017-03-21 16:45:45',
                        paymentMethod: '钻石店铺',
                        payNumber: '45654646',
                    },
                    {
                        orderAmount: '899（含运费10.00）',
                        orderID: '65454654546',
                        orderSource: '移动端',
                        orderStatus: '交易完成',
                        ownerTime: '2017-03-21 16:45:45',
                        paymentMethod: '钻石店铺',
                        payNumber: '45654646',
                    },
                ],
                searchCategory: '',
                searchList: [
                    {
                        label: '订单编号',
                        value: '1',
                    },
                    {
                        label: '卖家账号',
                        value: '2',
                    },
                    {
                        label: '店铺名称',
                        value: '3',
                    },
                    {
                        label: '支付单号',
                        value: '4',
                    },
                ],
                searchWord: '',
            };
        },
        methods: {
            cancelOrder(row) {
                row.orderStatus = '已取消';
            },
            exportData() {
                this.$refs.orderTable.exportCsv({
                    filename: '商品订单数据',
                });
            },
            receiveGoods(row) {
                row.orderStatus = '交易完成';
            },
            toView() {
                const self = this;
                self.$router.push(
                    {
                        path: 'order/view',
                    },
                );
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="order-wrap">
            <tabs value="name1">
                <tab-pane label="商品订单" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>点击查看操作将显示订单（包括订单物品）的详细信息</p>
                            <p>点击取消操作可以取消订单（在线支付但未付款的订单和货到付款但未发货的订单）</p>
                            <p>如果平台已确认收到买家的付款，但系统支付状态并未变更，可以点击收到货款操作
                                (仅限于下单后7日内可更改收款状态)，并填写相关信息后更改订单支付状态</p>
                        </div>
                        <div class="store-body">
                            <div class="store-body-header">
                                <i-button class="export-btn" @click="exportData" type="ghost">导出数据</i-button>
                                <div class="store-body-header-right">
                                    <i-input v-model="searchWord" placeholder="请输入关键词进行搜索">
                                        <i-select v-model="searchCategory" slot="prepend" style="width: 100px">
                                            <i-option :key="item"
                                                      :value="item.value"
                                                      v-for="item in searchList">{{ item.label }}</i-option>
                                        </i-select>
                                        <i-button slot="append" type="primary">搜索</i-button>
                                    </i-input>
                                </div>
                            </div>
                            <i-table class="shop-table"
                                     :context="self"
                                     :columns="columns"
                                     :data="list"
                                     ref="orderTable" highlight-row ></i-table>
                        </div>
                        <div class="page">
                            <page :total="100" show-elevator></page>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>