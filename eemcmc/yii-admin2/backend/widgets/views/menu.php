<nav class="menu" data-toggle="menu">
	<ul class="nav nav-primary">
		<?php foreach ($menus as $menu): ?>
			<?php $class = !empty($menu['children']) ? ' class="show"' : ' class="active"'; ?>
			<li<?php if ($controller == $menu['url']): echo $class; endif;?>>

				<?php //显示主菜单; ?>
				<a href="/<?php echo $menu['url']; ?>">
					<?php if (!empty($menu['icon'])): ?>
						<i class="<?php echo $menu['icon']; ?>"></i>
					<?php endif; ?>
					<?php echo $menu['name']; ?>
				</a>

				<?php //是否有子菜单; ?>
				<?php if (!empty($menu['children'])): ?>
					<ul class="nav">
						<?php foreach ($menu['children'] as $key => $child): ?>
							<li<?php if ($action == $child['url']): ?> class="active"<?php endif; ?>>
								<a href="/<?php echo $child['url']; ?>">
									<?php if (!empty($child['icon'])): ?>
										<i class="<?php echo $child['icon']; ?>"></i>
									<?php endif; ?>
									<?php echo $child['name']; ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

			</li>
		<?php endforeach; ?>
	</ul>
</nav>

