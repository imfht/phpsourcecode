<template>
	<div id="app_content" class="snap-content" style='padding-bottom:80px;'>
		<div style=" width: 100%; display: block;">
			<div class="snap-drawer snap-drawer-right" id="userBase">
				<div class="scrollable sidebar-scrollable hide" id="rightSidebar" style="display: block;">
					<div class="scrollable-content">
						<div id="menu-height-box" style="">
							<div class="user-account-header">
								<p>
									<span id="user-avater-box">
                						<img id="user-avater" v-lazy="getAvaterFile" width="80" height="80">
              						</span>
								</p>
								<h3>{{userInfo.username}}</h3>
								<p>积分:<span id="user_score_sum">{{userInfo.score}}</span></p>
								<router-link to="/home" class="icon-angle-left icon-2x close-sidebar active-btn" title="关闭右侧菜单"></router-link>
							</div>
							<div class="user-account-main">
								<ul>
									<li class="li-order">
										<a title="我的订单" href="#/myorder">
											我的订单
											<span class="order"></span>
											<em class="icon-angle-right"></em>
										</a>
									</li>
									<li class="li-coupons">
										<a title="我的优惠券" href="#/voucher">
											我的优惠券
											<span class="coupons"></span>
											<em class="icon-angle-right"></em>
										</a>
									</li>
									<li class="li-integral">
										<a href="#/exchange" title="我的积分中心">
											我的积分中心
											<span class="integral"></span>
											<em class="icon-angle-right"></em>
										</a>
									</li>
									<li class="li-recharge">
										<a href="#/recharge" title="我的充值">
											我的充值
											<span class="recharge"></span>
											<em class="icon-angle-right"></em>
										</a>
									</li>
									<li id="li-scan-code" class="li-scan-code">
										<a href="#/scanCourse" title="商品扫一扫">
											商品扫一扫
											<span class="scan-course"></span>
											<em class="icon-angle-right"></em>
										</a>
									</li>
								</ul>
							</div>
							<div class="user-account-footer">
								<a @click='help' title="使用帮用助">
									使用帮助
									<span class="help"></span>
								</a>
								<a @click="about" title="关于缤果">
									关于我们
									<span class="about"></span>
								</a>
							</div>
						</div>
						<div class="recharge-opera-box" v-show='showHelp||showAbout'>
							<div class="recharge-opera-wrap">
								<div class="recharge-opera-inner" style='padding:0;margin-top:50%;'>
									<div style='padding:20px;'>
										<h1 style='font-weight:600;margin-bottom: 10px;' v-show='showHelp'>使用帮助</h1>
										<h1 style='font-weight:600;margin-bottom: 10px;' v-show='showAbout'>关于我们</h1>
										<p style='text-align: center;font-size: 14px;color:#A6A6A6;' v-show='showHelp'>{{config.help}}</p>
										<p style='text-align: center;font-size: 14px;color:#A6A6A6;' v-show='showAbout'>{{config.about}}</p>
									</div>
									<div style='border-top:1px solid #e8e8e8;text-align: center;padding:8px 0;color:#19CF77;' @click='sure'>确定</div>
								</div>
								<div class="recharge-opera-background"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="btn">
			<button class="btn-quit" @click="quit">退出</button>
		</div>
	</div>
</template>

<script>
	import fetch from './../fetch';
	import { getFile } from "./../util";
	import defaultAvater from "./../assets/img/default_avater.png";
	
	export default {
		data() {
			return {
				userInfo: {},
				config:[],
				showHelp:false,
				showAbout:false
			}
		},
		created() {
			if(this.$localStorage.get('user')) {
				this.userInfo = this.$localStorage.get('user');
			}
			fetch("/api/user").then((res) => {
				if(res) {
					this.userInfo = res.data.data.user;
				}
			});
			//获取config
			fetch('/api/config').then((res)=>{
				if(res){
					this.config = res.data.data.config;
				}
			})
		},
		computed: {
			getAvaterFile() {
				if(this.userInfo.avater_id) {
					return getFile(this.userInfo.avater);
				} else {
					return defaultAvater;
				}
			}
		},
		methods: {
			//退出登录
			quit() {
				this.$router.push('/login');
				localStorage.token = "";
				localStorage.user = "";
			},
			//使用帮助
			help(){
				
				let reg = /[a-zA-Z]+:\/\/[^\s]*/;
				if(reg.test(this.config.help)){
					location.href = this.config.help;
				}else{
					this.showHelp = true;
				}
					
			},
			//关于我们
			about(){
				let reg = /[a-zA-Z]+:\/\/[^\s]*/;
				if(reg.test(this.config.about)){
					location.href = this.config.about;
				}else{
					this.showAbout = true;
				}
			},
			sure(){
				this.showHelp = false;
				this.showAbout = false;
			}
		}
	}
</script>

<style scoped>
	.btn {
		margin-top: 20px;
	}
	
	.btn-quit {
		font-size: 1.05rem;
		color: #fff;
		border: 0;
		text-align: center;
		padding: .64rem 0;
		border-radius: 4px;
		width: 90%;
		margin-left: 5%;
		background: #ff4146;
	}
</style>