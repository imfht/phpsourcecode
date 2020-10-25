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
										<td id="header-common-main" class="tc">&nbsp;</td>
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
		<div id="view_page" class="transform-wrap" style="background-color: rgb(247, 247, 247); padding-top: 50px; min-height: 422px; ">
			<div id="user-no-focus-height" class="user-no-focus-height"></div>
			<div class="recharge-wrap">
				<div class="recharge-header">
					<div class="user-recharge">余额:<span>{{userInfo.money}}</span>元</div>
				</div>
				<div class="recharge-main">
					<h3>请选择充值方式</h3>
					<ul>
						<li>
							<table>
								<tbody>
									<tr>
										<td>
											<img src="../assets/img/2.png" width="100%" title="微信安全支付">
										</td>
										<td @click='wxpay'>微信安全支付</td>
									</tr>
								</tbody>
							</table>
						</li>
					</ul>
					<p></p>
				</div>
				<div class="recharge-record-wrap">
					<div class="recharge-record-menu" @click="showRecord">查看充值记录<span></span></div>
					<input id="recharge-recoder" type="hidden" value="0">
					<transition name="fade">
						<ul class="record">
							<li class="center" v-if="show">你还没有充值记录噢~</li>
							<!--<li v-else></li>-->
						</ul>
					</transition>
				</div>
				<div class="recharge-opera-box" v-show='recharge'>
					<div class="recharge-opera-wrap">
						<div class="recharge-opera-inner">
							<h1>请输入充值金额</h1>
							<label> 
            					<input type="text" id="pay-money" value="" v-model='money'>
            				</label>
							<a class="sure-btn" @click='sureCharge'>确认充值</a>
							<a class="close-btn" @click='close'><img src="../assets/img/off.png" width="100%"></a>
						</div>
						<div class="recharge-opera-background"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import fetch from './../fetch';
	import { Toast } from 'mint-ui';

	export default {
		data() {
			return {
				show: false,
				userInfo: [],
				recharge: false,
				money: '',
				jsApiParameters:{}
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
		},
		methods: {
			//查看充值记录
			showRecord() {
				this.show = !this.show;
			},
			wxpay() {
				this.recharge = true;
			},
			sureCharge() {
				if(!this.money || this.money <= 0) {
					Toast('请输入金额')
					return;
				}
				//微信充值
				fetch('api/wxRecharge', {
					money: this.money
				}).then((res) => {
					
					if(res){
						if(res.data.code == 0) {
							Toast(res.data.msg);
							this.$router.push('/login');
						}
						if(res.data.code==1){
							this.recharge = false;
							
							this.jsApiParameters = res.data.data.jsApiParameters;
							this.callpay();
						}
						
					}
					
				})
			},
			onBridgeReady(){
			   WeixinJSBridge.invoke(
			       'getBrandWCPayRequest', {
			           "appId":this.jsApiParameters.appId,     //公众号名称，由商户传入     
			           "timeStamp":this.jsApiParameters.timeStamp,         //时间戳，自1970年以来的秒数     
			           "nonceStr":this.jsApiParameters.nonceStr, //随机串     
			           "package":this.jsApiParameters.package,     
			           "signType":this.jsApiParameters.signType,         //微信签名方式：     
			           "paySign":this.jsApiParameters.paySign //微信签名 
			       },
			       (res)=>{     
			           if(res.err_msg == "get_brand_wcpay_request:ok" ) {
			           		Toast('支付成功');
			           }else {
			           		Toast('支付失败，请重试');
			           }
			           this.$router.push('/recharge');
			       }
			   ); 
			},
			callpay(){
				if (typeof WeixinJSBridge == "undefined"){
				   if( document.addEventListener ){
				       document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
				   }else if (document.attachEvent){
				       document.attachEvent('WeixinJSBridgeReady', onBridgeReady); 
				       document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
				   }
				}else{
				   this.onBridgeReady();
				}
			},
			close() {
				this.recharge = false;
			}
		}
	}
</script>

<style scoped>
	.fade-enter-active,
	.fade-leave-active {
		transition: opacity .5s
	}
	
	.fade-enter,
	.fade-leave-active {
		opacity: 0
	}
	
	.recharge-main ul li table tr td {
		text-align: center;
	}
</style>