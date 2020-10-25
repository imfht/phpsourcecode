<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                navigateColumns: [
                    {
                        align: 'center',
                        title: '排序',
                        type: 'index',
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'navigateName',
                        title: '导航名称',
                        width: 300,
                    },
                    {
                        key: 'shelves',
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
                        title: '是否显示',
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
                                    class: {
                                        'delete-ad': true,
                                    },
                                    on: {
                                        click() {
                                            self.remove(data.index);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '删除'),
                            ]);
                        },
                        title: '操作',
                        width: 180,
                    },
                ],
                navigateData: [
                    {
                        navigateName: '首页',
                        status: true,
                    },
                    {
                        navigateName: '首页',
                        status: true,
                    },
                    {
                        navigateName: '首页',
                        status: true,
                    },
                    {
                        navigateName: '首页',
                        status: true,
                    },
                ],
                self: this,
            };
        },
        methods: {
            addNavigate() {
                const self = this;
                self.$router.push(
                    {
                        path: 'navigate/add',
                    },
                );
            },
            edit() {
                const self = this;
                self.$router.push(
                    {
                        path: 'navigate/edit',
                    },
                );
            },
            remove(index) {
                this.navigateData.splice(index, 1);
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="shop-navigate">
            <tabs value="name1">
                <tab-pane label="导航列表" name="name1">
                    <card :bordered="false">
                        <div class="navigate-list">
                            <i-button type="ghost" @click.native="addNavigate">+添加导航</i-button>
                            <i-table class="navigate-table"
                                     :columns="navigateColumns"
                                     :context="self"
                                     :data="navigateData"
                                     ref="navigateList"
                                     highlight-row>
                            </i-table>
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