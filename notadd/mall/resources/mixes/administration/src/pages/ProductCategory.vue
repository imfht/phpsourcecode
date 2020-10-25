<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            window.console.log(to);
            injection.loading.start();
            const data = {};
            if (to.query.parent) {
                data.parent_id = to.query.parent;
            }
            injection.http.post(`${window.api}/mall/admin/product/category/list`, data).then(response => {
                window.console.log(response);
                next(vm => {
                    vm.category = response.data.current;
                    vm.level = response.data.level;
                    vm.list = response.data.data.map(item => {
                        item.loading = false;
                        return item;
                    });
                    vm.pagination = response.data.pagination;
                    vm.parent = to.query.parent;
                    injection.loading.finish();
                });
            }).catch(() => {
                injection.loading.fail();
            });
        },
        data() {
            const self = this;
            return {
                category: {},
                columns: [
                    {
                        align: 'center',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        key: 'order',
                        title: '排序',
                        width: 150,
                    },
                    {
                        key: 'name',
                        title: '分类名称',
                        width: 200,
                    },
                    {
                        align: 'center',
                        key: 'deposit',
                        render(h, data) {
                            return `${data.row.deposit} %`;
                        },
                        title: '分拥比例',
                        width: 150,
                    },
                    {
                        key: 'goodShow',
                        title: '商品展示',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            if (self.level === 3) {
                                return h('div', [
                                    h('router-link', {
                                        props: {
                                            to: `/mall/product/category/edit/${data.row.id}`,
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
                                                self.remove(data.index);
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
                            }
                            return h('div', [
                                h('dropdown', {
                                    scopedSlots: {
                                        list() {
                                            return h('dropdown-menu', [
                                                h('dropdown-item', {
                                                    nativeOn: {
                                                        click() {
                                                            self.$router.push({
                                                                path: `category/edit/${data.row.id}`,
                                                            });
                                                        },
                                                    },
                                                }, '编辑分类信息'),
                                                h('dropdown-item', {
                                                    nativeOn: {
                                                        click() {
                                                            self.$router.push({
                                                                path: '/mall/product/category/add',
                                                                query: {
                                                                    parent: data.row.id,
                                                                },
                                                            });
                                                        },
                                                    },
                                                }, '新增下级分类'),
                                                h('dropdown-item', {
                                                    nativeOn: {
                                                        click() {
                                                            self.$router.push({
                                                                path: '/mall/product/category',
                                                                query: {
                                                                    parent: data.row.id,
                                                                },
                                                            });
                                                        },
                                                    },
                                                }, '查看下级分类'),
                                            ]);
                                        },
                                    },
                                }, [
                                    h('i-button', {
                                        props: {
                                            size: 'small',
                                            type: 'ghost',
                                        },
                                    }, [
                                        '设置',
                                        h('icon', {
                                            props: {
                                                type: 'arrow-down-b',
                                            },
                                        }),
                                    ]),
                                ]),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.remove(data.index);
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
                        width: 200,
                    },
                ],
                level: 0,
                list: [],
                loading: false,
                pagination: {
                    current_page: 1,
                },
                parent: 0,
                searchCategory: '',
                searchWord: '',
                selection: [],
            };
        },
        methods: {
            addSubordinate() {
                const self = this;
                self.$router.push({
                    path: 'category/add/under',
                });
            },
            batchRemove() {
                const handlers = [];
                const self = this;
                self.loading = true;
                self.selection.forEach(item => {
                    handlers.push(self.$http.post(`${window.api}/mall/admin/product/category/remove`, {
                        id: item.id,
                    }));
                });
                self.$http.all(handlers).then(self.$http.spread(() => {
                    self.$notice.open({
                        title: '批量删除商品分类信息成功！',
                    });
                    self.refresh();
                })).finally(() => {
                    self.loading = false;
                });
            },
            editTypeNav() {
                const self = this;
                self.$router.push({
                    path: 'category/edit/nav',
                });
            },
            exportData() {
                this.$refs.list.exportCsv({
                    filename: '商品分类数据',
                });
            },
            refresh() {
                const self = this;
                self.$notice.open({
                    title: '正在刷新数据...',
                });
                self.$loading.start();
                self.$http.post(`${window.api}/mall/admin/product/category/list`, {
                    parent_id: self.$route.query.parent,
                }).then(response => {
                    window.console.log(response);
                    self.category = response.data.current;
                    self.level = response.data.level;
                    self.list = response.data.data.map(item => {
                        item.loading = false;
                        return item;
                    });
                    self.pagination = response.data.pagination;
                    self.$loading.finish();
                    self.$notice.open({
                        title: '刷新数据成功！',
                    });
                }).catch(() => {
                    self.$loading.fail();
                });
            },
            remove(index) {
                const self = this;
                self.list[index].loading = true;
                window.console.log(self.list[index]);
                self.$http.post(`${window.api}/mall/admin/product/category/remove`, {
                    id: self.list[index].id,
                }).then(() => {
                    self.$notice.open({
                        title: '删除分类信息成功！',
                    });
                    self.refresh();
                }).catch(() => {
                    self.$notice.error({
                        title: '删除分类信息失败！',
                    });
                }).finally(() => {
                    self.list[index].loading = false;
                });
            },
            selectionChange(selection) {
                this.selection = selection;
            },
        },
        watch: {
            $route: {
                handler() {
                    this.refresh();
                },
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="goods-category-look">
            <div class="edit-link-title" v-if="level === 1">
                <span style="margin-left: 20px">分类管理</span>
            </div>
            <div class="edit-link-title" v-if="level === 2">
                <router-link to="/mall/product/category">
                    <i-button type="text">
                        <icon type="chevron-left"></icon>
                    </i-button>
                </router-link>
                <span>分类管理 — "{{ category.name }}"的下级列表(二级)</span>
            </div>
            <div class="edit-link-title" v-if="level === 3">
                <router-link :to="{
                    query: {
                        parent: category.parent_id
                    },
                    to: '/mall/product/category'
                }">
                    <i-button type="text">
                        <icon type="chevron-left"></icon>
                    </i-button>
                </router-link>
                <span>分类管理 — "{{ category.name }}"的下级列表(三级)</span>
            </div>
            <card :bordered="false">
                <div class="prompt-box">
                    <p>提示</p>
                    <p>当店主添加商品时可选择商品分类，用户可根据分类查询商品列表</p>
                    <p>对分类做任何更改后，都需要到 站点设置>清理缓存 清理商品分类，新的设置才会生效</p>
                </div>
                <div class="store-body">
                    <div class="store-body-header">
                        <router-link :to="{
                            path: '/mall/product/category/add',
                            query: {
                                parent: category.id
                            }
                        }" v-if="category.id">
                            <i-button type="ghost">+新增数据</i-button>
                        </router-link>
                        <router-link :to="'/mall/product/category/add'" v-else>
                            <i-button type="ghost">+新增数据</i-button>
                        </router-link>
                        <i-button @click="exportData" type="ghost">导出数据</i-button>
                        <i-button :loading="loading" @click="batchRemove" type="ghost">批量删除</i-button>
                        <i-button class="refresh" icon="android-sync" type="text" @click.native="refresh">刷新</i-button>
                    </div>
                    <i-table class="shop-table"
                             :columns="columns"
                             :data="list"
                             highlight-row
                             ref="list"
                             @on-selection-change="selectionChange"></i-table>
                </div>
                <div class="page">
                    <page :current="pagination.current_page"
                          :page-size="pagination.per_page"
                          :total="pagination.total"
                          show-elevator></page>
                </div>
            </card>
        </div>
    </div>
</template>