<script>
    export default {
        computed: {
            login() {
                return this.$store.state.login;
            },
            user() {
                return this.$store.state.user;
            },
        },
        data() {
            return {
            };
        },
        methods: {
            signout() {
                this.$store.commit('login', false);
                this.$store.commit('user', {});
                localStorage.removeItem('user');
                this.$router.push('/login');
            },
        },
    };
</script>
<template>
    <div class="header-bar">
        <div class="container">
            <div class="header-welcome">
                西部母婴欢迎您!
                <span v-if="!login">
                    <router-link to="/signin" class="login">登录</router-link>
                    <span>|</span>
                    <router-link to="/signup" class="register">注册</router-link>
                </span>
                <span v-if="login">
                    {{ user.name }}
                    <a class="signOut" @clcik="signout">[退出]</a>
                </span>
            </div>
            <div class="header-bar-user ">
                <ul class="header-bar-nav">
                    <li :class="{ 'active': $route.path === '/mall/user/order' }">
                        <router-link to="/mall/user/order" target="_blank">我的订单</router-link>
                        <span>|</span>
                    </li>
                    <li :class="{ 'active': $route.path === '/mall/user' }">
                        <router-link to="/mall/user" target="_blank">个人中心</router-link>
                        <span>|</span>
                    </li>
                    <li :class="{ 'active': $route.path === '/mall/customer-serve' }">
                        <a>客户服务</a>
                        <span>|</span>
                        <ul>
                            <li>
                                <router-link to="/mall/customer-serve">客户服务</router-link>
                            </li>
                            <li>
                                <router-link to="/mall/help">帮助中心</router-link>
                            </li>
                            <li>
                                <router-link to="/mall/after-sale">售后中心</router-link>
                            </li>
                            <li>
                                <router-link to="/mall/customer-serve">客服中心</router-link>
                            </li>
                        </ul>
                    </li>
                    <li :class="{ 'active': $route.path === '/mall/store/apply' }">
                        <router-link to="/mall/store/apply" target="_blank">商家入驻</router-link>
                        <span>|</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
