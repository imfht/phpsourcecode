<script>
    import image from '../assets/images/adv.jpg';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                checkAll: false,
                checkAllGroup: [],
                indeterminate: false,
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
            delete() {},
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
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="goods-picture-look">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>图片管理—查看</span>
            </div>
            <card :bordered="false">
                <div class="picture-select">
                    <checkbox
                            :indeterminate="indeterminate"
                            v-model="checkAll"
                            @on-change="handleCheckAll">全选</checkbox>
                    <i-button class="delete-btn" type="ghost" @click.native="delete">删除</i-button>
                </div>
                <div v-for="(item, index) in pictureList" class="picture-check">
                    <img :src="item.img" alt="" @click="lookPicture(item)">
                    <i-button type="text" @click.native="removeImage">
                        <icon type="trash-a"></icon>
                    </i-button>
                    <checkbox v-model="item.single" @on-change="checkAllGroupChange()"></checkbox>
                    <p class="name">{{ item.name}}</p>
                    <p class="tip">{{ item.uploadTime}}</p>
                    <p class="tip">{{ item.size}}</p>
                </div>
                <div class="page">
                    <page :total="100" show-elevator></page>
                </div>
            </card>
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