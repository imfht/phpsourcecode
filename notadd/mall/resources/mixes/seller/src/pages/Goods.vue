<script>
    import image1 from '../assets/images/img_logo.png';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                goodsColumns: [
                    {
                        align: 'center',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        key: 'goodsName',
                        render(h, data) {
                            return h('div', {
                                class: {
                                    'goods-name-row': true,
                                },
                            }, [
                                h('div', {
                                    class: {
                                        img: true,
                                    },
                                }, [
                                    h('img', {
                                        domProps: {
                                            src: data.row.goodsImg,
                                        },
                                    }),
                                ]),
                                h('div', {
                                    class: {
                                        'right-text': true,
                                    },
                                }, [
                                    h('p', data.row.goodsName),
                                    h('div', [
                                        h('span', {
                                            class: {
                                                'left-num': true,
                                            },
                                        }, `商家货号：${data.row.sellNum}`),
                                        h('span', `SPU：${data.row.sellSpu}`),
                                    ]),
                                ]),
                            ]);
                        },
                        title: '商品名称',
                    },
                    {
                        align: 'center',
                        key: 'goodsPrice',
                        title: '价格',
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'goodsStock',
                        title: '库存',
                        width: 100,
                    },
                    {
                        align: 'center',
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
                        title: '上架',
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.editGoods(data.index);
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
                                            self.removeGoods(data.index);
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
                goodsData: [
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsStock: '54',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                        status: true,
                    },
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsStock: '54',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                        status: false,
                    },
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsStock: '54',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                        status: false,
                    },
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsStock: '54',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                        status: false,
                    },
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsStock: '54',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                        status: false,
                    },
                ],
                goodsRecoverColumns: [
                    {
                        align: 'center',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        key: 'goodsName',
                        render(h, data) {
                            return h('div', {
                                class: {
                                    'goods-name-row': true,
                                },
                            }, [
                                h('div', {
                                    class: {
                                        img: true,
                                    },
                                }, [
                                    h('img', {
                                        domProps: {
                                            src: data.row.goodsImg,
                                        },
                                    }),
                                ]),
                                h('div', {
                                    class: {
                                        'right-text': true,
                                    },
                                }, [
                                    h('p', data.row.goodsName),
                                    h('div', [
                                        h('span', {
                                            class: {
                                                'left-num': true,
                                            },
                                        }, `商家货号：${data.row.sellNum}`),
                                        h('span', `SPU：${data.row.sellSpu}`),
                                    ]),
                                ]),
                            ]);
                        },
                        title: '商品名称',
                    },
                    {
                        align: 'center',
                        key: 'goodsPrice',
                        title: '价格',
                        width: 180,
                    },
                    {
                        align: 'center',
                        key: 'goodsStock',
                        title: '库存',
                        width: 180,
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
                                }, '还原'),
                                h('i-button', {
                                    class: {
                                        'delete-ad': true,
                                    },
                                    on: {
                                        click() {
                                            self.removeRecoverGoods(data.index);
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
                goodsRecoverData: [
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsStock: '54',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                    },
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsStock: '54',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                    },
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsStock: '54',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                    },
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsStock: '54',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                    },
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsStock: '54',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                    },
                ],
                goodsSellerColumns: [
                    {
                        align: 'center',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        key: 'goodsName',
                        render(h, data) {
                            return h('div', {
                                class: {
                                    'goods-name-row': true,
                                },
                            }, [
                                h('div', {
                                    class: {
                                        img: true,
                                    },
                                }, [
                                    h('img', {
                                        domProps: {
                                            src: data.row.goodsImg,
                                        },
                                    }),
                                ]),
                                h('div', {
                                    class: {
                                        'right-text': true,
                                    },
                                }, [
                                    h('p', data.row.goodsName),
                                    h('div', [
                                        h('span', {
                                            class: {
                                                'left-num': true,
                                            },
                                        }, `商家货号：${data.row.sellNum}`),
                                        h('span', `SPU：${data.row.sellSpu}`),
                                    ]),
                                ]),
                            ]);
                        },
                        title: '商品名称',
                    },
                    {
                        align: 'center',
                        key: 'goodsPrice',
                        title: '价格',
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'goodsStock',
                        title: '库存',
                        width: 100,
                    },
                    {
                        align: 'center',
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
                        title: '上架',
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.editGoods(data.index);
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
                                            self.removeSellerGoods(data.index);
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
                goodsSellerData: [
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsStock: '54',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                        status: true,
                    },
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsStock: '54',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                        status: false,
                    },
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsStock: '54',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                        status: false,
                    },
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsStock: '54',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                        status: false,
                    },
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsStock: '54',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                        status: false,
                    },
                ],
                goodsViolationColumns: [
                    {
                        align: 'center',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        key: 'goodsName',
                        render(h, data) {
                            return h('div', {
                                class: {
                                    'goods-name-row': true,
                                },
                            }, [
                                h('div', {
                                    class: {
                                        img: true,
                                    },
                                }, [
                                    h('img', {
                                        domProps: {
                                            src: data.row.goodsImg,
                                        },
                                    }),
                                ]),
                                h('div', {
                                    class: {
                                        'right-text': true,
                                    },
                                }, [
                                    h('p', data.row.goodsName),
                                    h('div', [
                                        h('span', {
                                            class: {
                                                'left-num': true,
                                            },
                                        }, `商家货号：${data.row.sellNum}`),
                                        h('span', `SPU：${data.row.sellSpu}`),
                                    ]),
                                ]),
                            ]);
                        },
                        title: '商品名称',
                    },
                    {
                        align: 'center',
                        key: 'goodsReason',
                        title: '违规原因',
                        width: 180,

                    },
                    {
                        align: 'center',
                        key: 'goodsPrice',
                        title: '价格',
                        width: 180,
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.editGoods(data.index);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '还原'),
                                h('i-button', {
                                    class: {
                                        'delete-ad': true,
                                    },
                                    on: {
                                        click() {
                                            self.removeReviewGoods(data.index);
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
                goodsViolationData: [
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsReason: '商品分类被删除',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                    },
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsReason: '商品分类被删除',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                    },
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsReason: '商品分类被删除',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                    },
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsReason: '商品分类被删除',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                    },
                    {
                        goodsImg: image1,
                        goodsName: 'MIUI/小米 小米手机4 小米4代 MI4智能4G手机包邮 黑色 D-LTE（4G）/TD-SCD',
                        goodsPrice: '￥4826.07',
                        goodsReason: '商品分类被删除',
                        sellNum: 54277777777,
                        sellSpu: 324356,
                    },
                ],
                searchList: [
                    {
                        label: 'SPU',
                        value: '1',
                    },
                    {
                        label: '商品名称',
                        value: '2',
                    },
                    {
                        label: '商家货号',
                        value: '3',
                    },
                ],
                self: this,
            };
        },
        methods: {
            editGoods() {
                const self = this;
                self.$router.push(
                    {
                        path: 'goods/edit',
                    },
                );
            },
            removeGoods(index) {
                this.goodsData.splice(index, 1);
            },
            removeRecoverGoods(index) {
                this.goodsRecoverData.splice(index, 1);
            },
            removeReviewGoods(index) {
                this.goodsReviewData.splice(index, 1);
            },
            removeSellerGoods(index) {
                this.goodsSellerData.splice(index, 1);
            },
            removeViolationGoods(index) {
                this.goodsViolationData.splice(index, 1);
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="goods-wrap">
            <tabs value="name1">
                <tab-pane label="商品列表" name="name1">
                    <card :bordered="false">
                        <div class="goods-list">
                            <div class="goods-body-header">
                                <router-link to="/seller/goods/add">
                                    <i-button type="ghost" class="first-btn">发布新商品</i-button>
                                </router-link>
                                <router-link to="/seller/goods/claim">
                                    <i-button class="first-btn" type="ghost">认领商品</i-button>
                                </router-link>
                                <i-button type="ghost">批量删除</i-button>
                                <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
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
                            <i-table class="goods-table"
                                     :columns="goodsColumns"
                                     :context="self"
                                     :data="goodsData"
                                     ref="goodsList"
                                     highlight-row>
                            </i-table>
                        </div>
                        <div class="page">
                            <page :total="100" show-elevator></page>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="正在出售" name="name2">
                    <card :bordered="false">
                        <div class="goods-list">
                            <div class="goods-body-header">
                                <i-button type="ghost" class="first-btn">发布新商品</i-button>
                                <i-button type="ghost">批量删除</i-button>
                                <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
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
                            <i-table class="goods-table"
                                     :columns="goodsSellerColumns"
                                     :context="self"
                                     :data="goodsSellerData"
                                     ref="goodsSellerList"
                                     highlight-row>
                            </i-table>
                        </div>
                        <div class="page">
                            <page :total="100" show-elevator></page>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="违规商品" name="name3">
                    <card :bordered="false">
                        <div class="goods-list">
                            <div class="goods-body-header">
                                <i-button type="ghost">批量删除</i-button>
                                <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
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
                            <i-table class="goods-table"
                                     :columns="goodsViolationColumns"
                                     :context="self"
                                     :data="goodsViolationData"
                                     ref="goodsViolationList"
                                     highlight-row>
                            </i-table>
                        </div>
                        <div class="page">
                            <page :total="100" show-elevator></page>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="商品回收站" name="name4">
                    <card :bordered="false">
                        <div class="goods-list">
                            <div class="goods-body-header">
                                <i-button type="ghost" class="first-btn">批量还原</i-button>
                                <i-button type="ghost">批量删除</i-button>
                                <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
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
                            <i-table class="goods-table"
                                     :columns="goodsRecoverColumns"
                                     :context="self"
                                     :data="goodsRecoverData"
                                     ref="goodsRecoverList"
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