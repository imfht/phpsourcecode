<template>
  <div id="app-footer">
    <div class="app-footer-inner">
      <div id="app-footer-main" class="app-header-main">
        <div id="footer" class="footer fixed-wrap">
          <table border="0" cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
              <td class="td-left"></td>
              <td class="td-center" style="text-align: center;">
                <div id="shopping-cart-box" class="shopping-cart-box style-color" @click='toCart'>
                    <span class="cary_num_bg ft-color" >
                      <em id="cart_num" style="display: inline;" v-show='cartNum>0'>{{cartNum}}</em>
                    </span>
                  <a id="cart-btn">
                    <span id="cart_price" class="" v-if='cartNum==0'><i>¥</i>0.00</span>
                    <!--结算样式-->
                    <span id="cart_price" class="" v-else>去结算 ￥{{cartTotal}}</span>
                  </a>
                </div>
              </td>
              <td class="td-right"></td>
            </tr>
            </tbody>
          </table>
        </div>
        <div id="retun-top-btn" class="icon-angle-up icon-2x" style="display: none; "></div>
      </div>
    </div>
  </div>
</template>

<script>
	import { Toast } from 'mint-ui';
  export default {
    name: 'app',
    data () {
      return {
      	cartData:[],
        cartNum:0,
        cartTotal:0
      }
    },
    created(){
			this.updateCart();
      this.$bus.on('updateCart', this.updateCart);
		},
		methods:{
			updateCart() {
        this.cartNum = 0;
        this.cartTotal = 0;
        this.cartData = this.$localStorage.get('cartData');
        this.cartData.forEach((val, index) => {
            this.cartNum += this.cartData[index].num;
            this.cartTotal += parseInt(this.cartData[index].num) * parseFloat(this.cartData[index].price);
          })
            this.cartTotal=this.cartTotal.toFixed(2);
     	},
     	toCart(){
		   	if(this.cartNum==0){
		   		Toast('请先选择商品')
		   	}
		   	if(this.cartNum>0){
		   		this.$router.push('cart')
		   	}
		  }
   	}
  }
</script>

<style>
</style>
