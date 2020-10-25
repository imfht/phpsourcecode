<template>
	<div id="main">
		<div class="content">
			<div class="header-bar">
				<!--<div class="header-left" style="display: block;"></div>-->
				<router-link to="/home" class="header-left" style="display: block;">
				</router-link>
				<div class="header-title">登录</div>
			</div>
			<div class="siteinfo">手机账号登录</div>
			<div class="b-color-f">
				<div class="filter-top" id="scrollUp">
					<i class="iconfont icon-jiantou"></i>
				</div>
				<div class="search-div j-search-div ts-3">
					<footer class="close-search j-close-search"> 点击关闭</footer>
				</div>
				<div class="con b-color-f">
					<section class="user-center user-login margin-lr">
						<div class="text-all dis-box j-text-all" name="usernamediv">
							<label>账 号</label>
							<div class="box-flex input-text">
								<input class="j-input-text" name="username" datatype="s5-16" errormsg="昵称至少5个字符,最多16个字符！" type="text" placeholder="手机号" v-model="tel" maxlength="11">
							</div>
						</div>
						<div class="text-all dis-box j-text-all" name="passworddiv">
							<label>密 码</label>
							<div class="box-flex input-text">
								<input class="j-input-text" name="password" type="text" placeholder="请输入密码" v-model="pwd" v-if='eyeOpen'>
								<input class="j-input-text" name="password" type="password" placeholder="请输入密码" v-model="pwd" v-else>
								<i class="eyeOpen" :class='{"icon-eye-close":eyeClose,"icon-eye-open":eyeOpen}' @click="isshowPwd"></i>
							</div>

						</div>
					</section>
				</div>

			</div>
			<div class="m-tip">
				<button type="" class="btn-submit" style="margin-top: 40px" @click="login">登录</button>
				<div class="perhaps">或者</div>
				<button type="" class="btn-weixin" style="background-color: #09bb07;padding: .58rem 0;" @click='wxlogin'>
          <svg t="1487053882074" class="icon" style="vertical-align:middle;" viewBox="0 0 1024 1024" version="1.1"
               xmlns="http://www.w3.org/2000/svg" p-id="4651" xmlns:xlink="http://www.w3.org/1999/xlink" width="25"
               height="25">
            <path
              d="M669.180344 369.092219c10.257621 0 20.407796 0.753153 30.474059 1.87572-27.384697-127.512139-163.704432-222.249827-319.311443-222.249827-173.962054 0-316.466652 118.578676-316.466652 269.15102 0 86.913489 47.410799 158.289096 126.636189 213.644845l-31.652907 95.204315 110.63168-55.480592c39.585577 7.836479 71.349001 15.893992 110.851691 15.893992 9.931187 0 19.779486-0.49221 29.546943-1.257643-6.184863-21.161972-9.767458-43.320645-9.767458-66.314335C400.122445 481.298767 518.85257 369.092219 669.180344 369.092219zM499.018849 283.287995c23.827685 0 39.61423 15.675004 39.61423 39.489386 0 23.719214-15.786545 39.61423-39.61423 39.61423-23.719214 0-47.520293-15.895015-47.520293-39.61423C451.499579 298.964022 475.300658 283.287995 499.018849 283.287995zM277.536502 362.39161c-23.719214 0-47.657416-15.895015-47.657416-39.61423 0-23.813359 23.938202-39.489386 47.657416-39.489386 23.718191 0 39.503712 15.675004 39.503712 39.489386C317.040215 346.496595 301.255717 362.39161 277.536502 362.39161z"
              p-id="4652" fill="#ffffff"></path>
            <path
              d="M958.015681 615.758132c0-126.526695-126.609583-229.66061-268.810259-229.66061-150.572344 0-269.166369 103.134939-269.166369 229.66061 0 126.746706 118.594025 229.66368 269.166369 229.66368 31.514761 0 63.303768-7.948019 94.955652-15.880689l86.803995 47.532573-23.800056-79.090313C910.68777 750.32699 958.015681 687.133739 958.015681 615.758132zM601.936847 576.159252c-15.757892 0-31.652907-15.675004-31.652907-31.665187 0-15.772218 15.893992-31.652907 31.652907-31.652907 23.938202 0 39.613206 15.880689 39.613206 31.652907C641.54903 560.484248 625.874025 576.159252 601.936847 576.159252zM776.008394 576.159252c-15.647375 0-31.432896-15.675004-31.432896-31.665187 0-15.772218 15.785521-31.652907 31.432896-31.652907 23.720238 0 39.613206 15.880689 39.613206 31.652907C815.621601 560.484248 799.728632 576.159252 776.008394 576.159252z"
              p-id="4653" fill="#ffffff">
            </path>
          </svg>
          微信登录
        </button>
				<router-link to="/forgetPassword" class="forget-pwd">忘记密码 ?</router-link>
			</div>
		</div>
		<div class="footer-l">
			<div class="register linkforget" href="#/register">还没有账号?
				<router-link to="/register" style="color: #ff4146">
					立即注册
				</router-link>
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
				tel: null, //手机号
				pwd: null, //密码
				eyeClose: true, // 是否显示密码
				eyeOpen: false
			}
		},
		methods: {
			//改变密码类型
			isshowPwd() {
				this.eyeClose = !this.eyeClose;
				this.eyeOpen = !this.eyeOpen;
			},
			//登陆
			login() {
				//验证输入手机号
				if(!this.tel) {
					Toast('请输手机号');
					return;
				}
				if(!(/^1[358]\d{9}$/.test(this.tel))) {
					Toast('手机号输入有误');
					return;
				}
				if(!this.pwd) {
					Toast('请输入密码');
					return;
				}
				//取用户信息
				fetch('/api/public/login', { phone: this.tel, password: this.pwd }).then((res) => {
					if(res.data.code == 1) { //成功登陆
						//登陆之后获取用户信息储存                                                                      
						this.$localStorage.set('user', res.data.data.user);
						this.$localStorage.set('token', res.data.data.token);
						this.$router.push('/home');
					} else {
						Toast(res.data.msg)
					}
				});
			},
			//微信登陆
			wxlogin() {
				location.href = '/static/oauth.html#/?redirect=' + encodeURIComponent(location.origin + '/app#/user');
			}
		}

	}
