<script>
    import image1 from '../assets/images/head.png';

    export default {
        data() {
            return {
                goods: [
                    {
                        name: '6-25 今天',
                        childern: [
                            {
                                name: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                                imgsrc: image1,
                                pricle: 3299,
                            },
                            {
                                name: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                                imgsrc: image1,
                                pricle: 3299,
                            },
                        ],
                    },
                    {
                        name: '6-25 今天',
                        childern: [
                            {
                                name: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                                imgsrc: image1,
                                pricle: 3299,
                            },
                        ],
                    },
                    {
                        name: '6-25 今天',
                        childern: [
                            {
                                name: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                                imgsrc: image1,
                                pricle: 3299,
                            },
                            {
                                name: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                                imgsrc: image1,
                                pricle: 3299,
                            },
                            {
                                name: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                                imgsrc: image1,
                                pricle: 3299,
                            },
                            {
                                name: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                                imgsrc: image1,
                                pricle: 3299,
                            },
                        ],
                    },
                ],
            };
        },
        methods: {
            deleteItem(num) {
                this.goods.splice(num, 1);
            },
        },
    };
</script>
<template>
    <div class="footprint">
        <div class="saases-title">
            <span>我的足迹</span>
        </div>
        <div class="clearfix"></div>
        <div class="goods-list" v-for="(goods, index) in goods">
            <div class="data">
                <div class="data-main">{{ goods.name }}</div>
                <div class="delete" @click="deleteItem(index)">删除</div>
            </div>
            <div class="clearfix"></div>
            <div class="row" >
                <router-link to="/mall/search/product-details"  v-for="(item,ind) in goods.childern"  class="col-sm-3" :key="ind">
                    <div>
                        <img :src="item.imgsrc" alt="">
                        <div class="goods-msg">
                            <div class="price">
                                <i>￥</i><span>{{ item.pricle }}</span>
                            </div>
                            <div class="goods-name">
                                {{ item.name }}
                            </div>
                        </div>
                    </div>
                </router-link>
            </div>
        </div>

    </div>
</template>