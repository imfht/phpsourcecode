<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/mall/admin/store/grade/list`).then(response => {
                window.console.log(response);
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
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'level',
                        title: '级别',
                    },
                    {
                        align: 'center',
                        key: 'name',
                        title: '等级名称',
                    },
                    {
                        align: 'center',
                        key: 'publish_limit',
                        title: '可发布商品数',
                    },
                    {
                        align: 'center',
                        key: 'upload_limit',
                        title: '可上传商品数',
                    },
                    {
                        align: 'center',
                        key: 'price',
                        render(h, data) {
                            return `${parseInt(data.row.price, 10)} 元/年`;
                        },
                        title: '收费标准',
                    },
                    {
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('router-link', {
                                    props: {
                                        to: `/mall/store/grade/${data.row.id}/edit`,
                                    },
                                }, [
                                    h('i-button', {
                                        props: {
                                            size: 'small',
                                            type: 'ghost',
                                        },
                                    }, '编辑'),
                                ]),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.list[data.index].loading = true;
                                            self.$http.post(`${window.api}/mall/admin/store/grade/remove`, {
                                                id: data.row.id,
                                            }).then(() => {
                                                self.$notice.open({
                                                    title: '删除店铺等级信息成功！',
                                                });
                                                self.$notice.open({
                                                    title: '正在刷新数据...',
                                                });
                                                self.$loading.start();
                                                self.$http.post(`${window.api}/mall/admin/store/grade/list`).then(response => {
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
                                            }).catch(() => {
                                                self.$notice.error({
                                                    title: '删除店铺等级信息失败！',
                                                });
                                            }).finally(() => {
                                                self.list[data.index].loading = false;
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
                managementSearch: '',
                selection: [],
            };
        },
        methods: {
            batchRemove() {
                const self = this;
                const query = [];
                if (self.selection.length > 0) {
                    self.selection.forEach(item => {
                        query.push(self.$http.post(`${window.api}/mall/admin/store/grade/remove`, {
                            id: item.id,
                        }));
                    });
                    self.loading = true;
                    self.$http.all(query).then(() => {
                        self.$notice.open({
                            title: '批量删除店铺等级信息成功！',
                        });
                    }).catch(() => {
                        self.$notice.error({
                            title: '批量删除店铺等级信息失败！',
                        });
                    }).finally(() => {
                        self.loading = false;
                    });
                } else {
                    self.$notice.error({
                        title: '请选择一个店铺等级！',
                    });
                }
            },
            refresh() {
                const self = this;
                self.$notice.open({
                    title: '正在刷新数据...',
                });
                self.$loading.start();
                self.$http.post(`${window.api}/mall/admin/store/grade/list`).then(response => {
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
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="store-level">
            <tabs value="name1">
                <tab-pane label="店铺等级" name="name1">
                    <card :bordered="false">
                        <div class="advertisement-action">
                            <router-link to="/mall/store/grade/create">
                                <i-button class="add-data" type="ghost">+新增数据</i-button>
                            </router-link>
                            <i-button :loading="loading" type="ghost" @click.native="batchRemove">批量删除</i-button>
                            <i-button class="refresh" icon="android-sync" type="text" @click="refresh">刷新</i-button>
                            <div class="goods-body-header-right">
                                <i-input v-model="managementWord" placeholder="等级名称">
                                    <i-button slot="append" type="primary">搜索</i-button>
                                </i-input>
                            </div>
                        </div>
                        <i-table :columns="columns" :data="list" highlight-row @on-selection-change="selectionChange"></i-table>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>