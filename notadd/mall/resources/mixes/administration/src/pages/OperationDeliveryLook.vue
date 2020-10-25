<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                managementSearch: '',
                searchList: [
                    {
                        label: '订单号',
                        value: '1',
                    },
                    {
                        label: '运单号',
                        value: '2',
                    },
                    {
                        label: '收货人',
                        value: '3',
                    },
                    {
                        label: '手机号',
                        value: '4',
                    },
                ],
                columns: [
                    {
                        key: 'orderNum',
                        title: '订单号',
                    },
                    {
                        key: 'waybill',
                        title: '运单号',
                    },
                    {
                        key: 'receiver',
                        title: '收货人',
                    },
                    {
                        key: 'phone',
                        title: '手机号',
                    },
                    {
                        key: 'seatNumber',
                        title: '座机号',
                    },
                    {
                        key: 'status',
                        title: '状态',
                    },
                ],
                list: [
                    {
                        orderNum: '56347567',
                        phone: '15623456876',
                        receiver: '王琪梦',
                        seatNumber: '02988555544',
                        status: '已完成',
                        waybill: '436756863432',
                    },
                    {
                        orderNum: '56347567',
                        phone: '15623456876',
                        receiver: '王琪梦',
                        seatNumber: '02988555544',
                        status: '已完成',
                        waybill: '436756863432',
                    },
                    {
                        orderNum: '56347567',
                        phone: '15623456876',
                        receiver: '王琪梦',
                        seatNumber: '02988555544',
                        status: '已完成',
                        waybill: '436756863432',
                    },
                    {
                        orderNum: '56347567',
                        phone: '15623456876',
                        receiver: '王琪梦',
                        seatNumber: '02988555544',
                        status: '已完成',
                        waybill: '436756863432',
                    },
                ],
            };
        },
        methods: {
            exportData() {
                this.$refs.orderTable.exportCsv({
                    filename: '订单列表数据',
                });
            },
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="operation-delivery-look">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>自提点—查看订单</span>
            </div>
            <card :bordered="false">
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
                         :data="list" ref="orderTable"></i-table>
                <div class="page">
                    <page :total="150" show-elevator></page>
                </div>
            </card>
        </div>
    </div>
</template>