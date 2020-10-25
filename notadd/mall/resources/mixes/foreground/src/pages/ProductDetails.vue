<script>
    import Message from 'iview/src/components/message';
    import { swiper, swiperSlide } from 'vue-awesome-swiper';
    import Paginate from '../components/Paginate.vue';
    import SplinLine from '../components/SplinLine.vue';
    import NeedBrowse from '../components/NeedBrowse.vue';
    import Magnifier from '../components/Magnifier.vue';
    import Modal from '../components/Modal.vue';
    import EndTimer from '../components/Timer.vue';
    import small1 from '../assets/images/s1.jpg';
    import small2 from '../assets/images/s2.jpg';
    import small3 from '../assets/images/s3.jpg';
    import small4 from '../assets/images/s4.jpg';
    import small5 from '../assets/images/s5.jpg';
    import big1 from '../assets/images/b1.png';
    import big2 from '../assets/images/b2.jpg';
    import big3 from '../assets/images/b3.jpg';
    import big4 from '../assets/images/b4.jpg';
    import big5 from '../assets/images/b5.jpg';
    import img1 from '../assets/images/details/stool2.png';
    import img2 from '../assets/images/details/stool1.png';
    import user from '../assets/images/details/user-img.png';
    import img7 from '../assets/images/head.png';
    import logo from '../assets/images/img_lofo.png';
    import talk from '../assets/images/service.png';

    export default {
        data() {
            const reg = /^1[3|4|5|7|8][0-9]\d{8}$/;
            const validatorTel = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('手机号不能为空'));
                } else if (!reg.test(value)) {
                    callback(new Error('请输入正确手机号'));
                } else {
                    callback();
                }
            };
            const self = this;
            return {
                activeTab: 1,
                activeImg: img1,
                activeCoupon: {},
                banner: {
                    smalls: [
                        small1,
                        small2,
                        small3,
                        small4,
                        small5,
                    ],
                    bigs: [
                        big1,
                        big2,
                        big3,
                        big4,
                        big5,
                    ],
                },
                arrivalData: {
                    tel: '',
                    email: '',
                },
                arrivalRules: {
                    tel: [
                        {
                            required: true,
                            trigger: 'blur',
                            validator: validatorTel,
                        },
                    ],
                    email: [
                        {
                            required: true,
                            trigger: 'blur',
                            message: '邮箱地址不能为空',
                        },
                    ],
                },
                bigOption: {
                    autoplay: false,
                    loop: false,
                    notNextTick: true,
                    slideToClickedSlide: true,
                    controlBy: 'container',
                    onSlideNextStart() {
                        if (self.$refs.mySwiperB.swiper) {
                            self.$refs.mySwiperB.swiper
                                .slideTo(self.$refs.mySwiperA.swiper.activeIndex);
                            self.imgSrc = self.banner.bigs[self.$refs.mySwiperA.swiper.activeIndex];
                        }
                    },
                    onSlidePrevStart() {
                        if (self.$refs.mySwiperB.swiper) {
                            self.$refs.mySwiperB.swiper
                                .slideTo(self.$refs.mySwiperA.swiper.activeIndex);
                            self.imgSrc = self.banner.bigs[self.$refs.mySwiperA.swiper.activeIndex];
                        }
                    },
                },
                boxWidth: 384,
                boxHeight: 384,
                commendList: [
                    {
                        amount: 188,
                        img: img7,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                    {
                        amount: 188,
                        img: img7,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                    {
                        amount: 188,
                        img: img7,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                    {
                        amount: 188,
                        img: img7,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                    {
                        amount: 188,
                        img: img7,
                        name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                        price: 48.88,
                    },
                ],
                coupons: [
                    {
                        name: '满１０0减２',
                        useTime: '2016.08.01 - 2017.10.10',
                    },
                    {
                        name: '满５００减１００',
                        useTime: '2016.08.01 - 2017.10.10',
                    },
                ],
                data: [
                    {
                        children: [
                            {
                                label: '故宫',
                                value: 'gugong',
                            },
                            {
                                label: '天坛',
                                value: 'tiantan',
                            },
                            {
                                label: '王府井',
                                value: 'wangfujing',
                            },
                        ],
                        label: '北京',
                        value: 'beijing',
                    },
                    {
                        children: [
                            {
                                value: 'nanjing',
                                label: '南京',
                                children: [
                                    {
                                        value: 'fuzimiao',
                                        label: '夫子庙',
                                    },
                                ],
                            },
                            {
                                value: 'suzhou',
                                label: '苏州',
                                children: [
                                    {
                                        value: 'zhuozhengyuan',
                                        label: '拙政园',
                                    },
                                    {
                                        value: 'shizilin',
                                        label: '狮子林',
                                    },
                                ],
                            },
                        ],
                        label: '江苏',
                        value: 'jiangsu',
                    },
                ],
                distribution_address: [],
                evaluation: [
                    {
                        name: '皮皮虾',
                        userImg: user,
                        bigImg: '',
                        sorce: 3,
                        buyDate: '2016-12-30',
                        evaluationTxt: '包装精美 跟我想象的面料不太一样 但是超满意 裸睡王 贴身内衣的那种棉质 家人特别喜欢 超值的 就是被套有点大 理想尺码床单1米八 被罩2米3 但是不拆开卖 除了这个缺点 其他的都超满意',
                        evaluationImg: [img1, img2],
                        productInfo: {
                            color: '蓝色',
                            size: 'm',
                        },
                    },
                    {
                        name: '皮皮虾',
                        userImg: user,
                        bigImg: '',
                        sorce: 3,
                        buyDate: '2016-12-30',
                        evaluationTxt: '包装精美 跟我想象的面料不太一样 但是超满意 裸睡王 贴身内衣的那种棉质 家人特别喜欢 超值的 就是被套有点大 理想尺码床单1米八 被罩2米3 但是不拆开卖 除了这个缺点 其他的都超满意',
                        evaluationImg: [img1, img2],
                        productInfo: {
                            color: '蓝色',
                            size: 'm',
                        },
                    },
                    {
                        name: '皮皮虾',
                        userImg: user,
                        bigImg: '',
                        sorce: 3,
                        buyDate: '2016-12-30',
                        evaluationTxt: '包装精美 跟我想象的面料不太一样 但是超满意 裸睡王 贴身内衣的那种棉质 家人特别喜欢 超值的 就是被套有点大 理想尺码床单1米八 被罩2米3 但是不拆开卖 除了这个缺点 其他的都超满意',
                        evaluationImg: [img1, img2],
                        productInfo: {
                            color: '蓝色',
                            size: 'm',
                        },
                    },
                ],
                gap: 20,
                goodskind: [
                    {
                        onoff: true,
                        kind: '全部分类',
                        item: ['按销量', '按时间', '按地点', '按价格'],
                    },
                    {
                        onoff: false,
                        kind: '全部分类',
                        item: ['按销量', '按时间', '按地点', '按价格'],
                    },
                    {
                        onoff: false,
                        kind: '全部分类',
                        item: ['按销量', '按时间', '按地点', '按价格'],
                    },
                    {
                        onoff: false,
                        kind: '全部分类',
                        item: ['按销量', '按时间', '按地点', '按价格'],
                    },
                ],
                imgSrc: null,
                isActive: false,
                kefu: [
                    {
                        name: '售前客服',
                        items: [
                            '客服依依', '客服依依', '客服依依', '客服依依',
                        ],
                    },
                    {
                        name: '售后客服',
                        items: [
                            '客服依依', '客服依依', '客服依依',
                        ],
                    },
                ],
                product_intro: {
                    eval_num: 6298,
                    integral: 138,
                    name: 'Daisy London纯手工迷你雏菊IOTA纯银戒指S码M码L码年轻 与活力时尚简约',
                    offer: ['很便宜', '清仓大甩卖', '买二送一'],
                    original_price: 488.00,
                    price: 239.00,
                    transport_price: 12.00,
                    size: ['S', 'M', 'L', 'XL', 'XXL'],
                    type: ['套餐一', '套餐二', '套餐三'],
                    sales_num: 7764,
                    status: 1,
                },
                productNum: 1,
                productInfo: {
                    cailiao: '实木',
                    color: '原木 黑色 象牙白',
                    madeIn: '中国',
                    name: '西部母婴',
                    style: '欧式',
                },
                recommend_products: [
                    {
                        id: 1,
                        img: img7,
                        name: '西部母婴推荐哆啦荐哆啦A梦可爱儿A梦可荐哆啦A梦可爱儿爱儿...',
                        old_price: 60,
                        price: 46.88,
                    },
                    {
                        id: 1,
                        img: img7,
                        name: '西部母婴推荐哆啦荐哆啦A梦可爱儿A梦可荐哆啦A梦可爱儿爱儿...',
                        old_price: 60,
                        price: 46.88,
                    },
                    {
                        id: 1,
                        img: img7,
                        name: '西部母婴推荐哆啦荐哆啦A梦可爱儿A梦可荐哆啦A梦可爱儿爱儿...',
                        old_price: 60,
                        price: 46.88,
                    },
                    {
                        id: 1,
                        img: img7,
                        name: '西部母婴推荐哆啦荐哆啦A梦可爱儿A梦可荐哆啦A梦可爱儿爱儿...',
                        old_price: 60,
                        price: 46.88,
                    },
                ],
                seeAgain_products: [
                    {
                        id: 2,
                        img: img7,
                        name: '西部母婴推荐哆啦荐哆啦A梦可爱儿A梦可荐哆啦A梦可爱儿爱儿...',
                        price: 46.88,
                        sales: 187,
                    },
                    {
                        img: img7,
                        id: 1,
                        name: '西部母婴推荐哆啦荐哆啦A梦可爱儿A梦可荐哆啦A梦可爱儿爱儿...',
                        price: 46.88,
                        sales: 187,
                    },
                    {
                        id: 3,
                        img: img7,
                        name: '西部母婴推荐哆啦荐哆啦A梦可爱儿A梦可荐哆啦A梦可爱儿爱儿...',
                        price: 46.88,
                        sales: 187,
                    },
                ],
                modalTitle: '',
                selectRecommends: [],
                show: 0,
                showEvaluation: 'all',
                smallImgs: [img1, img2, img1, img2, img1, img1, img1],
                scale: 3,
                smallOption: {
                    autoplay: false,
                    centeredSlides: true,
                    loop: false,
                    normalizeSlideIndex: false,
                    notNextTick: true,
                    nextButton: '.swiper-button-next',
                    onSlideNextStart() {
                        this.activeIndex = self.$refs.mySwiperB.swiper.activeIndex;
                        self.$refs.mySwiperA.swiper
                            .slideTo(self.$refs.mySwiperB.swiper.activeIndex);
                    },
                    onSlidePrevStart() {
                        this.activeIndex = self.$refs.mySwiperB.swiper.activeIndex;
                        self.$refs.mySwiperA.swiper
                            .slideTo(self.$refs.mySwiperB.swiper.activeIndex);
                    },
                    onTap() {
                        this.activeIndex = self.$refs.mySwiperB.swiper.activeIndex;
                        self.$refs.mySwiperA.swiper
                            .slideTo(self.$refs.mySwiperB.swiper.activeIndex);
                    },
                    prevButton: '.swiper-button-prev',
                    spaceBetween: 10,
                    slideToClickedSlide: true,
                    slidesPerView: 3.8,
                    watchSlidesProgress: false,
                    watchSlidesVisibility: false,
                },
                store: {
                    img: logo,
                    name: 'xxx旗舰店',
                },
                talked: talk,
                wrapX: 0,
                wrapY: 0,
                current_page: 1,
                pages: 3,
                endTime: '2017-10-08 10:06:00',
            };
        },
        components: {
            Magnifier,
            NeedBrowse,
            SplinLine,
            swiper,
            swiperSlide,
            Modal,
            Paginate,
            EndTimer,
        },
        computed: {
            total_price() {
                let totalPrice = 0;
                this.selectRecommends.forEach(item => {
                    totalPrice += item.price;
                });
                return totalPrice.toFixed(2);
            },
            total_oldPrice() {
                let totalOldPrice = 0;
                this.selectRecommends.forEach(item => {
                    totalOldPrice += item.old_price;
                });
                return totalOldPrice.toFixed(2);
            },
        },
        methods: {
            getSuccess(item) {
                this.activeCoupon = item;
                this.$refs.modal.open();
                this.modalTitle = '提醒';
            },
            dosomething(n) {
                this.onOff = n;
            },
            arrivalGoods() {
                this.$refs.modal1.open();
                this.modalTitle = '到货通知';
            },
            closemodal() {
                this.$refs.modal.close();
            },
            closemodal1() {
                this.$refs.modal1.close();
            },
            closemodal2() {
                this.$refs.modal2.close();
            },
            switchPage(page) {
                this.current_page = page;
            },
            change(num) {
                this.goodskind[num].onoff = !this.goodskind[num].onoff;
            },
            getOffect() {
                const box = document.getElementsByClassName('product-img');
                this.wrapX = box[0].offsetLeft;
                this.wrapY = box[0].offsetTop;
            },
            selectRecommend(item, e) {
                const index = this.selectRecommends.indexOf(item);
                if (e.target.checked && index === -1) {
                    this.selectRecommends.push(item);
                } else {
                    this.selectRecommends.splice(index, 1);
                }
            },
            showImg(item, $event) {
                const source = $event.target.getAttribute('src');
                if (item.bigImg === source) {
                    item.bigImg = '';
                } else {
                    item.bigImg = source;
                }
            },
            tabSWitch(index) {
                this.activeTab = index;
            },
            submit() {
                const self = this;
                self.$refs.arrivalForm.validate(valid => {
                    if (valid) {
                        this.$refs.modal1.close();
                        this.$refs.modal2.open();
                        this.modalTitle = '到货通知';
                    } else {
                        Message.error('表单验证失败!');
                    }
                });
            },
        },
        mounted() {
            setTimeout(() => {
                this.imgSrc = this.banner.bigs[0];
                this.getOffect();
                window.onresize = () => {
                    this.getOffect();
                };
            }, 1100);
        },
    };
</script>
<template>
    <div class="product-details">
        <div class="basic-intro container clearfix">
            <div class="miaobaoxie">
                <router-link to="/slide">首页  >  xx旗舰店 > 尿不湿</router-link>
            </div>
            <div class="product-img">
                <swiper :options="bigOption" ref="mySwiperA">
                    <swiper-slide v-for="(item, index) in banner.bigs" :key="index">
                        <img :src="item">
                    </swiper-slide>
                    <div class="swiper-pagination" slot="pagination"></div>
                </swiper>
                <magnifier
                    :wrap-x='wrapX'
                    :wrap-y='wrapY'
                    :box-width='boxWidth'
                    :box-height='boxHeight'
                    :scale='scale'
                    :gap='gap'
                    :img-src='imgSrc'>
                </magnifier>
                <div class="swiper2">
                    <swiper :options="smallOption" ref="mySwiperB">
                        <swiper-slide v-for="(item, index) in banner.smalls" :key="index">
                            <img :src="item">
                        </swiper-slide>
                    </swiper>
                    <div class="swiper-button-prev icon iconfont icon-gengduo gengduo-left" slot="button-prev"></div>
                    <div class="swiper-button-next icon iconfont icon-gengduo" slot="button-next"></div>
                </div>
            </div>
            <div class="product-intro">
                <h3>{{ product_intro.name }}</h3>
                <p class="offer">{{ product_intro.offer.join('&nbsp;') }}</p>
                <div class="stork clearfix">
                    <p><i class="icon iconfont icon-miaosha"></i>双11品牌秒杀 <span class="pull-right">距离结束还有
                         <span class="price">
                                    <end-timer @mistake="dosomething" @time-end="dosomething" :endTime='endTime'>
                                    </end-timer>
                         </span>
                    </span></p>
                </div>
                <div class="price-box">
                    <p class="priceit"><span>价格</span><span class="price">￥{{ product_intro.price }}</span><span class="original-price">原价<s>￥{{ product_intro.original_price }}</s></span>
                    </p>
                    <p class="favourable"><span>优惠券</span>
                        <button class="paper"
                                @click="getSuccess(coupon)"
                                v-for="(coupon, index) in coupons"
                                :key="index">
                            {{ coupon.name }}
                        </button>
                    </p>
                </div>
                <ul class="sell-info">
                    <li class="sell-count">
                        <span>销量</span>
                        <span class="count">{{　product_intro.sales_num　}}</span>
                    </li>
                    <li class="evaluation-count">
                        <span>评价</span>
                        <span class="count">{{ product_intro.eval_num }}</span>
                    </li>
                    <li class="integral-count">
                        <span>赠送积分</span>
                        <span class="count">{{　product_intro.integral　}}</span>
                    </li>
                </ul>
                <div class="distribution">
                    <p>配送<span class="origin-adress">西安</span>至
                        <cascader class="destination" :data="data" v-model="distribution_address"></cascader>
                        运费：<span class="freigh">&nbsp;￥ {{ product_intro.transport_price }}</span></p>
                </div>
                <dl class="product-type-select clearfix">
                    <dt>尺码</dt>
                    <dd>
                        <ul>
                            <li v-for="size in product_intro.size">
                                <label class="form-control-radio">
                                    <input type="radio" name="size" :value="size">
                                    <span>{{ size }}</span>
                                </label>
                            </li>
                        </ul>
                    </dd>
                </dl>
                <dl class="product-type-select clearfix">
                    <dt>类型</dt>
                    <dd>
                        <ul>
                            <li v-for="type in product_intro.type">
                                <label class="form-control-radio">
                                    <input type="radio" name="package" value="type">
                                    <span>{{ type }}</span>
                                </label>
                            </li>
                        </ul>
                    </dd>
                </dl>
                <dl class="product-num clearfix">
                    <dt>数量</dt>
                    <dd>
                        <div class="input-group input-group-sm pull-left">
                            <span class="input-group-addon" @click="productNum > 1 ?productNum--:0">-</span>
                            <input type="number" class="form-control" v-model="productNum">
                            <span class="input-group-addon" @click="productNum++">+</span>
                        </div>
                    </dd>
                </dl>
                <ul  v-if=" product_intro.status === 1" class="product-buy clearfix">
                    <router-link to="/mall/search/product-details/submit-order">
                        <li class="buy"><a class="text-center">立刻购买</a></li>
                    </router-link>

                    <li class="basket"><a class="text-center">加入购物车</a></li>
                </ul>
                <ul  v-else=" product_intro.status === 2" class="product-buygrey clearfix">
                    <li class="buy"><a class="text-center">立刻购买</a></li>
                    <li class="basket"><a class="text-center">加入购物车</a></li>
                </ul>
                <div v-if=" product_intro.status === 1" class="stock pull-left">有货，今天下单预计1月22送到</div>
                <div v-else="product_intro.status === 2" class="nostock pull-left">您选择的商品<span>库存不足</span>，请选择其他商品或申请<button @click="arrivalGoods">到货通知</button>提示</div>
            </div>
        </div>
        <!--推荐购买-->
        <ul class="combination-buy container">
            <router-link :to="{ path: 'product-details' }" tag="li" class="text-center" v-for="(product, index) in recommend_products" :key="index">
                <a>
                    <img :src="product.img"/>
                </a>
                <p class="intro">{{ product.name }}</p>
                <p class="price">￥{{　product.price　}}</p>
                <div class="check-box select">
                    <label>
                        <input @change="selectRecommend(product,$event)" name="recommend" type="checkbox"
                                 class="input_check" :id="product.id">
                        <span></span>
                    </label>
                </div>
            </router-link>
            <li>
                <p class="original-price">原价：<s>￥{{ total_oldPrice }}</s></p>
                <p class="package-price">套餐价格：<span>￥{{　total_price　}}</span></p>
                <a class="text-center">立即购买</a>
            </li>
        </ul>
        <!--产品相关-->
        <div class="product-about container clearfix">
            <!--看了又看-->
            <div class="left-box">
                <div class="see-again-box follow">
                    <router-link to="../store/shop-home">
                        <div class="img">
                            <img :src="store.img" alt="">
                        </div>
                    </router-link>
                    <p class="name">{{ store.name }}</p>
                    <a class="shop">关注店铺</a>
                </div>
                <div class="see-again-box talked">
                    <h4>店铺客服</h4>
                    <div class="talkes">
                        <div class="talkeds" v-for="talk in kefu">
                            <p class="talk-name">{{ talk.name }}</p>
                           <div class="chilrd" v-for="item in talk.items">
                               {{ item }}&nbsp;&nbsp;&nbsp;<img :src="talked" alt="">
                           </div>
                        </div>
                    </div>
                </div>
                <div class="goods-kind">
                    <h4>商品分类</h4>
                    <ul class="goods-list">
                        <li class="goods-li" v-for="(good, index) in goodskind">
                            <span @click="change(index)" class="goods-btn" v-if="!good.onoff">+</span>
                            <span @click="change(index)" v-if="good.onoff" class="goods-btn">-</span>
                            <div class="goods-item">{{ good.kind }}</div>
                            <div class="clearfix"></div>
                            <ul class="goods-items" v-if="good.onoff && good.item.length > 0">
                                <li v-for="item in good.item">{{ item }}</li>
                            </ul>
                            <div class="clearfix"></div>
                        </li>
                    </ul>
                </div>
                <div class="see-again-box">
                    <h4>看了又看</h4>
                    <ul>
                        <router-link tag="li" to="/product-details" v-for="(item, index) in seeAgain_products" :key="index">
                            <a>
                                <img :src="item.img"/>
                            </a>
                            <p>{{ item.name }}
                            </p>
                            <p>
                                <span class="price">￥{{ item.price }}</span>
                                <span class="sales">销量：{{ item.sales }}</span>
                            </p>
                        </router-link>
                    </ul>
                </div>
            </div>

            <!--商品详情及用户评价-->
            <div class="details-evaluation pull-left">
                <ul class="clearfix nav" role="tablist">
                    <li class="pull-left text-center" :class="{active:activeTab===1}">
                        <i></i>
                        <a class="text-center a-block" name="details" @click="tabSWitch(1)">
                            商品详情
                        </a>
                    </li>
                    <li class="pull-left" :class="{active:activeTab===2}">
                        <i></i>
                        <a class="text-center a-block" name="evaluation" @click="tabSWitch(2)">
                            用户评价
                        </a>
                    </li>
                </ul>
                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane in active" id="details" v-if="activeTab===1">
                        <ul class="product-info row">
                            <li class="product-option  col-md-4">
                                <span class="option">品牌：</span>
                                <span class="option-value">{{ productInfo.name }}</span>
                            </li>
                            <li class="product-option  col-md-4">
                                <span class="option">地区：</span>
                                <span class="option-value">{{ productInfo.madeIn }}</span>
                            </li>
                            <li class="product-option  col-md-4">
                                <span class="option">材质：</span>
                                <span class="option-value">{{ productInfo.cailiao }}</span>
                            </li>
                            <li class="product-option  col-md-4">
                                <span class="option">风格：</span>
                                <span class="option-value">{{ productInfo.style }}</span>
                            </li>
                            <li class="product-option  col-md-4">
                                <span class="option">颜色：</span>
                                <span class="option-value">{{ productInfo.color }}</span>
                            </li>
                        </ul>
                        <div class="img-show">
                            <img src="../assets/images/details/details-img.png"/>
                        </div>
                    </div>
                    <div class="tab-pane" id="evaluation" v-if="activeTab===2">
                        <div class="evaluation-sorce">
                            <p>评分：<span class="sorce">9.98</span>
                                <span class="evaluation-count">评价人数：2290</span>
                            </p>
                        </div>
                        <div class="evaluation-select">
                            <div class="select">
                                <label class="radio-box">
                                    <input type="radio"
                                           name="evaluation-select"
                                           value="all"
                                           v-model="showEvaluation"/>
                                    <span></span>
                                    全部评价
                                </label>
                            </div>
                            <div class="select">
                                <label class="radio-box">
                                    <input type="radio"
                                           name="evaluation-select"
                                           value="image"
                                           v-model="showEvaluation"/>
                                    <span></span>
                                    图片
                                </label>
                            </div>
                            <ul class="sorting">
                                <li class="time-sorting"><a>按时间排序</a></li>
                                <li class="default-sorting"><a>默认排序</a></li>
                            </ul>
                        </div>
                        <ul class="evaluation-details">
                            <li class="evaluation-li" v-for="(item,index1) in evaluation">
                                <div class="clearfix evaluation-box">
                                    <div class="userinfo">
                                        <img :src="item.userImg">
                                        <p class="text-center">{{ item.name }}</p>
                                    </div>
                                    <div class="user-say">
                                        <div class="buy-info clearfix">
                                            <div class="buy-sorce">评分：
                                                <i v-if="item.sorce>=1" class="icon iconfont icon-xing1"></i>
                                                <i v-if="item.sorce>=2" class="icon iconfont icon-xing1"></i>
                                                <i v-if="item.sorce>=3" class="icon iconfont icon-xing1"></i>
                                                <i v-if="item.sorce>=4" class="icon iconfont icon-xing1"></i>
                                                <i v-if="item.sorce>=5" class="icon iconfont icon-xing1"></i>
                                            </div>
                                            <p><span class="date">{{ item.buyDate }}</span><span
                                                class="color">颜色：{{ item.productInfo.color }}</span><span class="size">尺码：{{ item.productInfo.size }}</span>
                                            </p>
                                        </div>
                                        <p class="user-description">{{ item.evaluationTxt }}</p>
                                        <ul class="buy-img clearfix">
                                            <li v-for="(value,index2) in item.evaluationImg"
                                                :class="{ zoom: item.bigImg === '' || item.bigImg !== value, ziim: item.bigImg === value }">
                                                <img :src="value" :i1="index1" :i2="index2"
                                                     @click="showImg(item, $event)"/>
                                            </li>
                                        </ul>
                                        <div class="active-img" v-if="item.bigImg">
                                            <img :src="item.bigImg"/>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <div class="text-right" v-show="pages > 1">
                            <paginate
                                :pageCount="pages"
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
                </div>
            </div>
        </div>
        <need-browse :commendList="commendList"></need-browse>
        <modal ref="modal" class="couponModal">
            <div slot="title">
                <div class="modal-title">
                    <div class="pull-left talk">
                        <h4 v-text="modalTitle"></h4>
                    </div>
                    <div class="pull-right">
                        <a class="closeit"><i class="icon iconfont icon-close closepict" @click="closemodal"></i></a>
                    </div>
                </div>
            </div>
            <div slot="body">
                <div class="couponBox">
                    <p class="tubiao"><i class="icon iconfont icon-icon-test"></i><span>恭喜！您已成功领取{{ activeCoupon.name }}优惠券</span></p>
                    <p>使用时间：{{ activeCoupon.useTime }}</p>
                    <p><router-link to="/mall/user/coupon" class="look">查看我的优惠券</router-link></p>
                </div>
            </div>
            <div slot="body">
            </div>
        </modal>
        <modal ref="modal1">
            <div slot="title">
                <div class="modal-title">
                    <div class="pull-left talk">
                        <h4 v-text="modalTitle"></h4>
                    </div>
                    <div class="pull-right">
                        <span class="closeit"><i class="icon iconfont icon-close closepict" @click="closemodal1"></i></span>
                    </div>
                </div>
            </div>
            <div slot="body">
                <i-form class="tablebox" ref="arrivalForm" :model="arrivalData" :rules="arrivalRules">
                    <form-item class="clearfix" label="手机号码" prop="tel">
                        <i-input class="putinit"
                                 v-model="arrivalData.tel"
                        ></i-input>
                    </form-item>
                    <form-item class="clearfix" label="邮箱地址" prop="email">
                        <i-input class="putinit"
                                 v-model="arrivalData.email"
                        ></i-input>
                    </form-item>
                </i-form>
                <button type="button" class="order-btn" slot="save_address" @click="submit">提交</button>
            </div>
        </modal>
        <modal ref="modal2">
            <div slot="title">
                <div class="modal-title">
                    <div class="pull-left talk">
                        <h4 v-text="modalTitle"></h4>
                    </div>
                    <div class="pull-right">
                        <span class="closeit"><i class="icon iconfont icon-close closepict" @click="closemodal2"></i></span>
                    </div>
                </div>
            </div>
            <div slot="body">
                <div class="remind">
                    <span>
                        商品在30日内到货，您将收到邮件或短信通知！
                    </span>
                </div>
            </div>
        </modal>
    </div>
</template>