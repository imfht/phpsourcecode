<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>插件列表</title>
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
		插件列表
	</button>
	<button type="button" onClick="location.href='<?php echo U('add');?>'" class="button border">
		<i class="icon-edit"></i>
		创建插件
	</button>
</div>
<table class="table table-bordered table-hover table-condensed table-responsive">
	<tr class="bg">
		<td width="100">标识</td>
		<td>插件名称</td>
		<td>描 述</td>
		<td>安 装</td>
		<td>作 者</td>
		<td>版 本</td>
		<td width="300">操 作</td>
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
			<td><?php echo $vo['name'];?></td>
			<td><?php echo $vo['title'];?></td>
			<td><?php echo $vo['description'];?></td>
			<td>
				    <?php if($vo['status']){ ?>
					<font color="red">√</font>
					<?php }else{ ?>
					<font color="blue">×</font>
				<?php } ?>
			</td>
			<td><?php echo $vo['author'];?></td>
			<td><?php echo $vo['version'];?></td>
			<td>
				<a href="<?php echo U('package',array('addon'=> $vo['name']));?>">打包</a> |
				    <?php if($vo['install']){ ?>
					    <?php if($vo['config']){ ?>
						<a href="<?php echo U('config',array('id'=>$vo['id']));?>">设置</a> |
					<?php }else{ ?>
						<span class="text-gray">设置</span> |
					<?php } ?>
					    <?php if($vo['status']){ ?>
						<a href="<?php echo U('disabled',array('addon'=> $vo['name']));?>"><span class="text-dot">禁用</span></a> |
					<?php }else{ ?>
						<a href="<?php echo U('enabled',array('addon'=> $vo['name']));?>"><span class="text-main">启用</span></a> |
					<?php } ?>
					<a href="<?php echo U('uninstall',array('addon'=> $vo['name']));?>">卸载</a> |
				<?php }else{ ?>
					<a href="<?php echo U('install',array('addon'=> $vo['name']));?>">安装</a> |
				<?php } ?>
				    <?php if($vo['IndexAction']){ ?>
					<a href="<?php echo $vo['IndexAction'];?>" target="_blank">前台</a> |
				<?php }else{ ?>
					<span class="text-gray">前台</span> |
				<?php } ?>
				    <?php if($vo['help']){ ?>
					<a href="<?php echo $vo['help'];?>" target="_blank">帮助</a>
				<?php }else{ ?>
					帮助
				<?php } ?>
			</td>
		</tr>
	<?php }}?>
</table>
</body>
</html>