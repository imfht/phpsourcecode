<?php
/* @var $this yii\web\View */

$this->title = '角色权限分配';
$this->params['breadcrumbs'][] = $this->title;
?>
<table class="table table-hover table-striped table-bordered"> 
    <thead>
		<tr>
			<th class="w150 text-right">模块</th>
			<th class="text-left">方法</th>
		</tr>
    </thead>
	<tbody>
		<?php foreach ($permissions as $main_id => $permission): ?>
			<tr>
				<th class="text-right w150">
					<label> <?php echo $permission['name']; ?> <input type="checkbox" class="select-row" value="<?php echo $main_id; ?>"></label>
				</th>
				<td>
					<?php foreach ($permission['children'] as $sub_id => $val): ?>
						<div class="group-item w150 pull-left">
							<label class="checkbox-inline"> <input <?php if (in_array($val['actions'][0], $my_permissions)): ?> checked="checked" <?php endif; ?> type="checkbox" data-parent="<?php echo $main_id; ?>" value="<?php echo $main_id; ?>_<?php echo $sub_id; ?>"> <?php echo $val['name']; ?> </label>
						</div>
					<?php endforeach; ?>
				</td>
			</tr>
		<?php endforeach; ?>
        <tr>
			<th class="text-right">
				<label> 全选 <input type="checkbox" class="select-all"></label>
			</th>
			<td>
				<button type="submit" id="submit" data-rolename="<?php echo $role_name; ?>" class="btn btn-submit btn-primary">
					<i class="icon-ok"></i>
					保存
				</button>
				<button type="button" id="back" class="btn">
					<i class="icon-backward"></i>
					返回
				</button>
			</td>
		</tr>
	</tbody>
</table>