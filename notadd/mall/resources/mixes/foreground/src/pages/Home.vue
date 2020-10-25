<script>
    import { swiper, swiperSlide } from 'vue-awesome-swiper';
    import store from '../store';
    import image1 from '../assets/images/img_04.png';
    import image3 from '../assets/images/img_06-1.png';
    import image4 from '../assets/images/kitty.png';
    import slideImg from '../assets/images/slide-img.png';
    import picture1 from '../assets/images/img_02-1.png';
    import picture2 from '../assets/images/img_02-2.png';
    import picture3 from '../assets/images/offer_img_01.png';
    import NeedBrowse from '../components/NeedBrowse.vue';

    export default {
        components: {
            NeedBrowse,
            swiper,
            swiperSlide,
        },
        computed: {
            hours() {
                const time = this.deviationTime / 1000;
                const hour = this.formate(parseInt((time / 3600), 10));
                return hour;
            },
            minutes() {
                const time = this.deviationTime / 1000;
                const minutes = this.formate(parseInt((time / 60) % 60, 10));
                return minutes;
            },
            seconds() {
                const time = this.deviationTime / 1000;
                const second = this.formate(parseInt(time % 60, 10));
                return second;
            },
        },
        data() {
            return {
                activeTab: 0,
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
                deviationTime: null,
                newProduct: [
                    {
                        img: image3,
                        name: '舒适耐用儿童睡椅',
                        price: 98.00,
                        title: '新品上架',
                    },
                    {
                        img: image3,
                        name: '舒适耐用儿童睡椅',
                        price: 98.00,
                        title: '新品上架',
                    },
                    {
                        img: image3,
                        name: '舒适耐用儿童睡椅',
                        price: 98.00,
                        title: '新品上架',
                    },
                    {
                        img: image3,
                        name: '舒适耐用儿童睡椅',
                        price: 98.00,
                        title: '新品上架',
                    },
                ],
                recommendList: [
                    {
                        icon: 'icon-dianzan',
                        title: '品质推荐',
                        recommend: [
                            {
                                img: image1,
                                intro: '满300减50优惠折扣',
                                title: '年末特惠',
                            },
                            {
                                img: image1,
                                intro: '满300减50优惠折扣',
                                title: '年末特惠',
                            },
                            {
                                img: image1,
                                intro: '满300减50优惠折扣',
                                title: '年末特惠',
                            },
                            {
                                img: image1,
                                intro: '满300减50优惠折扣',
                                title: '年末特惠',
                            },
                        ],
                    },
                    {
                        icon: 'icon-dianzan',
                        title: '特价优惠',
                        recommend: [
                            {
                                img: image1,
                                intro: '满300减50优惠折扣',
                                title: '年末特惠',
                            },
                            {
                                img: image1,
                                intro: '满300减50优惠折扣',
                                title: '年末特惠',
                            },
                            {
                                img: image1,
                                intro: '满300减50优惠折扣',
                                title: '年末特惠',
                            },
                            {
                                img: image1,
                                intro: '满300减50优惠折扣',
                                title: '年末特惠',
                            },
                        ],
                    },
                    {
                        icon: 'icon-dianzan',
                        title: '精选分类',
                        recommend: [
                            {
                                img: image1,
                                intro: '满300减50优惠折扣',
                                title: '年末特惠',
                            },
                            {
                                img: image1,
                                intro: '满300减50优惠折扣',
                                title: '年末特惠',
                            },
                            {
                                img: image1,
                                intro: '满300减50优惠折扣',
                                title: '年末特惠',
                            },
                            {
                                img: image1,
                                intro: '满300减50优惠折扣',
                                title: '年末特惠',
                            },
                        ],
                    },
                ],
                slideList: [
                    {
                        discount: '2折',
                        img: slideImg,
                        intro: '大牌低至大牌低至',
                        title: '品质生活',
                    },
                    {
                        discount: '2折',
                        img: slideImg,
                        intro: '大牌低至大牌低至',
                        title: '品质生活',
                    },
                    {
                        discount: '2折',
                        img: slideImg,
                        intro: '大牌低至大牌低至',
                        title: '品质生活',
                    },
                    {
                        discount: '2折',
                        img: slideImg,
                        intro: '大牌低至大牌低至',
                        title: '品质生活',
                    },
                ],
                spikeInfo: {
                    endTime: '2017-10-01 10:06:00',
                    spikeList: [
                        {
                            img: image4,
                            name: '舒适耐用儿童睡椅舒适耐用儿童睡椅',
                            price1: 34.00,
                            price2: 98.00,
                        },
                        {
                            img: image4,
                            name: '舒适耐用儿童睡椅舒适耐用儿童睡椅',
                            price1: 34.00,
                            price2: 98.00,
                        },
                        {
                            img: image4,
                            name: '舒适耐用儿童睡椅舒适耐用儿童睡椅',
                            price1: 34.00,
                            price2: 98.00,
                        },
                        {
                            img: image4,
                            name: '舒适耐用儿童睡椅舒适耐用儿童睡椅',
                            price1: 34.00,
                            price2: 98.00,
                        },
                        {
                            img: image4,
                            name: '舒适耐用儿童睡椅舒适耐用儿童睡椅',
                            price1: 34.00,
                            price2: 98.00,
                        },
                        {
                            img: image4,
                            name: '舒适耐用儿童睡椅舒适耐用儿童睡椅',
                            price1: 34.00,
                            price2: 98.00,
                        },
                    ],
                },
                scrollPos: 0,
                show: false,
                swiperOption: {
                    autoplay: 3000,
                    loop: true,
                    notNextTick: true,
                    pagination: '.swiper-pagination',
                    paginationClickable: true,
                    paginationBulletRender(s, index, className) {
                        return `<span class="${className}">${(index + 1)}</span>`;
                    },
                },
                floorList: [
                    {
                        module: '女装外套',
                        modulColor: '#ff2342',
                        module_img: picture1,
                        module_item: ['大衣', '卫衣', '针织', '打底', '大衣1'],
                        newProduct: [
                            {
                                img: picture2,
                                intro: '舒适耐实用儿童睡椅',
                                title: '新品上架',
                            },
                            {
                                img: picture2,
                                intro: '舒适耐实用儿童睡椅',
                                title: '新品上架',
                            },
                            {
                                img: picture2,
                                intro: '舒适耐实用儿童睡椅',
                                title: '新品上架',
                            },
                            {
                                img: picture2,
                                intro: '舒适耐实用儿童睡椅',
                                title: '新品上架',
                            },
                        ],
                        offerList: [
                            {
                                img: picture3,
                                intro: '舒适耐实用儿童睡椅',
                                title: '满减优惠',
                            },
                            {
                                img: picture3,
                                intro: '舒适耐实用儿童睡椅',
                                title: '满减优惠',
                            },
                        ],
                    },
                    {
                        module: '母婴专区',
                        modulColor: '#ff2342',
                        module_img: picture1,
                        module_item: ['大衣', '卫衣', '针织', '打底', '大衣1'],
                        newProduct: [
                            {
                                img: picture2,
                                intro: '舒适耐实用儿童睡椅',
                                title: '新品上架',
                            },
                            {
                                img: picture2,
                                intro: '舒适耐实用儿童睡椅',
                                title: '新品上架',
                            },
                            {
                                img: picture2,
                                intro: '舒适耐实用儿童睡椅',
                                title: '新品上架',
                            },
                            {
                                img: picture2,
                                intro: '舒适耐实用儿童睡椅',
                                title: '新品上架',
                            },
                        ],
                        offerList: [
                            {
                                img: picture3,
                                intro: '舒适耐实用儿童睡椅',
                                title: '满减优惠',
                            },
                            {
                                img: picture3,
                                intro: '舒适耐实用儿童睡椅',
                                title: '满减优惠',
                            },
                        ],
                    },
                    {
                        module: '棉品寝卧',
                        modulColor: '#f2ae99',
                        module_img: picture1,
                        module_item: ['大衣', '卫衣', '针织', '打底', '大衣1'],
                        newProduct: [
                            {
                                img: picture2,
                                intro: '舒适耐实用儿童睡椅',
                                title: '新品上架',
                            },
                            {
                                img: picture2,
                                intro: '舒适耐实用儿童睡椅',
                                title: '新品上架',
                            },
                            {
                                img: picture2,
                                intro: '舒适耐实用儿童睡椅',
                                title: '新品上架',
                            },
                            {
                                img: picture2,
                                intro: '舒适耐实用儿童睡椅',
                                title: '',
                            },
                        ],
                        offerList: [
                            {
                                img: picture3,
                                intro: '舒适耐实用儿童睡椅',
                                title: '满减优惠',
                            },
                            {
                                img: picture3,
                                intro: '舒适耐实用儿童睡椅',
                                title: '满减优惠',
                            },
                        ],
                    },
                    {
                        module: '护理美妆',
                        modulColor: '#5ec0a1',
                        module_img: picture1,
                        module_item: ['大衣', '卫衣', '针织', '打底', '大衣1'],
                        newProduct: [
                            {
                                img: picture2,
                                intro: '舒适耐实用儿童睡椅',
                                title: '新品上架',
                            },
                            {
                                img: picture2,
                                intro: '舒适耐实用儿童睡椅',
                                title: '新品上架',
                            },
                            {
                                img: picture2,
                                intro: '舒适耐实用儿童睡椅',
                                title: '新品上架',
                            },
                            {
                                img: picture2,
                                intro: '舒适耐实用儿童睡椅',
                                title: '新品上架',
                            },
                        ],
                        offerList: [
                            {
                                img: picture3,
                                intro: '舒适耐实用儿童睡椅',
                                title: '满减优惠',
                            },
                            {
                                img: picture3,
                                intro: '舒适耐实用儿童睡椅',
                                title: '满减优惠',
                            },
                        ],
                    },
                ],
            };
        },
        methods: {
            switchTab(index) {
                this.activeTab = index;
            },
            toTop() {
                const timeOut = setInterval(() => {
                    if (document.body.scrollTop !== 0 || document.documentElement.scrollTop !== 0) {
                        window.scrollBy(0, -20);
                    } else {
                        clearInterval(timeOut);
                    }
                }, 5);
            },
            formate(time) {
                if (time >= 10) {
                    return time;
                }
                return `0${time}`;
            },
            cutDown() {
                const endTime = new Date(this.spikeInfo.endTime).getTime();
                const nowTime = new Date().getTime();
                const deviationTime = (endTime - nowTime);
                this.deviationTime = deviationTime;
            },
        },
        mounted() {
            store.commit('loading', false);
            this.cutDown();
            const countdown = setInterval(() => {
                if (this.deviationTime > 0) {
                    this.deviationTime = this.deviationTime - 1000;
                } else {
                    clearInterval(countdown);
                }
            }, 1000);
            window.onscroll = () => {
                let scrollTop;
                if ((navigator.userAgent.indexOf('MSIE') >= 0) && (navigator.userAgent.indexOf('Opera') < 0)) {
                    scrollTop = document.documentElement.scrollTop;
                } else {
                    scrollTop = document.body.scrollTop;
                }
                if (scrollTop > 1300) {
                    this.show = true;
                } else {
                    this.show = false;
                }
            };
        },
    };
