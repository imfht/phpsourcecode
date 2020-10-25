<div class="container">

	<div class="row">
		<div class="span7">

			<div class="hero-unit"><h1 style="color:orange">亲，欢迎大家吐槽。</h1>
			</div>
			
		</div>

		<div class="span3">
			<div class="donate">
				<span class="badge badge-success">操作</span>
				<ul class="nav nav-pills nav-stacked" style="font-size:150%">
					<li><a href='create'>发布信息</a></li>
					<li><a href='manage'>管理信息</a></li>
				</ul>
				<hr/>
				<span class="badge badge-info">提示</span>
				<ul>
					<li><p style="color:purple;font-size:130%">有些重要信息可以发在这里...</p><i>
				<ul/>
			</div>
		</div>
	</div>
</div>

<?php $this->widget('bootstrap.widgets.TbThumbnails', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
	'template'=>"{items}\n{pager}",
)); ?>
