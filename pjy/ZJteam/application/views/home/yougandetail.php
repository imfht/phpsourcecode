<?php include 'application/views/home/header.php'?>	
<script>
$(function(){
	$("#yougan").addClass("active");
});
</script>
<div class="main_info clearfix">
		
  <div class="main_left"> 
   <div class="mod list clearfix" id="recommend_list_wrap"> 
    <h2 class="main_h2"><span class="bd"></span>支教有感 > <?php echo $yougandetail->title;?></h2> 
	<div class="article">
	<?php echo $yougandetail->content;?>
	
	</div>
   </div> 
   

  </div>
  
		<div class="main_right">
			
			<div class="get_invoice">
				<dl class="lp_project_invoice">
					<dt><h3 class="main_right_h3"><i class="icon flow_icon"></i>参与支教流程图</h3></dt>
					<dd id="reg" class="invoice_dd">
						<a href="" target="_blank" class="invoice_link">
							<span class="invoice_num">1</span> 
							<span class="invocie_word">注册</span> 
							<span class="invoice_otherword">个人实名注册填写相关信息</span>
						</a>
					</dd>
					<dd id="youjian" class="invoice_dd">
						<a href="" target="_blank" class="invoice_link">
							<span class="invoice_num">2</span> 
							<span class="invocie_word">选择</span> 
							<span class="invoice_otherword">选择喜欢的支教活动</span>
						</a>
					</dd>
					<dd id="chuli" class="invoice_dd">
						<a href="" target="_blank" class="invoice_link">
							<span class="invoice_num">3</span> 
							<span class="invocie_word">报名</span> 
							<span class="invoice_otherword">报名选择的支教活动</span>
						</a>
					</dd>
					<dd id="fapiao" class="invoice_dd">
						<a href="" target="_blank" class="invoice_link">
							<span class="invoice_num">4</span> 
							<span class="invocie_word">等待通知</span> 
							<span class="invoice_otherword">等待项目发起人联系通知</span>
						</a>
					</dd>
					<dd id="execute" class="invoice_dd">
						<a href="" target="_blank" class="invoice_link">
							<span class="invoice_num">5</span> 
							<span class="invocie_word">参与</span> 
							<span class="invoice_otherword">参与支教活动</span>
						</a>
					</dd>
					<dd id="finish" class="invoice_dd">
						<a href="" target="_blank" class="invoice_link">
							<span class="invoice_num">6</span> 
							<span class="invocie_word">结项</span> 
							<span class="invoice_otherword">完成支教活动</span>
						</a>
					</dd>
			</dl>

		<div class="status_invoice">
			<div class="flow-tips reg">
				<p>个人提交真实可靠的资料进行实名认证；公益机构提交所需机构资料完成注册。</p>
				<div class="flow-tips-arrow">&nbsp;</div>
			</div>
			<div class="flow-tips youjian">
				<p>实名认证的个人用户、在乐捐平台注册的公益机构，均可发起公益项目。</p>
				<div class="flow-tips-arrow">&nbsp;</div>
			</div>
			<div class="flow-tips chuli">
				<p>公募机构审核并确认项目，在十个工作日内反馈审核结果。</p>
				<div class="flow-tips-arrow">&nbsp;</div>
			</div>
			<div class="flow-tips fapiao">
				<p>审核通过的项目在乐捐平台上线，获得爱心网友捐款。</p>
				<div class="flow-tips-arrow">&nbsp;</div>
			</div>
			<div class="flow-tips execute">
				<p>项目执行方接收善款并执行项目，及时提交项目进展。</p>
				<div class="flow-tips-arrow">&nbsp;</div>
			</div>
			<div class="flow-tips finish">
				<p>项目执行方公示项目总结及善款使用报告，完成项目。</p>
				<div class="flow-tips-arrow">&nbsp;</div>
			</div>
		</div>
<!--[if !IE]>|xGv00|687a0c6e1f4548433aaf054c21848edf<![endif]-->
			</div>
			<?php include 'application/views/home/rightlist.php'?>	
		</div>
	</div>

<?php include 'application/views/home/footer.php'?>	