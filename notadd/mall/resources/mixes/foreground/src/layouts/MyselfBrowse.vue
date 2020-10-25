<template>
    <div class="browse-model">
        <div class="container">
            <div class="browse-commeneds text-left">
                <div class="recommend-name">
                    <i class="icon iconfont icon-xin"></i>
                    {{ browse.title }}
                    <span>{{ browse.intro }}</span>
                </div>
            </div>
            <div class="browse-content">
                <router-link to="/mall/search/product-details">
                    <dl class="col col-line" v-for="item in browse.commendList">
                        <dt><img :src="item.img"></dt>
                        <dd>
                            <p class="name">{{ item.name }}</p>
                            <p class="new-price">￥{{ item.price }}
                                <i class="pre-price">销量：{{ item.amount }}</i>
                            </p>
                        </dd>
                    </dl>
                </router-link>
            </div>
        </div>
    </div>
</template>
<script>
    import image1 from '../assets/images/img_07.png';

    export default {
        data() {
            return {
                browse: {
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
                    intro: '根据你的浏览推荐',
                    title: '猜你喜欢',
                },
            };
        },
    };
</script>