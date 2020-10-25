<template>
  <div id="app_content" class="snap-content">
    <div id="view_page" class="transform-wrap bg-color" style="min-height: 422px; ">
      <div class="product-info-wrap">
        <div class="header-focus hide" id="header-focus"></div>
        <div class="product-info-header">
          <div class="font-zero">
          		<img :src='getFile(goodDetail.file)' width="100%"/>
          </div>
          	
          <a id="return-btn" class="return-btn icon-angle-left icon-2x" href="#/index" title="返回"></a>
        </div>
        <div class="product-info-main">
          <div id="product-info-param" class="product-info-param">
            <p><span class="price ft-color"><i>¥</i>
              <span class="new-price">{{goodDetail.price}}</span></span>
              <span class="supermarket"> 原价 ¥ <span class="old-price">{{goodDetail.old_price}}</span></span>
            </p>
            <p class="title">
              <span class="single-name">{{goodDetail.name}}</span>
            </p>
            <p class="subtitle">{{goodDetail.subname}}</p>
            <table>
              <tbody>
              <tr>
                <td>规格&nbsp;&nbsp;&nbsp;&nbsp;<label class="spec">{{goodDetail.spec}}</label></td>
                <td><span></span>原产地&nbsp;&nbsp;&nbsp;&nbsp;
                  <label class="p_address">{{goodDetail.address}}</label>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
          <div id="product-mask-line" style="width:100%; height: 10px; background: #F5F5F5; "></div>
          <div id="product-info-content" class="product-info-content newline">
            <div v-html='goodDetail.detail'></div>
          </div>
          <div class="product-info-footer">
            <div class="fruit_detail_price">
              <p class="today-price">原价：<label class="old-price">{{goodDetail.old_price}}</label>元 /
              	<label class="spec">{{goodDetail.unit}}</label>
              </p>
              <p class="binggou-price ft-color">现价：<label class="new-price">{{goodDetail.price}}</label>元 /
                <label class="spec">{{goodDetail.unit}}</label>
              </p>
            </div>
            <p class="ft-color"></p>
          </div>
        </div>
      </div>
    </div>
    <div id="app-footer">
      <div class="app-footer-inner">
        <div id="app-footer-main" class="app-header-main">
          <div id="footer" class="footer fixed-wrap">
            <div id="footer-product-info">
              <a id="footer-product-btn" class="ft-color bd-color" style="padding: 0px 6%; display: none;"
                 href="#/index" v-show='num==0'>去选购商品
              </a>
              <a id="footer-product-btn-2" class="ft-color bd-color" style="padding: 0px 6%;" href="#/cart" v-show='num>0'>去结算￥
                <span id="view_subtotal">{{(price*num).toFixed(2)}}</span>
              </a>
            </div>
          </div>
          <div id="retun-top-btn" class="icon-angle-up icon-2x"></div>
        </div>
      </div>
    </div>
    <div class="user-no-focus fixed-wrap" id="user-no-focus"> 你还没关注，关注有新用户专享优惠哦！
      <a
        href="http://mp.weixin.qq.com/s?__biz=MzA4NzAwMzAwOQ==&amp;mid=200863069&amp;idx=1&amp;sn=9d5d2b8884ab97d90c8ab6532df958db#rd"
        title="去关注">去关注</a>
    </div>
    <div id="animate">
      <img id="cart-img" src="" width="100%" title="产品缩略图">
      <div id="cart">
        <img src="../assets/img/cart.png" width="100%" title="购物车">
      </div>
    </div>
    <div id="video-box"></div>
    <div id="location-box"></div>
    <div id="product-info-opera" class="product-info-opera hide" style="display: block; ">
      <table border="0" cellspacing="0" cellpadding="0">
        <tbody>
        <tr>
          <td><span class="minus icon-minus style-color" :class="{ forbidden: isActive }" @click="minus"></span></td>
          <td><span class="num ft-color bd-color" id="view_buy_num">{{num}}</span></td>
          <td><span class="add icon-plus style-color" @click="add"></span></td>
        </tr>
        </tbody>
      </table>
    </div>
    <input type="hidden" id="page-mark" value="product-info"> <input type="hidden" id="product-type" value="normal">
  </div>
</template>

<script>
  import Vue from 'vue'
  import fetch from "./../fetch";
  import {getFile} from "./../util";

  export default {
    name: 'app',
    data () {
      return {
        isActive: true,
        goodDetail:{},
        cartData:[],
        num:0,
        price:''
      }
    },
    props: {
      product: {
        type: Object
      }
    },
    created(){
    	this.cartData = this.$localStorage.get('cartData');
      this.updateProduct();
    		//获取商品详情
					fetch("/api/product/"+this.$route.query.id).then((res)=>{
						this.goodDetail = res.data.data.product;
					})

					//单个商品数量,价格
					for (let key in this.cartData) {
              if (this.cartData[key].id == this.$route.query.id) {
                  this.num = this.cartData[key].num;
                  this.price = this.cartData[key].price;
                }
              }
					if(this.num>0){
						this.isActive = false;
					}
    },
    methods: {
			getFile,
			 add(){//增加商品数量
      	let cartData = this.$localStorage.get('cartData');
      	if(this.num==0){
							cartData.push({
                  id: this.goodDetail.id,
                  name: this.goodDetail.name,
                  price: this.goodDetail.price,
                  num: 1,
                  file:this.goodDetail.file
                });
              this.num = 1;
              this.price = this.goodDetail.price;
              this.isActive = false;
              this.$localStorage.set('cartData', cartData);
	            // 更新footer购物车数量
	            this.$bus.emit('updateCart', true);
	            this.updateProduct();
	            return 0;
						}
      	
				for (let key in cartData) {
              if (cartData[key].id == this.$route.query.id) {
									cartData[key].num++;
									this.num = cartData[key].num;
                  this.price = cartData[key].price;
                }
              }
//						 this.isActive = false;
            this.$localStorage.set('cartData', cartData);
            // 更新footer购物车数量
            this.$bus.emit('updateCart', true);
            this.updateProduct();

			},
			minus(){//减少商品数量
      	let cartData = this.$localStorage.get('cartData');
				for (let key in cartData) {
              if (!this.isActive&&cartData[key].id == this.$route.query.id) {
                  cartData[key].num--;
                  this.num = cartData[key].num;
                  this.price = cartData[key].price;
                  if(cartData[key].num==0){
                     cartData[key].num=0
                   		this.isActive = true;
                   		cartData.splice(key,1)
                      }
                    }
                }

          this.$localStorage.set('cartData', cartData);
          // 更新footer购物车数量
          this.$bus.emit('updateCart', true);
          this.updateProduct();
			},
			updateProduct(){
        // 更新视图
        let cartData = this.$localStorage.get('cartData');
        for (let key in this.product) {
          for (let keyIn in cartData) {
            if (this.product[key].id == cartData[keyIn].id) {
              this.$set(this.product[key], Object.assign(this.product[key], {num: cartData[keyIn].num}))
              if(cartData[keyIn].num==0){
              	cartData.splice(keyIn,1)
              	this.$localStorage.set('cartData', cartData);
              }
            }
          }
        }
      }
    }
  }
</script>

<style>
</style>
