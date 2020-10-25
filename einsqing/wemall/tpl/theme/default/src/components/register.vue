<template>
	<div id="main">
		<div class="header-bar">
			<router-link to="/login" class="header-left" style="display: block;">
			</router-link>
			<div class="header-title">手机注册</div>
		</div>
		<div class="b-color-f">
			<div class="filter-top" id="scrollUp">
				<i class="iconfont icon-jiantou"></i>
			</div>
			<div class="search-div j-search-div ts-3">
				<footer class="close-search j-close-search">点击关闭</footer>
			</div>
			<div class="con b-color-f">
				<div class="user-center user-register of-hidden">
					<div id="j-tab-con" class="swiper-container-horizontal swiper-container-autoheight">
						<div class="swiper-wrapper">
							<section class="swiper-slide swiper-no-swiping swiper-slide-active">
								<div class="text-all dis-box j-text-all" name="mobilediv">
									<label>+86</label>
									<div class="box-flex input-text">
										<input class="j-input-text" id="mobile_phone" name="mobile" type="tel" placeholder="手机号" v-model='phoneNumber' maxlength="11">
										<i class="iconfont icon-guanbi is-null j-is-null"></i>
									</div>
								</div>
								<div class="text-all dis-box j-text-all" name="mobile_codediv">
									<div class="box-flex input-text" style="-webkit-box-flex: 1;">
										<input class="j-input-text" name="mobile_code" placeholder="请输入验证码" v-model="mobileCode">
										<i class="iconfont icon-guanbi is-null j-is-null"></i>
									</div>
									<a type="button" class="ipt-check-btn" id="sendsms" @click="sendCode">{{captcha_txt}}</a>
								</div>
								<div class="text-all dis-box j-text-all" name="smspassworddiv">
									<div class="box-flex input-text">
										<input class="j-input-text" name="smspassword" type="text" placeholder="请输入密码" v-model="password" v-if='eyeOpen'>
										<input class="j-input-text" name="smspassword" type="password" placeholder="请输入密码" v-model="password" v-else>
										<span :class='{"icon-eye-close":eyeClose,"icon-eye-open":eyeOpen}' @click="showPwd"></span>
									</div>
								</div>
								<div class="text-all dis-box j-text-all" name="repassworddiv">
									<div class="box-flex input-text">
										<input class="j-input-text" name="repassword" type="text" placeholder="请重新输入密码" v-model="surepassword" v-if='eyeOpen1'>
										<input class="j-input-text" name="repassword" type="password" placeholder="请重新输入密码" v-model="surepassword" v-else>
										<span class="iseyeOpen" :class='{"icon-eye-close":eyeClose1,"icon-eye-open":eyeOpen1}' @click="showPwd1"></span>
									</div>
								</div>
							</section>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="m-tip">
			<button type="" class="btn-submit" style="margin-top: 20px;" @click="userRegister">注册</button>
			<router-link to="/login" class="lo-tip">
				已注册直接登录
			</router-link>
		</div>
	</div>
</template>

<script>
	import fetch from './../fetch'
	import { Toast } from 'mint-ui';

	export default {
		data() {
			return {
				phoneNumber: '', //手机号
				password: '', //密码
				surepassword: '', //确认密码
				mobileCode: '', //短信验证码
				captcha_uuid: '', //获取短信时返回的验证值，注册时需要
				captcha_txt: "发送验证码",
				eyeClose: true,
				eyeOpen: false,
				eyeClose1: true,
				eyeOpen1: false
			}
		},
		components: {

		},
		methods: {
			// 发送验证码
			sendCode() {
				//验证输入手机号是否合法
				if(!this.phoneNumber) {
					Toast('手机号不能为空');
					return;
				}
				if(!(/^1[358]\d{9}$/.test(this.phoneNumber))) {
					Toast('手机号输入有误');
					return;
				}

				if(typeof this.captcha_txt == 'number') {
					return;
				}
				//倒计时
				let i = 1;
				this.captcha_txt = 60;
				let captcha_interval = setInterval(() => {
					this.captcha_txt -= i;
					if(this.captcha_txt == 0) {
						clearInterval(captcha_interval);
						this.captcha_txt = '发送验证码';
					}
				}, 1000);

				fetch('/api/public/sendSmsCaptcha', { phone: this.phoneNumber }).then((res) => {
					if(res.data.code) {
						//获取验证码
						this.captcha_uuid = res.data.data.uuid;
					} else {
						Toast(res.data.msg);
					}
				});
			},
			// 注册
			userRegister() {
				//验证信息
				if(!this.phoneNumber) {
					Toast('手机号不能为空');
					return;
				}
				if(!(/^1[358]\d{9}$/.test(this.phoneNumber))) {
					Toast('手机号输入有误');
					return;
				}

				if(!this.captcha_uuid || !this.mobileCode) {
					Toast('请先发送验证码');
					return;
				}
				if(this.password != this.surepassword) {
					Toast('密码两次输入有误');
					return;
				}

				fetch('/api/public/register', {
					phone: this.phoneNumber,
					password: this.password,
					captcha: this.mobileCode,
					captcha_uuid: this.captcha_uuid
				}).then((res) => {
					if(res.data.code == 1) {
						this.$router.go(-1)
					} else {
						Toast(res.data.msg)
					}
				})
			},
			//是否显示密码
			showPwd() {
				this.eyeClose = !this.eyeClose;
				this.eyeOpen = !this.eyeOpen;
			},
			showPwd1() {
				this.eyeClose1 = !this.eyeClose1;
				this.eyeOpen1 = !this.eyeOpen1;
			}
		}
	}
</script>

<style>
	.user-register .swiper-slide {
		padding: 0 1.3rem;
		box-sizing: border-box;
	}
	
	.user-register .hd {
		padding: 0 3rem;
		margin-bottom: .6rem;
		font-size: 1.05rem;
		text-align: center;
		border-bottom: 1px solid #F3F4F9;
	}
	
	.user-register .hd li {
		padding: .6rem 0;
		height: 2.6rem;
		line-height: 2.6rem;
		display: block;
		margin-bottom: -1px;
		position: relative;
	}
	
	.user-register .bd {
		padding-bottom: 1rem !important;
	}
	
	.lo-tip {
		color: #7D7B7B;
		font-size: 13px;
		margin-top: .6rem;
		float: right;
	}
	
	.ipt-check-btn {
		padding: 0 1.4rem;
		height: 1.6rem;
		line-height: 1.6rem;
		margin: .7rem 0;
		text-align: center;
		color: #555;
		display: block;
		border-left: 1px solid #F3F4F9;
		margin-left: 1.2rem;
		font-size: 1rem;
	}
	
	.text-all .input-text span {
		position: absolute;
		right: 22px;
		top: 8px;
	}
</style>