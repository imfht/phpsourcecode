<style>
.list_exchange {
	padding-top: 10px;
}

.list_exchange li[data-card]:first-of-type {
	margin-top: 0;
}
</style>



	<script>
		
		function getErrorStr(code){
			if(1==code){
				return '对不起，您的积分不足';
			}else if(2==code){
				return '兑换次数已经达到上限';
			}else if(3==code){
				return '当前兑换不在活动期内';
			}else if(4==code){
				return '当前活动关联的优惠券已失效';
			}else{
				return '兑换失败';
			}
		}
	
		function exchange(id, point,type) {

			confirm(
					'<label>扣除积分：<span>' + point + '</span></lael>',
					function() {
						loading(true);
						$.ajax({
							url: "/mobile/mcard",
							type:"get",
							data:{
								action:'exchange',
								mcid:4,
								wuid:389,
								talker:'ozdP9jkbKUglHNvd751CA99n4Xog',
								id:id
							},
							dataType:"json",
							success: function(res){
								loading(false);
								if(res.success){
									if(type==1){
										alert('兑换成功，请到我的优惠券中查看');
									}else if(type==2){
										alert('兑换成功，请到我的代金券中查看');
									}else if(type==3){
										alert('兑换成功，请到我的礼品券中查看');
									}									
									setTimeout('location.reload()',1500);
								}else{
									alert(getErrorStr(res.result),2000);
								}
							}
						});
					});
		}
	</script>

	<div class="container exchange ">
		<div class="body">
			<ul class="list_exchange">

				
				<li data-card="" onclick="//this.classList.toggle(&#39;on&#39;);"><header>
						<ul class="tbox">
							<li>
								<h5>500积分兑换一盒纸巾【演示】</h5>
								<p>有效期至2014年12月31日</p>
							</li>
						</ul>
					</header>
					<section>
						<div>
							<figure>
								
								<img src="__APPURL__/image/lipinquan.jpg">
								
							</figure>
							<p>亲，请猛击【立即兑换】进入兑换活动页面，祝您好运哦！</p>
						</div>
						<div class="des">						
							内容
							<dl>
								<dd>
									<span>适用范围</span><span>【演示】</span><br> <span>1、使用范围：此券在全国实体门店均可使用</span><br>
									<span>2、券有效期：请在2015年05月07日之前使用，逾期无效</span><br> <span>3、使用提示：一次消费仅可使用一次，此券不挂失，不找零，不兑换现金</span><br>
									<p>4、客服电话：400-6305-400</p>
									<p>
										<br>
									</p>
									<span>优惠券的最终解释权归店铺所有，如有任何疑问请进入商家店铺咨询服务</span>
								</dd>
								<dd>
									<p style="white-space: pre-line;">
										适用门店： 洞庭楚香、大雁门、红白山
									</p>
								</dd>
							</dl>							
						</div>
					</section>
					
					<footer>
						<dl class="box">
							<dd>
								<label><big>500</big>积分</label>
							</dd>
							<dd>
								<a href="javascript:;" onclick="exchange(96,500,3);">立即兑换</a>
							</dd>
						</dl>
					</footer></li>
					
					
			</ul>
			
		</div>