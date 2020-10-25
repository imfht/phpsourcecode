<script>
    import code from '../assets/images/weixin.png';

    export default {
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
            const passwordReg = /(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{6,16}$/;
            const passwordValidator = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('请输入密码'));
                } else if (!passwordReg.test(value)) {
                    callback(new Error('请输入符合规则的密码'));
                } else {
                    callback();
                }
            };
            const checkPasswordAgain = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('请再次输入密码'));
                } else if (value !== this.resetData.newPassword) {
                    callback(new Error('两次输入密码不一致!'));
                } else {
                    callback();
                }
            };
            return {
                codeImg: code,
                countdown: 60,
                countdownStart: false,
                identityData: {
                    phone: '',
                    phoneCode: '',
                    code: '',
                },
                identityRule: {
                    phone: {
                        required: true,
                        trigger: 'blur',
                        type: 'number',
                        validator: validatorPhone,
                    },
                    phoneCode: {
                        required: true,
                        message: '请填写手机验证码',
                        trigger: 'blur',
                    },
                    code: {
                        required: true,
                        message: '请填写验证码',
                        trigger: 'blur',
                    },
                },
                loading: false,
                resetData: {
                    newPassword: '',
                    passwordAgain: '',
                },
                resetRule: {
                    newPassword: {
                        required: true,
                        trigger: 'blur',
                        validator: passwordValidator,
                    },
                    passwordAgain: {
                        required: true,
                        trigger: 'blur',
                        validator: checkPasswordAgain,
                    },
                },
                temp: 1,
            };
        },
        methods: {
            getCode() {
                const self = this;
                self.$refs.identityForm.validateField('phone', valid => {
                    if (valid) {
                        self.loading = false;
                        self.$message.error('请输入正确手机号');
                    } else {
                        self.$message.success('提交成功!');
                    }
                });
            },
            submitResultIdentity() {
                const self = this;
                self.loading = true;
                this.$refs.identityForm.validate(valid => {
                    if (valid) {
                        this.temp += 1;
                        self.loading = false;
                    } else {
                        self.loading = false;
                        self.$message.error('表单验证失败!');
                    }
                });
            },
            submitResetData() {
                const self = this;
                self.loading = true;
                this.$refs.resetPasswordForm.validate(valid => {
                    if (valid) {
                        this.temp += 1;
                        self.loading = false;
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
    <div class="reset-password">
        <div class="container">
            <div class="account-content modify-password">
                <div class="modify-heading">
                    <div class="modify-status">
                        <ul class="clearfix">
                            <li class="clearfix" :class="{ already: temp >= 1 }">
                                <div class="step">
                                    <i class="step-list">1</i>
                                    <p class="modify-margin">验证身份</p>
                                </div>
                            </li>
                            <li class="clearfix" :class="{ already: temp >= 2 }">
                                <ul class="clearfix cricle-box">
                                    <li class="cricle" v-for="item in 17"></li>
                                </ul>
                                <div class="step">
                                    <i class="step-list">2</i>
                                    <p class="p-modify">修改登录密码</p>
                                </div>
                            </li>
                            <li class="clearfix" :class="{ already: temp >= 3 }">
                                <ul class="clearfix cricle-box">
                                    <li class="cricle" v-for="item in 17"></li>
                                </ul>
                                <div class="step">
                                    <i class="step-list">3</i>
                                    <p class="modify-margin">完成</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="modify-content1" v-if="temp===1">
                    <i-form class="signup-form" ref="identityForm" :model="identityData" :rules="identityRule">
                        <form-item class="clearfix" prop="phone" label="手机号">
                            <i-input class="signup-form-control"
                                     type="text"
                                     v-model="identityData.phone">
                            </i-input>
                        </form-item>
                        <form-item class="clearfix" prop="phoneCode" label="请填写手机验证码">
                            <i-input class="signup-form-control signup-form-code pull-left"
                                     type="text"
                                     v-model="identityData.phoneCode">
                            </i-input>
                            <div class="signup-form-control signup-form-obtain-code signup-form-code pull-left" @click="getCode">
                                <i v-if="countdownStart">{{ countdown }}秒</i>
                                <i v-if="!countdownStart">获取验证码</i>
                            </div>
                        </form-item>
                        <form-item  class="clearfix" prop="code" label="验证码">
                            <i-input class="signup-form-control pull-left signup-form-code"
                                     type="text"
                                     v-model="identityData.code">
                            </i-input>
                            <div class="signup-form-control verification-code pull-left">
                                <img :src="codeImg" alt="">
                            </div>
                            <a class="pull-left">看不清?换一张</a>
                        </form-item>
                        <form-item>
                            <i-button :loading="loading" class="order-btn" @click.prevent="submitResultIdentity">
                                <span v-if="!loading">提交</span>
                                <span v-else>正在提交…</span>
                            </i-button>
                        </form-item>
                    </i-form>
                </div>
                <div class="modify-content2" v-if="temp===2">
                    <i-form class="signup-form" ref="resetPasswordForm" :model="resetData" :rules="resetRule">
                        <form-item class="clearfix" prop="newPassword" label="新的登录密码">
                            <i-input class="signup-form-control"
                                     type="text"
                                     v-model="resetData.newPassword">
                            </i-input>
                            <p class="tip">由字母加数字符号至少两种以上数字组成的密码，6-20位半角字符，区分大小写</p>
                        </form-item>
                        <form-item class="clearfix" prop="passwordAgain" label="再次输入密码">
                            <i-input class="signup-form-control"
                                     type="text"
                                     v-model="resetData.passwordAgain">
                            </i-input>
                        </form-item>
                        <form-item>
                            <i-button :loading="loading" class="order-btn" @click.prevent="submitResetData">
                                <span v-if="!loading">提交</span>
                                <span v-else>正在提交…</span>
                            </i-button>
                        </form-item>
                    </i-form>
                </div>
                <div class="modify-content3" v-if="temp===3">
                    <div class="modify-success text-center">密码修改成功!</div>
                </div>
            </div>
        </div>
    </div>
</template>