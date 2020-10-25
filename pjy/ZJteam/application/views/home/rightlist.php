			<div class="main_bottom_right_question" style="padding-top:30px;">
				<div class="zuixinjz_t">
					<h3 class="questions_t main_right_h3"><i class="icon flow_icon"></i>保险基金</h3>
					<a class="more a-yjfk" href="<?php echo site_url("home/safelist");?>" target="_blank">更多</a>
				</div>
				<ol class="question_c">
				<?php foreach($safe as $safe):?>
					<li><a href="<?php echo site_url("home/safedetail/".$safe['id']);?>"><span style="display:block;float:left;"><?php echo $safe['title'];?></span><span style="display:block;float:right;"><?php echo $safe['addtime'];?></span></a></li>
				<?php endforeach;?>
				</ol>
			</div><!--[if !IE]>|xGv00|d7b2f68e360767064cba1d81ae9a1e5c<![endif]-->
			<div class="main_bottom_right_question" style="padding-top:30px;">
				<div class="zuixinjz_t">
					<h3 class="questions_t main_right_h3"><i class="icon flow_icon"></i>支教有感</h3>
					<a class="more a-yjfk" href="<?php echo site_url("home/youganlist");?>" target="_blank">更多</a>
				</div>
				<ol class="question_c">
				<?php foreach($yougan as $yougan):?>
					<li><a href="<?php echo site_url("home/yougandetail/".$yougan['id']);?>"><span style="display:block;float:left;"><?php echo $yougan['title'];?></span><span style="display:block;float:right;"><?php echo $yougan['addtime'];?></span></a></li>
				<?php endforeach;?>
				</ol>
			</div>
			<div class="main_bottom_right_question" style="padding-top:30px;">
				<div class="zuixinjz_t">
					<h3 class="questions_t main_right_h3"><i class="icon flow_icon"></i>扫描关注微信公众平台</h3>
				</div>
				<img src="<?php echo base_url()?>Public/images/erweima.jpg" width="150" style="margin-left:30px;"></img>
			</div>