<template>
	<div id="main">
		<div class="b-color-f">
			<div class="filter-top" id="scrollUp"><i class="iconfont icon-jiantou"></i></div>
			<div class="search-div j-search-div ts-3">
				<footer class="close-search j-close-search"> 点击关闭</footer>
			</div>
			<div class="header-bar">
				<div class="header-left" style="display: block;" onclick="history.go(-1)"></div>
				<div class="header-title">密码重置</div>
			</div>
			<div class="con" id="pjax-container">
				<div id="show" style="">
					<section class="user-center j-f-tel margin-lr">
						<div class="text-all dis-box j-text-all" name="write_mobilediv">
							<label>+86</label>
							<div class="box-flex input-text">
								<input class="j-input-text" name="write_mobile" type="tel" placeholder="请输入手机号" v-model="phone" maxlength="11">
								<i class="iconfont icon-guanbi is-null j-is-null"></i>
							</div>
						</div>
						<p class="fl t-remark" style="font-size: .92rem">为了您的安全，我们会向你手机发送验证码</p>
						<input type="hidden" name="enabled_sms" value="1">
						<button class="btn-submit" style='margin-bottom:1.3rem;'>
			              <div @click="nextStep" style="color: #fff">下一步</div>
			            </button>
					</section>
				</div>
				<div class="div-messages"></div>
			</div>
		</div>
	</div>
</template>

<script>
	import { Toast } from 'mint-ui';
	export default {
		data() {
			return {
				phone: ''
			}
		},
		methods: {
			nextStep() {
				if(!this.phone) {
					Toast('请输手机号');
					return;
				}
				if(!(/^1[358]\d{9}$/.test(this.phone))) {
					Toast('手机号输入有误');
					return;
				}
				this.$router.push({ path: '/resetPassword', query: { phone: this.phone } })
			}
		}
	}
</script>

<style>

</style>