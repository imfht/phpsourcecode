<script>
    import injection from '../helpers/injection';
    import image from '../assets/images/adv.jpg';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                action: `${window.api}/mall/admin/upload`,
                album: {
                    logoList: [],
                    type: '',
                },
                checkAll: false,
                checkAllGroup: [],
                goodsPicture: {
                    sortType: '',
                },
                indeterminate: false,
                loading: false,
                modalPicture: {
                    img: '',
                },
                pictureList: [
                    {
                        img: image,
                        name: '商品rey的主图1',
                        uploadTime: '上传时间：2017/02/11 12:30:17',
                        single: false,
                        size: '原图尺寸：400*400',
                    },
                    {
                        img: image,
                        name: '商品rey的主图2',
                        uploadTime: '上传时间：2017/02/11 12:30:17',
                        single: false,
                        size: '原图尺寸：400*400',
                    },
                    {
                        img: image,
                        name: '商品rey的主图3',
                        uploadTime: '上传时间：2017/02/11 12:30:17',
                        single: false,
                        size: '原图尺寸：400*400',
                    },
                    {
                        img: image,
                        name: '商品rey的主图4',
                        uploadTime: '上传时间：2017/02/11 12:30:17',
                        single: false,
                        size: '原图尺寸：400*400',
                    },
                    {
                        img: image,
                        name: '商品rey的主图5',
                        uploadTime: '上传时间：2017/02/11 12:30:17',
                        single: false,
                        size: '原图尺寸：400*400',
                    },
                ],
                pictureModal: false,
                sortType: [
                    {
                        label: '从小到大',
                        value: '1',
                    },
                    {
                        label: '时间',
                        value: '2',
                    },
                ],
                uploadModal: false,
            };
        },
        methods: {
            checkAllGroupChange() {
                this.indeterminate = false;
                this.checkAll = true;
                const select = [];
                this.pictureList.forEach(item => {
                    if (item.single === false) {
                        this.checkAll = false;
                        this.indeterminate = true;
                    } else {
                        select.push(item);
                    }
                });
                if (select.length === 0) {
                    this.indeterminate = false;
                }
            },
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            handleCheckAll() {
                if (this.checkAll) {
                    this.pictureList.forEach(item => {
                        item.single = true;
                    });
                } else {
                    this.pictureList.forEach(item => {
                        item.single = false;
                    });
                    this.indeterminate = false;
                }
            },
            lookPicture(item) {
                this.modalPicture.img = item.img;
                this.pictureModal = true;
            },
            removeImage(index) {
                this.pictureList.splice(index, 1);
            },
            removeLogo(index) {
                this.album.logoList.splice(index, 1);
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.album.validate(valid => {
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
                    desc: `文件 ${file.name} 格式不正确`,
                });
            },
            uploadPicture() {
                this.uploadModal = true;
            },
            uploadSuccess(data) {
                console.log(data.data.path);
                const self = this;
                injection.loading.finish();
                self.$notice.open({
                    title: data.message,
                });
                self.album.logoList.push(data.data.path);
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="goods-picture-manage">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>相册管理-默认相册</span>
            </div>
            <card :bordered="false">
                <div class="goods-list">
                    <div class="btn-group">
                        <checkbox
                                :indeterminate="indeterminate"
                                v-model="checkAll"
                                @on-change="handleCheckAll">全选</checkbox>
                        <i-button class="first-btn" type="ghost" @click.native="uploadPicture">上传图片</i-button>
                        <i-button type="ghost" class="first-btn">添加水印</i-button>
                        <i-button type="ghost">批量删除</i-button>
                        <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
                        <row class="float-right">
                            <i-col span="12">排序方式</i-col>
                            <i-col span="12">
                                <i-select v-model="goodsPicture.sortType">
                                    <i-option v-for="item in sortType"
                                              :value="item.value">{{ item.label }}</i-option>
                                </i-select>
                            </i-col>
                        </row>
                    </div>
                    <div v-for="(item, index) in pictureList" class="picture-check">
                        <img :src="item.img" alt="" @click="lookPicture(item)">
                        <i-button type="text" @click.native="removeImage">
                            <icon type="trash-a"></icon>
                        </i-button>
                        <checkbox v-model="item.single" @on-change="checkAllGroupChange()"></checkbox>
                        <p>{{ item.name}}</p>
                        <p class="tip">{{ item.uploadTime}}</p>
                        <p class="tip">{{ item.size}}</p>
                    </div>
                </div>
                <div class="page">
                    <page :total="100" show-elevator></page>
                </div>
            </card>
            <modal
                    v-model="uploadModal"
                    title="上传图片" class="upload-picture-modal">
                <div>
                    <i-form ref="album" :model="album" :rules="pictureValidate" :label-width="100">
                        <row>
                            <i-col span="12">
                                <form-item label="选择相册">
                                    <i-select v-model="album.type">
                                        <i-option v-for="item in albumTYpe" :value="item.value"
                                                  :key="item">{{ item.label }}</i-option>
                                    </i-select>
                                </form-item>
                            </i-col>
                        </row>
                        <row>
                            <i-col span="20">
                                <form-item label="选择图片" prop="logo">
                                    <div class="image-preview" v-if="album.logoList"
                                         v-for="(item, index) in album.logoList">
                                        <img :src="item">
                                        <i-button type="text" @click.native="removeLogo(index)">
                                            <icon type="trash-a"></icon>
                                        </i-button>
                                    </div>
                                    <upload :action="action"
                                            :before-upload="uploadBefore"
                                            :format="['jpg','jpeg','png']"
                                            :headers="{
                                                Authorization: `Bearer ${$store.state.token.access_token}`
                                            }"
                                            multiple
                                            :max-size="2048"
                                            :on-error="uploadError"
                                            :on-format-error="uploadFormatError"
                                            :on-success="uploadSuccess"
                                            ref="upload"
                                            :show-upload-list="false">
                                    </upload>
                                    <p class="tip">支持JPG，GIF，PNG格式，大小不超过4096KB的图片上传;
                                        浏览文件是可以按住CTRL或移位键多选</p>
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
                    v-model="pictureModal"
                    title="查看图片" class="upload-picture-modal picture-look-modal">
                <div>
                    <img :src="modalPicture.img" alt="">
                </div>
            </modal>
        </div>
    </div>
</template>