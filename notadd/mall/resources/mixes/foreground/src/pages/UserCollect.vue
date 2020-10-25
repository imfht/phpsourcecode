<script>
    import image1 from '../assets/images/head.png';

    export default {
        data() {
            return {
                goods: [
                    {
                        name: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                        imgSrc: image1,
                        num: 3324,
                        oldPrice: 46.88,
                        price: 3299,
                    },
                    {
                        name: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                        imgSrc: image1,
                        num: 3324,
                        oldPrice: 46.88,
                        price: 3299,
                    },
                    {
                        name: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                        imgSrc: image1,
                        num: 3324,
                        oldPrice: 46.88,
                        price: 3299,
                    },
                ],
            };
        },
        methods: {
            cancel(num) {
                this.goods.splice(num, 1);
            },
        },
    };
</script>
<template>
    <div class="my-collect">
        <div class="collect-title">
            <span>我的收藏</span>
        </div>
        <div class="clearfix"></div>
        <div class="row">
            <div  v-for="(item, index) in goods" class="col-sm-3">
                <div>
                    <router-link to="/mall/search/product-details">
                        <img :src="item.imgSrc" alt="">
                    </router-link>
                    <div class="goods-msg">
                        <div class="price">
                            <i>￥</i><span>{{ item.price }}</span>
                            <span class="old-price">￥{{ item.oldPrice }}</span>
                        </div>
                        <router-link  to="/mall/search/product-details" class="goods-name">
                            {{ item.name }}
                        </router-link>
                        <div class="sales">
                            <div class="month-sales">本月销量 <b>{{ item.num }} </b>件</div>
                            <div @click.self="cancel(index)" class="cancel">取消收藏</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>