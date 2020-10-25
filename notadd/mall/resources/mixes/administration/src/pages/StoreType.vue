<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/mall/admin/store/type/list`, {
                order: 'asc',
                sort: 'order',
            }).then(response => {
                next(vm => {
                    vm.list = response.data.data.map(item => {
                        item.loading = false;
                        return item;
                    });
                    injection.loading.finish();
                });
            }).catch(() => {
                injection.loading.fail();
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
                        align: 'center',
                        key: 'order',
                        render(h, data) {
                            const row = data.row;
                            return h('tooltip', {
                                props: {
                                    placement: 'right-end',
                                },
                                scopedSlots: {
                                    content() {
                                        return '回车更新数据';
                                    },
                                    default() {
                                        return h('i-input', {
                                            on: {
                                                'on-change': event => {
                                                    row.order = event.target.value;
                                                },
                                                'on-enter': () => {
                                                    self.update(row);
                                                },
                                            },
                                            props: {
                                                type: 'ghost',
                                                value: self.list[data.index].order,
                                            },
                                            ref: 'order',
                                            style: {
                                                width: '48px',
                                            },
                                        });
                                    },
                                },
                            });
                        },
                        title: '排序',
                    },
                    {
                        align: 'center',
                        key: 'name',
                        render(h, data) {
                            const row = data.row;
                            return h('tooltip', {
                                props: {
                                    placement: 'right-end',
                                },
                                scopedSlots: {
                                    content() {
                                        return '回车更新数据';
                                    },
                                    default() {
                                        return h('i-input', {
                                            on: {
                                                'on-change': event => {
                                                    row.name = event.target.value;
                                                },
                                                'on-enter': () => {
                                                    self.update(row);
                                                },
                                            },
                                            props: {
                                                type: 'ghost',
                                                value: self.list[data.index].name,
                                            },
                                            style: {
                                                width: '168px',
                                            },
                                        });
                                    },
                                },
                            });
                        },
                        title: '分类名称',
                    },
                    {
                        align: 'center',
                        key: 'amount_of_deposit',
                        title: '保证金数额',
                    },
                    {
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('router-link', {
                                    props: {
                                        to: `/mall/store/type/${data.row.id}/edit`,
                                    },
                                }, [
                                    h('i-button', {
                                        props: {
                                            size: 'small',
                                            type: 'ghost',
                                        },
                                    }, '设置'),
                                ]),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.list[data.index].loading = true;
                                            self.$http.post(`${window.api}/mall/admin/store/type/remove`, {
                                                id: data.row.id,
                                            }).then(() => {
                                                self.$notice.open({
                                                    title: '删除店铺类型信息成功！',
                                                });
                                                self.refresh();
                                            }).catch(() => {
                                                self.$notice.error({
                                                    title: '删除店铺类型信息失败！',
                                                });
                                            }).finally(() => {
                                                self.list[data.index].loading = true;
                                            });
                                        },
                                    },
                                    props: {
                                        loading: self.list[data.index].loading,
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
                        width: 230,
                    },
                ],
                list: [],
                loading: false,
                selection: [],
            };
        },
        methods: {
            batchRemove() {
                const self = this;
                if (self.selection.length === 0) {
                    self.$notice.error({
                        title: '请先选择一个店铺类型！',
                    });
                } else {
                    const query = [];
                    self.selection.forEach(item => {
                        query.push(self.$http.post(`${window.api}/mall/admin/store/type/remove`, {
                            id: item.id,
                        }));
                    });
                    self.loading = true;
                    self.$http.all(query).then(() => {
                        self.$notice.open({
                            title: '批量删除店铺类型信息成功！',
                        });
                        self.refresh();
                    }).catch(() => {
                        self.$notice.error({
                            title: '批量删除店铺类型信息失败！',
                        });
                    }).finally(() => {
                        self.loading = false;
                    });
                }
            },
            refresh() {
                const self = this;
                self.$notice.open({
                    title: '正在刷新数据...',
                });
                self.$loading.start();
                self.$http.post(`${window.api}/mall/admin/store/type/list`, {
                    order: 'asc',
                    sort: 'order',
                }).then(response => {
                    self.list = response.data.data.map(item => {
                        item.loading = false;
                        return item;
                    });
                    self.$loading.finish();
                    self.$notice.open({
                        title: '刷新数据成功！',
                    });
                }).catch(() => {
                    self.$loading.fail();
                });
            },
            selectionChange(val) {
                this.selection = val;
            },
            update(type) {
                const self = this;
                self.$loading.start();
                window.console.log(type);
                self.$http.post(`${window.api}/mall/admin/store/type/edit`, type).then(() => {
                    self.$loading.finish();
                    self.$notice.open({
                        title: '更新数据成功！',
                    });
                    self.refresh();
                }).catch(() => {
                    self.$loading.fail();
                });
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="store-category">
            <tabs value="name1">
                <tab-pane label="店铺分类" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>商家入驻时可指定此处设置店铺分类</p>
                            <p>对分类作任何更改后，都需要到 设置 -> 清理缓存 清理店铺分类，新的设置才会生效</p>
                        </div>
                        <div class="store-body">
                            <div class="store-body-header">
                                <router-link to="/mall/store/type/add">
                                    <i-button class="export-btn" type="ghost">新增数据</i-button>
                                </router-link>
                                <i-button :loading="loading" :style="{
                                    marginLeft: '20px'
                                }" type="ghost" @click.native="batchRemove">批量删除</i-button>
                                <i-button type="text" icon="android-sync" class="refresh" @click.native="refresh">刷新</i-button>
                            </div>
                            <i-table class="shop-table"
                                     :columns="columns"
                                     :data="list"
                                     :context="self"
                                     highlight-row
                                     @on-selection-change="selectionChange">
                            </i-table>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>