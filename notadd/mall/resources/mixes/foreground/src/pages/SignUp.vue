<script>
    import Message from 'iview/src/components/message';
    import Modal from '../components/Modal.vue';
    import FooterBar from '../layouts/FooterBar.vue';

    export default {
        components: {
            FooterBar,
            Message,
            Modal,
        },
        data() {
            const reg = /^1[3|4|5|7|8][0-9]\d{8}$/;
            const validatorPhone = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('手机号不能为空'));
                } else if (!reg.test(value)) {
                    callback(new Error('请输入正确手机号'));
                } else {
                    callback();
                }
            };
            const validatePass = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('请输入密码'));
                } else {
                    if (this.signUpData.passwordAgain !== '') {
                        // 对第二个密码框单独验证
                        this.$refs.signUpData.validateField('passwordAgain');
                    }
                    callback();
                }
            };
            const validatePassCheck = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('请再次输入密码'));
                } else if (value !== this.signUpData.password) {
                    callback(new Error('两次输入密码不一致!'));
                } else {
                    callback();
                }
            };
            return {
                countdownStart: false,
                countdown: 60,
                loading: false,
                signUpData: {
                    email: '',
                    password: '',
                    passwordAgain: '',
                    phone: '',
                    code: '',
                    agree: false,
                },
                signUpRule: {
                    email: [
                        {
                            required: true,
                            message: '请填写用户名',
                            trigger: 'blur',
                        },
                    ],
                    password: [
                        {
                            required: true,
                            trigger: 'blur',
                            validator: validatePass,
                        },
                    ],
                    passwordAgain: [
                        {
                            required: true,
                            trigger: 'blur',
                            type: 'number',
                            validator: validatePassCheck,
                        },
                    ],
                    phone: [
                        {
                            required: true,
                            trigger: 'blur',
                            type: 'number',
                            validator: validatorPhone,
                        },
                    ],
                    code: [
                        {
                            required: true,
                            message: '请填写验证码',
                            trigger: 'blur',
                        },
                    ],
                },
            };
        },
        methods: {
            agree() {
                this.signUpData.agree = true;
                this.close();
            },
            close() {
                this.$refs.modal.close();
            },
            getCode() {
                const self = this;
                self.$refs.signUpForm.validateField('phone', valid => {
                    if (valid) {
                        self.loading = false;
                    } else {
                        Message.success(
                            {
                                content: '发送成功!',
                            },
                        );
                    }
                });
            },
            showModel() {
                this.$refs.modal.open();
            },
            submit(name) {
                const self = this;
                self.loading = true;
                this.$refs[name].validate(valid => {
                    if (valid) {
                        if (this.signUpData.agree) {
                            Message.success('提交成功!');
                        } else {
                            Message.error('请先同意用户注册协议!');
                        }
                    } else {
                        self.loading = false;
                        Message.error('表单验证失败!');
                    }
                });
            },
        },
    };
