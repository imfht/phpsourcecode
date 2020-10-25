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
                column: {
                    illegal: [
                        {
                            align: 'center',
                            type: 'selection',
                            width: 60,
                        },
                        {
                            align: 'center',
                            key: 'goodsName',
                            title: '商品名称',
                        },
                        {
                            align: 'center',
                            key: 'spu',
                            title: 'spu',
                        },
                        {
                            align: 'center',
                            key: 'goodsPrice',
                            title: '商品价格',
                        },
                        {
                            align: 'center',
                            key: 'goodsStatus',
                            title: '商品状态',
                        },
                        {
                            align: 'center',
                            key: 'reviewStatus',
                            title: '审核状态',
                        },
                        {
                            align: 'center',
                            key: 'goodsImg',
                            render(h, data) {
                                return h('tooltip', {
                                    props: {
                                        placement: 'right-end',
                                    },
                                    scopedSlots: {
                                        content() {
                                            return h('img', {
                                                domProps: {
                                                    src: data.row.goodsImg,
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
                        },
                        {
                            align: 'center',
                            key: 'adWord',
                            title: '广告词',
                        },
                        {
                            align: 'center',
                            key: 'styleId',
                            title: '分类ID',
                        },
                        {
                            align: 'center',
                            key: 'styleName',
                            title: '分类名称',
                        },
                        {
                            align: 'center',
                            key: 'action',
                            render(h, data) {
                                return h('div', [
                                    h('dropdown', {
                                        scopedSlots: {
                                            list() {
                                                return h('dropdown-menu', [
                                                    h('dropdown-item', '查看商品详情'),
                                                    h('dropdown-item', {
                                                        on: {
                                                            click() {
                                                                self.lookGoodsSku();
                                                            },
                                                        },
                                                        props: {
                                                            name: 'goodSku',
                                                        },
                                                    }, '查看商品SKU'),
                                                    h('dropdown-item', '加入商品库'),
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
                                                self.removeShelf(data.index);
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
                    product: [
                        {
                            align: 'center',
                            type: 'selection',
                            width: 60,
                        },
                        {
                            align: 'center',
                            key: 'goodsName',
                            title: '商品名称',
                        },
                        {
                            align: 'center',
                            key: 'spu',
                            title: 'spu',
                        },
                        {
                            align: 'center',
                            key: 'goodsPrice',
                            title: '商品价格',
                        },
                        {
                            align: 'center',
                            key: 'goodsStatus',
                            title: '商品状态',
                        },
                        {
                            align: 'center',
                            key: 'reviewStatus',
                            title: '审核状态',
                        },
                        {
                            align: 'center',
                            key: 'goodsImg',
                            render(h, data) {
                                return h('tooltip', {
                                    props: {
                                        placement: 'right-end',
                                    },
                                    scopedSlots: {
                                        content() {
                                            return h('img', {
                                                domProps: {
                                                    src: data.row.goodsImg,
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
                        },
                        {
                            align: 'center',
                            key: 'adWord',
                            title: '广告词',
                        },
                        {
                            align: 'center',
                            key: 'styleId',
                            title: '分类ID',
                        },
                        {
                            align: 'center',
                            key: 'styleName',
                            title: '分类名称',
                        },
                        {
                            align: 'center',
                            key: 'action',
                            render(h, data) {
                                return h('div', [
                                    h('dropdown', {
                                        scopedSlots: {
                                            list() {
                                                return h('dropdown-menu', [
                                                    h('dropdown-item', '查看商品详情'),
                                                    h('dropdown-item', {
                                                        nativeOn: {
                                                            click() {
                                                                self.lookGoodsSku();
                                                            },
                                                        },
                                                        props: {
                                                            name: 'goodSku',
                                                        },
                                                    }, '查看商品SKU'),
                                                    h('dropdown-item', '加入商品库'),
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
                                            size: 'small',
                                            type: 'ghost',
                                        },
                                        style: {
                                            marginLeft: '10px',
                                        },
                                    }, '下架'),
                                ]);
                            },
                            title: '操作',
                            width: 180,
                        },
                    ],
                    sku: [
                        {
                            align: 'center',
                            key: 'skuId',
                            title: 'SKU编号',
                            width: 185,
                        },
                        {
                            align: 'center',
                            key: 'goodsImg',
                            render(h) {
                                return h('icon', {
                                    props: {
                                        type: 'image',
                                    },
                                });
                            },
                            title: '商品图片',
                            width: 185,
                        },
                        {
                            align: 'center',
                            key: 'skuStock',
                            title: 'SKU库存',
                            width: 185,
                        },
                        {
                            align: 'center',
                            key: 'skuPrice',
                            title: 'SKU价格(元)',
                            width: 185,
                        },
                    ],
                },
                list: {
                    illegal: [
                        {
                            adWord: '纯棉易用七天',
                            goodsImg: image1,
                            goodsName: '太阳镜眼睛放蓝光紫外线',
                            goodsPrice: '99.00',
                            goodsStatus: '出售中',
                            reviewStatus: '通过',
                            spu: '001',
                            styleId: 30,
                            styleName: '运动健康>户外>鞋服',
                        },
                        {
                            adWord: '纯棉易用七天',
                            goodsImg: image1,
                            goodsName: '太阳镜眼睛放蓝光紫外线',
                            goodsPrice: '99.00',
                            goodsStatus: '出售中',
                            reviewStatus: '通过',
                            spu: '001',
                            styleId: 30,
                            styleName: '运动健康>户外>鞋服',
                        },
                        {
                            adWord: '纯棉易用七天',
                            goodsImg: image1,
                            goodsName: '太阳镜眼睛放蓝光紫外线',
                            goodsPrice: '99.00',
                            goodsStatus: '出售中',
                            reviewStatus: '通过',
                            spu: '001',
                            styleId: 30,
                            styleName: '运动健康>户外>鞋服',
                        },
                        {
                            adWord: '纯棉易用七天',
                            goodsImg: image1,
                            goodsName: '太阳镜眼睛放蓝光紫外线',
                            goodsPrice: '99.00',
                            goodsStatus: '出售中',
                            reviewStatus: '通过',
                            spu: '001',
                            styleId: 30,
                            styleName: '运动健康>户外>鞋服',
                        },
                        {
                            adWord: '纯棉易用七天',
                            goodsImg: image1,
                            goodsName: '太阳镜眼睛放蓝光紫外线',
                            goodsPrice: '99.00',
                            goodsStatus: '出售中',
                            reviewStatus: '通过',
                            spu: '001',
                            styleId: 30,
                            styleName: '运动健康>户外>鞋服',
                        },
                    ],
                    product: [
                        {
                            adWord: '纯棉易用七天',
                            goodsImg: image1,
                            goodsName: '太阳镜眼睛放蓝光紫外线',
                            goodsPrice: '99.00',
                            goodsStatus: '出售中',
                            reviewStatus: '通过',
                            spu: '001',
                            styleId: 30,
                            styleName: '运动健康>户外>鞋服',
                        },
                        {
                            adWord: '纯棉易用七天',
                            goodsImg: image1,
                            goodsName: '太阳镜眼睛放蓝光紫外线',
                            goodsPrice: '99.00',
                            goodsStatus: '出售中',
                            reviewStatus: '通过',
                            spu: '001',
                            styleId: 30,
                            styleName: '运动健康>户外>鞋服',
                        },
                        {
                            adWord: '纯棉易用七天',
                            goodsImg: image1,
                            goodsName: '太阳镜眼睛放蓝光紫外线',
                            goodsPrice: '99.00',
                            goodsStatus: '出售中',
                            reviewStatus: '通过',
                            spu: '001',
                            styleId: 30,
                            styleName: '运动健康>户外>鞋服',
                        },
                        {
                            adWord: '纯棉易用七天',
                            goodsImg: image1,
                            goodsName: '太阳镜眼睛放蓝光紫外线',
                            goodsPrice: '99.00',
                            goodsStatus: '出售中',
                            reviewStatus: '通过',
                            spu: '001',
                            styleId: 30,
                            styleName: '运动健康>户外>鞋服',
                        },
                        {
                            adWord: '纯棉易用七天',
                            goodsImg: image1,
                            goodsName: '太阳镜眼睛放蓝光紫外线',
                            goodsPrice: '99.00',
                            goodsStatus: '出售中',
                            reviewStatus: '通过',
                            spu: '001',
                            styleId: 30,
                            styleName: '运动健康>户外>鞋服',
                        },
                    ],
                },
                goodsReview: {
                    switch1: true,
                },
                loading: false,
                modal: false,
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
                        label: '分类ID',
                        value: '3',
                    },
                    {
                        label: '店铺ID',
                        value: '4',
                    },
                    {
                        label: '店铺名称',
                        value: '5',
                    },
                    {
                        label: '品牌ID',
                        value: '6',
                    },
                    {
                        label: '品牌名称',
                        value: '7',
                    },
                ],
                skuData: [
                    {
                        skuId: 133026,
                        skuPrice: 99.00,
                        skuStock: 334455,
                    },
                ],
            };
        },
        methods: {
            exportData() {
                this.$refs.goodsList.exportCsv({
                    filename: '商品管理数据',
                });
            },
            exportShelfData() {
                this.$refs.goodsList.exportCsv({
                    filename: '商品管理数据',
                });
            },
            lookGoodsSku() {
                this.modal = true;
            },
            remove(index) {
                this.list.product.splice(index, 1);
            },
            removeShelf(index) {
                this.list.illegal.splice(index, 1);
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.goodsReview.validate(valid => {
                    if (valid) {
                        window.console.log(valid);
                    } else {
                        self.loading = false;
                        self.$notice.error({
                            title: '请正确填写设置信息！',
                        });
                    }
                });
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="goods-wrap">
            <tabs value="name1">
                <tab-pane label="所有商品" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>上架，当商品处于非上架状态时，前台将不能浏览该商品，店主可控制商品上架状态</p>
                            <p>违规下架，当商品处于违规下架状态时，前台将不能购买该商品，只有管理员可控制商品违规下架状态，
                                并且商品只有重新编辑后才能上架</p>
                            <p>设置项中可以查看商品详细、查看商品SKU。查看商品详细，跳转到商品详细页。查看商品SKU，显示商
                                品的SKU、图片、价格、库存信息</p>
                        </div>
                        <div class="goods-list">
                            <div class="goods-body-header">
                                <i-button class="export-btn" @click="exportData" type="ghost">导出数据</i-button>
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
                                     :columns="column.product"
                                     :data="list.product"
                                     ref="goodsList"
                                     highlight-row>
                            </i-table>
                        </div>
                        <modal value="goodSku"
                            v-model="modal"
                            title="商品“222616”的SKU列表" class="good-sku-modal">
                            <i-table ref="goodsSKU"
                                     class="goods-table"
                                     :columns="column.sku"
                                     :context="self"
                                     :data="skuData"></i-table>
                        </modal>
                        <div class="page">
                            <page :total="100" show-elevator></page>
                        </div>
                    </card>
                </tab-pane>
                <tab-pane label="违规下架" name="name2">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>上架，当商品处于非上架状态时，前台将不能浏览该商品，店主可控制商品上架状态</p>
                            <p>违规下架，当商品处于违规下架状态时，前台将不能购买该商品，只有管理员可控制商品违规下架状态，
                                并且商品只有重新编辑后才能上架</p>
                            <p>设置项中可以查看商品详细、查看商品SKU。查看商品详细，跳转到商品详细页。查看商品SKU，显示商
                                品的SKU、图片、价格、库存信息</p>
                        </div>
                        <div class="goods-list">
                            <div class="goods-body-header">
                                <i-button class="export-btn" @click="exportShelfData" type="ghost">导出数据</i-button>
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
                                     :columns="column.illegal"
                                     :data="list.illegal"
                                     ref="illegal"
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