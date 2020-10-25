<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>用户列表</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
</head>
<body>
	<!-- 按钮组 -->
	<div class="container-layout">
        <div class="bg border margin-little-bottom padding-left padding-top padding-bottom">
            <button type="button" onClick="location.href='<?php echo U('index');?>'" class="button bg">
                <i class="icon-th-list"></i>
                用户列表
            </button>
            <button type="button" onClick="location.href='<?php echo U('add');?>'" class="button">
                <i class="icon-edit"></i>
                添加用户
            </button>
        </div>
    </div>
    <!-- 按钮组 End -->
	<div class="container-layout">
	<table class="table table-bordered table-hover table-condensed table-responsive">
		<tr class="bg">
			<td widtd="80">uid</td>
			<td>账户</td>
			<td>昵称</td>
			<td widtd="150">用户组</td>
			<td widtd="80">积分</td>
			<td widtd="80">性别</td>
			<td widtd="80">状态</td>
			<td>最近登录IP</td>
			<td>登录时间</td>
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
				<td><?php echo $vo['uid'];?></td>
				<td><?php echo $vo['username'];?></td>
				<td><?php echo $vo['nickname'];?></td>
				<td><?php echo $vo['rname'];?></td>
				<td><?php echo $vo['credits'];?></td>
				<td>
					    <?php if($vo['sex']==1){ ?>
						男
					<?php }else if($vo['sex']==2){ ?>
						女
					<?php }else if($vo['sex']==3){ ?>
						保密
					<?php } ?>
				</td>
				<td>
					    <?php if($vo['user_status']==1){ ?>
						正常
					<?php }else{ ?>
						<div style="color:red;">锁定</div>
					<?php } ?>
				</td>
				<td><?php echo $vo['lastip'];?></td>
				<td><?php echo hd_date($vo['logintime'],'Y-m-d h:i:s');?></td>
				<td>
					<a href="<?php echo U('edit',array('uid'=> $vo['uid']));?>">
						<i class="icon-edit"></i>
						编辑
					</a> |
					<a href="javascript:;" class="dialogs" data-toggle="click" data-target="#mydialog<?php echo $vo['uid'];?>" data-mask="1" data-width="20%">
						<i class="icon-trash-o"></i>
						删除
					</a>
					<!-- 删除确认框 Start -->
					<div id="mydialog<?php echo $vo['uid'];?>">
						<div class="dialog bouncein">
							<div class="dialog-head">
								<span class="close rotate-hover"></span>
								<strong>删除确认对话框</strong>
							</div>
							<div class="dialog-body">
								是否确认删除该用户？
							</div>
							<div class="dialog-foot">
								<button class="button bg-sub radius-none" onClick="location.href='<?php echo U('del',array('uid'=> $vo['uid']));?>'">
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
		<tr>
			<td colspan="10"><?php echo $page;?></td>
		</tr>
	</table>
</div>
</body>
</html>