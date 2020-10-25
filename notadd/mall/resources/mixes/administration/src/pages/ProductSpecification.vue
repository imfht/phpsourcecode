<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/mall/admin/product/specification/list`).then(response => {
                const data = response.data.data;
                next(vm => {
                    vm.list = data.map(item => {
                        item.loading = false;
                        return item;
                    });
                    vm.pagination = response.data.pagination;
                    injection.loading.finish();
                });
            }).catch(() => {
                injection.loading.fail();
            });
        },
        data() {
            const self = this;
            return {
                managementSearch: '',
                searchList: [
                    {
                        label: '规格名称',
                        value: '1',
                    },
                    {
                        label: '规格ID',
                        value: '2',
                    },
                    {
                        label: '快捷定位名称',
                        value: '3',
                    },
                    {
                        label: '快捷定位ID',
                        value: '4',
                    },
                ],
                columns: [
                    {
                        align: 'center',
                        key: 'order',
                        title: '规格排序',
                        width: 120,
                    },
                    {
                        align: 'center',
                        key: 'id',
                        title: '规格ID',
                        width: 120,
                    },
                    {
                        align: 'center',
                        key: 'name',
                        title: '规格名称',
                        width: 160,
                    },
                    {
                        key: 'location',
                        render(h, data) {
                            return data.row.category.breadcrumb;
                        },
                        title: '快捷定位',
                    },
                    {
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('router-link', {
                                    props: {
                                        to: `/mall/product/specification/${data.row.id}/edit`,
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
                                            self.$http.post(`${window.api}/mall/admin/product/specification/remove`, {
                                                id: data.row.id,
                                            }).then(() => {
                                                self.$notice.open({
                                                    title: '删除规格信息成功！',
                                                });
                                                self.refresh();
                                            }).catch(() => {
                                                self.$notice.error({
                                                    title: '删除规格信息失败！',
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
                pagination: {
                    current_page: 1,
                },
            };
        },
        methods: {
            refresh() {
                const self = this;
                self.$loading.start();
                self.$notice.open({
                    title: '正在刷新数据...',
                });
                self.$http.post(`${window.api}/mall/admin/product/specification/list`).then(response => {
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
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="goods-standard">
            <tabs value="name1">
                <tab-pane label="规格管理" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>规格将会对应到商品发布的规格，规格值由店铺自己增加</p>
                            <p>默认安装中会添加一个颜色规格，请不要删除，只有这个颜色规格才能在商品详细页显示为图片</p>
                        </div>
                        <div class="advertisement-action">
                            <router-link to="/mall/product/specification/add">
                                <i-button class="add-data" type="ghost">+新增数据</i-button>
                            </router-link>
                            <i-button class="refresh" icon="android-sync" type="text" @click="refresh">刷新</i-button>
                            <div class="goods-body-header-right">
                                <i-input v-model="managementWord" placeholder="请输入关键词进行搜索">
                                    <i-select v-model="managementSearch" slot="prepend" style="width: 100px;">
                                        <i-option v-for="item in searchList"
                                                  :value="item.value">{{ item.label }}</i-option>
                                    </i-select>
                                    <i-button slot="append" type="primary">搜索</i-button>
                                </i-input>
                            </div>
                        </div>
                        <i-table highlight-row :columns="columns" :data="list"></i-table>
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