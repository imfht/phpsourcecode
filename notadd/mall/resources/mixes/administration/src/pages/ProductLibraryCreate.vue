<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/mall/admin/product/category/list`).then(response => {
                const structures = response.data.structure;
                next(vm => {
                    vm.data.categories = Object.keys(structures).map(index => {
                        const item = structures[index];
                        item.label = item.name;
                        item.value = item.id;
                        const children = item.children;
                        item.children = Object.keys(children).map(i => {
                            const sub = children[i];
                            sub.label = sub.name;
                            sub.value = sub.id;
                            const down = sub.children;
                            sub.children = Object.keys(down).map(n => {
                                const son = down[n];
                                son.label = son.name;
                                son.value = son.id;
                                return son;
                            });
                            return sub;
                        });
                        return item;
                    });
                    injection.loading.finish();
                });
            }).catch(() => {
                injection.loading.fail();
            });
        },
        data() {
            return {
                action: `${window.api}/mall/admin/upload`,
                addAlbum: true,
                addPhoneAlbum: true,
                data: {
                    brands: [
                        {
                            label: '华为',
                            value: '1',
                        },
                        {
                            label: '苹果',
                            value: '2',
                        },
                    ],
                    deliveryAreas: [
                        {
                            label: '333',
                            value: '1',
                        },
                        {
                            label: '444',
                            value: '2',
                        },
                    ],
                    priceRanges: [
                        {
                            label: '100-200',
                            value: '1',
                        },
                        {
                            label: '400-600',
                            value: '2',
                        },
                    ],
                    productionPlaces: [
                        {
                            label: '南',
                            value: '1',
                        },
                        {
                            label: '北',
                            value: '2',
                        },
                    ],
                    publicPraises: [
                        {
                            label: '好',
                            value: '1',
                        },
                        {
                            label: '一般',
                            value: '2',
                        },
                    ],
                    categories: [],
                },
                form: {
                    barcode: '',
                    brand_id: 0,
                    category: [],
                    delivery_area: '',
                    image: '',
                    name: '',
                    picture: '',
                    price_range: '',
                    production_place: '',
                    public_praise: '',
                    remarks: '',
                    selectStyle: ['个护化妆', '营养辅食'],
                    selling_point: '',
                },
                isEditPicture: false,
                isEditText: false,
                isPcPicture: false,
                isPhonePicture: false,
                loading: false,
                rules: {
                    image: [
                        {
                            message: '商品图片不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                    name: [
                        {
                            message: '商品名称不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                },
            };
        },
        methods: {
            addAlbumPicture() {
                this.addAlbum = false;
                this.isPcPicture = true;
            },
            addPhonePicture() {
                this.addPhoneAlbum = false;
                this.isPhonePicture = true;
            },
            addText() {
                const self = this;
                self.isEditText = !self.isEditText;
            },
            closePcAlbum() {
                this.isPcPicture = false;
                this.addAlbum = true;
            },
            closePhoneAlbum() {
                this.isPhonePicture = false;
                this.addPhoneAlbum = true;
            },
            editInformation() {
                const self = this;
                self.$router.push({
                    path: 'edit/category',
                });
            },
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            removeImage() {
                this.form.image = '';
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.form.validate(valid => {
                    if (valid) {
                        const form = self.form;
                        if (form.category.length) {
                            form.category_id = form.category[form.category.length - 1];
                        }
                        self.$http.post(`${window.api}/mall/admin/product/library/create`, form).then(response => {
                            window.console.log(response);
                            self.$notice.open({
                                title: '添加产品库数据成功！',
                            });
                            self.$router.push('/mall/product/library');
                        }).catch(() => {
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
            submitTextContent() {},
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
            uploadErrorPicture(file) {
                this.$notice.warning({
                    title: '文件格式不正确',
                    desc: `文件 ${file.name} 格式不正确`,
                });
            },
            uploadFormatError(file) {
                this.$notice.warning({
                    title: '文件格式不正确',
                    desc: `文件 ${file.name} 格式不正确`,
                });
            },
            uploadSuccess(data) {
                const self = this;
                injection.loading.finish();
                self.$notice.open({
                    title: data.message,
                });
                self.form.image = data.data.path;
            },
            uploadSuccessPicture(data) {
                const self = this;
                injection.loading.finish();
                self.$notice.open({
                    title: data.message,
                });
                self.form.picture = data.data.path;
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="goods-library-add">
            <div class="store-refund-process">
                <div class="edit-link-title">
                    <i-button type="text" @click.native="goBack">
                        <icon type="chevron-left"></icon>
                    </i-button>
                    <span>商品库管理 —— 新增</span>
                </div>
                <div>
                    <card :bordered="false">
                        <i-form ref="form" :model="form" :rules="rules" :label-width="200">
                            <div class="library-application">
                                <h5>商品基本信息</h5>
                                <div class="application-content refund-module">
                                    <row>
                                        <i-col span="12">
                                            <form-item label="商品分类">
                                                <cascader :data="data.categories"
                                                          trigger="click"
                                                          v-model="form.category"></Cascader>
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="12">
                                            <form-item label="商品名称" prop="name">
                                                <i-input v-model="form.name"></i-input>
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="12">
                                            <form-item label="商品卖点">
                                                <i-input v-model="form.selling_point" type="textarea"
                                                         :autosize="{minRows: 3,maxRows: 5}"></i-input>
                                                <p class="tip">商品卖点最长不超过140个汉字</p>
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="10">
                                            <form-item label="商品条形码">
                                                <i-input v-model="form.barcode"></i-input>
                                                <p class="tip">请填写商品条形码下方数字</p>
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row class="row-goods-upload">
                                        <i-col span="24">
                                            <form-item label="商品图片" prop="image">
                                                <div class="image-preview" v-if="form.image">
                                                    <img :src="form.image">
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
                                                        :on-success="uploadSuccess"
                                                        ref="upload"
                                                        :show-upload-list="false"
                                                        v-if="form.image === '' || form.image === null">
                                                </upload>
                                                <p class="tip">第一张图片为默认主图，图片支持JPG、gif、png格式上传或从图片空间中选择，
                                                    建议使用尺寸800*800像素以上，大小不超过4M的正方形图片，单击选中图片，
                                                    可进行上传，替换和删除
                                                </p>
                                                <upload class="upload-picture-button"
                                                        :action="action"
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
                                                        :show-upload-list="false">
                                                    <i-button type="ghost">从图片空间上传</i-button>
                                                </upload>
                                            </form-item>
                                        </i-col>
                                    </row>
                                </div>
                            </div>
                            <div class="library-application">
                                <h5>商品详情描述</h5>
                                <div class="application-content">
                                    <row>
                                        <i-col span="10">
                                            <form-item label="商品品牌">
                                                <i-select v-model="form.brand_id">
                                                    <i-option v-for="item in data.brands" :value="item.value"
                                                              :key="item">{{ item.label }}</i-option>
                                                </i-select>
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="24">
                                            <form-item label="商品属性" class="good-edit-style">
                                                <row>
                                                    <i-col span="6">
                                                        <span class="style-title">价格区间</span>
                                                        <i-select v-model="form.price_range">
                                                            <i-option v-for="item in data.priceRanges" :value="item.value"
                                                                      :key="item">{{ item.label }}</i-option>
                                                        </i-select>
                                                    </i-col>
                                                    <i-col span="6">
                                                        <span class="style-title">口碑</span>
                                                        <i-select v-model="form.public_praise">
                                                            <i-option v-for="item in data.publicPraises" :value="item.value"
                                                                      :key="item">{{ item.label }}</i-option>
                                                        </i-select>
                                                    </i-col>
                                                    <i-col span="6">
                                                        <span class="style-title">区域配送</span>
                                                        <i-select v-model="form.delivery_area">
                                                            <i-option v-for="item in data.deliveryAreas" :value="item.value"
                                                                      :key="item">{{ item.label }}</i-option>
                                                        </i-select>
                                                    </i-col>
                                                    <i-col span="6">
                                                        <span class="style-title">产地</span>
                                                        <i-select v-model="form.production_place">
                                                            <i-option v-for="item in data.productionPlaces" :value="item.value"
                                                                      :key="item">{{ item.label }}</i-option>
                                                        </i-select>
                                                    </i-col>
                                                </row>
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="24">
                                            <form-item label="商品描述" class="remark-input">
                                                <row>
                                                    <i-col span="24">
                                                        <div>
                                                            <tabs type="card">
                                                                <tab-pane label="电脑端" class="pc-module-content">
                                                                    <row>
                                                                        <i-col span="18">
                                                                            <div class="edit-content-area">

                                                                            </div>
                                                                        </i-col>
                                                                        <i-col span="6"></i-col>
                                                                    </row>
                                                                    <i-button class="close-album"
                                                                              @click.native="addAlbumPicture"
                                                                              v-if="addAlbum"
                                                                              type="ghost">插入相册图片</i-button>
                                                                    <div class="picture-edit-area" v-if="isPcPicture">
                                                                        <i-button class="close-album"
                                                                                  @click.native="closePcAlbum"
                                                                                  type="ghost">关闭相册</i-button>
                                                                        <p>用户相册>全部图片</p>
                                                                        <div class="picture-content">
                                                                            <row>
                                                                                <i-col span="4" v-for="img in [1,2,3,4,5,6,7,8,9]">
                                                                                    <img src="../assets/images/adv.jpg" alt="">
                                                                                </i-col>
                                                                            </row>
                                                                            <div class="page">
                                                                                <page :total="100" show-elevator></page>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </tab-pane>
                                                                <tab-pane label="手机端" class="mobile-module-content">
                                                                    <row class="row-phone-line">
                                                                        <i-col span="10" class="request-col-border">
                                                                            <div class="pro-des">
                                                                                <div class="pro-bg">
                                                                                    <span>图片总数不得超过20张，
                                                                                        文字不得超过500字</span>
                                                                                </div>
                                                                                <div class="pro-bg2">
                                                                                    <upload :action="action"
                                                                                            :before-upload="uploadBefore"
                                                                                            :format="['jpg','jpeg','png']"
                                                                                            :headers="{
                                                                                                Authorization: `Bearer ${$store.state.token.access_token}`
                                                                                            }"
                                                                                            :max-size="2048"
                                                                                            :on-error="uploadError"
                                                                                            :on-format-error="uploadErrorPicture"
                                                                                            :on-success="uploadSuccessPicture"
                                                                                            ref="upload"
                                                                                            :show-upload-list="false">
                                                                                        <i-button type="ghost">插入图片</i-button>
                                                                                    </upload>
                                                                                    <i-button @click.native="addText" class="ivu-button-text"
                                                                                              type="ghost">添加文字</i-button>
                                                                                </div>
                                                                                <div class="pro-content">
                                                                                    <div class="image-preview" v-if="form.picture">
                                                                                        <img :src="form.picture">
                                                                                        <icon type="close" @click.native="removePicture"></icon>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </i-col>
                                                                        <i-col span="14" class="request-col-line">
                                                                            <row>
                                                                                <i-col span="18">
                                                                                    <ul class="request">
                                                                                        <li>
                                                                                            <p>1.基本要求</p>
                                                                                            <div>
                                                                                                <p>（1）手机详情总体大小：图片+文字，图片不超过20张，文字不超过5000字；</p>
                                                                                                <p>建议：所有图片都是本宝贝相关的图片</p>
                                                                                            </div>
                                                                                        </li>
                                                                                        <li>
                                                                                            <p>2.图片大小要求</p>
                                                                                            <div>
                                                                                                <p>（1）建议使用宽度480~620像素、高度小于等于960像素的图片；</p>
                                                                                                <p>（2）格式为：jpg、jepg、gif、png</p>
                                                                                                <p>举例：可以上传一张宽度为480，高度为960像素，格式为jpg的图片</p>
                                                                                            </div>
                                                                                        </li>
                                                                                        <li>
                                                                                            <p>3.文字要求</p>
                                                                                            <div>
                                                                                                <p>（1）每次插入文字不能超过500个字，标点、特殊字符按照一个字计算；</p>
                                                                                                <p>（2）请手动输入文字，不要复制粘网页上的文字，防止出现乱码；</p>
                                                                                                <p>（3）以下特殊字符“<”、“>”、“ " ”、“ ' ”、“\”会被替换为空</p>
                                                                                                <p>建议：不要添加太多的文字，这样看起来更清晰</p>
                                                                                            </div>
                                                                                        </li>
                                                                                    </ul>
                                                                                </i-col>
                                                                                <i-col span="10"></i-col>
                                                                            </row>

                                                                        </i-col>
                                                                    </row>
                                                                    <div>
                                                                        <i-button class="close-album"
                                                                                  @click.native="addPhonePicture"
                                                                                  v-if="addPhoneAlbum"
                                                                                  type="ghost">插入相册图片</i-button>
                                                                        <div class="picture-edit-area"
                                                                             v-if="isPhonePicture">
                                                                            <i-button type="ghost" class="close-album"
                                                                                      @click.native="closePhoneAlbum">
                                                                                关闭相册</i-button>
                                                                            <p>用户相册>全部图片</p>
                                                                            <div class="picture-content">
                                                                                <row class="row-phone-line">
                                                                                    <i-col span="4" v-for="img in [1,2,3,4,5,6,7,8,9]">
                                                                                        <img src="../assets/images/adv.jpg" alt="">
                                                                                    </i-col>
                                                                                </row>
                                                                                <div class="page">
                                                                                    <page :total="100" show-elevator></page>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="text-edit-area" v-if="isEditText">
                                                                            <span>还可以输入500字</span><br>
                                                                            <i-input  type="textarea"
                                                                                      v-model="form.remarks"
                                                                                      :autosize="{minRows: 6,maxRows: 8}"></i-input>
                                                                            <i-button type="ghost">确认</i-button>
                                                                            <i-button @click.native="submitTextContent"
                                                                                      type="ghost">提交</i-button>
                                                                        </div>
                                                                    </div>
                                                                </tab-pane>
                                                            </tabs>
                                                        </div>
                                                    </i-col>
                                                </row>
                                            </form-item>
                                        </i-col>
                                    </row>
                                </div>
                            </div>
                            <div class="library-application">
                                <h5>商品基本信息</h5>
                                <div class="application-content refund-module">
                                    <row>
                                        <i-col span="12">
                                            <form-item label="商品重量">
                                                <i-input></i-input>
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="12">
                                            <form-item label="商品体积">
                                                <i-input></i-input>
                                            </form-item>
                                        </i-col>
                                    </row>
                                </div>
                            </div>
                            <row>
                                <i-col span="18">
                                    <form-item label="">
                                        <i-button :loading="loading" @click.native="submit" type="primary">
                                            <span v-if="!loading">确认提交</span>
                                            <span v-else>正在提交…</span>
                                        </i-button>
                                    </form-item>
                                </i-col>
                            </row>
                        </i-form>
                    </card>
                </div>
            </div>
        </div>
    </div>
</template>