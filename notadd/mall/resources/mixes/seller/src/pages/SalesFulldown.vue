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
                        key: 'activeName',
                        title: '分类名称',
                    },
                    {
                        align: 'center',
                        key: 'startTime',
                        title: '开始时间',
                    },
                    {
                        align: 'center',
                        key: 'endTime',
                        title: '结束时间',
                    },
                    {
                        align: 'center',
                        key: 'status',
                        title: '状态',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('router-link', {
                                    props: {
                                        to: '/seller/sales/fulldown/detail',
                                    },
                                }, [
                                    h('i-button', {
                                        props: {
                                            size: 'small',
                                            type: 'ghost',
                                        },
                                    }, '详细'),
                                ]),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.remove(data.index);
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
                list: [
                    {
                        activeName: '春季服装大秒杀活动',
                        businessmenName: '数码数码',
                        businessmenId: '222',
                        endTime: '2018-02-30',
                        startTime: '2018-04-23',
                        status: '已结束',
                    },
                    {
                        activeName: '春季服装大秒杀活动',
                        businessmenName: '数码数码',
                        businessmenId: '222',
                        endTime: '2018-02-30',
                        startTime: '2018-04-23',
                        status: '已结束',
                    },
                    {
                        activeName: '春季服装大秒杀活动',
                        businessmenName: '数码数码',
                        businessmenId: '222',
                        endTime: '2018-02-30',
                        startTime: '2018-04-23',
                        status: '已结束',
                    },
                    {
                        activeName: '春季服装大秒杀活动',
                        businessmenName: '数码数码',
                        businessmenId: '222',
                        endTime: '2018-02-30',
                        startTime: '2018-04-23',
                        status: '进行中',
                    },
                ],
                searchList: [
                    {
                        label: '商品名称',
                        value: '1',
                    },
                    {
                        label: 'spu',
                        value: '2',
                    },
                ],
                searchResult: 'name',
            };
        },
        methods: {
            look() {
                const self = this;
                self.$router.push({
                    path: 'spikes/look',
                });
            },
            remove(index) {
                this.list.splice(index, 1);
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="sales-fulldown">
            <tabs value="name1">
                <tab-pane label="活动列表" name="name1">
                    <card :bordered="false">
                        <div class="spikes-content">
                            <div class="goods-body-header">
                                <router-link to="/seller/sales/fulldown/create" class="first-btn">
                                    <i-button type="ghost">+添加活动</i-button>
                                </router-link>
                                <div class="goods-body-header-right">
                                    <i-input v-model="managementWord" placeholder="请输入关键词进行搜索">
                                        <i-select v-model="searchResult" slot="prepend" style="width: 100px;">
                                            <i-option value="name">活动名称</i-option>
                                        </i-select>
                                        <i-button slot="append" type="primary">搜索</i-button>
                                    </i-input>
                                </div>
                            </div>
                            <i-table class="goods-table"
                                     :columns="columns"
                                     :context="self"
                                     :data="list">
                            </i-table>
                            <div class="page">
                                <page :total="100" show-elevator></page>
                            </div>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>
