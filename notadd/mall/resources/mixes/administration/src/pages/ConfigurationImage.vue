<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                action: `${window.api}/mall/admin/upload`,
                defaultImage: {
                    goodsImage: '',
                    shopImage: '',
                    shopLogo: '',
                },
                imageLoading: false,
                loading: false,
                form: {
                    imageType: '',
                },
                radioList: [
                    {
                        content: '按照文件名存放（例：店铺ID/图片）',
                        label: 'fileName',
                    },
                    {
                        content: '按照年份存放（例：店铺ID/年/图片）',
                        label: 'year',
                    },
                    {
                        content: '按照年月存放（例：店铺ID/年/月/图片）',
                        label: 'yearMonth',
                    },
                    {
                        content: '按照年月日存放（例：店铺ID/年/月/日/图片）',
                        label: 'yearMonthDay',
                    },
                ],
                validate: {
                    imageType: [
                        {
                            message: '请选择图片存放类型',
                            required: true,
                            trigger: 'change',
                        },
                    ],
                },
            };
        },
        methods: {
            removeGoodsImage() {
                this.defaultImage.goodsImage = '';
            },
            removeShopImage() {
                this.defaultImage.shopImage = '';
            },
            removeShopLogo() {
                this.defaultImage.shopLogo = '';
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.form.validate(valid => {
                    if (valid) {
                        self.$Message.success('提交成功!');
                    } else {
                        self.loading = false;
                        self.$notice.error({
                            title: '请正确填写设置信息！',
                        });
                    }
                });
            },
            submitImage() {
                const self = this;
                self.imageLoading = true;
//                if (valid) {
//                    self.$Message.success('提交成功!');
//                } else {
//                    self.imageLoading = false;
//                    self.$notice.error({
//                        title: '请正确填写设置信息！',
//                    });
//                }
            },
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
            uploadGoodsImageSuccess(data) {
                const self = this;
                injection.loading.finish();
                self.$notice.open({
                    title: data.message,
                });
                self.defaultImage.goodsImage = data.data.path;
            },
            uploadShopLogoSuccess(data) {
                const self = this;
                injection.loading.finish();
                self.$notice.open({
                    title: data.message,
                });
                self.defaultImage.shopLogo = data.data.path;
            },
            uploadShopImageSuccess(data) {
                const self = this;
                injection.loading.finish();
                self.$notice.open({
                    title: data.message,
                });
                self.defaultImage.shopImage = data.data.path;
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="configuration-image">
            <tabs value="uploadParameters">
                <tab-pane label="上传参数" name="uploadParameters">
                    <card :bordered="false">
                        <i-form :label-width="200" ref="form" :model="form" :rules="validate">
                            <row>
                                <i-col span="24">
                                    <form-item label="图片存放类型：" prop="imageType">
                                        <radio-group v-model="form.imageType">
                                            <radio v-for="item in radioList"
                                                   :label="item.label">{{ item.content }}</radio>
                                        </radio-group>
                                    </form-item>
                                </i-col>
                            </row>
                            <form-item>
                                <i-button @click.native="submit" type="primary">
                                    <span v-if="!loading">确认提交</span>
                                    <span v-else>正在提交…</span>
                                </i-button>
                            </form-item>
                        </i-form>
                    </card>
                </tab-pane>
                <tab-pane label="默认图片" name="defaultImage">
                    <card :bordered="false">
                        <i-form :label-width="200" ref="defaultImage" :model="defaultImage">
                            <row>
                                <i-col span="12">
                                    <form-item label="默认商品图片">
                                        <div class="image-preview" v-if="defaultImage.goodsImage">
                                            <img :src="defaultImage.goodsImage">
                                            <icon type="close" @click.native="removeGoodsImage"></icon>
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
                                                :on-success="uploadGoodsImageSuccess"
                                                ref="upload"
                                                :show-upload-list="false"
                                                v-if="defaultImage.goodsImage === '' || defaultImage.goodsImage === null">
                                        </upload>
                                        <p class="prompt">图片大小为300*300px</p>
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12">
                                    <form-item label="默认店铺LOGO">
                                        <div class="image-preview" v-if="defaultImage.shopLogo">
                                            <img :src="defaultImage.shopLogo">
                                            <icon type="close" @click.native="removeShopLogo"></icon>
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
                                                :on-success="uploadShopLogoSuccess"
                                                ref="upload"
                                                :show-upload-list="false"
                                                v-if="defaultImage.shopLogo === '' || defaultImage.shopLogo === null">
                                        </upload>
                                        <p class="prompt">图片大小为200*60px</p>
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="12">
                                    <form-item label="默认店铺头像">
                                        <div class="image-preview" v-if="defaultImage.shopImage">
                                            <img :src="defaultImage.shopImage">
                                            <icon type="close" @click.native="removeShopImage"></icon>
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
                                                :on-success="uploadShopImageSuccess"
                                                ref="upload"
                                                :show-upload-list="false"
                                                v-if="defaultImage.shopImage === '' || defaultImage.shopImage === null">
                                        </upload>
                                        <p class="prompt">图片大小为100*100px</p>
                                    </form-item>
                                </i-col>
                            </row>
                            <form-item>
                                <i-button @click.native="submitImage" type="primary">
                                    <span v-if="!imageLoading">确认提交</span>
                                    <span v-else>正在提交…</span>
                                </i-button>
                            </form-item>
                        </i-form>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>