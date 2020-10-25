<template>
    <ul class="right-side" :class="{fixed:show}">
        <li>
            <router-link class="text-center" to="/mall/user">
                <i class="icon iconfont icon-denglu"></i>我的
            </router-link>
        </li>
        <li>
            <router-link class="text-center" to="/mall/cart-settlement">
                <i class="icon iconfont icon-gouwuche"></i>购物车
            </router-link>
        </li>
        <li>
            <router-link class="text-center" to="/mall/customer-serve">
                <i class="icon iconfont icon-kefu"></i>客服
            </router-link>
        </li>
        <li @click="toTop" class="toTop" v-if="show">
            <a class="text-center">
                <p>TOP</p>
                <i class="icon iconfont icon-top"> </i>
            </a>
        </li>
    </ul>
</template>

<script>
    export default {
        data() {
            return {
                show: false,
            };
        },
        methods: {
            toTop() {
                const timeOut = setInterval(() => {
                    if (document.body.scrollTop !== 0 || document.documentElement.scrollTop !== 0) {
                        window.scrollBy(0, -20);
                    } else {
                        clearInterval(timeOut);
                    }
                }, 5);
            },
        },
        mounted() {
            window.onscroll = () => {
                let scrollTop;
                if ((navigator.userAgent.indexOf('MSIE') >= 0) && (navigator.userAgent.indexOf('Opera') < 0)) {
                    scrollTop = document.documentElement.scrollTop;
                } else {
                    scrollTop = document.body.scrollTop;
                }
                if (scrollTop > 800) {
                    this.show = true;
                } else {
                    this.show = false;
                }
            };
        },
    };
</script>
