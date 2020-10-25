<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>钩子列表</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
</head>
<body>
	<div class="bg border margin-little-bottom padding-left padding-top padding-bottom">
	<button type="button" onClick="location.href='<?php echo U('index');?>'" class="button bg border">
		<i class="icon-th-list"></i>
		钩子列表
	</button>
	<button type="button" onClick="location.href='<?php echo U('add');?>'" class="button border">
		<i class="icon-edit"></i>
		添加钩子
	</button>
</div>
<table class="table table-bordered table-hover table-condensed table-responsive">
	<tr class="bg">
		<td width="80">ID</td>
		<td width="80">钩子名称</td>
		<td>钩子描述</td>
		<td>类 型</td>
		<td>开 启</td>
		<td>系 统</td>
		<td>操 作</td>
	</tr>
	        <?php
        //初始化
        $hd['list']['vo'] = array(
            'first' => false,
            'last'  => false,
            'total' => 0,
            'index' => 0
        );
        if (empty($data)) {
            echo '';
        } else {
            $listId = 0;
            $listShowNum=0;
            $listNextId=0;
            foreach ($data as $vo) {
                //开始值
                if ($listId<0) {
                    $listId++;
                    continue;
                }
                //步长
                if($listId!=$listNextId){$listId++;continue;}
                //显示条数
                if($listShowNum>=100)break;
                //第几个值
                $hd['list'][vo]['index']++;
                //第1个值
                $hd['list'][vo]['first']=($listId == 0);
                //最后一个值
                $hd['list'][vo]['last']= (count($data)-1 <= $listId);
                //总数
                $hd['list'][vo]['total']++;
                //增加数
                $listId++;
                $listShowNum++;
                $listNextId+=1
                ?>
		<tr>
			<td><?php echo $vo['id'];?></td>
			<td><?php echo $vo['name'];?></td>
			<td><?php echo $vo['description'];?></td>
			<td>
				    <?php if($vo['type']==1){ ?>
					控制器
					<?php }else{ ?>
					视图
				<?php } ?>
			</td>
			<td>
				    <?php if($vo['status']==1){ ?>
					<font color="red">√</font>
					<?php }else{ ?>
					<font color="blue">×</font>
				<?php } ?>
			</td>
			<td>
				    <?php if($vo['is_system']==1){ ?>
					<font color="red">√</font>
					<?php }else{ ?>
					<font color="blue">×</font>
				<?php } ?>
			</td>
			<td width="280">
				<a href="<?php echo U('edit',array('id'=> $vo['id']));?>">
					<i class="icon-edit"></i>
					编辑
				</a> |
				    <?php if($vo['is_system']==1){ ?>
					<span class="text-gray">
						<i class="icon-trash-o"></i>
						删除
					</span>
				<?php }else{ ?>
					<a href="javascript:;" data-mask="1" data-width="20%" class="dialogs" data-toggle="click" data-target="#mydialog<?php echo $vo['id'];?>">
					<i class="icon-trash-o"></i>
					删除
				</a>
				<!-- 删除确认框 Start -->
				<div id="mydialog<?php echo $vo['id'];?>">
					<div class="dialog bouncein">
						<div class="dialog-head">
							<span class="close icon-times"></span>
							<strong>删除确认对话框</strong>
						</div>
						<div class="dialog-body">
							是否确认删除该钩子数据？
						</div>
						<div class="dialog-foot">
							<button class="button dialog-close">取 消</button>
							<button class="button bg-green" onClick="location.href='<?php echo U('del',array('id'=> $vo['id']));?>'">确 认</button>
						</div>
					</div>
				</div>
				<!-- 删除确认框 End -->
				<?php } ?>
			</td>
		</tr>
	<?php }}?>
</table>
</body>
</html>