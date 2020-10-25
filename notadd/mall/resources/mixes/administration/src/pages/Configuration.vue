<script>
    import injection from '../helpers/injection';
    import image1 from '../assets/images/img_logo.png';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/mall/admin/configuration/get`).then(response => {
                const data = response.data.data;
                next(vm => {
                    injection.loading.finish();
                    vm.form.email = data.email;
                    vm.form.logo = data.logo;
                    vm.form.phone = data.phone;
                });
            }).catch(() => {
                injection.loading.error();
            });
        },
        data() {
            const self = this;
            return {
                action: `${window.api}/mall/admin/upload`,
                form: {
                    email: '',
                    logo: '',
                    phone: '',
                },
                loading: false,
                mainNavColumns: [
                    {
                        align: 'center',
                        key: 'index',
                        render(h, data) {
                            return h('i-input', {
                                props: {
                                    type: 'ghost',
                                    value: data.index + 1,
                                },
                            });
                        },
                        title: '排序',
                        width: 160,
                    },
                    {
                        align: 'center',
                        key: 'name',
                        title: '导航名称',
                        width: 300,

                    },
                    {
                        key: 'enabled',
                        render(h, data) {
                            return h('i-switch', {
                                props: {
                                    size: 'large',
                                    value: data.row.enabled,
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
                        title: '是否显示',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('router-link', {
                                    props: {
                                        to: '/mall/configuration/edit/main',
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
                                            self.delete(data.index);
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
                        width: '180',
                    },
                ],
                mainNavData: [
                    {
                        enabled: true,
                        name: '数码办公',
                    },
                    {
                        enabled: true,
                        name: '数码办公',
                    },
                    {
                        enabled: true,
                        name: '数码办公',
                    },
                    {
                        enabled: true,
                        name: '数码办公',
                    },
                ],
                navColumns: [
                    {
                        align: 'center',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        align: 'center',
                        key: 'name',
                        title: '分类名称',
                        width: 200,

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
                        title: '分类图标',
                        width: 180,
                    },
                    {
                        align: 'center',
                        key: 'category',
                        render(h, data) {
                            return h('i-switch', {
                                props: {
                                    size: 'large',
                                    value: data.row.category,
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
                        title: '推荐分类',
                        width: 180,
                    },
                    {
                        align: 'center',
                        key: 'type',
                        render(h, data) {
                            return h('i-switch', {
                                props: {
                                    size: 'large',
                                    value: data.row.type,
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
                        title: '推荐品牌',
                    },
                    {
                        key: 'ad',
                        render(h, data) {
                            return h('i-switch', {
                                props: {
                                    size: 'large',
                                    value: data.row.ad,
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
                        title: '广告',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h) {
                            return h('router-link', {
                                props: {
                                    to: '/mall/configuration/category/edit',
                                },
                            }, [
                                h('i-button', {
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '编辑'),
                            ]);
                        },
                        title: '操作',
                        width: '140',
                    },
                ],
                navData: [
                    {
                        ad: true,
                        category: true,
                        goodsImg: image1,
                        name: '数码办公',
                        type: true,
                    },
                    {
                        ad: true,
                        category: true,
                        goodsImg: image1,
                        name: '礼品箱包',
                        type: true,
                    },
                    {
                        ad: true,
                        category: true,
                        goodsImg: image1,
                        name: '家用电器',
                        type: true,
                    },
                    {
                        ad: true,
                        category: true,
                        goodsImg: image1,
                        name: '珠宝手表',
                        type: true,
                    },
                    {
                        ad: true,
                        category: true,
                        goodsImg: image1,
                        name: '运动健康',
                        type: true,
                    },
                ],
                rules: {
                    email: [
                        {
                            required: true,
                            type: 'email',
                            message: '请输入正确的电子邮箱账号',
                            trigger: 'change',
                        },
                    ],
                    logo: [
                        {
                            message: '请上传网站 Logo',
                            required: true,
                            trigger: 'change',
                            type: 'string',
                        },
                    ],
                    phone: [
                        {
                            required: true,
                            pattern: /^0\d{2,3}-?\d{7,8}$/,
                            message: '请输入正确的电话号码',
                            trigger: 'change',
                        },
                    ],
                },
            };
        },
        methods: {
            delete(index) {
                this.mainNavData.splice(index, 1);
            },
            removeLogo() {
                const self = this;
                self.form.logo = '';
                self.$refs.form.validateField('logo');
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.form.validate(valid => {
                    if (valid) {
                        self.$http.post(`${window.api}/mall/admin/configuration/set`, self.form).then(() => {
                            self.$notice.open({
                                title: '更新商城配置成功！',
                            });
                        }).finally(() => {
                            self.loading = false;
                        });
                    } else {
                        self.loading = false;
                        self.$notice.error({
                            title: '请正确填写设置信息！',
                        });
                    }
                });
            },
            toEdit() {},
            uploadBefore() {
                injection.loading.start();
            },
            uploadError(error, data) {
                const self = this;
                injection.loading.error();
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
                    desc: `文件 ${file.name} 格式不正确，请上传 jpg 或 png 格式的图片。`,
                });
            },
            uploadSuccess(data) {
                const self = this;
                injection.loading.finish();
                self.$notice.open({
                    title: data.message,
                });
                self.form.logo = data.data.path;
                self.$refs.form.validateField('logo');
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="mall-configuration">
            <tabs value="default">
                <tab-pane label="商城设置" name="default">
                    <card :bordered="false">
                        <i-form :label-width="200" :model="form" ref="form" :rules="rules">
                            <row>
                                <i-col span="12">
                                    <form-item label="网站 Logo" prop="logo">
                                        <div class="image-preview" v-if="form.logo">
                                            <img :src="form.logo">
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
                                                v-if="form.logo === '' || form.logo === null">
                                        </upload>
                                        <p class="tip">默认网站LOGO，通用头部显示，最佳显示尺寸为240*60像素</p>
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12">
                                    <form-item label="平台客服联系电话" prop="phone">
                                        <i-input v-model="form.phone"></i-input>
                                        <p class="tip">商城中心右下侧显示，方便客户遇到问题时咨询，多个请用半角逗号“，”隔开</p>
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12">
                                    <form-item label="平台客服电子邮件" prop="email">
                                        <i-input v-model="form.email"></i-input>
                                        <p class="tip">商城中心右下侧显示，方便客户遇到问题时咨询</p>
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12">
                                    <form-item>
                                        <i-button :loading="loading" type="primary" @click.native="submit">
                                            <span v-if="!loading">确认提交</span>
                                            <span v-else>正在提交…</span>
                                        </i-button>
                                    </form-item>
                                </i-col>
                            </row>
                        </i-form>
                    </card>
                </tab-pane>
                <tab-pane label="分类导航" name="nav">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>"编辑分类导航"功能可以设置前台左上侧商品分类导航的相关信息，可以设置一级分类前图标，推荐分类，
                                推荐品牌以及两张广告图片</p>
                            <p>分类导航设置完成后，需要清除缓存</p>
                        </div>
                        <i-table class="goods-table"
                                 :context="self"
                                 :columns="navColumns"
                                 :data="navData"
                                 highlight-row></i-table>
                    </card>
                </tab-pane>
                <tab-pane label="主导航" name="mainNav">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>商城前台主导航默认商品分类与首页为最前列，其余按照数字大小排序</p>
                        </div>
                        <router-link to="configuration/create">
                            <i-button type="ghost" class="add-data">新增数据</i-button>
                        </router-link>
                        <i-table class="goods-table"
                                 :context="self"
                                 :columns="mainNavColumns"
                                 :data="mainNavData"
                                 highlight-row></i-table>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>