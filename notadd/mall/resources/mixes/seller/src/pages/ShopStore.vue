<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                addStore: {
                    address: '',
                    meassage: '',
                    name: '',
                    phone: '',
                    province: [],
                },
                addValidate: {
                    address: [
                        {
                            message: '详细地址不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    name: [
                        {
                            message: '门店名称不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    province: [
                        {
                            message: '所在地区不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
                editStore: {
                    address: '',
                    meassage: '',
                    name: '',
                    phone: '',
                    province: [],
                },
                editValidate: {
                    address: [
                        {
                            message: '详细地址不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    name: [
                        {
                            message: '门店名称不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    province: [
                        {
                            message: '所在地区不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
                goodsApplication: false,
                goodsColumns: [
                    {
                        align: 'center',
                        key: 'storeName',
                        title: '门店名称',
                        width: 200,
                    },
                    {
                        align: 'center',
                        key: 'phone',
                        title: '电话',
                    },
                    {
                        align: 'center',
                        key: 'address',
                        title: '详细地址',
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
                        address: '陕西省西安市雁塔区高新二路',
                        phone: '13777777777',
                        storeName: '合作伙伴',
                    },
                    {
                        address: '陕西省西安市雁塔区高新二路',
                        phone: '13777777777',
                        storeName: '合作伙伴',
                    },
                    {
                        address: '陕西省西安市雁塔区高新二路',
                        phone: '13777777777',
                        storeName: '合作伙伴',
                    },
                    {
                        address: '陕西省西安市雁塔区高新二路',
                        phone: '13777777777',
                        storeName: '合作伙伴',
                    },
                ],
                loading: false,
                modify: false,
                provinceList: [
                    {
                        children: [
                            {
                                children: [
                                    {
                                        label: '婴儿推车',
                                        value: '婴儿推车',
                                    },
                                    {
                                        label: '自行车',
                                        value: '自行车',
                                    },
                                    {
                                        label: '婴儿推车',
                                        value: '婴儿推车',
                                    },
                                    {
                                        label: '电动车',
                                        value: '电动车',
                                    },
                                    {
                                        label: '安全座椅',
                                        value: '安全座椅',
                                    },
                                ],
                                label: '童车童床',
                                value: '童车童床',
                            },
                            {
                                label: '营养辅食',
                                value: '营养辅食',
                            },
                            {
                                label: '尿裤湿巾',
                                value: '尿裤湿巾',
                            },
                        ],
                        label: '个护化妆',
                        value: '个护化妆',
                    },
                    {
                        children: [
                            {
                                children: [
                                    {
                                        label: '婴儿推车1',
                                        value: '婴儿推车1',
                                    },
                                    {
                                        label: '自行车2',
                                        value: '自行车2',
                                    },
                                    {
                                        label: '婴儿推车3',
                                        value: '婴儿推车3',
                                    },
                                    {
                                        label: '电动车',
                                        value: '电动车',
                                    },
                                    {
                                        label: '安全座椅4',
                                        value: '安全座椅4',
                                    },
                                ],
                                label: '服饰寝居',
                                value: '服饰寝居',
                            },
                            {
                                children: [
                                    {
                                        label: '婴儿推车1',
                                        value: '婴儿推车1',
                                    },
                                    {
                                        label: '自行车2',
                                        value: '自行车2',
                                    },
                                ],
                                label: '营养辅食',
                                value: '营养辅食',
                            },
                            {
                                children: [
                                    {
                                        label: '车1',
                                        value: '车1',
                                    },
                                    {
                                        label: '自行车2',
                                        value: '自行车2',
                                    },
                                ],
                                label: '尿裤湿巾',
                                value: '尿裤湿巾',
                            },
                        ],
                        label: '家用电器',
                        value: '家用电器',
                    },
                ],
                searchList: [
                    {
                        label: '店铺名称',
                        value: '店铺名称',
                    },
                    {
                        label: '商品名称',
                        value: '商品名称',
                    },
                    {
                        label: '商品分类',
                        value: '商品分类',
                    },
                ],
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
                self.$refs.addStore.validate(valid => {
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
            submitEdit() {
                const self = this;
                self.loading = true;
                self.$refs.editStore.validate(valid => {
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
        <div class="shop-store">
            <tabs value="name1">
                <tab-pane label="门店列表" name="name1">
                    <card :bordered="false">
                        <div class="goods-list">
                            <div class="goods-body-header">
                                <i-button type="ghost" @click.native="addGoods">+新增门店</i-button>
                                <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
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
                            title="新增门店" class="upload-picture-modal">
                        <div>
                            <i-form ref="addStore" :model="addStore" :rules="addValidate" :label-width="100">
                                <row>
                                    <i-col span="14">
                                        <form-item label="门店名称" prop="name">
                                            <i-input v-model="addStore.name"></i-input>
                                            <p class="tip">不同地址建议使用不同名称以示区别，如“本初科技（钟楼店）”</p>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="14">
                                        <form-item label="所在地区" prop="province">
                                            <cascader :data="provinceList" trigger="hover"
                                                      v-model="addStore.province"></Cascader>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="14">
                                        <form-item label="详细地址" prop="address">
                                            <i-input v-model="addStore.address"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="14">
                                        <form-item label="联系电话">
                                            <i-input v-model="addStore.phone"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="14">
                                        <form-item label="公交信息">
                                            <i-input v-model="addStore.meassage" type="textarea"
                                                     :autosize="{minRows: 3,maxRows: 5}"></i-input>
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
                    <modal
                            v-model="modify"
                            title="编辑门店" class="upload-picture-modal">
                        <div>
                            <i-form ref="editStore" :model="editStore" :rules="editValidate" :label-width="100">
                                <row>
                                    <i-col span="14">
                                        <form-item label="门店名称" prop="name">
                                            <i-input v-model="editStore.name"></i-input>
                                            <p class="tip">不同地址建议使用不同名称以示区别，如“本初科技（钟楼店）”</p>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="14">
                                        <form-item label="所在地区" prop="province">
                                            <cascader :data="provinceList" trigger="hover"
                                                      v-model="editStore.province"></Cascader>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="14">
                                        <form-item label="详细地址" prop="address">
                                            <i-input v-model="editStore.address"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="14">
                                        <form-item label="联系电话">
                                            <i-input v-model="editStore.phone"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="14">
                                        <form-item label="公交信息">
                                            <i-input v-model="editStore.meassage" type="textarea"
                                                     :autosize="{minRows: 3,maxRows: 5}"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="20">
                                        <form-item>
                                            <i-button :loading="loading" type="primary" @click.native="submitEdit">
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