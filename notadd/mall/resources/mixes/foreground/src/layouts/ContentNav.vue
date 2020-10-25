<template>
    <div class="content-nav">
        <div class="container">
            <div class="classification">
                <i class="icon iconfont icon-menu"></i>所有分类
                <menu-bar></menu-bar>
            </div>
            <ul class="header-bar-nav">
                <li v-for="item in navigation" :class="{ 'active': $route.path === item.to }">
                    <router-link :to="item.to" target="_blank">{{ item.text }}</router-link>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    import MenuBar from './MenuBar.vue';

    export default {
        components: {
            MenuBar,
        },
        data() {
            return {
                navigation: [
                    {
                        to: '/mall',
                        text: '首页',
                    },
                    {
                        to: '/mall/spike',
                        text: '秒杀',
                    },
                ],
            };
        },
    };
</script>
