<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.all([
                injection.http.post(`${window.api}/mall/admin/store/list`, {
                    status: 'opening',
                }),
                injection.http.post(`${window.api}/mall/admin/store/list`, {
                    status: 'review',
                }),
            ]).then(injection.http.spread((opening, review) => {
                next(vm => {
                    vm.data.opening = Object.keys(opening.data.data).map(index => {
                        const item = opening.data.data[index];
                        item.loading = false;
                        return item;
                    });
                    vm.data.review = Object.keys(review.data.data).map(index => {
                        const item = review.data.data[index];
                        item.loading = false;
                        return item;
                    });
                });
            })).catch(() => {
                injection.loading.fail();
            });
        },
        data() {
            const self = this;
            return {
                column: {
                    opening: [
                        {
                            align: 'center',
                            fixed: 'left',
                            type: 'selection',
                            width: 60,
                        },
                        {
                            align: 'center',
                            key: 'shopID',
                            title: '店铺ID',
                            width: 190,
                        },
                        {
                            align: 'center',
                            key: 'shopName',
                            title: '店铺名称',
                            width: 170,
                        },
                        {
                            align: 'center',
                            key: 'ownerId',
                            title: '店主账号',
                            width: 170,
                        },
                        {
                            align: 'center',
                            key: 'businessNumber',
                            title: '商家账号',
                            width: 170,
                        },
                        {
                            align: 'center',
                            key: 'shopImg',
                            render(h, data) {
                                return h('tooltip', {
                                    props: {
                                        placement: 'right-end',
                                    },
                                    scopedSlots: {
                                        content() {
                                            return h('img', {
                                                domProps: {
                                                    src: data.row.shopImg,
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
                            title: '店铺头像',
                            width: 100,
                        },
                        {
                            align: 'center',
                            key: 'shopLogo',
                            render(h, data) {
                                return h('tooltip', {
                                    props: {
                                        placement: 'right-end',
                                    },
                                    scopedSlots: {
                                        content() {
                                            return h('img', {
                                                domProps: {
                                                    src: data.row.shopLogo,
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
                            title: '店铺LOGO',
                            width: 100,
                        },
                        {
                            align: 'center',
                            key: 'shopLevel',
                            title: '店铺等级',
                            width: 170,
                        },
                        {
                            align: 'center',
                            key: 'shopTime',
                            title: '开店时间',
                            width: 170,
                        },
                        {
                            align: 'left',
                            key: 'endTime',
                            title: '到期时间',
                            width: 170,
                        },
                        {
                            align: 'center',
                            fixed: 'right',
                            key: 'action',
                            render(h) {
                                return h('div', [
                                    h('i-button', {
                                        on: {
                                            click() {
                                                self.lookShop();
                                            },
                                        },
                                        props: {
                                            size: 'small',
                                            type: 'ghost',
                                        },
                                    }, '查看'),
                                    h('i-button', {
                                        on: {
                                            click() {
                                                self.toEdit();
                                            },
                                        },
                                        props: {
                                            size: 'small',
                                            type: 'ghost',
                                        },
                                        style: {
                                            marginLeft: '10px',
                                        },
                                    }, '编辑'),
                                ]);
                            },
                            title: '操作',
                            width: 180,
                        },
                    ],
                    review: [
                        {
                            align: 'center',
                            fixed: 'left',
                            type: 'selection',
                            width: 60,
                        },
                        {
                            align: 'center',
                            key: 'memberID',
                            title: '会员ID',
                            width: 190,
                        },
                        {
                            align: 'center',
                            key: 'memberAccount',
                            title: '会员账号',
                            width: 170,
                        },
                        {
                            align: 'center',
                            key: 'applicationStatus',
                            title: '申请状态',
                            width: 170,
                        },
                        {
                            align: 'center',
                            key: 'shopLength',
                            title: '开店时长',
                            width: 170,
                        },
                        {
                            align: 'center',
                            key: 'contactName',
                            title: '联系人姓名',
                            width: 120,
                        },
                        {
                            align: 'center',
                            key: 'contactPhone',
                            title: '联系人电话',
                            width: 120,
                        },
                        {
                            align: 'center',
                            key: 'contactEmail',
                            title: '联系邮箱',
                            width: 120,
                        },
                        {
                            align: 'center',
                            key: 'companyName',
                            title: '公司名称',
                            width: 170,
                        },
                        {
                            align: 'center',
                            key: 'companyAddress',
                            title: '公司地址',
                            width: 170,
                        },
                        {
                            align: 'left',
                            key: 'companyPhone',
                            title: '公司电话',
                            width: 170,
                        },
                        {
                            align: 'center',
                            fixed: 'right',
                            key: 'action',
                            render(h) {
                                return h('i-button', {
                                    on: {
                                        click() {
                                            self.look();
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '查看');
                            },
                            title: '操作',
                            width: 90,
                        },
                    ],
                },
                data: {
                    opening: [],
                    review: [],
                },
                applicationSearch: '',
                applicationWord: '',
                managementWord: '',
                managementSearch: '',
                searchApplicationList: [
                    {
                        label: '会员账号',
                        value: '1',
                    },
                    {
                        label: '会员ID',
                        value: '2',
                    },
                ],
                searchList: [
                    {
                        label: '店铺名称',
                        value: '1',
                    },
                    {
                        label: '店主账号',
                        value: '2',
                    },
                    {
                        label: '商家账号',
                        value: '3',
                    },
                ],
            };
        },
        methods: {
            exportData() {
                this.$refs.managementTable.exportCsv({
                    filename: '店铺管理数据',
                });
            },
            look() {
                const self = this;
                self.$router.push({
                    path: 'store/look/application',
                });
            },
            lookShop() {
                const self = this;
                self.$router.push({
                    path: 'store/look',
                });
            },
            toEdit() {
                const self = this;
                self.$router.push({
                    path: 'store/edit',
                });
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="store-wrap">
            <tabs value="name1">
                <tab-pane label="店铺管理" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>如果当前时间超过店铺有效期或店铺处于关闭状态，前台将不能继续浏览该店铺，
                                但是店主仍然可以编辑该店铺</p>
                        </div>
                        <div class="store-body">
                            <div class="store-body-header">
                                <i-button class="export-btn" @click="exportData" type="ghost">导出数据</i-button>
                                <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
                                <div class="store-body-header-right">
                                    <i-input v-model="managementWord" placeholder="请输入关键词进行搜索">
                                        <i-select v-model="managementSearch" slot="prepend" style="width: 100px;">
                                            <i-option v-for="item in searchList"
                                                      :value="item.value">{{ item.label }}</i-option>
                                        </i-select>
                                        <i-button slot="append" type="primary">搜索</i-button>
                                    </i-input>
                                </div>
                            </div>
                            <i-table ref="managementTable"
                                     class="shop-table"
                                     :columns="column.opening"
                                     :data="data.opening"></i-table>
                        </div>
                        <div class="page">
                            <page :total="100" show-elevator></page>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="开店申请" name="name2">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>如果当前时间超过店铺有效期或店铺处于关闭状态，前台将不能继续浏览该店铺，
                                但是店主仍然可以编辑该店铺</p>
                        </div>
                        <div class="store-body">
                            <div class="store-body-header">
                                <div class="store-body-header-right">
                                    <i-input v-model="applicationWord" placeholder="请输入关键词进行搜索">
                                        <i-select v-model="applicationSearch" slot="prepend" style="width: 100px;">
                                            <i-option :key="item"
                                                      :value="item.value"
                                                      v-for="item in searchApplicationList">
                                                {{ item.label }}</i-option>
                                        </i-select>
                                        <i-button slot="append" type="primary">搜索</i-button>
                                    </i-input>
                                </div>
                            </div>
                            <i-table highlight-row class="shop-table"
                                     :columns="column.review"
                                     :data="data.review"
                                     ref="applicationTable" >
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