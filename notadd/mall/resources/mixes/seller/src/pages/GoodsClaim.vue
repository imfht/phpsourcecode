<script>
    import image1 from '../assets/images/adv.jpg';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                columns: [
                    {
                        key: 'name',
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
                                            src: data.row.image,
                                        },
                                    }),
                                ]),
                                h('div', {
                                    class: {
                                        'right-text': true,
                                    },
                                }, [
                                    h('p', data.row.name),
                                ]),
                            ]);
                        },
                        title: '商品名称',
                    },
                    {
                        align: 'center',
                        key: 'category',
                        title: '商品分类',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.modal = true;
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '认领'),
                            ]);
                        },
                        title: '操作',
                        width: 180,
                    },
                ],
                form: {
                    freight: '',
                    market_price: '',
                    price: '',
                    stock: '',
                },
                list: [
                    {
                        category: '电子产品',
                        image: image1,
                        name: '可用十年 创意椭圆不锈钢肥皂(带底托精装) 神奇除味去',
                    },
                    {
                        category: '电子产品',
                        image: image1,
                        name: '可用十年 创意椭圆不锈钢肥皂(带底托精装) 神奇除味去',
                    },
                    {
                        category: '电子产品',
                        image: image1,
                        name: '可用十年 创意椭圆不锈钢肥皂(带底托精装) 神奇除味去',
                    },
                ],
                loading: false,
                modal: false,
                searchList: [
                    {
                        label: '商品名称',
                        value: '1',
                    },
                    {
                        label: '商品分类',
                        value: '2',
                    },
                ],
            };
        },
        methods: {
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.form.validate(valid => {
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
    <div class="seller-wrap">
        <div class="goods-claim">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>商品列表-认领商品</span>
            </div>
            <card :bordered="false">
                <div class="goods-body-header">
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
                         :columns="columns"
                         :context="self"
                         :data="list">
                </i-table>
                <div class="page">
                    <page :total="100" show-elevator></page>
                </div>
            </card>
            <modal
                    v-model="modal"
                    title="认领商品" class="goods-claim-modal upload-picture-modal">
                <div>
                    <i-form :label-width="100" :model="form" ref="form" :rules="rules">
                        <form-item label="商品价格">
                            <row>
                                <i-col span="4">
                                    <i-input v-model="form.price"></i-input>
                                </i-col>
                                <i-col span="2">元</i-col>
                            </row>
                            <p class="tip">价格必须是0.01~9999999之间的数字，且不能高于市场价</p>
                        </form-item>
                        <form-item label="市场价格">
                            <row>
                                <i-col span="4">
                                    <i-input v-model="form.market_price"></i-input>
                                </i-col>
                                <i-col span="2">元</i-col>
                            </row>
                            <p class="tip">价格必须是0.01~9999999之间的数字，此价格仅为市场参考售价，请根据该实际情况认真填写</p>
                        </form-item>
                        <form-item label="商品库存">
                            <row>
                                <i-col span="4">
                                    <i-input v-model="form.stock"></i-input>
                                </i-col>
                            </row>
                            <p class="tip">商铺库存数量必须为0~9999999999之间的整数</p>
                        </form-item>
                        <form-item label="固定运费">
                            <row>
                                <i-col span="4">
                                    <i-input v-model="form.freight"></i-input>
                                </i-col>
                                <i-col span="2">元</i-col>
                            </row>
                        </form-item>
                        <row>
                            <i-col span="20">
                                <form-item>
                                    <i-button :loading="loading" type="primary" @click.native="submit">
                                        <span v-if="!loading">确认提交</span>
                                        <span v-else>正在提交…</span>
                                    </i-button>
                                </form-item>
                            </i-col>
                        </row>
                    </i-form>
                </div>
            </modal>
        </div>
    </div>
</template>