</script>

<style>
	.content {
		min-height: calc(100vh - 60px);
	}
	
	.footer-l {
		height: 40px;
	}
	
	.footer-l a {
		color: #666;
	}
	
	.linkforget {
		text-align: center;
		font-size: 14px;
		margin-top: 12px;
		color: #a0a0a0
	}
	
	.header-bar {
		top: 0;
		z-index: 1055;
		width: 100%;
		height: 45px;
		background-color: rgba(255, 255, 255, .9);
	}
	
	.header-bar .header-left {
		cursor: pointer;
		height: 45px;
		width: 45px;
		float: left;
		background-image: url(../assets/img/left.png);
		background-repeat: no-repeat;
		background-position-x: left;
		background-position-y: center;
		display: none;
		z-index: 1;
		position: absolute;
		background-size: 32px;
	}
	
	.siteinfo {
		height: 80px;
		line-height: 80px;
		text-align: center;
		font-size: 13px;
		color: #a0a0a0;
	}
	
	.m-tip {
		margin: 0 1.3rem;
	}
	
	.perhaps {
		text-align: center;
		font-size: 13px;
		margin: 20px;
		color: #a0a0a0;
	}
	
	.header-bar .header-title {
		text-align: center;
		line-height: 45px;
		width: 100%;
		position: absolute;
		font-size: 18px;
	}
	
	.b-color-f {
		background: #fff;
	}
	
	.filter-top {
		width: 4.6rem;
		height: 4.6rem;
		text-align: center;
		line-height: 4.6rem;
		background: rgba(0, 0, 0, 0.5);
		border-radius: 100%;
		bottom: 12rem;
		right: 1.6rem;
		left: inherit;
	}
	
	.filter-top {
		display: none;
	}
	
	.filter-top .icon-jiantou {
		position: absolute;
		left: 0;
		right: 0;
		font-size: 2rem;
		color: #fff;
		-moz-transform: rotate(90deg);
		-webkit-transform: rotate(90deg);
		-ms-transform: rotate(90deg);
		-o-transform: rotate(90deg);
		transform: rotate(90deg);
	}
	
	.search-div {
		background: #fff;
		position: fixed;
		height: 100%;
		width: 100%;
		left: 0;
		top: 100%;
		right: 0;
		bottom: 0;
		z-index: 112;
		visibility: hidden;
	}
	
	.ts-3 {
		-webkit-transition: all .3s;
		-moz-transition: all .3s;
		-o-transition: all .3s;
		transition: all .3s;
	}
	
	.close-search {
		height: 4.6rem;
		line-height: 4.6rem;
		color: #777;
		position: absolute;
		bottom: 0;
		font-size: 1.7rem;
		text-align: center;
		width: 100%;
	}
	
	.user-center {
		font-size: 1.6rem;
	}
	
	.margin-lr {
		margin: 0 1.3rem;
	}
	
	.text-all {
		border-bottom: 1px solid #F6F6F9;
		padding: .1rem 0;
		width: 100%;
		overflow: hidden;
	}
	
	.dis-box {
		display: -webkit-box;
		display: -moz-box;
		display: -ms-box;
		display: box;
		position: relative;
	}
	
	.text-all label {
		font-size: 1.05rem;
		display: block;
		height: 3rem;
		line-height: 3rem;
		margin-right: 0.8rem;
		vertical-align: middle;
	}
	
	.input-text {
		position: relative;
	}
	
	.box-flex {
		-webkit-box-flex: 1;
		-moz-box-flex: 1;
		-ms-box-flex: 1;
		box-flex: 1;
		display: block;
		width: 100%;
	}
	
	.input-text input {
		border: 0;
		height: 3rem;
		line-height: 2rem;
		padding: .5rem 0;
		box-sizing: border-box;
		width: 100%;
		color: #555;
		font-size: 1.05rem;
		padding-right: 3rem;
	}
	
	input:required,
	input:valid,
	input:invalid {
		border: 0 none;
		outline: 0 none;
		-webkit-box-shadow: none;
		-moz-box-shadow: none;
		-ms-box-shadow: none;
		-o-box-shadow: none;
		box-shadow: none;
		ã€€-webkit-appearance: none;
		-webkit-tap-highlight-color: rgba(255, 255, 255, 0);
	}
	
	.is-null {
		font-size: 2.1rem;
		color: #ddd;
		top: 50%;
		transition: all 0.2s;
		margin-top: -1.2rem;
		z-index: 10;
		position: absolute;
		right: 0.2rem;
		visibility: hidden;
		opacity: 0;
		-webkit-transition: all 0.1s;
		-moz-transition: all 0.1s;
		-o-transition: all 0.1s;
		transition: all 0.1s;
	}
	
	.icon-guanbi:before {
		content: "\e630";
	}
	
	.user-center .t-remark {
		margin-top: 1.2rem;
	}
	
	.t-remark,
	.t-remark:link,
	.t-remark a:link,
	.t-remark a {
		color: #777;
		font-size: 1.05rem;
	}
	
	.user-center .btn-submit {
		margin-top: 1.2rem;
	}
	
	.btn-submit {
		background: #ff4146;
		border: 1px solid #ff4146;
	}
	
	.btn-submit,
	.btn-weixin,
	.btn-submit1,
	.btn-disab,
	.btn-cart,
	.btn-reset,
	.btn-default,
	.btn-alipay,
	.btn-wechat {
		font-size: 1.05rem;
		color: #fff;
		border: 0;
		text-align: center;
		padding: .64rem 0;
		border-radius: 4px;
		width: 100%;
	}
	
	a {
		text-decoration: none;
		color: #333;
		outline: 0;
	}
	
	.forget-pwd {
		float: right;
		margin-top: 1.2rem;
		font-size: .96rem;
		color: #a0a0a0;
	}
	/*.eye{
		position: absolute;
		right:60px;
		top: 9px;
	}
	.eye img{
		width: 30px;
		height: 30px;
	}*/
	
	.input-text .eyeOpen {
		position: absolute;
		right: 60px;
		top: 9px;
	}
</style>