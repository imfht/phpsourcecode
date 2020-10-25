<script>
    import Affix from '../components/Affix.vue';
    import phone from '../assets/images/phone.png';

    export default {
        components: [
            Affix,
        ],
        data() {
            return {
                active: 1,
                now: 0,
                productList: [
                    {
                        img: phone,
                        title: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                        num: 100,
                        saled: 25,
                        price: 32.99,
                        oldPrice: 46.88,
                        stoke: 100,
                    },
                    {
                        img: phone,
                        title: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                        num: 100,
                        saled: 25,
                        price: 32.99,
                        oldPrice: 46.88,
                        stoke: 50,
                    },
                    {
                        img: phone,
                        title: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                        num: 100,
                        saled: 25,
                        price: 32.99,
                        oldPrice: 46.88,
                        stoke: 100,
                    },
                    {
                        img: phone,
                        title: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                        num: 100,
                        saled: 25,
                        price: 32.99,
                        oldPrice: 46.88,
                        stoke: 500,
                    },
                    {
                        img: phone,
                        title: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                        num: 100,
                        saled: 25,
                        price: 32.99,
                        oldPrice: 46.88,
                        stoke: 150,
                    },
                    {
                        img: phone,
                        title: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                        num: 100,
                        saled: 25,
                        price: 32.99,
                        oldPrice: 46.88,
                        stoke: 1000,
                    },
                ],
                times: [
                    {
                        time: '08:00',
                    },
                    {
                        time: '12:00',
                    },
                    {
                        time: '14:00',
                    },
                    {
                        time: '16:00',
                    },
                    {
                        time: '22:00',
                    },
                ],
            };
        },
        methods: {
            progress(product) {
                return ((product.saled / product.stoke) * 100).toFixed(0);
            },
            saledWidth(item) {
                const progressWidth = (item.saled / item.stoke) * 128;
                return {
                    width: `${progressWidth}px`,
                };
            },
            switchTab(index) {
                this.active = index;
            },
        },
    };
</script>
<template>
    <div class="spike">
        <div class="container">
            <affix :offsetTop-top="100">
                <ul class="time-list clearfix">
                    <li class="spike-time pull-left"
                        :class="{active:active===index}"
                        @click="switchTab(index)"
                        v-for="(item,index) in times">
                        <span :class="{now:now === index}">{{ item.time }}</span>
                        <i class="end" v-if="now > index">已经结束</i>
                        <i class="now" v-if="now === index">正在进行</i>
                        <i class="end" v-if="now < index">即将开始</i>
                    </li>
                </ul>
            </affix>
            <ul class="spike-list clearfix">
                <li v-for="(product,index) in productList" class="pull-left" :key="index">
                    <router-link to="/mall/search/product-details">
                        <div class="img">
                            <img :src="product.img"/>
                        </div>
                        <p class="title">{{ product.title }}</p>
                        <div class="clearfix progress-line">
                            <span class="pull-left">已售{{ progress(product) }}%</span>
                            <div class="progress-box pull-left">
                                <div class="progress" :style='saledWidth(product)'></div>
                            </div>
                        </div>
                        <div class="pay-box pull-right">
                            <span><i>￥</i>{{ product.price }}</span>
                            <s>￥{{ product.oldPrice }}</s>
                            <a>立即抢购</a>
                        </div>
                    </router-link>
                </li>
            </ul>
        </div>
    </div>
</template>