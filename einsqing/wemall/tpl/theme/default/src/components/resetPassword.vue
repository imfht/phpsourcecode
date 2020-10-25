<template>
	<div id="main">
		<div class="b-color-f">
			<div class="filter-top" id="scrollUp"><i class="iconfont icon-jiantou"></i></div>
			<div class="search-div j-search-div ts-3">
				<footer class="close-search j-close-search"> 点击关闭</footer>
			</div>
			<div class="header-bar">
				<div class="header-left" style="display: block;" onclick="history.go(-1)"></div>
				<div class="header-title">密码重置</div>
			</div>
			<div class="con" id="pjax-container">
				<div id="check" style="display: block;">
					<section class="user-center user-forget-tel margin-lr">
						<p class="fl t-remark2" style="font-size: 1.05rem">
							您的手机号：+86
							<span id="show_mobile">{{phone}}</span>
						</p>
						<div class="text-all dis-box j-text-all" name="sms_codediv">
							<div class="input-text input-check box-flex" style="-webkit-box-flex: 1;"><input type="hidden" name="mobile" value="13838309026">
								<input class="j-input-text" type="text" name="sms_code" placeholder="请输入验证码" v-model='captcha'>
								<input class="j-input-text" type="text" name="newpassword" placeholder="新密码" v-model='password' v-if='showPassword'>
								<input class="j-input-text" type="password" name="newpassword" placeholder="新密码" v-model='password' v-else>
								<i class="eyeOpen1" :class='{"icon-eye-close":eyeClose,"icon-eye-open":eyeOpen}' @click="isshowPwd1"></i>

								<input class="j-input-text" type="text" name="newpasswords" placeholder="重复新密码" v-model='surepassword' v-if='showPassword1'>
								<input class="j-input-text" type="password" name="newpasswords" placeholder="重复新密码" v-model='surepassword' v-else>
								<i class="eyeOpen2" :class='{"icon-eye-close":eyeClose1,"icon-eye-open":eyeOpen1}' @click="isshowPwd2"></i>
							</div>
								<a type="button" id="sendsms" class="ipt-check-btn" @click="sendCode()">{{captcha_txt}}</a><br />
						</div>
						<input type="hidden" name="enabled_sms" value="1">
						<button type="" class="btn-submit" @click="resetPassword()" style='margin-bottom:3rem;'>重置密码</button>
					</section>
				</div>
				<div class="div-messages"></div>
			</div>
		</div>
	</div>
</template>

<script>
	import fetch from './../fetch'
	import { Toast } from 'mint-ui';
	export default {
		data() {
			return {
				phone: '', //手机号
				captcha: null, //验证码
				password: null, //密码
				surepassword: null, //确认密码
				captcha_uuid: null, //获取短信时返回的验证值，改密码时需要
				showPassword: false, //是否显示密码
				showPassword1: false,
				captcha_txt: "发送验证码",
				eyeClose: true,
				eyeOpen: false,
				eyeClose1: true,
				eyeOpen1: false

			}
		},
		created() {
			this.phone = this.$route.query.phone;
		},
		methods: {
			// 发送验证码
			sendCode() {

				if(typeof this.captcha_txt == 'number') {
					return;
				}
				let i = 1;
				this.captcha_txt = 60;
				let captcha_interval = setInterval(() => {
					this.captcha_txt -= i;
					if(this.captcha_txt == 0) {
						clearInterval(captcha_interval);
						this.captcha_txt = '发送验证码';
					}
				}, 1000);

				fetch('/api/public/sendSmsCaptcha', { phone: this.phone }).then((res) => {
					if(res.data.code) {
						this.captcha_uuid = res.data.data.uuid;
					} else {
						Toast(res.data.msg);
					}
				});
			},
			// 密码重置
			resetPassword() {
				//修改密码
				if(!this.captcha_uuid || !this.captcha) {
					Toast('请先发送验证码');
					return;
				}
				if(this.password != this.surepassword) {
					Toast('密码两次输入有误');
					return;
				}

				fetch('/api/public/resetPassword', { phone: this.phone, password: this.password, captcha: this.captcha, captcha_uuid: this.captcha_uuid }).then((res) => {

					if(res.data.code) {
						this.$router.push('login');
					} else {
						Toast(res.data.msg);
					}
				});
			},
			//改变密码类型
			isshowPwd1() {
				this.eyeClose = !this.eyeClose;
				this.eyeOpen = !this.eyeOpen;
				this.showPassword = !this.showPassword;
			},
			isshowPwd2() {
				this.eyeClose1 = !this.eyeClose1;
				this.eyeOpen1 = !this.eyeOpen1;
				this.showPassword1 = !this.showPassword1;
			}
		}
	}
</script>

<style scoped>
	.j-text-all {
		position: relative;
	}
	
	.eyeOpen1 {
		position: absolute;
		bottom:60px;
		left: 290px;
	}
	
	.eyeOpen2 {
		position: absolute;
		bottom: 15px;
		left: 290px;
	}
</style>