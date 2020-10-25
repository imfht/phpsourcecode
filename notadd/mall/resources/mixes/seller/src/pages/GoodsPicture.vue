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
                albumTYpe: [
                    {
                        label: '默认相册',
                        value: '1',
                    },
                    {
                        label: '本地',
                        value: '2',
                    },
                ],
                createAlbum: {
                    albumIntro: '',
                    albumName: '',
                    albumSort: '',
                },
                createModal: false,
                goodsPicture: {
                    sortType: '',
                },
                loading: false,
                modalTitle: '创建相册',
                pictureList: [
                    {
                        img: image,
                        name: '相册名称1',
                        num: '455',
                    },
                    {
                        img: image,
                        name: '相册名称2',
                        num: '455',
                    },
                    {
                        img: image,
                        name: '相册名称3',
                        num: '455',
                    },
                    {
                        img: image,
                        name: '相册名称4',
                        num: '455',
                    },
                    {
                        img: image,
                        name: '相册名称5',
                        num: '455',
                    },
                    {
                        img: image,
                        name: '相册名称6',
                        num: '455',
                    },
                    {
                        img: image,
                        name: '相册名称7',
                        num: '455',
                    },
                ],
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
            createAlbumModal() {
                this.createModal = true;
                this.modalTitle = '创建相册';
            },
            editImage() {
                this.createModal = true;
                this.modalTitle = '编辑相册';
            },
            pictureManage() {
                const self = this;
                self.$router.push({
                    path: 'picture/manage',
                });
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
            submitCreateAlbum() {
                const self = this;
                self.loading = true;
                self.$refs.createAlbum.validate(valid => {
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
        <div class="goods-picture">
            <tabs value="name1">
                <tab-pane label="相册管理" name="name1">
                    <card :bordered="false">
                        <div class="goods-list">
                            <div class="btn-group">
                                <i-button class="first-btn" type="ghost" @click.native="createAlbumModal">创建相册</i-button>
                                <i-button type="ghost" @click.native="uploadPicture">上传图片</i-button>
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
                            <div class="picture-group">
                                <ul class="clearfix">
                                    <li v-for="(item, index) in pictureList">
                                        <div class="img">
                                            <img :src="item.img" alt="" @click="pictureManage">
                                            <i-button type="text" @click.native="removeImage(index)">
                                                <icon type="trash-a"></icon>
                                            </i-button>
                                        </div>
                                        <div class="img-intro">
                                            <span @click="pictureManage">{{ item.name }}</span>
                                            <i-button type="text" @click.native="editImage">
                                                <icon type="edit"></icon>
                                            </i-button>
                                        </div>
                                        <p class="tip">共{{ item.num }}张</p>
                                    </li>
                                </ul>
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
                                    <i-col span="22">
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
                            v-model="createModal"
                            :title="modalTitle" class="upload-picture-modal">
                        <div>
                            <i-form ref="createAlbum" :model="createAlbum" :rules="createValidate" :label-width="100">
                                <row>
                                    <i-col span="12">
                                        <form-item label="相册名称">
                                            <i-input v-model="createAlbum.albumName"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="12">
                                        <form-item label="排序">
                                            <i-input v-model="createAlbum.albumSort"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="20">
                                        <form-item label="描述">
                                            <i-input type="textarea" v-model="createAlbum.albumIntro"
                                                     :autosize="{minRows: 3,maxRows: 5}"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="20">
                                        <form-item>
                                            <i-button :loading="loading" type="primary" @click.native="submitCreateAlbum">
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