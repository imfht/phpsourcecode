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
											<a id="back-btn" href="#/user" title="返回" data-status="1">
												<i class="icon-angle-left icon-2x ft-color"></i>
											</a>
										</td>
										<td id="header-common-main" class="tc">
											<div class="exchange-canuser">可用积分&nbsp;:&nbsp;<span id="user_score">{{user.score}}</span></div>
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
		<div id="view_page" class="transform-wrap" style="background-color: rgb(243, 243, 243); padding-top: 50px; min-height: 422px; ">
			<div id="user-no-focus-height" class="user-no-focus-height"></div>
			<div class="exchange-wrap">
				<div class="exchange-header">
					<ul class="exchange-menu">
						<li id="integral-exchange-tab" :class="{'current':scoreExchange}" @click='scoreexchange'>积分兑换</li>
						<li id="exchange-prize-tab" :class="{'current':scoreExchanged}" @click='scoreexchanged'>兑换历史</li>
					</ul>
				</div>
				<div class="exchange-main">
					<div class="integral-exchange" id="integral-exchange" v-show='scoreExchange'>
						<table>
							<tbody>
								<tr v-for='item in couponList'>
									<td class='gift-prize'><img src="../assets/img/iconfont-daijinjuan.png" /></td>
									<td class='gift-prize'>
										<dl>
											<dt>{{item.price}}元代金券</dt>
											<dd class='prize-num'>{{item.score}}积分</dd>
											<dd class='prize-type'>代金券兑换</dd>
										</dl>
									</td>
									<td class='gift-btn'>
										<a @click='exchange(item)'>兑换</a>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="exchange-prize" id="exchange-prize" style="display: block;" v-show='scoreExchanged'>
						<table>
							<tbody>
								<tr v-for='item in couponLists'>
									<td class='gift-prize'><img src="../assets/img/iconfont-daijinjuan.png" /></td>
									<td class='gift-prize'>
										<dl>
											<dt>{{item.price}}元代金券</dt>
											<dd class='prize-num'>{{item.menu.score}}积分</dd>
											<dd class='prize-type'>代金券兑换</dd>
										</dl>
									</td>
									<td class='gift-btn'>
										<a></a>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="exchange-windows-wrap" id="exchange-windows-wrap" v-show='isExchange' style="display: block;">
			<div class="exchange-windows-inner" style="margin-top:50%;">
				<div>兑换<span id="exchange-gift-name"></span>将消耗你
					<span id="exchange-gift-score">{{score}}</span>积分，是否兑换
				</div>
				<div class="sure-box">
					<a id="exchange-o" class="no" title="否" @click='exchangeNo'>否</a>
					<a id="exchange-yes" data-id="" data-type="" class="yes" title="是" @click='exchangeYes'>是</a>
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
				couponList: [],
				user: [],
				scoreExchange: true,
				scoreExchanged: false,
				isExchange: false,
				score: 0,
				selectItem: [],
				couponLists: []
			}
		},
		created() {
			Indicator.open();
			fetch("/api/user").then((res) => {
				if(res) {
					this.user = res.data.data.user;
				}
			});

			//获取优惠券列表
			fetch('api/addons/couponList').then((res) => {
				if(res) {
					Indicator.close();
					this.couponList = res.data.data.coupon;
				}
			})
			//获取优惠券列表
			fetch('api/addons/myCoupon').then((res) => {
				if(res) {

					this.couponLists = res.data.data.coupon;
				}
			})
		},
		methods: {
			//积分兑换
			scoreexchange() {
				this.scoreExchange = true;
				this.scoreExchanged = false;
			},
			//兑换历史
			scoreexchanged() {
				this.scoreExchanged = true;
				this.scoreExchange = false;
			},
			//弹出框
			exchange(item) {
				this.isExchange = true;
				this.score = item.score;
				this.selectItem = item;
			},
			exchangeNo() {
				this.isExchange = false;
			},
			exchangeYes() {
				fetch('api/addons/couponChange', {
					id: this.selectItem.id
				}).then((res) => {
					if(res.data.code == 1) {
						this.isExchange = false;
						Toast(res.data.msg)
						//						this.couponLists.unshift(this.selectItem)
						fetch('api/addons/myCoupon').then((res) => {
							if(res) {
								this.couponLists = res.data.data.coupon;
							}
						})
						fetch("/api/user").then((res) => {
							if(res) {
								this.user = res.data.data.user;
								this.$localStorage.set('user', res.data.data.user);
							}
						});
					}
					if(res.data.code == 0) {
						Toast(res.data.msg)
						this.isExchange = false;
					}
				})
			}
		}
	}
</script>

<style>

</style>