</script>
<template>
    <div class="signup">
        <div class="header-bar-logo">
            <div class="container">
                <router-link to="/mall">
                    <img src="../assets/images/logo.png" alt="">
                </router-link>
            </div>
        </div>
        <div class="signup-content">
            <div class="signup-title clearfix">
                <span>注册</span>
                <router-link to="/signin">已有账号？点击登录</router-link>
            </div>
            <i-form class="signup-form" ref="signUpForm" :model="signUpData" :rules="signUpRule">
                <form-item prop="email" label="邮箱账号">
                    <i-input class="signup-form-group signup-form-control" type="text" v-model="signUpData.email">
                    </i-input>
                </form-item>
                <form-item prop="password" label="密码">
                    <i-input class="signup-form-group signup-form-control" type="password" v-model="signUpData.password">
                    </i-input>
                </form-item>
                <form-item prop="passwordAgain" label="确认密码">
                    <i-input class="signup-form-group signup-form-control" type="password" v-model="signUpData.passwordAgain">
                    </i-input>
                </form-item>
                <form-item prop="phone" label="手机号">
                    <i-input class="signup-form-group signup-form-control" type="text" v-model="signUpData.phone">
                    </i-input>
                </form-item>
                <form-item prop="code" label="验证码">
                    <i-input  class="signup-form-group signup-form-control signup-form-code" type="text" v-model="signUpData.code">
                    </i-input>
                    <div class="signup-form-control signup-form-obtain-code" @click="getCode">
                        <i v-if="countdownStart">{{ countdown }}秒</i>
                        <i v-if="!countdownStart">获取验证码</i>
                    </div>
                </form-item>
                <form-item label="协议条款">
                    <label class="ivu-checkbox-wrapper ivu-checkbox-group-item">
                        <span class="ivu-checkbox">
                            <input
                                type="checkbox"
                                class="ivu-checkbox-input"
                                v-model="signUpData.agree"
                                value="remember">
                            <span class="ivu-checkbox-inner"></span>
                        </span>
                        <span class="tip">用户注册即代表同意协议条款
                        </span>
                    </label>
                    <a class="protocol-content" @click="showModel"> 《xx协议条款》</a>
                </form-item>
                <form-item label="">
                    <i-button :loding="loading" class="register" @click="submit('signUpForm')">
                        <span v-if="!loading">注册</span>
                        <span v-else>正在提交…</span>
                    </i-button>
                </form-item>
            </i-form>
        </div>
        <modal ref="modal">
            <div slot="title">
                <h4 class="modal-title pull-left" v-text="'商城用户注册协议'"></h4>
                <a class="pull-right" @click="close"><img src="../assets/images/close.png" alt=""></a>
            </div>
            <div slot="body">
                <div class="content">
                    <p>用户注册协议</p>
                    <p>本协议是您与京东网站（简称;本站;，网址：www.jd.com）所有者（以下简称为;京东;）之间就京东网站服务等相关事宜所订立的契约，请您仔细阅读本注册协议，您点击;同意并继续;按钮后，本协议即构成对双方有约束力的法律文件。</p>
                    <p>第1条 本站服务条款的确认和接纳</p>

                    <p>1.1本站的各项电子服务的所有权和运作权归京东所有。用户同意所有注册协议条款并完成注册程序，才能成为本站的正式用户。用户确认：本协议条款是处理双方权利义务的契约，始终有效，法律另有强制性规定或双方另有特别约定的，依其规定。</p>
                    <p>1.2用户点击同意本协议的，即视为用户确认自己具有享受本站服务、下单购物等相应的权利能力和行为能力，能够独立承担法律责任。</p>
                    <p>1.3如果您在18周岁以下，您只能在父母或监护人的监护参与下才能使用本站。</p>
                    <p>1.4京东保留在中华人民共和国大陆地区法施行之法律允许的范围内独自决定拒绝服务、关闭用户账户、清除或编辑内容或取消订单的权利。</p>
                    <p>第2条 本站服务</p>

                    <p>2.1京东通过互联网依法为用户提供互联网信息等服务，用户在完全同意本协议及本站规定的情况下，方有权使用本站的相关服务。</p>
                    <p>2.2用户必须自行准备如下设备和承担如下开支：（1）上网设备，包括并不限于电脑或者其他上网终端、调制解调器及其他必备的上网装置；（2）上网开支，包括并不限于网络接入费、上网设备租用费、手机流量费等。</p>
                    <p>第3条 用户信息</p>
                    <p>2.1京东通过互联网依法为用户提供互联网信息等服务，用户在完全同意本协议及本站规定的情况下，方有权使用本站的相关服务。</p>
                    <p>2.2用户必须自行准备如下设备和承担如下开支：（1）上网设备，包括并不限于电脑或者其他上网终端、调制解调器及其他必备的上网装置；（2）上网开支，包括并不限于网络接入费、上网设备租用费、手机流量费等。</p>
                    <p>第3条 用户信息</p>

                    <p>3.1用户应自行诚信向本站提供注册资料，用户同意其提供的注册资料真实、准确、完整、合法有效，用户注册资料如有变动的，应及时更新其注册资料。如果用户提供的注册资料不合法、不真实、不准确、不详尽的，用户需承担因此引起的相应责任及后果，并且京东保留终止用户使用京东各项服务的权利。</p>
                </div>
            </div>
            <button type="button" class="order-btn" slot="save_address" @click="agree">同意并继续</button>
        </modal>
        <footer-bar></footer-bar>
    </div>
</template>