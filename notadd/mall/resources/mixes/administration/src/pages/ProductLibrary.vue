<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/mall/admin/product/library/list`).then(response => {
                window.console.log(response);
                const data = response.data.data;
                const pagination = response.data.pagination;
                next(vm => {
                    vm.list = data.map(item => {
                        item.loading = false;
                        return item;
                    });
                    vm.pagination = pagination;
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
                        key: 'name',
                        title: '商品名称',
                    },
                    {
                        align: 'center',
                        key: 'image',
                        render(h, data) {
                            return h('tooltip', {
                                props: {
                                    placement: 'right-end',
                                },
                                scopedSlots: {
                                    content() {
                                        return h('img', {
                                            domProps: {
                                                src: data.row.image,
                                            },
                                            style: {
                                                maxHeight: '200px',
                                                maxWidth: '200px',
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
                        title: '商品图片',
                        width: 120,
                    },
                    {
                        align: 'left',
                        key: 'advertising',
                        title: '广告词',
                    },
                    {
                        align: 'center',
                        render(h, data) {
                            if (data.row.category) {
                                return data.row.category.id;
                            }
                            return '';
                        },
                        title: '分类ID',
                    },
                    {
                        align: 'center',
                        render(h, data) {
                            if (data.row.category) {
                                return data.row.category.name;
                            }
                            return '';
                        },
                        title: '分类名称',
                    },
                    {
                        align: 'center',
                        render(h, data) {
                            if (data.row.brand) {
                                return data.row.brand.id;
                            }
                            return '';
                        },
                        title: '品牌ID',
                    },
                    {
                        align: 'center',
                        render(h, data) {
                            if (data.row.brand) {
                                return data.row.brand.name;
                            }
                            return '';
                        },
                        title: '品牌名称',
                    },
                    {
                        align: 'center',
                        key: 'created_at',
                        title: '发布时间',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('router-link', {
                                    props: {
                                        to: `library/edit/${data.row.id}`,
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
                                            self.$http.post(`${window.api}/mall/admin/product/library/remove`, {
                                                id: data.row.id,
                                            }).then(() => {
                                                self.$notice.open({
                                                    title: '删除商品库信息成功！',
                                                });
                                                self.$notice.open({
                                                    title: '正在刷新商品库信息...',
                                                });
                                                self.$loading.start();
                                                self.$http.post(`${window.api}/mall/admin/product/library/list`).then(response => {
                                                    self.$loading.finish();
                                                    self.list = response.data.data.map(item => {
                                                        item.loading = false;
                                                        return item;
                                                    });
                                                    self.pagination = response.data.pagination;
                                                }).catch(() => {
                                                    self.$loading.fail();
                                                });
                                            }).catch(() => {
                                                self.$notice.error({
                                                    title: '删除商品库信息失败！',
                                                });
                                            }).finally(() => {
                                                self.list[data.index].loading = false;
                                            });
                                            console.log(data.row, self);
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
                        width: 160,
                    },
                ],
                filers: [
                    {
                        label: '商品名称',
                        value: '商品名称',
                    },
                    {
                        label: '分类名称',
                        value: '分类名称',
                    },
                    {
                        label: '品牌名称',
                        value: '品牌名称',
                    },
                ],
                form: {
                    filter: '',
                    keyword: '',
                },
                list: [],
                pagination: {
                    current_page: 1,
                },
            };
        },
        methods: {
            remove(index) {
                this.list.splice(index, 1);
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="goods-library">
            <tabs value="name1">
                <tab-pane label="商品库管理" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>商品库中该数据的删除不影响店铺已经认领的商品</p>
                        </div>
                        <div class="store-body">
                            <div class="store-body-header">
                                <router-link to="/mall/product/library/add">
                                    <i-button class="export-btn" type="ghost">+新增数据</i-button>
                                </router-link>
                                <div class="store-body-header-right">
                                    <i-input v-model="form.keyword" placeholder="请输入关键词进行搜索">
                                        <i-select v-model="form.filter" slot="prepend" style="width: 100px">
                                            <i-option :value="item.value"
                                                      :key="item"
                                                      v-for="item in searchList">{{ item.label }}
                                            </i-option>
                                        </i-select>
                                        <i-button slot="append" type="primary">搜索</i-button>
                                    </i-input>
                                </div>
                            </div>
                            <i-table class="shop-table"
                                     :context="self"
                                     :columns="columns"
                                     :data="list"
                                     ref="orderTable"
                                     highlight-row>
                            </i-table>
                        </div>
                        <div class="page">
                            <page :current="pagination.current_page"
                                  :page-size="pagination.per_page"
                                  :total="pagination.total"
                                  show-elevator></page>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>