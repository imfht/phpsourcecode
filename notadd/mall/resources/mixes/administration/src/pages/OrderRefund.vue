<script>
    import image1 from '../assets/images/img_logo.png';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                allOrderColumns: [
                    {
                        align: 'center',
                        fixed: 'left',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        align: 'center',
                        key: 'orderID',
                        title: '订单编号',
                        width: 190,
                    },
                    {
                        align: 'center',
                        key: 'orderAmount',
                        title: '订单金额',
                        width: 200,
                    },
                    {
                        align: 'center',
                        key: 'applyReason',
                        title: '申请原因',
                        width: 200,
                    },
                    {
                        align: 'center',
                        key: 'goodsImg',
                        render(h, data) {
                            return h('tooltip', {
                                props: {
                                    placement: 'right-end',
                                },
                                scopedSlots: {
                                    content() {
                                        return h('img', {
                                            domProps: {
                                                src: data.row.goodsImg,
                                            },
                                        });
                                    },
                                    default() {
                                        return h('icon', {
                                            props: {
                                                type: 'image',
                                            },
                                        });
                                    },
                                },
                            });
                        },
                        title: '申请图片',
                        width: 170,
                    },
                    {
                        align: 'center',
                        key: 'applyTime',
                        title: '申请时间',
                        width: 200,
                    },
                    {
                        align: 'center',
                        key: 'involvingGoods',
                        title: '涉及商品',
                        width: 170,
                    },
                    {
                        align: 'center',
                        key: 'orderId',
                        title: '商品ID',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'orderNumber',
                        title: '订单编号',
                        width: 200,
                    },
                    {
                        align: 'center',
                        key: 'processingTime',
                        title: '商家处理时间',
                        width: 200,
                    },
                    {
                        align: 'center',
                        key: 'handlingNotes',
                        title: '商家处理备注',
                        width: 200,
                    },
                    {
                        align: 'center',
                        key: 'merchantID',
                        title: '商家ID',
                        width: 200,
                    },
                    {
                        align: 'left',
                        key: 'buyerID',
                        title: '买家ID',
                        width: 200,
                    },
                    {
                        align: 'center',
                        fixed: 'right',
                        key: 'action',
                        render(h, data) {
                            if (data.row.applyTime === '已完成') {
                                return h('i-button', {
                                    on: {
                                        click() {
                                            self.handelOrder();
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
                                        self.look();
                                    },
                                },
                                props: {
                                    size: 'small',
                                    type: 'ghost',
                                },
                            }, '查看');
                        },
                        title: '操作',
                        width: 140,
                    },
                ],
                allOrderData: [
                    {
                        applyReason: '2017-03-21 16:45:45',
                        applyTime: '已完成',
                        buyerID: '455646544',
                        goodsImg: image1,
                        handlingNotes: 'ddqwd',
                        involvingGoods: '45654646',
                        merchantID: '4565464',
                        orderAmount: '移动端',
                        orderId: '钻石店铺',
                        orderID: '65454654546',
                        orderNumber: '5464546',
                        processingTime: '2017-03-21 16:45:45',
                    },
                    {
                        applyReason: '2017-03-21 16:45:45',
                        applyTime: '已完成',
                        buyerID: '455646544',
                        goodsImg: image1,
                        handlingNotes: 'ddqwd',
                        involvingGoods: '45654646',
                        merchantID: '4565464',
                        orderAmount: '移动端',
                        orderId: '钻石店铺',
                        orderID: '65454654546',
                        orderNumber: '5464546',
                        processingTime: '2017-03-21 16:45:45',
                    },
                    {
                        applyReason: '2017-03-21 16:45:45',
                        applyTime: '待处理',
                        buyerID: '455646544',
                        goodsImg: image1,
                        handlingNotes: 'ddqwd',
                        involvingGoods: '45654646',
                        merchantID: '4565464',
                        orderAmount: '移动端',
                        orderId: '钻石店铺',
                        orderID: '65454654546',
                        orderNumber: '5464546',
                        processingTime: '2017-03-21 16:45:45',
                    },
                    {
                        applyReason: '2017-03-21 16:45:45',
                        applyTime: '待处理',
                        buyerID: '455646544',
                        goodsImg: image1,
                        handlingNotes: 'ddqwd',
                        involvingGoods: '45654646',
                        merchantID: '4565464',
                        orderAmount: '移动端',
                        orderId: '钻石店铺',
                        orderID: '65454654546',
                        orderNumber: '5464546',
                        processingTime: '2017-03-21 16:45:45',
                    },
                ],
                pendingColumns: [
                    {
                        align: 'center',
                        fixed: 'left',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        align: 'center',
                        key: 'orderID',
                        title: '退单编号',
                        width: 190,
                    },
                    {
                        align: 'center',
                        key: 'orderAmount',
                        title: '退款金额',
                        width: 200,
                    },
                    {
                        align: 'center',
                        key: 'applyReason',
                        title: '申请原因',
                        width: 200,
                    },
                    {
                        align: 'center',
                        key: 'goodsImg',
                        render(h, data) {
                            return h('tooltip', {
                                props: {
                                    placement: 'right-end',
                                },
                                scopedSlots: {
                                    content() {
                                        return h('img', {
                                            domProps: {
                                                src: data.row.goodsImg,
                                            },
                                        });
                                    },
                                    default() {
                                        return h('icon', {
                                            props: {
                                                type: 'image',
                                            },
                                        });
                                    },
                                },
                            });
                        },
                        title: '申请图片',
                        width: 170,
                    },
                    {
                        align: 'center',
                        key: 'applyTime',
                        title: '申请时间',
                        width: 200,
                    },
                    {
                        align: 'center',
                        key: 'involvingGoods',
                        title: '涉及商品',
                        width: 170,
                    },
                    {
                        align: 'center',
                        key: 'orderId',
                        title: '商品ID',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'orderNumber',
                        title: '订单编号',
                        width: 200,
                    },
                    {
                        align: 'center',
                        key: 'processingTime',
                        title: '商家处理时间',
                        width: 200,
                    },
                    {
                        align: 'center',
                        key: 'handlingNotes',
                        title: '商家处理备注',
                        width: 200,
                    },
                    {
                        align: 'center',
                        key: 'merchantID',
                        title: '商家ID',
                        width: 200,
                    },
                    {
                        align: 'left',
                        key: 'buyerID',
                        title: '买家ID',
                        width: 200,
                    },
                    {
                        align: 'center',
                        fixed: 'right',
                        key: 'action',
                        render(h) {
                            return h('i-button', {
                                on: {
                                    click() {
                                        self.refundHandel();
                                    },
                                },
                                props: {
                                    size: 'small',
                                    type: 'ghost',
                                },
                            }, '处理');
                        },
                        title: '操作',
                        width: 140,
                    },
                ],
                pendingData: [
                    {
                        applyReason: '2017-03-21 16:45:45',
                        applyTime: '已完成',
                        buyerID: '455646544',
                        goodsImg: image1,
                        handlingNotes: 'ddqwd',
                        involvingGoods: '45654646',
                        merchantID: '4565464',
                        orderAmount: '移动端',
                        orderId: '钻石店铺',
                        orderID: '65454654546',
                        orderNumber: '5464546',
                        processingTime: '2017-03-21 16:45:45',
                    },
                    {
                        applyReason: '2017-03-21 16:45:45',
                        applyTime: '交易完成',
                        buyerID: '455646544',
                        goodsImg: image1,
                        handlingNotes: 'ddqwd',
                        involvingGoods: '45654646',
                        merchantID: '4565464',
                        orderAmount: '移动端',
                        orderId: '钻石店铺',
                        orderID: '65454654546',
                        orderNumber: '5464546',
                        processingTime: '2017-03-21 16:45:45',
                    },
                    {
                        applyReason: '2017-03-21 16:45:45',
                        applyTime: '交易完成',
                        buyerID: '455646544',
                        goodsImg: image1,
                        handlingNotes: 'ddqwd',
                        involvingGoods: '45654646',
                        merchantID: '4565464',
                        orderAmount: '移动端',
                        orderId: '钻石店铺',
                        orderID: '65454654546',
                        orderNumber: '5464546',
                        processingTime: '2017-03-21 16:45:45',
                    },
                    {
                        applyReason: '2017-03-21 16:45:45',
                        applyTime: '交易完成',
                        buyerID: '455646544',
                        goodsImg: image1,
                        handlingNotes: 'ddqwd',
                        involvingGoods: '45654646',
                        merchantID: '4565464',
                        orderAmount: '移动端',
                        orderId: '钻石店铺',
                        orderID: '65454654546',
                        orderNumber: '5464546',
                        processingTime: '2017-03-21 16:45:45',
                    },
                    {
                        applyReason: '2017-03-21 16:45:45',
                        applyTime: '交易完成',
                        buyerID: '455646544',
                        goodsImg: image1,
                        handlingNotes: 'ddqwd',
                        involvingGoods: '45654646',
                        merchantID: '4565464',
                        orderAmount: '移动端',
                        orderId: '钻石店铺',
                        orderID: '65454654546',
                        orderNumber: '5464546',
                        processingTime: '2017-03-21 16:45:45',
                    },
                    {
                        applyReason: '2017-03-21 16:45:45',
                        applyTime: '交易完成',
                        buyerID: '455646544',
                        goodsImg: image1,
                        handlingNotes: 'ddqwd',
                        involvingGoods: '45654646',
                        merchantID: '4565464',
                        orderAmount: '移动端',
                        orderId: '钻石店铺',
                        orderID: '65454654546',
                        orderNumber: '5464546',
                        processingTime: '2017-03-21 16:45:45',
                    },
                ],
                reasonColumns: [
                    {
                        align: 'center',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        align: 'center',
                        key: 'sort',
                        title: '排序',
                        width: 150,
                    },
                    {
                        align: 'left',
                        key: 'reason',
                        title: '原因',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.reasonEdit();
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '编辑'),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.reasonData.splice(data.index, 1);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, '删除'),
                            ]);
                        },
                        title: '操作',
                        width: 180,
                    },
                ],
                reasonData: [
                    {
                        reason: '不能按时发货',
                        sort: 1,
                    },
                    {
                        reason: '不能按时发货',
                        sort: 1,
                    },
                    {
                        reason: '不能按时发货',
                        sort: 1,
                    },
                ],
                searchCategory: '',
                searchList: [
                    {
                        label: '退单编号',
                        value: '1',
                    },
                    {
                        label: '商品名称',
                        value: '2',
                    },
                    {
                        label: '买家账号',
                        value: '3',
                    },
                    {
                        label: '店铺名称',
                        value: '4',
                    },
                    {
                        label: '订单编号',
                        value: '5',
                    },
                ],
                searchWord: '',
            };
        },
        methods: {
            addData() {
                const self = this;
                self.$router.push({
                    path: 'refund/add',
                });
            },
            exportData(table) {
                if (table === 1) {
                    this.$refs.pendingTable.exportCsv({
                        filename: '待处理退款数据',
                    });
                } else if (table === 2) {
                    this.$refs.allOrderTable.exportCsv({
                        filename: '所有退款数据',
                    });
                }
            },
            look() {
                const self = this;
                self.$router.push({
                    path: 'refund/all/look',
                });
            },
            handelOrder() {
                const self = this;
                self.$router.push({
                    path: 'refund/all/handel',
                });
            },
            reasonEdit() {
                const self = this;
                self.$router.push({
                    path: 'refund/reedit',
                });
            },
            refundHandel() {
                const self = this;
                self.$router.push({
                    path: 'refund/process',
                });
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="order-refund">
            <tabs value="name1">
                <tab-pane label="待处理" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>卖家提交申请，商家同意并经平台确认后，退款金额以买家付款方式返回给买家</p>
                        </div>
                        <div class="store-body">
                            <div class="store-body-header">
                                <i-button class="export-btn" @click="exportData(1)" type="ghost">导出数据</i-button>
                                <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
                                <div class="store-body-header-right">
                                    <i-input v-model="searchWord" placeholder="请输入关键词进行搜索">
                                        <i-select v-model="searchCategory" slot="prepend" style="width: 100px">
                                            <i-option v-for="item in searchList" :value="item.value"
                                                      :key="item">{{ item.label }}</i-option>
                                        </i-select>
                                        <i-button slot="append" type="primary">搜索</i-button>
                                    </i-input>
                                </div>
                            </div>
                            <i-table ref="pendingTable"
                                     class="shop-table"
                                     :columns="pendingColumns"
                                     :context="self"
                                     :data="pendingData"></i-table>
                        </div>
                        <div class="page">
                            <page :total="100" show-elevator></page>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="所有记录" name="name2">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>卖家提交申请，商家同意并经平台确认后，退款金额以买家付款方式返回给买家</p>
                        </div>
                        <div class="store-body">
                            <div class="store-body-header">
                                <i-button class="export-btn" @click="exportData(2)" type="ghost">导出数据</i-button>
                                <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
                                <div class="store-body-header-right">
                                    <i-input v-model="searchWord" placeholder="请输入关键词进行搜索">
                                        <i-select v-model="searchCategory" slot="prepend" style="width: 100px">
                                            <i-option v-for="item in searchList"
                                                      :value="item.value"
                                                      :key="item">{{ item.label }}</i-option>
                                        </i-select>
                                        <i-button slot="append" type="primary">搜索</i-button>
                                    </i-input>
                                </div>
                            </div>
                            <i-table class="shop-table"
                                     :context="self"
                                     :columns="allOrderColumns"
                                     :data="allOrderData"
                                     ref="allOrderTable" ></i-table>
                        </div>
                        <div class="page">
                            <page :total="100" show-elevator></page>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="原因设定" name="name3">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>系统初始化的原因不能删除</p>
                            <p>排序显示规则为排序小的在前</p>
                        </div>
                        <div class="store-body">
                            <i-button class="add-data" @click.native="addData"
                                      type="ghost">新增数据</i-button>
                            <i-table class="shop-table"
                                     :columns="reasonColumns"
                                     :context="self"
                                     :data="reasonData" ></i-table>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>