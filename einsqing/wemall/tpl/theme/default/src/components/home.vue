<template>
	<div id="app">
		<v-header @selectedMenu="childEventHandler"></v-header>

		<div class="transform-wrap" style="min-height: 422px; padding-top: 58px;">
			<div class="user-no-focus-height"></div>
			<div class="product-voucher"></div>
			<div>
				<ul class="product-box">
					<li data-model="0" label-cate="1" label-id="2" v-for="item in product" v-show="item.category_id == selectedMenu">
						<div class="product-item-box">
							<div class="product-item-inner">
								<div class="product-item-header">
									<div class="lazy-wrap font-zero">
										<router-link :to="{path:'/productDetail',query:{id:item.id}}">
											<img v-lazy="getFile(item.file)" width="100%" heigh="auto">
										</router-link>
									</div>
								</div>
								<div class="product-item-main">
									<div class="product-item-content">
										<h3>
                      						<a href="#/product_view/normal/9150" title="智利XJ级车厘子">{{item.name}}</a>
                    					</h3>
										<p class="product-item-subtotal-box product-item-subtotal-box-2" v-show='item.num>0'>
											<i>x</i>
											<em class="num">{{item.num}}</em>
											<i>小计¥</i>
											<em>{{ (item.price*item.num).toFixed(2)}}</em>
										</p>
										<p class="product-itme-price-box product-itme-price-box-2" v-show="!item.num">
											<span class="price"><i>¥</i>{{ item.price }}</span>
											<span class="unit"> /{{ item.spec }}</span>
											<span class="supermarket">¥{{ item.old_price }}</span>
										</p>
									</div>
									<div class="product-opera-wrap">
										<div class="product-opera-inner">
											<div class="product-buy-oprea" v-show='item.num>0'>
												<span class="btn active-btn">
                           							<i class="icon-minus" style="line-height: 39px;" @click='minus(item)'></i>
                         						</span>
												<span class="btn active-btn">
                        							<i class="icon-plus" style="line-height: 39px;" @click='add(item)'></i>
                        						</span>
											</div>
											<span class="btn active-btn btn-mai" @click="addCart(item,$event)" v-show='!item.num'>买</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</li>
				</ul>
			</div>
		</div>
		<!--<div class="cart" :class='{move:ismove}'>
	    	<img src="../assets/img/home_car.png"/>
	    </div>-->

		<v-footer></v-footer>
	</div>
</template>

<script>
	import Vue from 'vue'
	import header from '../components/header'
	import footer from '../components/footer'
	import fetch from "./../fetch";
	import { getFile } from "./../util";
	import { Toast, Indicator } from 'mint-ui';

	export default {
		name: 'app',
		data() {
			return {
				selectedMenu: 0,
				docState: 'saved',
				product: [],
				cartData: [],
				ismove: false,
				singlePrice: '',
				popup:{}
			}
		},
		components: {
			'v-header': header,
			'v-footer': footer,
		},
		created() {
			Indicator.open({text:'加载中...'});
			this.cartData = this.$localStorage.get('cartData');
			this.product = this.$localStorage.get('product');
			this.updateProduct();
			fetch('/api/product').then((res) => {
				if(res) {
					Indicator.close();
					this.product = res.data.data.product;

					// 缓存商品
					this.$localStorage.set('product', this.product);

					this.updateProduct();
				}
			});
		},
		methods: {
			childEventHandler(id) {
				this.selectedMenu = id;
			},
			updateProduct() {
				// 更新视图
				this.product = this.$localStorage.get('product');
				let cartData = this.$localStorage.get('cartData');
				for(let key in this.product) {
					for(let keyIn in cartData) {
						if(this.product[key].id == cartData[keyIn].id) {
							this.$set(this.product[key], Object.assign(this.product[key], { num: cartData[keyIn].num }))

							if(cartData[keyIn].num == 0) {
								cartData.splice(keyIn, 1)
								this.$localStorage.set('cartData', cartData);
							}
						}
					}
				}
			},
			addCart(item,e) {
				let cartData = this.$localStorage.get('cartData');
				if(!item.num) {
					Vue.set(item, 'num', 1)
				}
				cartData.push({
					id: item.id,
					name: item.name,
					price: item.price,
					num: 1,
					file: item.file
				});

				this.$localStorage.set('cartData', cartData);

				// 更新footer购物车数量
				this.$bus.emit('updateCart', true);
				this.updateProduct();
				//动画
				//this.ismove = true;
				//setTimeout(()=>{
				//	this.ismove = false;
				//},1500)
//				let rect = e.target.getBoundingClientRect();
//			    this.popup.x = rect.left;
//			    this.popup.y = rect.top;
			},
			add(item) { //增加商品数量
				let cartData = this.$localStorage.get('cartData');
				for(let key in cartData) {
					if(cartData[key].id == item.id) {
						cartData[key].num++;
					}
				}

				this.$localStorage.set('cartData', cartData);
				// 更新footer购物车数量
				this.$bus.emit('updateCart', true);
				this.updateProduct();

			},
			minus(item) { //减少商品数量
				let cartData = this.$localStorage.get('cartData');
				for(let key in cartData) {
					if(cartData[key].id == item.id) {
						cartData[key].num--;

						if(cartData[key].num == 0) {
							cartData[key].num = 0
							//cartData.splice(key,1)先更新视图再删
						}
					}
				}

				this.$localStorage.set('cartData', cartData);
				// 更新footer购物车数量
				this.$bus.emit('updateCart', true);
				this.updateProduct();
			},
			getFile
		}
	}
</script>

<style>
	.cart {
		width: 30px;
		height: 30px;
		position: fixed;
		z-index: 10000;
		bottom: -60px;
		left: 50%;
		margin-left: -15px;
		border: 4px solid #ff4146;
		border-radius: 50%;
		background: #fff;
		padding: 5px;
		box-shadow: 0 0 2px 5px #ffb6c1;
	}
	
	.cart img {
		width: 100%;
	}
	
	.move {
		animation: mymove 1.5s;
	}
	
	@keyframes mymove {
		0 {
			bottom: -60px;
		}
		20% {
			bottom: 65px;
		}
		80% {
			bottom: 65px;
		}
		100% {
			bottom: -60px;
		}
	}
</style>