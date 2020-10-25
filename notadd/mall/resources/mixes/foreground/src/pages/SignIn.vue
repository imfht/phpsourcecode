<script>
    import FooterBar from '../layouts/FooterBar.vue';
    import FooterContent from '../layouts/FooterContent.vue';
    import bannerImg from '../assets/images/login_banner.png';

    export default {
        components: {
            FooterBar,
            FooterContent,
        },
        data() {
            return {
                bannerStyle: {
                    backgroundImage: `url(${bannerImg})`,
                },
                loading: false,
                signData: {
                    account: '',
                    password: '',
                },
                signRule: {
                    account: [
                        {
                            required: true,
                            message: '请填写用户名',
                            trigger: 'blur',
                        },
                    ],
                    password: [
                        {
                            required: true,
                            message: '请填写密码',
                            trigger: 'blur',
                        },
                        {
                            type: 'string',
                            min: 6,
                            message: '密码长度不能小于6位',
                            trigger: 'blur',
                        },
                    ],
                },
                remember: false,
            };
        },
        methods: {
            handleSubmit(name) {
                const self = this;
                self.loading = true;
                this.$refs[name].validate(valid => {
                    if (valid) {
                        self.$message.success('提交成功!');
                    } else {
                        self.loading = false;
                        self.$message.error('表单验证失败!');
                    }
                });
            },
        },
    };
</script>
<template>
    <div class="signin ">
        <div class="header-bar-logo">
            <div class="container">
                <router-link to="/mall">
                    <img src="../assets/images/logo.png" alt="">
                </router-link>
            </div>
        </div>
        <div class="signup-content" :style="bannerStyle">
            <div class="container clearfix">
                <div class="signin-content">
                    <div class="signup-title clearfix">
                        <span>密码登录</span>
                        <router-link to="/signup">没有账号？立即注册</router-link>
                    </div>
                    <i-form ref="signForm" :model="signData" :rules="signRule">
                        <form-item prop="account" class="signup-form-group">
                            <i-input type="text" v-model="signData.account" placeholder="邮箱账号">
                                <icon class="icon iconfont icon-denglu" type="ios-person-outline" slot="prepend"></icon>
                            </i-input>
                        </form-item>
                        <form-item prop="password" class="signup-form-group">
                            <i-input type="password" v-model="signData.password" placeholder="登录密码">
                                <icon class="icon iconfont icon-mima" type="ios-locked-outline" slot="prepend"></icon>
                            </i-input>
                        </form-item>
                        <form-item class="signup-form-group signup-form-group-password">
                            <label class="ivu-checkbox-wrapper ivu-checkbox-group-item">
                                <span class="ivu-checkbox">
                                    <input
                                        type="checkbox"
                                        class="ivu-checkbox-input"
                                        v-model="remember"
                                        value="remember">
                                    <span class="ivu-checkbox-inner"></span>
                                </span>
                                <span>记住密码</span>
                            </label>
                            <router-link class="pull-right" to="/mall/reset-password">
                                忘记密码
                            </router-link>
                        </form-item>
                        <form-item class="signup-form-group">
                            <i-button :loading="loading" class="register" type="primary" @click="handleSubmit('signForm')">
                                <span v-if="!loading">登录</span>
                                <span v-else>正在提交…</span>
                            </i-button>
                        </form-item>
                        <form-item class="signup-form-group third-party">
                            第三方账号登录
                            <a href="">
                                <i class="icon iconfont icon-weixin"></i>
                            </a>
                            <a href=""><i class="icon iconfont icon-qq"></i></a>
                        </form-item>
                    </i-form>
                </div>
            </div>
        </div>
        <footer-content></footer-content>
        <footer-bar></footer-bar>
    </div>
</template>