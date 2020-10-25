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
											<ul class="order-menu-tabs">
												<li v-for="menu in menus">
													<span id="noreceive-btn" :class="[ selectedMenu == menu.id? 'ft-color && bd-color': '' ]" @click="selectMenu(menu.id)">{{ menu.name }}</span>
												</li>
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
		<div id="view_page" class="transform-wrap" style="padding-top: 50px; min-height: 422px; ">
			<div class="order-wrap">
				<div class="loading-wrap hide" id="order-loading" style="z-index: 9999999;display: none;">
					<img src="../assets/img/bingo_loading.gif" width="64" height="64" alt="缤哥正在努力加载中...">
					<p>宾哥正在努力加载中...</p>
				</div>
				<div class="tab-content" id="order_tabs">
					<div class="hide active" id="order-noreceive-list" style="display: block; ">
						<ul class="order-box">
							<li name="item-611302" class="model0" v-for='(item,index) in order' v-show='(unFinish&&item.status!=="已完成"&&item.status!=="已取消")||item.status==state'>
								<p class="order-num ft-color">订单编号：{{item.orderid}}</p>
								<div class="order-content">
									<div class="order-item-header">
										<p class="order-time">下单时间：{{item.created_at}}</p>
										<p class="order-time">支付方式：{{item.payment}}</p>
										<p class="order-time" v-if='item.stores.length==0'>接收方式：送货上门</p>
										<p class="order-time" v-else>接收方式：到店自提</p>
										<p class="order-time" v-show='item.stores.length!==0'>自取地址：{{item.stores.address}}</p>
										<p class="order-time" v-show='item.stores.length!==0'>联系电话：{{item.stores.phone}}</p>
										<p class="order-time" v-if='item.contact'>接收地址：{{item.contact.province}} {{item.contact.city}} {{item.contact.district}} {{item.contact.address}}</p>
										<p class="order-time" v-if='item.contact'>联系电话：{{item.contact.phone}}</p>
										<p class="order-time" v-show='item.coupon.price'>优惠券优惠：{{item.coupon.price}}元</p>
										<a class="order-tracking ft-color bd-style tc" title="订单跟踪">{{item.status}}</a>
									</div>
									<div class="order-item-main">
										<table class="order-item-tabel order-item-tabel-1702100404184" data-id="1702100404184">
											<tbody>
												<tr v-for='(food,index) in item.detail'>
													<td class="title">{{index+1}}.{{food.name}}</td>
													<td class="num"><em>X</em>{{food.num}}</td>
													<td class="subtotal ft-color">¥{{food.num*food.price}}</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="order-item-total">
										<table class="order-total-tabel">
											<tbody>
												<tr>
													<td class="delivery-fee">运费：{{fee}} 元</td>
													<td class="order-total tr">
														<p>总计：<span class="ft-color">{{item.totalprice}} 元</span></p>
														<input type="hidden" value="0.00">
														<input type="hidden" value="46.80">
													</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="order-item-footer">
										<table class="order-menu-tabel order-unfinished" v-show='item.status!=="已完成"'>
											<tbody>
												<tr>
													<td class="fix-btn">
														<a href="javascript:void(0);" class="order-fixed-info tc" v-show='item.pay_status=="已支付"&&item.status!=="已发货"'>已付款</a>
														<a href="javascript:void(0);" class="order-fixed-info tc" v-show='item.pay_status=="未支付"&&item.status!=="已发货"' @click='pay(item)'>未付款</a>
														<a href="javascript:void(0);" class="order-fixed-info tc" v-show='(item.status=="已发货"&&item.pay_status=="已支付")||(item.status=="已发货"&&item.payment=="货到付款")' @click='confirm(item)'>确认收货</a>
													</td>
													<td>
														<div id="go-to-play-611302">
															<a href="javascript:void(0);" class="cancel-order-btn tc fr style-color" @click="cancel(item,index)" v-show='item.status!=="已完成"'>取消订单</a>
														</div>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</li>
						</ul>
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
		name: 'hello',
		data() {
			return {
				menus: [{
						id: 1,
						name: "未收货订单",
					},
					{
						id: 2,
						name: "已收货订单",
					}
				],
				selectedMenu: 0,
				order: [],
				fee: 0,
				unFinish: true, //未收货
				state: ''
			}
		},
		created() {
			Indicator.open();
			this.selectedMenu = this.menus[0].id;
			//获取所有订单
			fetch('/api/order').then((res) => {
				if(res){
					this.order = res.data.data.order;
					Indicator.close();
				}
			});

			//费用
			let feeList = this.$localStorage.get('fee');
			feeList.forEach((val, index) => {
				this.fee += parseFloat(feeList[index].value);
			})
			
		},
		methods: {
			selectMenu(id) {
				this.selectedMenu = id;
				if(id == 1) {
					this.unFinish = true;
					this.state = '';
				}
				if(id == 2) {
					this.unFinish = false;
					this.state = '已完成';
				}
			},
			pay(item) {
				//微信支付
				if(item.payment == '微信支付') {
					fetch(`/api/pay/wxpay/${item.id}`).then((res) => {
						if(!res) {
							return;
						}
						WeixinJSBridge.invoke('getBrandWCPayRequest', res.data.data.jsApiParameters, (res) => {
							if(res.err_msg == "get_brand_wcpay_request:ok") {
								Toast("支付成功");
								// 这里可以跳转到订单完成页面向用户展示
								this.$router.go(0)
							} else {
								Toast("支付失败，请重试");
							}
						});
					});
				}
				//支付宝
				if(item.payment == '支付宝') {
					fetch(`/api/pay/alipay/${item.id}`).then((res) => {
						if(!res) {
							return;
						}
						if(res.data.code) {
							location.href = res.data.data.url;
						} else {
							Toast(res.data.msg);
						}
					});
				}
			},
			//取消订单
			cancel(item, index) {
				fetch('/api/order', {
					id: item.id,
					status: -1
				}).then((res) => {
					if(res.data.code == 1) {
						item.status = '已取消';
					}
				})
			},
			//确认收货
			confirm(item) {
				fetch('/api/order/confirmReceipt/' + item.id).then((res) => {
					if(res.data.code == 1) {
						Toast(res.data.msg);
						item.status = '已完成';
					}
				})
			}
		}
	}
</script>

<style>

</style>