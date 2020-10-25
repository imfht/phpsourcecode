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
                        label: '店铺ID',
                        value: '1',
                    },
                    {
                        label: '店铺名称',
                        value: '2',
                    },
                    {
                        label: '店主账号',
                        value: '3',
                    },
                    {
                        label: '商家账号',
                        value: '4',
                    },
                ],
                columns: [
                    {
                        align: 'center',
                        key: 'userName',
                        title: '用户名',
                        width: 120,
                    },
                    {
                        align: 'center',
                        key: 'reallyName',
                        title: '真实姓名',
                    },
                    {
                        align: 'center',
                        key: 'serviceName',
                        title: '服务站名称',
                    },
                    {
                        align: 'center',
                        key: 'area',
                        title: '所在地区',
                    },
                    {
                        align: 'center',
                        key: 'address',
                        title: '详细地址',
                    },
                    {
                        align: 'center',
                        key: 'status',
                        render(h, data) {
                            return h('i-switch', {
                                props: {
                                    size: 'large',
                                    value: data.row.status,
                                },
                                scopedSlots: {
                                    close() {
                                        return h('span', '关闭');
                                    },
                                    open() {
                                        return h('span', '开启');
                                    },
                                },
                            });
                        },
                        title: '状态',
                    },
                    {
                        align: 'center',
                        key: 'applicationTime',
                        title: '申请时间',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.edit(data.index);
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
                                            self.look(data.index);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, '查看订单'),
                            ]);
                        },
                        title: '操作',
                        width: 200,
                    },
                ],
                list: [
                    {
                        address: '陕西省西安市高新区高新二路国土资源大厦公寓楼',
                        applicationTime: '2017-2-3',
                        area: '陕西省西安市',
                        reallyName: '王琦铭',
                        serviceName: '财富中心自提点',
                        status: true,
                        userName: '克罗地亚',
                    },
                    {
                        address: '陕西省西安市高新区高新二路国土资源大厦公寓楼',
                        applicationTime: '2017-2-3',
                        area: '陕西省西安市',
                        reallyName: '王琦铭',
                        serviceName: '财富中心自提点',
                        status: true,
                        userName: '克罗地亚',
                    },
                    {
                        address: '陕西省西安市高新区高新二路国土资源大厦公寓楼',
                        applicationTime: '2017-2-3',
                        area: '陕西省西安市',
                        reallyName: '王琦铭',
                        serviceName: '财富中心自提点',
                        status: true,
                        userName: '克罗地亚',
                    },
                    {
                        address: '陕西省西安市高新区高新二路国土资源大厦公寓楼',
                        applicationTime: '2017-2-3',
                        area: '陕西省西安市',
                        reallyName: '王琦铭',
                        serviceName: '财富中心自提点',
                        status: true,
                        userName: '克罗地亚',
                    },
                ],
            };
        },
        methods: {
            addData() {
                const self = this;
                self.$router.push({
                    path: 'delivery/add',
                });
            },
            edit() {
                const self = this;
                self.$router.push({
                    path: 'delivery/edit',
                });
            },
            look() {
                const self = this;
                self.$router.push({
                    path: 'delivery/look',
                });
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="operation-delivery">
            <tabs value="name1">
                <tab-pane label="自提点管理" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>仅展示已拥有自提点商家,无自提点商家可通过搜索查询</p>
                        </div>
                        <div class="album-action">
                            <i-button class="add-data" type="ghost" @click.native="addData">+新增数据</i-button>
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
                        <i-table :columns="columns"
                                 :context="self"
                                 :data="list"
                                 highlight-row
                                 ref="managementTable">
                        </i-table>
                        <div class="page">
                            <page :total="150" show-elevator></page>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>