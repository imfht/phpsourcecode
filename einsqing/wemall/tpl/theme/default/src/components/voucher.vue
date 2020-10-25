<template>
	<div id="app_content" class="snap-content">
		<div id="app-header" class="app-header box-sizing fixed-wrap" style="position: fixed; ">
			<div class="app-header-inner">
				<div id="app-header-main" class="app-header-main">
					<div class="header-common">
						<div class="header-common-inner">
							<table border="0" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="active-btn tl">
											<a id="back-btn" href="#/user" title="返回" data-status="1"><i class="icon-angle-left icon-2x ft-color"></i></a>
										</td>
										<td id="header-common-main" class="tc">
											<ul class="voucher-menu-tabs">
												<li @click="unused()"><span id="unused" class="bd-color" :class='{"ft-color":isShow}'>未使用代金券</span></li>
												<li @click="expire()"><span id="expire" :class='{"ft-color":!isShow}'>已过期代金券</span></li>
											</ul>
										</td>
										<td class="active-btn">
											<router-link to="/user">
												<img src="../assets/img/account.png" width="30">
											</router-link>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="view_page" class="transform-wrap" style=" min-height: 422px; ">
			<div class="voucher-list-wrap">
				<div class="voucher-list-main" id="voucher-unused" style="display: block;">
					<div class="common-no-result-wrap" v-show='isShow'>
						<div class="font-size" v-show='couponList.length==0'>
							<p class="tc">你没有未使用代金券哦</p>
						</div>
						<ul v-show='couponList.length!==0' style='margin-top: 18%;'>
							<li v-for='item in couponList'>
								<div class="voucher-item-box" style="margin:0;">
									<div class="voucher-cover" style='margin-bottom:0;'>
										<img src="../assets/img/iconfont-daijinjuan.png" />
									</div>
									<div class="user-voucher-info">
										<h3>
			            					<p>{{item.price}}元代金券</p>
			            					<p>到期：{{item.last_time}}</p>
			            					<p>规则：消费即可 抵扣{{item.price}}元</p>
			            				</h3>
									</div>
								</div>
							</li>
						</ul>
					</div>
					<div class="voucher-list-main" id="voucher-expire" style="display: block;" v-show='!isShow'>
						<div class="common-no-result-wrap">
							<div class="font-size" v-show='expireList.length==0'>
								<p class="tc">你没有已过期代金券哦</p>
							</div>
							<ul v-show='expireList.length!==0'>
								<li v-for='item in expireList'>
									<div class="voucher-item-box" style="margin:0;padding-bottom:0;">
										<div class="voucher-cover" style='margin-bottom:3%;'>
											<img src="../assets/img/iconfont-daijinjuan.png" />
										</div>
										<div class="user-voucher-info">
											<h3>
	            					<p>{{item.price}}元代金券</p>
	            					<p>到期：{{item.last_time}}</p>
	            					<p>规则：消费即可 抵扣{{item.price}}元</p>
	            				</h3>
										</div>
									</div>
								</li>
							</ul>
							<div id="voucher-expire-tips" class="tc" v-show='expireList.length!==0'>以上代金券为3个月内过期的代金券</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import fetch from './../fetch';
	import { Toast, Indicator } from 'mint-ui';

	export default {
		data() {
			return {
				isShow: true,
				couponList: [],
				expireList: []
			}
		},
		created() {
			Indicator.open();
			//所有的代金券
			fetch('api/addons/myCoupon').then((res) => {
				if(res) {
					this.couponList = res.data.data.coupon;
				}
			})
			//过期的
			fetch('api/addons/myCoupon', {
				status: -1
			}).then((res) => {
				if(res) {
					Indicator.close()
					this.expireList = res.data.data.coupon;
				}
			})
		},
		methods: {
			unused() {
				this.isShow = true;
			},
			expire() {
				this.isShow = false;
			}
		}
	}
</script>

<style>

</style>