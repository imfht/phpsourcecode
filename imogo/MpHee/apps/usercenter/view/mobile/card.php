	<script type="text/javascript">
		function charge(){
			loading(true);
			$.ajax({
				url: "/mobile/mcard",
				type:"get",
				data:{
					action:'signin',
					mcid:4,
					wuid:389,
					talker:'ozdP9jkbKUglHNvd751CA99n4Xog'
				},
				dataType:"json",
				success: function(res){
					if(res.success){
						loading(false);
						alert('签到成功',1500);
						var point=res.result;
						var $currPointsp=$('a#myPoint span').last();
						var currPoint=$currPointsp.html();
						currPoint=Number(currPoint)+point;
						$currPointsp.html(currPoint);
						$('li#signButton').hide();
						$('li#signResult').show();
					}
				}
			});
		}
	</script>
	<div class="container card">
		<header>
			<div class="header card">

				<div class="card" data-side="1" onclick="this.classList.toggle(&#39;on&#39;);">					
					<div class="back">
						<div class="fg" style="background-image: url({$cardinfo['cardfan']});">
							<div class="backtag">
								<canvas data-bgcolor="#d90001" width="54" height="30"></canvas>
							</div>
							<div class="info">
									
								<p class="addr">{$cardinfo['cardaddress']}</p>
								
									
								<p class="tel">
									<a class="autotel" href="tel:{$cardinfo['cardphone']}" onclick="event.stopPropagation();">{$cardinfo['cardphone']}</a>
								</p>
								
							</div>
							<p class="keywords">{$cardinfo['cardname']}</p>
						</div>
					</div>
					<div class="front">
						<div class="fg" style="background-image: url({$cardinfo['cardzheng']});">
							<img id="cardlogoimg" src="__APPURL__/image/card.png" style="margin-top: 39px;width:185px;height:65px;">
							<img data-src="{###barcodeimg###}" style="display: none;">
							<div class="fc">
								<span class="cname" style="color: {$cardinfo['cardnamecolor']};">{$cardinfo['cardname']}</span>
								
								<span class="n" style="color: {$cardinfo['numcolor']}; text-shadow: rgb(0, 0, 0) 0px -1px;">102163</span>
							</div>
						</div>
					</div>
				</div>


			</div>
			
			<div>
				<ul class="box group_btn all">
					
					<li><a id="myPoint" href="{url('index/jifen')}"><span>我的积分</span><span>1000</span></a></li>		
					<li id="signButton"><a href="javascript:charge();"><span>签到即送</span><span>10</span></a></li>
					<li id="signResult" style="padding-top: 12px;display:none;"><a><img src="__APPURL__/image/signed.png" width="20px" height="20px" style="vertical-align: -4px;margin-right:2px;">已签到</a></li>
					<li id="recharge" style="padding-top: 12px;"><a href="{url('index/chong')}">在线充值</a></li>
					
				</ul>
			</div>
			
		</header>
		<div class="body">
			<ul class="list_ul">
				<div>
					<li class="li_d"><a href="{url('mobile/userinfo')}"><label class="label"><i>&nbsp;</i>会员卡资料 <span>&nbsp;</span></label></a></li>
				</div>
				
				<div>
						
					<li class="li_i"><a class="label" href="tel:{$cardinfo['cardphone']}"><i>&nbsp;</i>{$cardinfo['cardphone']}
							<span>&nbsp;</span></a></li>
					
						
					<li class="li_j"><a>
							<label class="label"><i>&nbsp;</i>{$cardinfo['cardaddress']} <span>&nbsp;</span></label>
					</a></li>
					
				</div>
			</ul>
			<p class="page-url"><a href="http://www.thinkteam.tk/" target="_blank" class="page-url-link">此功能由“T-Team”平台提供{$uuid}-{$id['ppid']}</a></p>
		</div>
	<script type="text/javascript">
		var selectdata = '{$cardinfo["cardselect"]}';
		var select = eval('(' + selectdata + ')');
		
		for(var i = 0;i < select.length; i++ ){
			var selectli = '<li class="li_z"><a href="'+select[i].url+'"><label class="label"><i class="fa '+select[i].icon+'">&nbsp;</i>'+select[i].name+' <span>&nbsp;</span></label></a></li>';
			$(".li_d").before(selectli);
		}
	</script>