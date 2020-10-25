<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>会员组列表</title>
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
			会员组列表
		</button>
		<button type="button" onClick="location.href='<?php echo U('add');?>'" class="button">
			<i class="icon-edit"></i>
			添加会员组
		</button>
	</div>
	<form action="<?php echo U('updateSort',array('mid'=> $hd['get']['mid']));?>" method="POST">
		<table class="table table-bordered table-hover table-condensed table-responsive">
			<tr class="bg">
				<td width="80">rid</td>
				<td  width="200">会员组名</td>
				<td>描述</td>
				<td width="100">系统</td>
				<td width="100">积分小于</td>
				<td width="100">评论审核</td>
				<td width="100">允许短消息</td>
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
					<td><?php echo $vo['rid'];?></td>
					<td><?php echo $vo['rname'];?></td>
					<td><?php echo $vo['title'];?></td>
					<td>
						    <?php if($vo['system']){ ?>
							<font color="red">√</font>
							<?php }else{ ?>
							<font color="blue">×</font>
						<?php } ?>
					</td>
					<td><?php echo $vo['creditslower'];?></td>
					<td>
						    <?php if($vo['comment_state']){ ?>
							<font color="red">√</font>
							<?php }else{ ?>
							<font color="blue">×</font>
						<?php } ?>
					</td>
					<td>
						    <?php if($vo['allowsendmessage']){ ?>
							<font color="red">√</font>
							<?php }else{ ?>
							<font color="blue">×</font>
						<?php } ?>
					</td>
					<td width="300">
						<a href="<?php echo U('edit',array('rid'=> $vo['rid']));?>">
							<i class="icon-edit"></i>
							修改
						</a> |
						    <?php if($vo['system']==0){ ?>
							<a href="javascript:;" class="dialogs" data-toggle="click" data-target="#mydialog<?php echo $vo['rid'];?>" data-mask="1" data-width="20%">
								<i class="icon-trash-o"></i>
								删除
							</a>
							<?php }else{ ?>
							<span class="text-gray">
								<i class="icon-trash-o"></i>
								删除
							</span>
						<?php } ?>
						<!-- 删除确认框 Start -->
						<div id="mydialog<?php echo $vo['rid'];?>">
							<div class="dialog bouncein">
								<div class="dialog-head">
									<span class="close icon-times"></span>
									<strong>删除确认对话框</strong>
								</div>
								<div class="dialog-body">
									是否确认删除该会员组？
								</div>
								<div class="dialog-foot">
									<button class="button dialog-close">取 消</button>
									<button class="button bg-green" onClick="location.href='<?php echo U('del',array('rid'=>$vo['rid']));?>'">确 认</button>
								</div>
							</div>
						</div>
						<!-- 删除确认框 End -->
					</td>
				</tr>
			<?php }}?>
		</table>
	</form>
</div>
</body>
</html>