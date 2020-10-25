<!-- 引入bs-confirmation -->
<script src="/bower_components/bs-confirmation/bootstrap-confirmation.js"></script>
<!-- 引入css -->
<link rel="stylesheet" type="text/css" href="/assets/css/global.css">

<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
    <ol class="breadcrumb">
      <li><a href="<?=base_url('dashboard/index')?>"><i class="fa fa-dashboard"></i> 首页</a></li>
      <li class="active"><?=$title?></li>
    </ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<div class="box box-info">
			<div class="box-header with-border">
				<h3 class="box-title"><?=$title?></h3>
				<div class="box-tools pull-right">
					<a class="btn btn-sm btn-primary" href="add">添加</a>
				</div><!-- /.box-tools -->
			</div><!-- /.box-header -->
			<div class="box-body">
				<table class="table table-hover table-striped table-bordered">
					<thead>
						<tr>
							<th>头像</th>
							<th>昵称</th>
							<th>金额</th>
							<th>日期</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($result as $item):?>
							<tr>
								<td><img width="60" height="60" src="<?=$item->get('user')->get('avatarUrl');?>" /></td>
								<td><?=$item->get('user')->get('nickName');?></td>
								<td><?=$item->get('amount')?></td>
								<td><?=$item->get('updatedAt')->format('Y-m-d');?></td>
							</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div><!-- /.box-body -->
			<div class="box-footer">
			</div><!-- box-footer -->
		</div><!-- /.box -->
	</section>
	<!-- /.content -->
</div>