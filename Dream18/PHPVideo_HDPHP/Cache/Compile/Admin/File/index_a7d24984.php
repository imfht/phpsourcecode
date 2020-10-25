<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>附件管理</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
</head>
<body>
	<div class="container-layout">
		<div class="bg border margin-little-bottom padding-left padding-top padding-bottom">
			<button type="button" onClick="location.href='<?php echo U('index');?>'" class="button radius-none bg-sub">
				<i class="icon-th-list"></i>
				附件管理
			</button>
		</div>
	</div>
	<div class="container-layout">
		<table class="table table-bordered table-hover table-condensed table-responsive">
			<tr class="bg">
				<td><input type="checkbox" name="" value=""/></td>
				<td>ID</td>
				<td>预览</td>
				<td>文件名</td>
				<td>大小</td>
				<td>上传时间</td>
				<td>所属用户</td>
				<td>操作</td>
			</tr>
			<tr>
				        <?php
        //初始化
        $hd['list']['vo'] = array(
            'first' => false,
            'last'  => false,
            'total' => 0,
            'index' => 0
        );
        if (empty($upload)) {
            echo '';
        } else {
            $listId = 0;
            $listShowNum=0;
            $listNextId=0;
            foreach ($upload as $vo) {
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
                $hd['list'][vo]['last']= (count($upload)-1 <= $listId);
                //总数
                $hd['list'][vo]['total']++;
                //增加数
                $listId++;
                $listShowNum++;
                $listNextId+=1
                ?>
					<td><input type="checkbox" name="" value=""/></td>
					<td><?php echo $vo['id'];?></td>
					<td>
						    <?php if($vo['image'] && is_file($vo['path'])){ ?>
                        <a href="<?php echo $vo['pic'];?>" target="_blank">
                            <img src="http://localhost/PHPUnion/<?php echo $vo['path'];?>" title="点击预览大图" onmouseover="view_image(this)"/>
                        </a>
                        <?php }else{ ?>
                            <img src="http://localhost/PHPUnion/Static/image/upload-pic.png'" title="点击预览大图"/>
                    	<?php } ?>
					</td>
					<td><?php echo $vo['basename'];?></td>
					<td><?php echo get_size($vo['size']);?></td>
					<td><?php echo date("Y-m-d",$vo['addtime']);?></td>
					<td><?php echo $vo['username'];?></td>
					<td>
						<a href="javascript:;" data-mask="1" data-width="20%" class="dialogs" data-toggle="click" data-target="#mydialog<?php echo $vo['id'];?>">
							<i class="icon-trash-o"></i>
							删除
						</a>
						<!-- 删除确认框 Start -->
						<div id="mydialog<?php echo $vo['id'];?>">
							<div class="dialog bouncein radius-none">
								<div class="dialog-head">
									<span class="close icon-times"></span>
									<strong>删除确认对话框</strong>
								</div>
								<div class="dialog-body">
									是否确认删除该附件？
								</div>
								<div class="dialog-foot">
									<button class="button bg-sub radius-none" onClick="location.href='<?php echo U('del',array('id'=> $vo['id']));?>'">
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
				<?php }}?>
			</tr>
			<tr>
				<td colspan="8">
					<div class="line">
						<div class="x9">
							<button type="button" class="button">
								<i class="icon-check-square-o"></i>
								全 选
							</button>
							<button type="button" class="button">
								<i class="icon-trash-o"></i>
								批量删除
							</button>
						</div>
						<div class="x3">
							<?php echo $page;?>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>