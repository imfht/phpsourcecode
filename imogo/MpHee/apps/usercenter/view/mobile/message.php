<script>
		$().ready(
				function() {
					$("#nav_1 a").on("touchstart touchend mouseover mouseout",
							function(e) {
								switch (e.type) {
								case "touchstart":
								case "mouseover":
									this.classList.add("hover");
									break;
								case "touchend":
								case "mouseout":
									this.classList.remove("hover");
									break;
								}
							});
				});

		function readMsg(id,tis) {
			
			var li = tis.parentNode;
			try{
				li.classList.toggle("on");
			}catch(e){
				$(li).toggleClass('on');
			}
			
			$(tis).find('span.noticeTip').hide();
			var $num=$('footer p#Js-msg-num');
			var count=$num.attr('data-count');
			if(count){
				count=Number(count)-1;
				if(count>0){
					$num.attr('data-count',count);
				}else{
					$num.removeAttr('data-count');
				}
			}
			
			//已经阅读
			$.ajax({
				url: "/mobile/mcard",
				type:"get",
				data:{
					action:'readNotice',
					mcid:4,
					wuid:389,
					talker:'ozdP9jkbKUglHNvd751CA99n4Xog',
					to:'notice',
					id:id
				},
				dataType:"json",
				success: function(res){
				 	//do nothing 
				}
			});

		}
	</script>

	<div class="container coupon message ">
		<header>
			<nav class="p_10">
				<ul id="nav_1" class="box">
					<li><a href="./wjj-消息_files/wjj-消息.htm" class="on">广播</a></li>
					<li><a href="http://www.weijuju.com/mobile/mc/cardMessage2.jsp?wuid=389&uid=120&talker=ozdP9jkbKUglHNvd751CA99n4Xog&mcid=4">系统消息</a></li>
				</ul>
			</nav>
		</header>
		<div class="body">
			<ul class="list_message">
			
				
			</ul>
			
			<div class="noData"></div>
			
		</div>