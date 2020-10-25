<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>权限列表</title>
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
			权限列表
		</button>
		<button type="button" onClick="location.href='<?php echo U('add');?>'" class="button">
			<i class="icon-edit"></i>
			添加权限
		</button>
	</div>
</div>

<!-- Content Start -->
<form action="" meodth="POST">
	<div class="container-layout">
		<div class="bg border margin-top padding">
			温馨提示：权限节点菜单的变动将影响后台菜单布局！
		</div>
		<table class="table table-hover table-bordered margin-top">
			<tr class="bg">
				<td width="80">排序</td>
				<td width="80">ID</td>
				<td>权限名称</td>
				<td width="150">状态</td>
				<td width="150">类型</td>
				<td width="200">操作</td>
			</tr>
			        <?php
        //初始化
        $hd['list']['n'] = array(
            'first' => false,
            'last'  => false,
            'total' => 0,
            'index' => 0
        );
        if (empty($node)) {
            echo '';
        } else {
            $listId = 0;
            $listShowNum=0;
            $listNextId=0;
            foreach ($node as $n) {
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
                $hd['list'][n]['index']++;
                //第1个值
                $hd['list'][n]['first']=($listId == 0);
                //最后一个值
                $hd['list'][n]['last']= (count($node)-1 <= $listId);
                //总数
                $hd['list'][n]['total']++;
                //增加数
                $listId++;
                $listShowNum++;
                $listNextId+=1
                ?>
	            <tr>
	                <td width="50">
	                    <input type="text" value="<?php echo $n['list_order'];?>" name="list_order[<?php echo $n['nid'];?>]" style="width:50px;"/>
	                </td>
	                <td><?php echo $n['nid'];?></td>
	                <td><?php echo $n['_name'];?></td>
	                <td>
	                        <?php if($n['is_show']==1){ ?>
	                        显示
	                        <?php }else{ ?>
	                        <span class="text-red">隐藏</span>
	                    <?php } ?>
	                </td>
	                <td>
	                        <?php if($n['type']==1){ ?>
	                        <span class="text-sub">权限菜单</span>
	                        <?php }else{ ?>
	                        普通菜单
	                    <?php } ?>
	                </td>
	                <td align="center">
	                        <?php if($n['_level']==3){ ?>
	                        <span class="disabled">添加子权限  | </span>
	                        <?php }else{ ?>
	                        <a href="<?php echo U('add',array('pid'=>$n['nid']));?>">添加子权限</a> |
	                    <?php } ?>

	                        <?php if($n['is_system']==0){ ?>
	                        <a href="<?php echo U('edit',array('nid'=>$n['nid']));?>">修改</a> |
	                        <a href="<?php echo U('del',array('nid'=>$n['nid']));?>">删除</a>
	                        <?php }else{ ?>
	                        <span class="disabled">修改 | </span>
	                        <span class="disabled">删除</span>
	                    <?php } ?>
	                </td>
	            </tr>
    		<?php }}?>
		</table>
	</div>
</form>
<!-- Content End -->
</body>
</html>