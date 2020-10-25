<?php mc_template_part('header'); ?>
<?php foreach($page as $val) : ?>
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
				<div id="single">
					<h1 class="title text-center"><?php echo $val['title']; ?></h1>
					<hr>
					<div id="entry">
						<?php echo mc_magic_out($val['content']); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endforeach; ?>
<?php mc_template_part('footer'); ?>