</script>
<template>
    <div class="home padding-attribute">
        <!---->
        <div>
            <div id="myCarousel" class="carousel slide" data-ride="carousel">
                <swiper :options="swiperOption" ref="mySwiperA">
                    <swiper-slide v-for="(item, index) in slideList" :key="item.Id">
                        <router-link to="/">
                            <img :src="item.img">
                        </router-link>
                    </swiper-slide>
                    <div class="swiper-pagination" slot="pagination"></div>
                </swiper>
            </div>
            <!--新品上架-->
            <div class="new-product">
                <div class="container">
                    <div>
                        <router-link :to="{ name: 'product-details' }" class="col-sm-3 col-xs-3 row-content row-margin"
                                     v-for="(item, index) in newProduct" :key="index">
                            <div class="img-box">
                                <img :src="item.img" alt="">
                            </div>
                            <div class="product">
                                <p class="name">{{ item.title }}</p>
                                <p class="intro"><span>{{ item.name }}</span> <span class="money">&yen; <i>{{ item.price }}</i></span></p>
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>
            <!--限时秒杀-->
            <div class="time-spike">
                <div class="container">
                    <div class="title clearfix">
                        <h3>
                            <i class="icon iconfont icon-miaosha"></i>
                            <a @mouseover="switchTab(0)" :class="{active:activeTab === 0}">限时秒杀</a>
                            <a @mouseover="switchTab(1)" :class="{active:activeTab === 1}">新品预售</a>
                            <a @mouseover="switchTab(2)" :class="{active:activeTab === 2}">满减活动</a>
                        </h3>
                        <p class="cutDown" v-if="activeTab === 0">还有&nbsp;<span>{{ hours }}</span><i>:</i><span>{{ minutes }}</span><i>:</i><span>{{ seconds }}</span>&nbsp;结束</p>
                    </div>
                    <div class="spike-content row">
                        <router-link :to="{ name: 'product-details' }" v-for="(item, index) in spikeInfo.spikeList" :key="index">
                            <dl class="col col-line">
                                <dt><img :src="item.img"></dt>
                                <dd>
                                    <p class="name">{{ item.name }}</p>
                                    <p class="new-price">￥{{ item.price1 }}<s class="pre-price">￥{{ item.price2 }}</s></p>
                                </dd>
                            </dl>
                        </router-link>
                    </div>
                </div>
            </div>
            <!--品质推荐-->
            <div class="product-recommend">
                <div class="container">
                    <div class="product-recommend-model product-recommen" v-for="recommend in recommendList">
                        <div class="recommend-name">
                            <i class="icon iconfont" :class="recommend.icon"></i>
                            {{ recommend.title }}
                        </div>
                        <div class="recommend-content clearfix">
                            <router-link class="recommend-content-model" v-for="(item, index) in recommend.recommend" :key="index"
                                         :to="{ name: 'product-details' }">
                                <div class="content-model text-center">
                                    <p class="name">{{ item.title }}</p>
                                    <p class="intro">{{ item.intro }}</p>
                                </div>
                                <div class="intro-img">
                                    <img :src="item.img" alt="">
                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
            <div  v-scroll-spy="scrollPos">
                <div id="wowen-clothing" class="wowen-clothing" v-for="(typeList, typeIndex) in floorList" :key="typeIndex">
                    <div class="container">
                        <div class="wowen-clothing-model">
                            <div class="recommend-name" :style="{ background: typeList.modulColor}">
                                <div class="before-block" :style="{ background: typeList.modulColor}">
                                    <div>
                                    </div>
                                </div>
                                <div class="model-name">
                                    <i class="icon iconfont icon-nvzhuang"></i>
                                    {{ typeList.module }}
                                </div>
                                <div class="model-content">
                                    <ul class="product-kinds">
                                        <li v-for="(item, itemIndex) in typeList.module_item" :key="itemIndex">
                                            <a>{{ item }}<span>|</span></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="clothing-content clearfix">
                                <router-link class="content-left-img" to="/mall/home">
                                    <img :src="typeList.module_img" alt="">
                                </router-link>
                                <div class="content-right-model">
                                    <div class="content">
                                        <ul class="model-content">
                                            <li class="content-item item-line" v-for="(item, index) in typeList.newProduct" :key="index">
                                                <router-link :to="{name: 'product-details'}">
                                                    <div class="img-box">
                                                        <img :src="item.img">
                                                    </div>
                                                    <div class="product">
                                                        <p class="name" :style="{ color: typeList.modulColor}">{{ item.title }}</p>
                                                        <p class="intro"><span>{{ item.intro }}</span>
                                                            <span class="money"></span>
                                                        </p>
                                                    </div>
                                                </router-link>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="content-offer">
                                        <div class="content-item item-line" v-for="item in typeList.offerList">
                                            <router-link class="product" :to="{name: 'product-details'}">
                                                <p class="name" :style="{ color: typeList.modulColor}">{{ item.title }}</p>
                                                <p class="intro"><span>{{ item.intro }}</span> <span class="money"></span></p>
                                            </router-link>
                                            <router-link class="img-box" :to="{name: 'product-details'}">
                                                <img :src="item.img" alt="">
                                            </router-link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--需要 浏览推荐-->
                <need-browse :commendList="commendList"></need-browse>
                <!--楼层导航-->
            </div>
            <nav class="bs-js-navbar-scrollspy" v-if="show">
                <ul class="floor nav">
                    <li :class="{active:scrollPos == index}" v-for="(floor, index) in floorList">
                        <a @click="$scrollTo(index)">{{ floor.module }}</a>
                    </li>
                    <li @click="toTop">
                        <a>
                            <p>TOP</p>
                            <i class="icon iconfont icon-top"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>
