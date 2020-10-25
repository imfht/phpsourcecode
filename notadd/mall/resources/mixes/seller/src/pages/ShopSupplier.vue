<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                addValidate: {
                    name: [
                        {
                            message: '供货商名称不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
                goods: {
                    name: '',
                    person: '',
                    phone: '',
                    remarks: '',
                },
                goodsApplication: false,
                goodsColumns: [
                    {
                        align: 'center',
                        key: 'supplier',
                        title: '供货商',
                        width: 200,
                    },
                    {
                        align: 'center',
                        key: 'contactPerson',
                        title: '联系人',
                    },
                    {
                        align: 'center',
                        key: 'contactPhone',
                        title: '联系电话',
                    },
                    {
                        align: 'center',
                        key: 'remarks',
                        title: '备注',
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
                                }, '编辑'),
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
                                }, '删除'),
                            ]);
                        },
                        title: '操作',
                        width: 180,
                    },
                ],
                goodsData: [
                    {
                        contactPerson: '王某',
                        contactPhone: '13777777777',
                        remarks: '合作伙伴',
                        supplier: '本处科级',
                    },
                    {
                        contactPerson: '王某',
                        contactPhone: '13777777777',
                        remarks: '合作伙伴',
                        supplier: '本处科级',
                    },
                    {
                        contactPerson: '王某',
                        contactPhone: '13777777777',
                        remarks: '合作伙伴',
                        supplier: '本处科级',
                    },
                    {
                        contactPerson: '王某',
                        contactPhone: '13777777777',
                        remarks: '合作伙伴',
                        supplier: '本处科级',
                    },
                ],
                goodsModify: {
                    name: '',
                    person: '',
                    phone: '',
                    remarks: '',
                },
                loading: false,
                modify: false,
                ruleValidate: {
                    name: [
                        {
                            message: '供货商名称不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
                self: this,
            };
        },
        methods: {
            addGoods() {
                this.goodsApplication = true;
            },
            edit() {
                this.modify = true;
            },
            remove(index) {
                this.goodsData.splice(index, 1);
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.goodsModify.validate(valid => {
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
            submitModify() {
                const self = this;
                self.loading = true;
                self.$refs.goods.validate(valid => {
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
        <div class="shop-supplier">
            <tabs value="name1">
                <tab-pane label="供货商" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>供货商信息可以与商品关联，商品发布/编辑时可选择供货商，商品列表支持根据供货商快速查找</p>
                        </div>
                        <div class="goods-list">
                            <div class="goods-body-header">
                                <i-button type="ghost" @click.native="addGoods">+新增供货商</i-button>
                                <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
                                <div class="goods-body-header-right">
                                    <i-input v-model="managementWord" placeholder="请输入关键词进行搜索">
                                        <span slot="prepend">供货商名称</span>
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
                    <modal
                            v-model="goodsApplication"
                            title="新增供货商" class="upload-picture-modal">
                        <div>
                            <i-form ref="goods" :model="goods" :rules="addValidate" :label-width="100">
                                <row>
                                    <i-col span="14">
                                        <form-item label="供货商名称" prop="name">
                                            <i-input v-model="goods.name"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="14">
                                        <form-item label="联系人">
                                            <i-input v-model="goods.person"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="14">
                                        <form-item label="联系电话">
                                            <i-input v-model="goods.phone"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="14">
                                        <form-item label="备注信息">
                                            <i-input v-model="goods.remarks"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="20">
                                        <form-item>
                                            <i-button :loading="loading" type="primary" @click.native="submitModify">
                                                <span v-if="!loading">确认提交</span>
                                                <span v-else>正在提交…</span>
                                            </i-button>
                                        </form-item>
                                    </i-col>
                                </row>
                            </i-form>
                        </div>
                    </modal>
                    <modal
                            v-model="modify"
                            title="编辑供货商" class="upload-picture-modal">
                        <div>
                            <i-form ref="goodsModify" :model="goodsModify" :rules="ruleValidate" :label-width="100">
                                <row>
                                    <i-col span="14">
                                        <form-item label="供货商名称" prop="name">
                                            <i-input v-model="goods.name"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="14">
                                        <form-item label="联系人">
                                            <i-input v-model="goods.person"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="14">
                                        <form-item label="联系电话">
                                            <i-input v-model="goods.phone"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="14">
                                        <form-item label="备注信息">
                                            <i-input v-model="goods.remarks"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
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
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>