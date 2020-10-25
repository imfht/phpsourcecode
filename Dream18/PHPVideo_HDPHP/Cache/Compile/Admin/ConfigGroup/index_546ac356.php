<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>配置组列表</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
</head>
<body>
	<div class="bg border margin-little-bottom padding-left padding-top padding-bottom">
		<button type="button" onClick="location.href='<?php echo U('index');?>'" class="button radius-none bg-sub">
			<i class="icon-th-list"></i>
			配置组列表
		</button>
		<button type="button" onClick="location.href='<?php echo U('add');?>'" class="button radius-none">
			<i class="icon-edit"></i>
			添加配置组
		</button>
	</div>
<table class="table table-bordered table-hover table-condensed table-responsive">
	<tr>
		<th width="80">排序</th>
		<th width="80">cid</th>
		<th width="140">组名标识</th>
		<th>组名称</th>
		<th>系 统</th>
		<th>操 作</th>
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
			<td><?php echo $vo['csort'];?></td>
			<td><?php echo $vo['cid'];?></td>
			<td><?php echo $vo['cname'];?></td>
			<td><?php echo $vo['ctitle'];?></td>
			<td>
				    <?php if($vo['system']==1){ ?>
					√
				<?php }else{ ?>
					<div style="color:red;">×</div>
				<?php } ?>
				</td>
			<td width="280">
				<a href="<?php echo U('edit',array('cid'=> $vo['cid']));?>"    <?php if($vo['system']==1){ ?>disabled="disabled"<?php } ?>>
					<i class="icon-edit"></i>
					编辑
				</a> |
				<a href="javascript:;" data-mask="1" data-width="20%" class="dialogs" data-toggle="click"     <?php if($vo['system']==1){ ?>disabled="disabled"<?php } ?>data-target="#mydialog<?php echo $vo['cid'];?>">
					<i class="icon-trash-o"></i>
					删除
				</a>
				<!-- 删除确认框 Start -->
				<div id="mydialog<?php echo $vo['cid'];?>">
					<div class="dialog bouncein radius-none">
						<div class="dialog-head">
							<span class="close icon-times"></span>
							<strong>删除确认对话框</strong>
						</div>
						<div class="dialog-body">
							是否确认删除该配置组？
						</div>
						<div class="dialog-foot">
							<button class="button bg-sub radius-none" onClick="location.href='<?php echo U('del',array('cid'=> $vo['cid']));?>'">
								<i class="icon-check-square-o"></i>
								确 认
							</button>
							<button class="button dialog-close radius-none">
								<i class="icon-times"></i>
								取 消
							</button>
						</div>
					</div>
				</div>
				<!-- 删除确认框 End -->
			</td>
		</tr>
	<?php }}?>
</table>
</body>
</html>