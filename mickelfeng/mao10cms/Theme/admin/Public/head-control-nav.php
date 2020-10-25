<ol class="breadcrumb <?php if(CONTROLLER_NAME=='Index' && ACTION_NAME=='pro_all') : ?>mb-20<?php else : ?>mb-0<?php endif; ?>" id="baobei-term-breadcrumb">
	<li>
		<a href="<?php echo U('control/index/index'); ?>">
			首页
		</a>
	</li>
	<?php if(CONTROLLER_NAME=='Index' && ACTION_NAME=='index') : ?>
	<!--li class="active">
		应用中心
	</li-->
	<?php else : ?>
	<!--li>
		<a href="<?php echo U('control/index/index'); ?>">
			应用中心
		</a>
	</li-->
	<?php if(CONTROLLER_NAME=='Index') : ?>
		<?php if(ACTION_NAME=='set') : ?>
		<li class="active">
			网站设置
		</li>
		<?php elseif(ACTION_NAME=='pro_all') : ?>
		<li class="active">
			订单管理
		</li>
		<?php elseif(ACTION_NAME=='paytools') : ?>
		<li class="active">
			支付接口
		</li>
		<?php elseif(ACTION_NAME=='manage') : ?>
		<li class="active">
			用户管理 <span class="ml-10 label label-warning"><?php echo M('page')->where("type = 'user'")->count('id'); ?></span>
		</li>
		<?php elseif(ACTION_NAME=='tixian') : ?>
		<li class="active">
			提现记录
		</li>
		<?php elseif(ACTION_NAME=='images') : ?>
		<li class="active">
			图片管理
		</li>
		<?php endif; ?>
	<?php elseif(CONTROLLER_NAME=='Weixin') : ?>
		<li>
			<a href="<?php echo U('control/weixin/index'); ?>">
				微信连接
			</a>
		</li>
		<?php if(ACTION_NAME=='index') : ?>
		<li class="active">
			接口设置
		</li>
		<?php elseif(ACTION_NAME=='qunfa') : ?>
		<li class="active">
			信息群发
		</li>
		<?php elseif(ACTION_NAME=='huifu') : ?>
		<li class="active">
			自动回复
		</li>
		<?php endif; ?>
	<?php endif; ?>
	<?php endif; ?>
</ol>