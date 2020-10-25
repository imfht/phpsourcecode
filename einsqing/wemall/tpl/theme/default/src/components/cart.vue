<template>
	<div class="snap-content" style="padding-bottom: 50px; height: auto; ">
		<div class="cart-wrap" style="padding-bottom: 60px; background-color: rgb(245, 245, 245);">
			<div class="scrollable-content">
				<div class="list-group">
					<div class="mask" style="display:none;">
						<img src=""> <br>正在跳转到支付页面，请稍候...
					</div>
					<div style="-webkit-transform: translate3d(0,0,0);transform: translate3d(0,0,0)">
						<div id="cart_min_height" style="min-height: 572px; ">
							<div class="cart-settlement-wrap">
								<div class="cart-settlement-inner">
									<h1 class="user-shopping-cart">已购买商品
                    					<span class="user-shopping-edit" id="user-shopping-edit" @click="cartShopEdit" v-if='!cartShop'>编辑</span>
                    					<span class="user-shopping-edit" id="user-shopping-edit" @click="cartShopEdit" v-else>完成</span>
                  					</h1>
									<ul class="shopping-box">
										<li v-for='food in cartData' v-show='food.num>0'>
											<div class="shopping-item-box">
												<div class="img-box loading-box fl" style="min-height:50px;width: 50px;">
													<img :src="getFile(food.file)" style="width:50px;height: 50px;" />
												</div>
												<div class="shopping-operate">
													<p class="shopping-title">{{food.name}}</p>
													<p class="price">{{food.price}}</p>
												</div>
												<div class="editCarShop">
													<div style="margin-top:28.5px;">
														<div class="subtotal_num_box">
															<span class="subtotal_num_14155" v-show='!cartShop'>{{food.num}}</span>
														</div>
														<table class="num editCarShop_num" border="0" cellspacing="0" cellpadding="0" v-show='cartShop'>
															<tbody>
																<tr>
																	<td style="padding-left:0;width:40px;max-width:40px;min-width:40px;">
																		<a class="minus" @click='minus(food)'></a>
																	</td>
																	<td><span class="subtotal_num_14155">{{food.num}}</span></td>
																	<td>
																		<a class="plus" @click='add(food)'></a>
																	</td>
																</tr>
															</tbody>
														</table>
													</div>
												</div>
											</div>
										</li>
									</ul>
									<ul class="shopping-box cart-special-fruit-box" style="border: 0px; display: none; ">
									</ul>
									<div class="shopping-bag-select-btn shopping-bag-select-activeing" style="display: none; ">
										<img src="../assets/img/shopping-bag.png" width="20" height="20">
										<span>请选择您喜欢的包装</span>
									</div>
									<div class="user-join-actives hide" style="display: none; ">
										<h3>现有以下活动（<span>灰色表示未参与</span>）</h3>
										<ul>
											<li>
												<span class="first-order-current hide" data-info="新用户首单免运费，已经为您减免2.00元" style="display: block; ">满20元减2元</span>
											</li>
										</ul>
									</div>
									<div class="shopping-info">
										<table class="pay-info-wrap" border="0" cellspacing="0" cellpadding="0">
											<tbody>
												<tr>
													<td style="width:130px;">总计共<span id="all_num">{{cartNum}}</span>份商品</td>
													<td>
														<table class="pay-info fr" border="0" cellspacing="0" cellpadding="0">
															<tbody>
																<!--<tr v-show='cartTotal>20'>
																	<td>优惠：</td>
																	<td><span>¥ </span><span>2.00</span></td>
																</tr>-->
																<tr>
																	<td>运费：</td>
																	<td>
																		<span>¥ </span><span>{{fee}}</span>
																	</td>
																</tr>
																<tr>
																	<td>总价：</td>
																	<td><span>¥ </span><span>{{cartTotal}}</span></td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="add-price add-price-menu add-price-bottom add-price-white add-price-model-1">
								<table class="add-price-parent" border="0" cellspacing="0" cellpadding="0">
									<tbody></tbody>
								</table>
							</div>
							<div class="pay-mothed shipping-method">
								<div class="element-inner" style="padding:4px 0;">
									<table style="font-size: 14px;">
										<tbody>
											<tr>
												<td>送货 <br>方式</td>
												<td class="play-box">
													<ul class="select-send-method">
														<li class="hide" style="display: list-item; ">
															<label for="send_type1">送货上门</label>
															<input type="radio" name="send_type" id="send_type1" value="1" checked="" @click='sendType1'>
															<label class="round" :class="{ current: isActive1 }" for="send_type1"></label>
														</li>
														<li id="ziti_btn" class="hide" style="display: list-item; ">
															<label for="send_type2">到店自提</label>
															<input type="radio" name="send_type" id="send_type2" value="2" @click='sendType2'>
															<label class="round" for="send_type2" :class="{ current: isActive2 }"></label>
														</li>
													</ul>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="cart-settlement-wrap address-manage" id="address-manage" style="padding:3px 0;" v-show='isActive1'>
								<table class="cart-settlement-inner user-info">
									<tbody style="font-size: 14px;">
										<tr>
											<td>收货 <br>信息</td>
											<td class="user-shopping-cart" style="font-size: 0.9em;color:#777;padding:4px 12px 4px 0;">
												<a href="#/address">
													<p v-show='!addressId' class="new-address" id="new-address" style="padding: 11px 0px 10px; font-size: 14px;">请选择收货地址</p>
													<div class="has-default-address" id="has-default-address" v-if='addressId'>
														<input id="contact_id" type="hidden" value="13">
														<p>
															<span id="cname">{{defaultAdd.name}}</span>&nbsp; &nbsp;&nbsp;
															<span id="cphone">{{defaultAdd.phone}}</span>
														</p>
														<p style="color:black;">
															<span id=""></span>
															<span id="caddress" v-if='defaultAdd.province'>{{defaultAdd.province.name}} </span>
															<span id="caddress" v-if='defaultAdd.city'>{{defaultAdd.city.name}} </span>
															<span id="caddress" v-if='defaultAdd.district'>{{defaultAdd.district.name}} </span>
															<span id="caddress">{{defaultAdd.address}}</span>
														</p>
													</div>
												</a>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="pay-mothed " id="ziti_addr_div" v-show='isActive2'>
								<div class="element-inner" style="padding:4px 0;line-height: 42px;">
									<table style="font-size: 14px;height:42px;">
										<tbody>
											<tr>
												<td>自提 <br>地址</td>
												<td class="play-box" style="font-size: 0.9em;color:#777;padding:4px 12px 4px 0;">
													<!--<p>-->
													<select style="width:100%;border: 1px solid #fff;height:34px;" id="point" name="point" class="hat_select" v-model='storeId'>
														<option :value="store.id" v-for='store in stores' @click='store(store)'>{{store.address}}</option>

													</select>
													<!--</p>-->
												</td>
												<td>
													<img src="../assets/img/icon_position.png?v=1" width="25" height="25" alt="自提" style='vertical-align:middle;'>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="cart-settlement-wrap address-manage" id="user_point" style="padding:3px 0;" v-show='isActive2'>
								<table class="cart-settlement-inner user-info" style="width:100%;">
									<tbody>
										<tr>
											<td>联系 <br>电话</td>
											<td class="user-shopping-cart" style="font-size: 0.9em;color:#777;padding:4px 12px 4px 0;">
												<p>
													<input type="text" name="tel" id="tel" placeholder="请输入手机号（重要）" value="" required="" style="border: 1px solid #fff;width:91%;height:34px;" v-model='phone' maxlength="11">
												</p>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="coupons-info" style="position:relative;">
								<div class="element-inner" id="coupons-info-inner">
									<table style="font-size: 14px;">
										<tbody>
											<tr>
												<td>用代 <br>金券</td>
												<td>
													<router-link to="/addVoucher">
														<p @click='selectedVoucher'>
															<span v-if='selectVoucher'>{{selectVoucher.price}}元代金券</span>
															<span v-else id="user_voucher_title" style="color: #333">请选择代金券</span>
														</p>
													</router-link>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="goods-time" style="position:relative;">
								<div class="element-inner" id="goods-time-inner">
									<table style="font-size: 14px;">
										<tbody>
											<tr>
												<td><span id="send_name">收货<br>时间</span></td>
												<td>
													<p>
														<select style="height: 24px; width:90%;border: 1px solid #fff;" id="deliveryTime" name="deliveryTime" class="hat_select" v-model="selectedDeliveryTime">
															<option :value="item" v-for="(item, index) in deliveryTime">{{item}}</option>

														</select>
													</p>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="pay-mothed" id="pay-mothed" style="position:relative;">
								<div class="element-inner" id="goods-time-inner2" style="padding:3px 0;height: 50px;">
									<table style="font-size: 14px;height: 50px;">
										<tbody>
											<tr>
												<td>付款 <br>方式</td>
												<td>
													<p>
														<select style="width:90%;border: 1px solid #fff;height:34px;" id="paymento" name="pay_type" class="hat_select" v-model='selectPayment'>
															<option :value="pay.id" v-for='(pay,index) in payment' @click='paymentWay(pay)'>{{pay.name}}</option>
														</select>
													</p>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="remark-box">
								<div class="element-inner" id="remark-box-inner">
									<table style="font-size: 14px;">
										<tbody>
											<tr>
												<td>备注 <br>信息</td>
												<td>
													<p>
														<span class="td_right">
                            <input type="text" name="note" id="note" placeholder="买家留言" style="width: 90%;height: 34px;border: 1px solid #fff;" v-model="remark">
                          </span>
													</p>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="navbar navbar-app cart-footer" style="position: fixed; bottom: 0px; left: 0px; width: 100%; height: auto; background-color: rgb(255, 255, 255); border-top-width: 1px; border-top-style: solid; border-top-color: #ff4146; ">
			<a href="#/index" class="cart_returnBtn"><img src="../assets/img/arrow_b.png">&nbsp;&nbsp;返回购物</a>
			<a class="order_submitBtn" @click='orderSubmit'>提交订单&nbsp;
				<img src="../assets/img/arrow_w.png">
			</a>
		</div>
		<router-view @selectedVouchers='voucher'></router-view>
	</div>
