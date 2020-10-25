<script>
    import image1 from '../assets/images/head.png';

    export default {
        data() {
            return {
                attentionStore: [
                    {
                        store_img: image1,
                        store_name: 'XXX母婴用品店1',
                        storeList: [
                            {
                                img: image1,
                                name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                                price: 48.88,
                            },
                            {
                                img: image1,
                                name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                                price: 48.88,
                            },
                            {
                                img: image1,
                                name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                                price: 48.88,
                            },
                        ],
                    },
                    {
                        store_img: image1,
                        store_name: 'XXX母婴用品店2',
                        storeList: [
                            {
                                img: image1,
                                name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                                price: 48.88,
                            },
                            {
                                img: image1,
                                name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                                price: 48.88,
                            },
                            {
                                img: image1,
                                name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                                price: 48.88,
                            },
                        ],
                    },
                    {
                        store_img: image1,
                        store_name: 'XXX母婴用品店3',
                        storeList: [
                            {
                                img: image1,
                                name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                                price: 48.88,
                            },
                            {
                                img: image1,
                                name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                                price: 48.88,
                            },
                            {
                                img: image1,
                                name: '西部母婴推荐哆啦A梦可爱儿童玩具',
                                price: 48.88,
                            },
                        ],
                    },
                ],
            };
        },
        methods: {
            cancelAttention(store) {
                const index = this.attentionStore.indexOf(store);
                if (index !== -1) {
                    this.attentionStore.splice(index, 1);
                }
            },
        },
    };
</script>
<template>
    <div class="collect-store">
        <div class="store-title">
            <h4>我关注的店铺</h4>
        </div>
        <div class="attention-store clearfix" v-for="(store, index) in attentionStore" ref="store">
            <div class="left-store text-center">
                <router-link to="/mall/store/shop-home">
                    <dl class="store">
                        <dt><img :src="store.store_img"></dt>
                        <dd>
                            <p class="name">{{ store.store_name }} <span>自营</span></p>
                        </dd>
                    </dl>
                </router-link>
                <div class="store-btn clearfix">
                    <button class="customer-service">联系客服</button>
                    <button class="unsubscribe" @click="cancelAttention(store)">取消关注</button>
                </div>
            </div>
            <div class="store-product clearfix">
                <router-link to="/mall/search/product-details">
                    <dl class="store-product-model " v-for="item in store.storeList">
                        <dt><img :src="item.img"></dt>
                        <dd>
                            <p class="name">{{ item.name }}</p>
                            <p class="new-price">￥{{ item.price }}</p>
                        </dd>
                    </dl>
                </router-link>
            </div>
        </div>
    </div>
</template>