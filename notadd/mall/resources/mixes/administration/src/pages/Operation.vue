<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                managementSearch: '',
                searchList: [
                    {
                        label: '账单编号',
                        value: '1',
                    },
                    {
                        label: '原账单编号',
                        value: '2',
                    },
                    {
                        label: '商家名称',
                        value: '3',
                    },
                ],
                columns: [
                    {
                        align: 'center',
                        key: 'number',
                        title: '账单编号',
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'orderMoney',
                        title: '订单金额（含运费）',
                    },
                    {
                        align: 'center',
                        key: 'freight',
                        title: '运费',
                    },
                    {
                        align: 'center',
                        key: 'commission',
                        title: '收取佣金',
                    },
                    {
                        align: 'center',
                        key: 'refund',
                        title: '退单金额',
                    },
                    {
                        align: 'center',
                        key: 'shopCosts',
                        title: '店铺费用',
                    },
                    {
                        align: 'center',
                        key: 'distribution',
                        title: '分销佣金',
                    },
                    {
                        align: 'center',
                        key: 'settlement',
                        title: '本期应结',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'accountData',
                        title: '出账日期',
                    },
                    {
                        align: 'center',
                        key: 'status',
                        title: '帐单状态',
                    },
                    {
                        align: 'center',
                        key: 'businessName',
                        title: '商家名称',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            if (data.row.params === 1 || data.row.params === 4) {
                                return h('i-button', {
                                    on: {
                                        click() {
                                            self.handel(data.index);
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
                                        self.look(data.index);
                                    },
                                },
                                props: {
                                    class: 'delete-ad',
                                    size: 'small',
                                    type: 'ghost',
                                },
                            }, '查看');
                        },
                        title: '操作',
                        width: 120,
                    },
                ],
                list: [
                    {
                        accountData: '2017-5-9',
                        businessName: 'Rey旗舰店',
                        commission: '37.00',
                        distribution: '10.00',
                        freight: '12.00',
                        params: 1,
                        refund: '0.00',
                        number: '01',
                        orderMoney: '999.00',
                        settlement: '865.00',
                        shopCosts: '30.00',
                        status: '已出账',
                    },
                    {
                        accountData: '2017-5-9',
                        businessName: 'Rey旗舰店',
                        commission: '37.00',
                        distribution: '10.00',
                        freight: '12.00',
                        params: 2,
                        refund: '0.00',
                        number: '01',
                        orderMoney: '999.00',
                        settlement: '865.00',
                        shopCosts: '30.00',
                        status: '商家已确认',
                    },
                    {
                        accountData: '2017-5-9',
                        businessName: 'Rey旗舰店',
                        commission: '37.00',
                        distribution: '10.00',
                        freight: '12.00',
                        params: 3,
                        refund: '0.00',
                        number: '01',
                        orderMoney: '999.00',
                        settlement: '865.00',
                        shopCosts: '30.00',
                        status: '平台已审核',
                    },
                    {
                        accountData: '2017-5-9',
                        businessName: 'Rey旗舰店',
                        commission: '37.00',
                        distribution: '10.00',
                        freight: '12.00',
                        params: 4,
                        refund: '0.00',
                        number: '01',
                        orderMoney: '999.00',
                        settlement: '865.00',
                        shopCosts: '30.00',
                        status: '结算完成',
                    },
                ],
            };
        },
        methods: {
            exportData() {
                this.$refs.managementTable.exportCsv({
                    filename: '结算管理数据',
                });
            },
            handel() {
                const self = this;
                self.$router.push({
                    path: 'operation/settlement',
                });
            },
            look() {
                const self = this;
                self.$router.push({
                    path: 'operation/settlement',
                });
            },
            remove(index) {
                this.list.splice(index, 1);
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="operation">
            <tabs value="name1">
                <tab-pane label="结算管理" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>账单计算公式：订单金额（含运费）-佣金金额-退单金额+退还金额-店铺促销费用+订金订单中的未退定金+
                                下单时使用的平台红包-全部退款时应扣除的平台红包-分销佣金</p>
                            <p>账单处理流程为：系统出账>商家确认>平台审核>财务支付（完成结算）4个环节，其中平台审核和财务支付
                                需要平台介入，请予以关注</p>
                        </div>
                        <div class="album-action">
                            <i-button class="add-data" type="ghost" @click.native="exportData">导出数据</i-button>
                            <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
                            <div class="goods-body-header-right">
                                <i-input v-model="managementWord" placeholder="请输入关键词进行搜索">
                                    <i-select v-model="managementSearch" slot="prepend" style="width: 150px;">
                                        <i-option v-for="item in searchList"
                                                  :value="item.value">{{ item.label }}</i-option>
                                    </i-select>
                                    <i-button slot="append" type="primary">搜索</i-button>
                                </i-input>
                            </div>
                        </div>
                        <i-table highlight-row :columns="columns" :context="self"
                                 :data="list" ref="managementTable"></i-table>
                        <div class="page">
                            <page :total="150" show-elevator></page>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>