</template>

<script>
	import fetch from "./../fetch";
	import { getFile } from "./../util";
	import { Toast, Indicator } from 'mint-ui';
	export default {
		data() {
			return {
				cartShop: false,
				isActive1: true,
				isActive2: false,
				cartData: [],
				cartNum: 0, //总数
				cartTotal: 0, //总钱
				feeList: [], //费用
				fee: 0,
				payment: [], //支付方式
				selectPayment: 0,
				defaultAdd: [],
				deliveryTime: [], //配送时间
				selectedDeliveryTime: '',
				remark: '', //备注
				stores: [], //门店
				storeId: 0,
				phone: '',
				selectVoucher: '', //选择代金券
				couponId: 0,
				addressId: '',
				paymentName:''
			}
		},
		created() {
			//获取费用
			fetch('api/tpl/fee').then((res) => {
				if(res) {
					this.feeList = res.data.data.feeList;
					this.$localStorage.set('fee', this.feeList);
				}
			});
			this.updateCart();
			//获取默认地址
			if(this.$localStorage.get('defaultAdd')) {
				this.addressId = this.$localStorage.get('defaultAdd').id;
			}
			if(this.$route.query.address) {
				this.addressId = this.$route.query.address.id;
			}
			if(this.addressId) {
				//获取收货人列表
				fetch("/api/contact/" + this.addressId).then((res) => {
					if(res.data.code == 1) {
						this.defaultAdd = res.data.data.contact;
					}
				})
			}

			//获取支付方式
			fetch('/api/payment').then((res) => {
				if(res) {
					this.payment = res.data.data.payment;
				}
			});
			//			获取配送时间
			fetch('/api/addons/deliveryTime').then((res) => {
				if(res) {
					this.deliveryTime = res.data.data.delivery_time;
					this.selectedDeliveryTime = this.deliveryTime[0];
				}
			});
			//			到店自提
			fetch('/api/addons/stores').then((res) => {
				if(res) {
					this.stores = res.data.data.stores;
					this.storeId = this.stores[0].id;
				}

			});
		},
		methods: {
			getFile,
			cartShopEdit() {
				this.cartShop = !this.cartShop;
			},
			add(food) { //增加商品数量
				for(let key in this.cartData) {
					if(this.cartData[key].id == food.id) {
						this.cartData[key].num++;
					}
				}
				this.$localStorage.set('cartData', this.cartData);
				this.updateCart();
			},
			minus(food) { //减少商品数量
				for(let key in this.cartData) {
					if(this.cartData[key].id == food.id) {
						this.cartData[key].num--;
						if(this.cartData[key].num == 0) {
							this.cartData.splice(key, 1);
							if(this.cartData.length == 0) {
								this.$router.push('/home')
							}

						}
					}
				}

				this.$localStorage.set('cartData', this.cartData);
				this.updateCart();
			},
			updateCart() {
				this.cartNum = 0;
				this.cartTotal = 0;
				this.fee = 0;
				this.cartData = this.$localStorage.get('cartData');
				this.feeList = this.$localStorage.get('fee');

				this.cartData.forEach((val, index) => {
					this.cartNum += parseInt(this.cartData[index].num);
					this.cartTotal += parseInt(this.cartData[index].num) * parseFloat(this.cartData[index].price);
				})
				//费用
				this.feeList.forEach((val, index) => {
					this.fee += parseFloat(this.feeList[index].value);
				})
				this.cartTotal += this.fee;
				this.cartTotal = this.cartTotal.toFixed(2)

				// 更新nav购物车数量
				this.$bus.emit('updateCart', true);
			},
			sendType1() {
				if(this.isActive1) {
					return;
				}
				this.isActive1 = !this.isActive1;
				this.isActive2 = !this.isActive2;
			},
			sendType2() {
				if(this.isActive2) {
					return;
				}
				this.isActive2 = !this.isActive2;
				this.isActive1 = !this.isActive1;
			},
			paymentWay(pay) {
				this.selectPayment = pay.id;
				
			},
			store(store) {
				this.storeId = store.id;
			},
			selectedVoucher() {
				this.$bus.emit('showVoucher', true);
			},
			//选择代金券
			voucher(item) {
				this.selectVoucher = item;

			},
			//提交订单
			orderSubmit() {
				if(this.isActive1) {
					this.storeId = ''
					if(!this.defaultAdd.id) {
						Toast('请选择地址');
						return;
					}
				}
				//到店自提
				if(this.isActive2) {
					this.defaultAdd.id = ''
					if(!this.phone) {
						Toast('请输入手机号')
						return;
					}
					if(!(/^1[358]\d{9}$/.test(this.phone))) {
						Toast('手机号输入有误');
						return;
					}
				}

				//代金券
				if(this.selectVoucher) {
					this.couponId = this.selectVoucher.id;
				}
				const balance = this.$localStorage.get('user').money
				if(this.paymentName == 'balance') {
					if(this.cartTotal > balance) {
						//余额不够不能支付
						Toast('你的余额不足');
						return false;
					}
				}
				//费用
				let ids = ''
				for(let i = 0; i < this.feeList.length; i++) {
					if(i < this.feeList.length - 1) {
						ids += this.feeList[i].id + ",";
					} else {
						ids += this.feeList[i].id;
					}

				}
				for(let key in this.payment){
					if(this.payment[key].id==this.selectPayment){
						this.paymentName = this.payment[key].type;
					}
				}
				Indicator.open();
				fetch('/api/order', {
					token: this.$localStorage.get('token'),
					cartData: this.$localStorage.get('cartData'),
					payment_id: this.selectPayment,
					delivery_time: this.selectedDeliveryTime,
					contact_id: this.defaultAdd.id,
					remark: this.remark,
					fee_id: ids,
					store_address: this.storeId,
					phone: this.phone,
					coupon_id: this.couponId
				}).then((res) => {
					if(res) {
						Indicator.close();
						if(res.data.code) {

							//清空购物车
							this.$localStorage.set('cartData', []);
							this.$bus.emit('updateCart', true);

							this.$router.push({ path: '/checkoutSuccess', query: { id: res.data.data.order.id } });
							//订单id
							const order_id = res.data.data.order.id;

							//微信支付
							if(this.paymentName == 'wxpay') {

								fetch(`/api/pay/wxpay/${order_id}`).then((res) => {
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
							//支付宝支付
							if(this.paymentName == 'alipay') {

								fetch(`/api/pay/alipay/${order_id}`).then((res) => {
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
							//余额支付
							if(this.paymentName == 'balance') {
								fetch(`/api/pay/balance/${order_id}`).then((res) => {
									if(!res) {
										return;
									}
									if(res.data.code) {
										//获取用户列表
										fetch("/api/user").then((res) => {
											if(res) {
												this.$localStorage.set('user', res.data.data.user);
											}
										});
									} else {
										Toast(res.data.msg);
									}
								});
							}
						} else {
							Toast(res.data.msg);
						}
					}

				})
			}
		}
	}
</script>

<style>

</style>