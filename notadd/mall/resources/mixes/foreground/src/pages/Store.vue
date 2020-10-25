<script>
    import { swiper, swiperSlide } from 'vue-awesome-swiper';
    import Paginate from '../components/Paginate.vue';
    import shop from '../assets/images/shopBanner.png';
    import service from '../assets/images/service.png';
    import product from '../assets/images/thephone.png';
    import shopImg from '../assets/images/img_lofo.png';
    import NeedBrowse from '../components/NeedBrowse.vue';
    import image1 from '../assets/images/head.png';

    export default {
        components: {
            NeedBrowse,
            Paginate,
            swiper,
            swiperSlide,
        },
        data() {
            return {
                shop: {
                    afterSaleServices: [
                        {
                            name: '客服1',
                            img: service,
                        },
                        {
                            name: '客服2',
                            img: service,
                        },
                    ],
                    banner: [
                        {
                            img: shop,
                        },
                        {
                            img: shop,
                        },
                        {
                            img: shop,
                        },
                        {
                            img: shop,
                        },
                    ],
                    classificationList: [
                        {
                            title: '查看所有商品',
                            classifications: [
                                {
                                    id: 1,
                                    name: '按销量',
                                },
                                {
                                    id: 1,
                                    name: '按价格',
                                },
                                {
                                    id: 1,
                                    name: '按新品',
                                },
                                {
                                    id: 1,
                                    name: '特价折扣区',
                                },
                            ],
                            show: true,
                        },
                        {
                            title: '断码清仓区',
                            classifications: [
                                {
                                    id: 1,
                                    name: '按销量',
                                },
                                {
                                    id: 1,
                                    name: '按价格',
                                },
                            ],
                            show: false,
                        },
                        {
                            title: '潮牌',
                            classifications: [
                                {
                                    id: 1,
                                    name: '按新品',
                                },
                                {
                                    id: 1,
                                    name: '特价折扣区',
                                },
                            ],
                            show: false,
                        },
                        {
                            title: '男装',
                            classifications: [
                                {
                                    id: 1,
                                    name: '按销量',
                                },
                            ],
                            show: false,
                        },
                        {
                            title: '短袖衬衫',
                            classifications: [
                                {
                                    id: 1,
                                    name: '按销量',
                                },
                                {
                                    id: 1,
                                    name: '特价折扣区',
                                },
                            ],
                            show: false,
                        },
                        {
                            title: '裤子',
                            classifications: [
                                {
                                    id: 1,
                                    name: '按销量',
                                },
                                {
                                    id: 1,
                                    name: '特价折扣区',
                                },
                            ],
                            show: false,
                        },
                    ],
                    img: shopImg,
                    name: 'xxx旗舰店',
                    offHours: '23:00',
                    preSaleServices: [
                        {
                            name: '客服1',
                            img: service,
                        },
                        {
                            name: '客服2',
                            img: service,
                        },
                        {
                            name: '客服3',
                            img: service,
                        },
                        {
                            name: '客服4',
                            img: service,
                        },
                    ],
                    productList: [
                        {
                            id: '',
                            img: product,
                            old_price: 46.88,
                            price: 3299.00,
                            intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                            saleNum: 3324,
                        },
                        {
                            id: '',
                            img: product,
                            old_price: 46.88,
                            price: 3299.00,
                            intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                            saleNum: 3324,
                        },
                        {
                            id: '',
                            img: product,
                            old_price: 46.88,
                            price: 3299.00,
                            intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                            saleNum: 3324,
                        },
                        {
                            id: '',
                            img: product,
                            old_price: 46.88,
                            price: 3299.00,
                            intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                            saleNum: 3324,
                        },
                        {
                            id: '',
                            img: product,
                            old_price: 46.88,
                            price: 3299.00,
                            intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                            saleNum: 3324,
                        },
                        {
                            id: '',
                            img: product,
                            old_price: 46.88,
                            price: 3299.00,
                            intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                            saleNum: 3324,
                        },
                        {
                            id: '',
                            img: product,
                            old_price: 46.88,
                            price: 3299.00,
                            intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                            saleNum: 3324,
                        },
                        {
                            id: '',
                            img: product,
                            old_price: 46.88,
                            price: 3299.00,
                            intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                            saleNum: 3324,
                        },
                        {
                            id: '',
                            img: product,
                            old_price: 46.88,
                            price: 3299.00,
                            intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                            saleNum: 3324,
                        },
                        {
                            id: '',
                            img: product,
                            old_price: 46.88,
                            price: 3299.00,
                            intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                            saleNum: 3324,
                        },
                        {
                            id: '',
                            img: product,
                            old_price: 46.88,
                            price: 3299.00,
                            intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                            saleNum: 3324,
                        },
                        {
                            id: '',
                            img: product,
                            old_price: 46.88,
                            price: 3299.00,
                            intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                            saleNum: 3324,
                        },
                        {
                            id: '',
                            img: product,
                            old_price: 46.88,
                            price: 3299.00,
                            intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                            saleNum: 3324,
                        },
                        {
                            id: '',
                            img: product,
                            old_price: 46.88,
                            price: 3299.00,
                            intro: 'Apple iPhone 6 32G 金色 移动联通电信4G手机',
                            saleNum: 3324,
                        },
                    ],
                    score: 9.2,
                    workingHours: '9:00',
                },
                currect_page: 1,
                commendList: [
                    {
                        amount: 188,
                        img: image1,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                    {
                        amount: 188,
                        img: image1,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                    {
                        amount: 188,
                        img: image1,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                    {
                        amount: 188,
                        img: image1,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                    {
                        amount: 188,
                        img: image1,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                ],
                contractPostage: false,
                isDiscount: false,
                minPrice: 0,
                maxPrice: '',
                swiperOption: {
                    autoplay: 3000,
                    loop: true,
                    notNextTick: true,
                    pagination: '.swiper-pagination',
                    paginationClickable: true,
                },
                priceSort: true,
                sortBy: 1,
                total_page: 10,
            };
        },
        methods: {
            nextPage() {
                if (this.currect_page < this.total_page) {
                    this.currect_page += 1;
                }
            },
            prevPage() {
                if (this.currect_page > 1) {
                    this.currect_page -= 1;
                }
            },
            sortPrice() {
                this.priceSort = !this.priceSort;
                window.console.log(this.priceSort);
            },
            showSubcategories(item) {
                item.show = !item.show;
            },
            switchPage(page) {
                this.current_page = page;
            },
        },
    };
</script>
<template>
    <div class="shop-home">
        <div class="container home-shop clearfix">
            <swiper :options="swiperOption" ref="mySwiperA">
                <swiper-slide  v-for="(item,index) in shop.banner" :key="item.Id">
                    <router-link to="">
                        <img :src="item.img">
                    </router-link>
                </swiper-slide>
                <div class="swiper-pagination" slot="pagination"></div>
            </swiper>
        </div>
        <div class="container clearfix">
            <div class="shop-wrap pull-left">
                <div class="shop-title">
                    <img :src="shop.img" alt="">
                    <p>{{ shop.name }}</p>
                    <p>店铺评分：<i>{{ shop.score }}</i></p>
                    <a class="btn">关注店铺</a>
                </div>
                <div class="workingTime">
                    <p>工作时间  <span>{{ shop.workingHours }}-{{ shop.offHours }}</span></p>
                    <h5>售前客服</h5>
                    <ul class="clearfix">
                        <li class="pull-left" v-for="service in shop.preSaleServices">
                            <router-link to="/">
                                {{ service.name }}
                                <img :src="service.img" alt="">
                            </router-link>
                        </li>
                    </ul>
                    <h5>售后客服</h5>
                    <ul class="clearfix">
                        <li class="pull-left"
                            v-for="service in shop.afterSaleServices">
                            <router-link to="/">
                                {{ service.name }}
                                <img :src="service.img" alt="">
                            </router-link>
                        </li>
                    </ul>
                </div>
                <div class="categories">
                    <h3>商品分类</h3>
                    <ul>
                        <li v-for="item in shop.classificationList">
                            <p @click="showSubcategories(item)">
                                <i class="text-center" v-if="item.show === false">+</i>
                                <i class="text-center" v-if="item.show === true">-</i>
                                {{ item.title }}</p>
                            <transition name="slide">
                                <ul class="clearfix" v-show="item.show">
                                    <li class="pull-left"
                                        v-for="classification in item.classifications">
                                        {{ classification.name }}
                                    </li>
                                </ul>
                            </transition>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="product-list pull-right clearfix">
                <div class="arrangement-method clearfix">
                    <ul class="clearfix pull-left">
                        <li class="active text-center pull-left">
                            <label>
                                <input type="radio" name="sortBy" value="1" v-model="sortBy">
                                <span>人气<i class="icon iconfont icon-paixu"></i></span>
                            </label>
                        </li>
                        <li class="active text-center pull-left">
                            <label>
                                <input type="radio" name="sortBy" value="2" v-model="sortBy">
                                <span>销量<i class="icon iconfont icon-paixu"></i></span>
                            </label>
                        </li>
                        <li class="active text-center pull-left">
                            <label>
                                <input type="radio" name="sortBy" value="3" v-model="sortBy">
                                <span @click.stop="sortPrice">价格<i class="icon iconfont icon-paixu" :class="{rotate: priceSort}"></i></span>
                            </label>
                        </li>
                        <li class="text-center pull-left">
                            <label>
                                <input type="radio" name="sortBy" value="4" v-model="sortBy">
                                <span>新品<i class="icon iconfont icon-paixu"></i></span>
                            </label>
                        </li>
                    </ul>
                    <div class="price">
                        <input type="number" min="0" placeholder="￥" v-model="minPrice">
                        -
                        <input type="number" :min="minPrice" placeholder="￥" v-model="maxPrice">
                    </div>
                    <div class="check-box select">
                        <label class="ivu-checkbox-wrapper ivu-checkbox-group-item">
                        <span class="ivu-checkbox">
                            <input
                                type="checkbox"
                                class="ivu-checkbox-input"
                                v-model="contractPostage"
                                value="remember">
                            <span class="ivu-checkbox-inner"></span>
                        </span>
                            <span>包邮</span>
                        </label>
                    </div>
                    <div class="check-box select">
                        <label class="ivu-checkbox-wrapper ivu-checkbox-group-item">
                        <span class="ivu-checkbox">
                            <input
                                type="checkbox"
                                class="ivu-checkbox-input"
                                v-model="isDiscount"
                                value="remember">
                            <span class="ivu-checkbox-inner"></span>
                        </span>
                            <span>折扣</span>
                        </label>
                    </div>
                    <span class="page pull-right">
                    {{ currect_page }}/{{ total_page }}
                    <i @click="prevPage" class="icon iconfont icon-gengduo page-pre" :class="{active: currect_page === 1}"> </i>
                    <i @click="nextPage" class="icon iconfont icon-gengduo page-pre" :class="{active: currect_page === total_page}"> </i>
                </span>
                </div>
                <div class="product clearfix">
                    <router-link class="pull-left"
                                 :key="index"
                                 to="/mall/search/product-details" v-for="(product, index) in shop.productList">
                        <img :src="product.img"/>
                        <p>
                            <em>
                                <b>￥</b>
                                {{ product.price }}
                            </em>
                            <s>
                                <b>￥</b>
                                {{ product.old_price }}
                            </s>
                        </p>
                        <p class="product-intro">{{ product.intro }}</p>
                        <p class="sales">本月销量<span>{{ product.saleNum }}</span>件</p>
                    </router-link>
                </div>
            </div>
            <div class="text-center" v-show="total_page > 1">
                <paginate
                    :pageCount="total_page"
                    :pageRange="3"
                    :marginPages="2"
                    :clickHandler="switchPage"
                    prevText="上一页"
                    nextText="下一页"
                    containerClass="pagination no-margin"
                    pageClass="page-item">
                </paginate>
            </div>
        </div>
        <need-browse :commendList="commendList"></need-browse>
    </div>
</template>