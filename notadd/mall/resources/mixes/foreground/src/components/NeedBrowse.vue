<script>

    export default {
        data() {
            return {
                browse: {
                    intro: '根据你的浏览推荐',
                    title: '也许你需要它们',
                },
            };
        },
        props: {
            commendList: {
                type: Array,
            },
        },
    };
</script>
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
            <div class="browse-content clearfix">
                <router-link to="/mall/search/product-details" v-for="(item, index) in commendList" :key="index">
                    <dl class="col col-line">
                        <dt><img :src="item.img"></dt>
                        <dd>
                            <p class="name">{{ item.name }}</p>
                            <p class="new-price">￥{{ item.price }}<i class="pre-price">销量：{{ item.amount }}</i></p>
                        </dd>
                    </dl>
                </router-link>
            </div>
        </div>
    </div>
</template>