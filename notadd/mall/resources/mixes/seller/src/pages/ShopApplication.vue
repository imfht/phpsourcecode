<script>
    import image1 from '../assets/images/img_banner.png';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                action: `${window.api}/mall/admin/upload`,
                goods: {
                    initials: '',
                    logo: '',
                    name: '',
                    type: [],
                },
                goodsApplication: false,
                goodsColumns: [
                    {
                        align: 'center',
                        key: 'goodsLogo',
                        render(h, data) {
                            return h('tooltip', {
                                props: {
                                    placement: 'right-end',
                                },
                                scopedSlots: {
                                    content() {
                                        return h('img', {
                                            domProps: {
                                                src: data.row.goodsLogo,
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
                        title: '品牌图标',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'goodsName',
                        title: '品牌名称',
                    },
                    {
                        align: 'center',
                        key: 'show',
                        title: '所属类别',
                    },
                    {
                        align: 'center',
                        key: 'status',
                        title: '状态',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            if (data.row.status === '待审核') {
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
                                    }, '修改'),
                                    h('i-button', {
                                        class: {
                                            'delete-ad': true,
                                        },
                                        on: {
                                            click() {
                                                self.revoked(data.index);
                                            },
                                        },
                                        props: {
                                            size: 'small',
                                            type: 'ghost',
                                        },
                                    }, '撤销'),
                                ]);
                            }
                            if (data.row.status === '审核通过') {
                                return h('div', [
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
                                    }, '删除'),
                                ]);
                            }
                            return '';
                        },
                        title: '操作',
                        width: 180,
                    },
                ],
                goodsData: [
                    {
                        goodsLogo: image1,
                        goodsName: '海尔',
                        show: '家用电器>大型家电>冰箱',
                        status: '待审核',
                    },
                    {
                        goodsLogo: image1,
                        goodsName: '海尔',
                        show: '家用电器>大型家电>冰箱',
                        status: '待审核',
                    },
                    {
                        goodsLogo: image1,
                        goodsName: '海尔',
                        show: '家用电器>大型家电>冰箱',
                        status: '审核通过',
                    },
                ],
                goodsModify: {
                    initials: '',
                    logo: '',
                    name: '',
                    type: [],
                },
                loading: false,
                modify: false,
                modifyValidate: {
                    initials: [
                        {
                            message: '名称首字母不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    name: [
                        {
                            message: '品牌名称不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
                ruleValidate: {
                    initials: [
                        {
                            message: '名称首字母不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    name: [
                        {
                            message: '品牌名称不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
                self: this,
                styleData: [
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
            removeLogo() {
                this.goods.logo = '';
            },
            submit() {
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
            submitModify() {
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
            uploadBefore() {
                this.$loading.start();
            },
            uploadError(error, data) {
                const self = this;
                self.$loading.error();
                if (typeof data.message === 'object') {
                    for (const p in data.message) {
                        self.$notice.error({
                            title: data.message[p],
                        });
                    }
                } else {
                    self.$notice.error({
                        title: data.message,
                    });
                }
            },
            uploadFormatError(file) {
                this.$notice.warning({
                    title: '文件格式不正确',
                    desc: `文件 ${file.name} 格式不正确`,
                });
            },
            uploadPicture() {
                this.uploadModal = true;
            },
            uploadSuccess(data) {
                const self = this;
                self.$loading.finish();
                self.$notice.open({
                    title: data.message,
                });
                self.goods.logo = data.data.path;
            },
            uploadModifySuccess(data) {
                const self = this;
                self.$loading.finish();
                self.$notice.open({
                    title: data.message,
                });
                self.goodsModify.logo = data.data.path;
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="shop-application">
            <tabs value="name1">
                <tab-pane label="品牌申请" name="name1">
                    <card :bordered="false">
                        <div class="goods-list">
                            <div class="goods-body-header">
                                <i-button type="ghost" @click.native="addGoods">品牌申请</i-button>
                                <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
                                <div class="goods-body-header-right">
                                    <i-input v-model="managementWord" placeholder="请输入关键词进行搜索">
                                        <span slot="prepend">品牌名称</span>
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
                            title="品牌申请" class="upload-picture-modal">
                        <div>
                            <i-form ref="goods" :model="goods" :rules="ruleValidate" :label-width="100">
                                <row>
                                    <i-col span="14">
                                        <form-item label="品牌名称" prop="name">
                                            <i-input v-model="goods.name"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="14">
                                        <form-item label="名称首字母" prop="initials">
                                            <i-input v-model="goods.initials"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="14">
                                        <form-item label="品牌类别">
                                            <cascader :data="styleData" trigger="hover" v-model="goods.type"></cascader>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="24">
                                        <form-item label="品牌LOGO" prop="logo">
                                            <div class="image-preview" v-if="goods.logo">
                                                <img :src="goods.logo">
                                                <icon type="close" @click.native="removeLogo"></icon>
                                            </div>
                                            <upload :action="action"
                                                    :before-upload="uploadBefore"
                                                    :format="['jpg','jpeg','png']"
                                                    :headers="{
                                                        Authorization: `Bearer ${$store.state.token.access_token}`
                                                    }"
                                                    :max-size="2048"
                                                    :on-error="uploadError"
                                                    :on-format-error="uploadFormatError"
                                                    :on-success="uploadSuccess"
                                                    ref="upload"
                                                    :show-upload-list="false"
                                                    v-if="goods.logo === '' || goods.logo === null">
                                            </upload>
                                            <p class="tip">建议上传大小为150*50的品牌图片</p>
                                            <p class="tip">申请品牌的目的是方便买家通过品牌索引页查找商品，
                                                申请时请填写品牌所属的类别，方便平台归类</p>
                                            <p class="tip">在平台审核前，您可以编辑或撤销申请</p>
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
                            title="品牌修改" class="upload-picture-modal">
                        <div>
                            <i-form ref="goodsModify" :model="goodsModify" :rules="modifyValidate" :label-width="100">
                                <row>
                                    <i-col span="14">
                                        <form-item label="品牌名称" prop="name">
                                            <i-input v-model="goodsModify.name"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="14">
                                        <form-item label="名称首字母" prop="initials">
                                            <i-input v-model="goodsModify.initials"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="14">
                                        <form-item label="品牌类别">
                                            <cascader :data="styleData" trigger="hover" v-model="goodsModify.type"></cascader>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="24">
                                        <form-item label="品牌LOGO" prop="logo">
                                            <div class="image-preview" v-if="goodsModify.logo">
                                                <img :src="goodsModify.logo">
                                                <icon type="close" @click.native="removeLogo"></icon>
                                            </div>
                                            <upload :action="action"
                                                    :before-upload="uploadBefore"
                                                    :format="['jpg','jpeg','png']"
                                                    :headers="{
                                                        Authorization: `Bearer ${$store.state.token.access_token}`
                                                    }"
                                                    :max-size="2048"
                                                    :on-error="uploadError"
                                                    :on-format-error="uploadFormatError"
                                                    :on-success="uploadModifySuccess"
                                                    ref="upload"
                                                    :show-upload-list="false"
                                                    v-if="goodsModify.logo === '' || goodsModify.logo === null">
                                            </upload>
                                            <p class="tip">建议上传大小为150*50的品牌图片</p>
                                            <p class="tip">申请品牌的目的是方便买家通过品牌索引页查找商品，
                                                申请时请填写品牌所属的类别，方便平台归类</p>
                                            <p class="tip">在平台审核前，您可以编辑或撤销申请</p>
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
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>