<template>
	<div id="app_content" class="snap-content" style="padding-bottom: 50px; height: auto;" v-show='isshowVoucher'>
		<div id="cart_voucher" style='margin-bottom:0;'>
			<div class="my-coupons-wrap" style="display: block">
				<div class="my-coupons-top">
					<h1>
            			<a @click='backCart'></a>选择代金券
          			</h1>
					<div class="my-coupons-top-table-wrap">
						<div>
							<table>
								<tbody>
									<tr>
										<td>
											<input type="text" id="voucher_code" placeholder="请输入代金券码/礼券码" v-model='voucherCode'>
										</td>
										<td class="secord-child">
											<a href="javascript:void(0);" title="确定" @click='addCoupon'>确定</a>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>

					<div class="voucher-list-main" id="voucher-expire" style="display: block; width:125%;overflow-y:scroll;margin-top:10px;position:fixed;bottom:30px;top:107px;right:0;left:0;z-index: 1000;background: #f3f3f3;">
						<div class="" style='width:80%;'>
							<input type="hidden" value="">
							<ul>
								<li v-for='item in myCoupon'>
									<div class="voucher-item-box" style="margin:0;" @click='selectVoucher(item)'>
										<div class="voucher-cover">
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
							<div class="tc">本次购买可以使用的代金券</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import fetch from "./../fetch";
	import { Toast } from 'mint-ui';

	export default {
		data() {
			return {
				myCoupon: [],
				voucherCode: '', //代金券码
				isshowVoucher: true
			}
		},
		created() {
			fetch('/api/addons/myCoupon').then((res) => {
				if(res.data.code == 1) {
					this.myCoupon = res.data.data.coupon;
				}
			});

			this.$bus.on('showVoucher', this.showVoucher);
		},
		methods: {
			showVoucher() {
				this.isshowVoucher = true;
			},
			backCart() {
				this.isshowVoucher = false;
				this.$router.push('/cart')
			},
			//添加优惠券
			addCoupon() {
				if(!this.voucherCode) {
					Toast('请输入代金券码/礼券码')
					return;
				}
				fetch('api/addons/addCoupon', {
					code: this.voucherCode
				}).then((res) => {
					if(res.data.code == 1) {
						this.voucherCode = '';
						this.myCoupon.unshift(res.data.data.coupon);
					}
					if(res.data.code == 0) {
						this.voucherCode = '';
						Toast(res.data.msg)
					}
				})
			},
			//选择优惠券
			selectVoucher(item) {
				this.$emit('selectedVouchers', item)
				this.isshowVoucher = false;

			}
		}
	}
</script>

<style scoped>
	#app_content {
		position: fixed;
		left: 0;
		top: 0;
		right: 0;
		bottom: 0;
		z-index: 1000;
		background: #f3f3f3;
	}
</style>