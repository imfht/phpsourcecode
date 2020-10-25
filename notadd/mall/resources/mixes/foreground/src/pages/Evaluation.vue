<script>
    import SplinLine from '../components/SplinLine.vue';
    import product from '../assets/images/img_06.png';

    export default {
        component: [
            SplinLine,
        ],
        data() {
            return {
                loading: true,
                productImgs: [],
                product: {
                    img: product,
                    sixe: 'M',
                    title: 'Purrfect diary 咕噜日记1-7岁儿童可爱短袜5双装儿童可爱短袜5双装儿童可爱短袜5双装',
                },
                red: false,
                sorce: 5,
            };
        },
        methods: {
            deleteImg(e) {
                const index = e.target.getAttribute('i');
                this.productImgs.splice(index);
            },
            imageSelected(e) {
                const file = e.target.files[0];
                const self = this;
                const image = {
                    content: '',
                    file1: file,
                };
                const reader = new global.FileReader();
                reader.onload = () => {
                    image.content = reader.result;
                };
                reader.readAsDataURL(file);
                self.productImgs.push(image);
            },
            score(e) {
                this.red = true;
                this.sorce = Number(e.target.getAttribute('i')) + 1;
            },
        },
        mounted() {
            const self = this;
            self.$nextTick(() => {
                setTimeout(() => {
                    self.loading = false;
                }, 1000);
            });
        },
    };
</script>
<template>
    <div class="evaluation">
        <div v-if="!loading">
            <div class="evaluation-box container">
                <p class="prompt">温馨提示 ：发表评价，通过审核后，您将有机会获得10积分，优秀的精华评价将再获得40积分</p>
                <div class="product-evaluation">
                    <div class="product-info clearfix">
                        <div class="product-img">
                            <img ：src="product.img"/>
                        </div>
                        <div class="product-intro">{{ product.title }} <span
                            class="product-size">尺码：{{ product.size }}</span></div>
                    </div>
                    <ul class="product-eval clearfix">
                        <li class="clearfix evaluation-content">
                            <div class="title">
                                商品满意度
                            </div>
                            <div class="content">
                                <i class="icon iconfont icon-xing1" :i="index" @click="score($event)"
                                   :class="{red: sorce-1 >= index}" v-for="(item,index) in [1,2,3,4,5]"> </i>
                            </div>
                        </li>
                        <li class="clearfix evaluation-content">
                            <div class="title">
                                评价晒单
                            </div>
                            <div class="content">
                                <textarea class="evaluation-txt" placeholder="购物是否满意？请评价一下您购买的商品吧~"> </textarea>
                                <ul class="real-imgs clearfix">
                                    <li v-for="(productImg,index) in productImgs">
                                        <img :src="productImg.content "/>
                                        <div class="cover">
                                            <i class="icon iconfont icon-icon_shanchu" :i="index"
                                               @click="deleteImg($event)"> </i>
                                        </div>
                                    </li>
                                    <li v-if="productImgs.length<=4">
                                        <div class="diamond-upload-file">
                                            <div class="icon iconfont icon-tupian"></div>
                                            <input id="photo" type="file" @change="imageSelected($event)" accept="image/*">
                                        </div>
                                    </li>
                                </ul>
                                <p>每张不超过5M，支持格式jpg，jpeg，bmp，png</p>
                                <button class="order-btn" data-toggle="modal" data-target="#myModal">提交评价</button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <!--Modal-->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel">提示</h4>
                        </div>
                        <div class="modal-body">评价成功~！感谢您的评价，积分稍后会到账。</div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal -->
            </div>
        </div>
        <splin-line v-if="loading"></splin-line>
    </div>
</template>
