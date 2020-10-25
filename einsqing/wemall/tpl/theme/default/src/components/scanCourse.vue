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
										<td id="header-common-main" class="tl"><span class="ft-color">商品扫一扫</span></td>
										<td class="active-btn"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="view_page" class="transform-wrap" style="padding-top: 50px; min-height: 422px;">
			<div class="scan-course-wrap">
				<div class="scan-course-main">
					<p class="tc font-size"><img src="../assets/img/scan_course.png" width="45%"></p>
					<p class="tc ft-color">扫一扫商品包装上的条形码</p>
					<p class="tc ft-color">即可获取该水果更多详细信息</p>
					<p class="tc font-size"><img src="../assets/img/scan_example.png" width="95%"></p>
				</div>
			</div>
		</div>
		<div id="app-footer">
			<div class="app-footer-inner">
				<div id="app-footer-main" class="app-header-main">
					<div id="footer" class="footer fixed-wrap">
						<div class="common-footer bd-style">
							<a class="style-color tc" title="立即去扫一扫" @click='scan'>立即去扫一扫</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
'
<script>
	import fetch from './../fetch';
	import wx2 from 'weixin-js-sdk';

	export default {
		data() {
			return {
				jsSign: {}
			}
		},
		created() {
			fetch('/api/getJsSign',{
				url:encodeURIComponent(location.href.split('#')[0])
			}).then((res) => {
				this.jsSign = res.data.data.jsSign;

				wx2.config({
					debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
					appId: this.jsSign.appId, // 必填，公众号的唯一标识
					timestamp: this.jsSign.timestamp, // 必填，生成签名的时间戳
					nonceStr: this.jsSign.nonceStr, // 必填，生成签名的随机串
					signature: this.jsSign.signature, // 必填，签名，见附录1
					jsApiList: ['scanQRCode'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
				});

			})
		},
		methods: {

			scan() {
				wx2.ready(function() {
					wx2.scanQRCode({
						needResult: 0, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
						scanType: ["qrCode", "barCode"], // 可以指定扫二维码还是一维码，默认二者都有
						success: function(res) {
							var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
						}
					});

				});

			}
		}
	}
</script>

<style>

</style>