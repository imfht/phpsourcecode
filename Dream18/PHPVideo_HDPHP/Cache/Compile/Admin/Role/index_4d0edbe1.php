<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>管理组列表</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
</head>
<body>
	<div class="container-layout">
		<div class="bg border margin-little-bottom padding-left padding-top padding-bottom">
		<button type="button" onClick="location.href='<?php echo U('index');?>'" class="button bg">
			<i class="icon-th-list"></i>
			管理组列表
		</button>
		<button type="button" onClick="location.href='<?php echo U('add',array('mid'=> $hd['get']['mid']));?>'" class="button">
			<i class="icon-edit"></i>
			添加管理组
		</button>
	</div>
	<form action="<?php echo U('updateSort',array('mid'=> $hd['get']['mid']));?>" method="POST">
		<table class="table table-bordered table-hover table-condensed table-responsive">
			<tr class="bg">
				<td>rid</td>
				<td>角色名称</td>
				<td>描述</td>
				<td>系统</td>
				<td>操作</td>
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
					<td width="80"><?php echo $vo['rid'];?></td>
					<td width="300"><?php echo $vo['rname'];?></td>
					<td><?php echo $vo['title'];?></td>
					<td width="150">
						    <?php if($vo['system']){ ?>
							<font color="red">√</font>
							<?php }else{ ?>
							<font color="blue">×</font>
						<?php } ?>
					</td>
					<td width="300">
						<a href="<?php echo U('edit',array('rid'=> $vo['rid']));?>">修改</a> |							
						    <?php if($vo['rid']==1){ ?>
							<span class="text-gray">权限设置</span>
							<?php }else{ ?>
							<a href="<?php echo U('Access/edit',array('rid'=>$vo['rid']));?>">权限设置</a>
						<?php } ?> |
						    <?php if($vo['system']==0){ ?>
							<a href="javascript:;" class="dialogs" data-toggle="click" data-target="#mydialog<?php echo $vo['rid'];?>" data-mask="1" data-width="50%">删除</a>
							<!-- 删除确认框 Start -->
							<div id="mydialog<?php echo $vo['rid'];?>">
								<div class="dialog fadein-top">
									<div class="dialog-head">
										<span class="close rotate-hover"></span>
										<strong>删除确认对话框</strong>
									</div>
									<div class="dialog-body">
										是否删除「<?php echo $vo['rname'];?>」栏目？
									</div>
									<div class="dialog-foot">
										<button class="button dialog-close">取 消</button>
										<button class="button bg-green" onClick="location.href='<?php echo U('del',array('rid'=>$vo['rid']));?>'">确 认</button>
									</div>
								</div>
							</div>
							<!-- 删除确认框 End -->
							<?php }else{ ?>
							<span class="text-gray">删除</span>
						<?php } ?>
					</td>
				</tr>
			<?php }}?>
		</table>
	</form>
</div>
</body>
</html>