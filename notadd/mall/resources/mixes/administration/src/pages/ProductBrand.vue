<script>
    import injection from '../helpers/injection';
    import image1 from '../assets/images/img_logo.png';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/mall/admin/product/brand/list`).then(response => {
                window.console.log(response);
                next(vm => {
                    vm.list.data = response.data.data.map(item => {
                        item.loading = false;
                        item.recommend = item.recommend === 1;
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
                column: {
                    audit: [
                        {
                            align: 'center',
                            key: 'id',
                            title: '品牌ID',
                            width: 120,
                        },
                        {
                            align: 'center',
                            key: 'name',
                            title: '品牌名称',
                            width: 180,
                        },
                        {
                            align: 'center',
                            key: 'initials',
                            title: '首字母',
                            width: 180,
                        },
                        {
                            align: 'center',
                            key: 'brandPicture',
                            render(h, data) {
                                if (data.row.pic) {
                                    return h('tooltip', {
                                        props: {
                                            placement: 'right-end',
                                        },
                                        scopedSlots: {
                                            content() {
                                                return h('img', {
                                                    domProps: {
                                                        src: data.row.pic,
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
                                }
                                return '';
                            },
                            title: '品牌图片',
                            width: 200,
                        },
                        {
                            key: 'reviewStatus',
                            title: '审核状态',
                        },
                        {
                            align: 'center',
                            key: 'action',
                            render(h) {
                                return h('div', [
                                    h('i-button', {
                                        on: {
                                            click() {
                                            },
                                        },
                                        props: {
                                            size: 'small',
                                            type: 'ghost',
                                        },
                                    }, '通过'),
                                    h('i-button', {
                                        on: {
                                            click() {
                                            },
                                        },
                                        props: {
                                            size: 'small',
                                            type: 'ghost',
                                        },
                                        style: {
                                            marginLeft: '10px',
                                        },
                                    }, '拒绝'),
                                ]);
                            },
                            title: '操作',
                            width: 180,
                        },
                    ],
                    list: [
                        {
                            align: 'center',
                            key: 'id',
                            title: '品牌ID',
                            width: 120,
                        },
                        {
                            align: 'center',
                            key: 'name',
                            title: '品牌名称',
                        },
                        {
                            align: 'center',
                            key: 'initial',
                            title: '首字母',
                        },
                        {
                            align: 'center',
                            key: 'brandPicture',
                            render(h, data) {
                                if (data.row.logo) {
                                    return h('tooltip', {
                                        props: {
                                            placement: 'right-end',
                                        },
                                        scopedSlots: {
                                            content() {
                                                return h('img', {
                                                    domProps: {
                                                        src: data.row.logo,
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
                                }
                                return '';
                            },
                            title: '品牌图片',
                        },
                        {
                            align: 'center',
                            key: 'order',
                            title: '品牌排序',
                        },
                        {
                            align: 'center',
                            key: 'recommend',
                            render(h, data) {
                                if (data.row.recommend === true) {
                                    return h('span', {
                                        class: {
                                            'status-check': true,
                                        },
                                    }, [
                                        h('icon', {
                                            props: {
                                                type: 'checkmark-circled',
                                            },
                                        }),
                                        '是',
                                    ]);
                                }
                                return h('span', [
                                    h('icon', {
                                        props: {
                                            type: 'close-circled',
                                        },
                                    }),
                                    '否',
                                ]);
                            },
                            title: '是否推荐品牌',
                        },
                        {
                            key: 'show',
                            render(h, data) {
                                if (data.row.show === 'image') {
                                    return '图片';
                                }
                                return '文字';
                            },
                            title: '展示形式',
                        },
                        {
                            align: 'center',
                            key: 'action',
                            render(h, data) {
                                return h('div', [
                                    h('router-link', {
                                        props: {
                                            to: `/mall/product/brand/edit/${data.row.id}`,
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
                                                self.list.data[data.index].loading = true;
                                                self.$http.post(`${window.api}/mall/admin/product/brand/remove`, {
                                                    id: self.list.data[data.index].id,
                                                }).then(() => {
                                                    self.$notice.open({
                                                        title: '删除商品品牌信息成功！',
                                                    });
                                                    self.$loading.start();
                                                    self.$notice.open({
                                                        title: '正在刷新数据...',
                                                    });
                                                    const api = `${window.api}/mall/admin/product/brand/list`;
                                                    self.$http.post(api).then(response => {
                                                        const items = response.data.data.map(item => {
                                                            item.loading = false;
                                                            return item;
                                                        });
                                                        self.list.data = items;
                                                        self.pagination = response.data.pagination;
                                                        self.$loading.finish();
                                                        self.$notice.open({
                                                            title: '刷新数据完成！',
                                                        });
                                                    }).catch(() => {
                                                        self.$loading.fail();
                                                    });
                                                }).catch(() => {
                                                    self.$notice.error({
                                                        title: '删除商品品牌信息错误！',
                                                    });
                                                }).finally(() => {
                                                    self.list.data[data.index].loading = false;
                                                });
                                            },
                                        },
                                        props: {
                                            loading: self.list.data[data.index].loading,
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
                },
                list: {
                    audit: [
                        {
                            brandId: '001',
                            initials: 'Y',
                            name: '迪卡侬',
                            pic: image1,
                            reviewStatus: '未审核',
                        },
                    ],
                    data: [
                        {
                            brandId: '001',
                            initials: 'Y',
                            isshow: '是',
                            name: '迪卡侬',
                            pic: image1,
                            sort: 4,
                            status: true,
                            showStyle: '图片',
                        },
                    ],
                },
                searchList: [
                    {
                        label: '品牌ID',
                        value: '1',
                    },
                    {
                        label: '品牌名称',
                        value: '2',
                    },
                ],
                pagination: {},
            };
        },
        methods: {
            exportData() {
                this.$refs.brand.exportCsv({
                    filename: '品牌管理数据',
                });
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="goods-brand">
            <tabs value="name1">
                <tab-pane label="品牌管理" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>当店主添加商品时可选择商品品牌，用户可根据品牌查询商品列表
                                被推荐品牌，将在前台品牌推荐模块显示</p>
                            <p>在品牌列表页面，品牌将按类别分组，即具有相同类别的品牌为一组，品牌类别与品牌分类无联系</p>
                        </div>
                        <div class="brand-management">
                            <div class="store-body-header">
                                <router-link to="/mall/product/brand/add">
                                    <i-button class="add-data" type="ghost">+新增数据</i-button>
                                </router-link>
                                <i-button @click="exportData" type="ghost">导出数据</i-button>
                                <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
                                <div class="store-body-header-right">
                                    <i-input v-model="managementWord" placeholder="请输入关键词进行搜索">
                                        <i-select v-model="managementSearch" slot="prepend" style="width: 100px;">
                                            <i-option v-for="item in searchList"
                                                      :value="item.value">{{ item.label }}
                                            </i-option>
                                        </i-select>
                                        <i-button slot="append" type="primary">搜索</i-button>
                                    </i-input>
                                </div>
                            </div>
                        </div>
                        <i-table :columns="column.list"
                                 :data="list.data"
                                 highlight-row
                                 ref="brand">
                        </i-table>
                    </card>
                </tab-pane>
                <tab-pane label="待审核" name="name2">
                    <card :bordered="false">
                        <div class="brand-management">
                            <div class="store-body-header">
                                <div class="store-body-header-right">
                                    <i-input v-model="managementWord" placeholder="请输入关键词进行搜索">
                                        <i-select v-model="managementSearch" slot="prepend" style="width: 100px;">
                                            <i-option v-for="item in searchList"
                                                      :value="item.value">{{ item.label }}
                                            </i-option>
                                        </i-select>
                                        <i-button slot="append" type="primary">搜索</i-button>
                                    </i-input>
                                </div>
                            </div>
                        </div>
                        <i-table :columns="column.audit"
                                 :data="list.audit"
                                 highlight-row
                                 ref="brand">
                        </i-table>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>