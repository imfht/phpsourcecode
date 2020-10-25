<script>
    import image from '../assets/images/img_banner.png';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                action: `${window.api}/mall/admin/upload`,
                loading: false,
                self: this,
                setting: {
                    autoplay: false,
                    autoplaySpeed: 2000,
                    dots: 'inside',
                    trigger: 'click',
                    arrow: 'hover',
                },
                shop: {
                    image: '',
                    level: '白金店铺',
                    logo: '',
                    phone: '',
                    type: '',
                },
                shopValidate: {},
                slideImg: {
                    img: 0,
                    list: [image, image, image, image],
                    picture1: '',
                    picture2: '',
                    picture3: '',
                    picture4: '',
                    pictureLink1: '',
                    pictureLink2: '',
                    pictureLink3: '',
                    pictureLink4: '',
                },
            };
        },
        methods: {
            removeImage() {
                this.shop.image = '';
            },
            removeLogo() {
                this.shop.logo = '';
            },
            removePicture1() {
                this.slideImg.picture1 = '';
            },
            removePicture2() {
                this.slideImg.picture2 = '';
            },
            removePicture3() {
                this.slideImg.picture3 = '';
            },
            removePicture4() {
                this.slideImg.picture4 = '';
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.shop.validate(valid => {
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
            submitImage() {
                const self = this;
                self.loading = true;
                self.$refs.slideImg.validate(valid => {
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
            uploadSuccess(data) {
                const self = this;
                self.$loading.finish();
                self.$notice.open({
                    title: data.message,
                });
                self.shop.logo = data.data.path;
            },
            uploadSuccessImage(data) {
                const self = this;
                self.$loading.finish();
                self.$notice.open({
                    title: data.message,
                });
                self.shop.image = data.data.path;
            },
            uploadSuccessSlide1(data) {
                const self = this;
                self.$loading.finish();
                self.$notice.open({
                    title: data.message,
                });
                self.slideImg.picture1 = data.data.path;
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="shop-wrap">
            <tabs value="name1">
                <tab-pane label="店铺设置" name="name1">
                    <card :bordered="false">
                        <i-form ref="shop" :model="shop" :rules="shopValidate" :label-width="180">
                            <row>
                                <i-col span="14">
                                    <form-item label="店铺等级">
                                        {{ shop.level}}
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="14">
                                    <form-item label="主营商品" prop="type">
                                        <i-input v-model="shop.type"></i-input>
                                        <p class="tip">关键字最多可输入50字，请用","进行分隔，例如，"男装，女装，童装"</p>
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="14">
                                    <form-item label="店铺LOGO" prop="logo">
                                        <div class="image-preview" v-if="shop.logo">
                                            <img :src="shop.logo">
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
                                                v-if="shop.logo === '' || shop.logo === null">
                                        </upload>
                                        <p class="tip">建议使用200像素×60像素内的GIF或PNG透明图片</p>
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="14">
                                    <form-item label="店铺头像" prop="image">
                                        <div class="image-preview" v-if="shop.image">
                                            <img :src="shop.image">
                                            <icon type="close" @click.native="removeImage"></icon>
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
                                                :on-success="uploadSuccessImage"
                                                ref="upload"
                                                :show-upload-list="false"
                                                v-if="shop.image === '' || shop.image === null">
                                        </upload>
                                        <p class="tip">建议使用200像素×60像素内的GIF或PNG透明图片</p>
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="14">
                                    <form-item label="店铺电话">
                                        <i-input v-model="shop.phone"></i-input>
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="14">
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
                <tab-pane label="轮播图设置" name="name2" class="slide-module">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.最多可上传4张图片</p>
                            <p>2.支持JPG，JPEG，GIF，PNG格式上传，建议图片宽度990像素，高度在300像素至400像素之间，
                                大小4MB以内的图片。提交的2〜4张图片可以进行轮播，一张图片没有轮播效果</p>
                        </div>
                        <i-form ref="slideImg" :model="slideImg" :rules="SlideValidate" :label-width="180">
                            <carousel
                                    v-model="slideImg.img"
                                    :autoplay="setting.autoplay"
                                    :autoplay-speed="setting.autoplaySpeed"
                                    :dots="setting.dots"
                                    :trigger="setting.trigger"
                                    :arrow="setting.arrow">
                                <carousel-item v-for="item in slideImg.list">
                                    <div class="demo-carousel">
                                        <img :src="item" alt="">
                                    </div>
                                </carousel-item>
                            </carousel>
                            <div class="row-link">
                                <row :gutter="16">
                                    <i-col span="6">
                                        <div class="image-preview" v-if="slideImg.picture1">
                                            <img :src="slideImg.picture1">
                                            <icon type="close" @click.native="removePicture1"></icon>
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
                                                :on-success="uploadSuccessSlide1"
                                                ref="upload"
                                                :show-upload-list="false"
                                                v-if="slideImg.picture1 === '' || slideImg.picture1 === null">
                                        </upload>
                                        <p>跳转链接</p>
                                        <i-input v-model="slideImg.pictureLink1">
                                            <span slot="prepend">http://</span>
                                        </i-input>
                                    </i-col>
                                    <i-col span="6">
                                        <div class="image-preview" v-if="slideImg.picture2">
                                            <img :src="slideImg.picture2">
                                            <icon type="close" @click.native="removePicture2"></icon>
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
                                                :on-success="uploadSuccessSlide2"
                                                ref="upload"
                                                :show-upload-list="false"
                                                v-if="slideImg.picture2 === '' || slideImg.picture2 === null">
                                        </upload>
                                        <p>跳转链接</p>
                                        <i-input v-model="slideImg.pictureLink2">
                                            <span slot="prepend">http://</span>
                                        </i-input>
                                    </i-col>
                                    <i-col span="6">
                                        <div class="image-preview" v-if="slideImg.picture3">
                                            <img :src="slideImg.picture3">
                                            <icon type="close" @click.native="removePicture3"></icon>
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
                                                :on-success="uploadSuccessSlide3"
                                                ref="upload"
                                                :show-upload-list="false"
                                                v-if="slideImg.picture3 === '' || slideImg.picture3 === null">
                                        </upload>
                                        <p>跳转链接</p>
                                        <i-input v-model="slideImg.pictureLink3">
                                            <span slot="prepend">http://</span>
                                        </i-input>
                                    </i-col>
                                    <i-col span="6">
                                        <div class="image-preview" v-if="slideImg.picture4">
                                            <img :src="slideImg.picture4">
                                            <icon type="close" @click.native="removePicture4"></icon>
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
                                                :on-success="uploadSuccessSlide4"
                                                ref="upload"
                                                :show-upload-list="false"
                                                v-if="slideImg.picture4 === '' || slideImg.picture4 === null">
                                        </upload>
                                        <p>跳转链接</p>
                                        <i-input v-model="slideImg.pictureLink4">
                                            <span slot="prepend">http://</span>
                                        </i-input>
                                    </i-col>
                                </row>
                            </div>
                            <div class="submit-btn">
                                <i-button :loading="loading" type="primary" @click.native="submitImage">
                                    <span v-if="!loading">确认提交</span>
                                    <span v-else>正在提交…</span>
                                </i-button>
                            </div>
                        </i-form>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>