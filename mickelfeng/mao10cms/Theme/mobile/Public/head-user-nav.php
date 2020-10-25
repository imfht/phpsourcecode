<div class="user-nav">
	<div class="container">
		<ul class="nav nav-tabs nav-justified">
			<li <?php if(ACTION_NAME=='index') echo 'class="active"'; ?>>
				<a href="<?php echo U('user/index/index?id='.$_GET['id']); ?>">
					<i class="fa fa-home"></i> 首页
				</a>
			</li>
			<li <?php if(ACTION_NAME=='pages') echo 'class="active"'; ?>>
				<a href="<?php echo U('user/index/pages?id='.$_GET['id']); ?>">
					<i class="fa fa-list"></i> 主题
				</a>
			</li>
			<li <?php if(ACTION_NAME=='comments') echo 'class="active"'; ?>>
				<a href="<?php echo U('user/index/comments?id='.$_GET['id']); ?>">
					<i class="fa fa-comments"></i> 评论
				</a>
			</li>
			<li <?php if(ACTION_NAME=='shoucang') echo 'class="active"'; ?>>
				<a href="<?php echo U('user/index/shoucang?id='.$_GET['id']); ?>">
					<i class="fa fa-star"></i> 收藏
				</a>
			</li>
			<li <?php if(ACTION_NAME=='guanzhu') echo 'class="active"'; ?>>
				<a href="<?php echo U('user/index/guanzhu?id='.$_GET['id']); ?>">
					<i class="fa fa-heart"></i> 关注
				</a>
			</li>
			<li <?php if(ACTION_NAME=='fans') echo 'class="active"'; ?>>
				<a href="<?php echo U('user/index/fans?id='.$_GET['id']); ?>">
					<i class="fa fa-heart-o"></i> 粉丝
				</a>
			</li>
			<?php if(mc_user_id()==$_GET['id']) : ?>
			<li <?php if(ACTION_NAME=='edit') echo 'class="active"'; ?>>
				<a href="<?php echo U('user/index/edit?id='.$_GET['id']); ?>">
					<i class="fa fa-cog"></i> 资料
				</a>
			</li>
			<?php endif; ?>
			<?php if(mc_user_id()==$_GET['id'] && mc_option('pro_close')!=1) : ?>
			<li <?php if(ACTION_NAME=='pro') echo 'class="active"'; ?>>
				<a href="<?php echo U('user/index/pro?id='.$_GET['id']); ?>">
					<i class="fa fa-shopping-cart"></i> 订单
				</a>
			</li>
			<?php endif; ?>
		</ul>
	</div>
</div>