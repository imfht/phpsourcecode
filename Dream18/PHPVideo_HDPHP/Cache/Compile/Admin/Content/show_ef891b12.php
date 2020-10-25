<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>添加视频列表</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
</head>
<body>
	<div class="container-layout">
	<div class="bg border margin-top margin-little-bottom padding-left padding-top padding-bottom">
		<button type="button" onClick="location.href='<?php echo U('show',array('cid'=> $hd['get']['cid']));?>'" class="button bg">
			<i class="icon-th-list"></i>
			视频列表
		</button>
		<button type="button" onClick="location.href='<?php echo U('add',array('cid'=> $hd['get']['cid']));?>'" class="button">
			<i class="icon-edit"></i>
			添加视频
		</button>
		<button type="button" onClick="location.href='<?php echo U('show',array('cid'=> $hd['get']['cid'],'content_status'=> 0));?>'" class="button">
			<i class="icon-eraser"></i>
			未 审 核
		</button>
	</div>
</div>
<div class="container-layout">
	<table class="table table-bordered table-hover table-condensed table-responsive">
		<tr class="bg">
			<td><label><input type="checkbox"></label></td>
			<td>aid</td>
			<td>排序</td>
			<td>标题</td>
			<td>所属频道</td>
			<td>状态</td>
			<td>发布者</td>
			<td>发布时间</td>
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
        if (empty($field)) {
            echo '';
        } else {
            $listId = 0;
            $listShowNum=0;
            $listNextId=0;
            foreach ($field as $vo) {
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
                $hd['list'][vo]['last']= (count($field)-1 <= $listId);
                //总数
                $hd['list'][vo]['total']++;
                //增加数
                $listId++;
                $listShowNum++;
                $listNextId+=1
                ?>
			<tr>
				<td><label><input type="checkbox" name="aid" value="<?php echo $vo['aid'];?>"/></label></td>
				<td><?php echo $vo['aid'];?></td>
				<td><?php echo $vo['arc_sort'];?></td>
				<td><?php echo $vo['title'];?></td>
				<td><?php echo $vo['_cate'];?></td>
				<td>
					    <?php if($vo['content_status']==1){ ?>
						已审核
					<?php }else if($vo['content_status']==2){ ?>
						草稿
						<?php }else{ ?>
						<div style="color:red;">未审核</div>
					<?php } ?>
				</td>
				<td><?php echo $vo['_author'];?></td>
				<td><?php echo date('Y-m-d H:i:s',$vo['addtime']);?></td>
				<td width="350">
					    <?php if($vo['content_status']==1){ ?>
						<a href="" target="_blank">
							<span class="icon-toggle-right (alias)"></span>
							访问播放
						</a> |
					<?php } ?>
					    <?php if($vo['content_status']==0){ ?>
						<a href="<?php echo U('audit',array('aid'=> $vo['aid'], 'status'=> 1));?>">
							<span class="icon-check-circle-o"></span>
							点击审核
						</a> |
						<?php }else if($vo['content_status']==1){ ?>
						<a href="<?php echo U('audit',array('aid'=> $vo['aid'], 'status'=> 0));?>">
							<i class="icon-times"></i>
							取消审核
						</a> |
					<?php } ?>
					<a href="<?php echo U('edit',array('cid'=> $hd['get']['cid'], 'aid'=> $vo['aid']));?>">
						<i class="icon-edit"></i>
						修 改
					</a> |	
					<a href="javascript:;" class="dialogs" data-toggle="click" data-target="#mydialog<?php echo $vo['aid'];?>" data-mask="1" data-width="20%"     <?php if($vo['system']==1){ ?>disabled="disabled"<?php } ?>>
						<i class="icon-trash-o"></i>
						删 除
					</a>
					<!-- 删除确认框 Start -->
					<div id="mydialog<?php echo $vo['aid'];?>">
						<div class="dialog bouncein">
							<div class="dialog-head">
								<span class="close icon-times"></span>
								<strong>删除确认对话框</strong>
							</div>
							<div class="dialog-body">
								是否确认删除该视频数据？
							</div>
							<div class="dialog-foot">
								<button class="button bg-sub radius-none" onClick="location.href='<?php echo U('del',array('aid'=> $vo['aid']));?>'">
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
			<td colspan="9">
				<div class="line">
					<div class="x9">
						<button type="button" class="button bg">
							<span class="icon-arrows"></span>
							全 选
						</button>
						    <?php if($vo['content_status']==0){ ?>
							<button type="button" class="button bg">
								<span class="icon-check-circle"></span>
								批量审核
							</button>
							<?php }else{ ?>
							<button type="button" class="button bg">
								<span class="icon-ban"></span>
								批量取消审核
							</button>
						<?php } ?>
						<button type="button" class="button bg">
							<span class="icon-list-ul"></span>
							更改排序
						</button>
						<button type="button" class="button bg">
							<span class="icon-reply"></span>
							批量删除
						</button>
					</div>
					<div class="x3"><?php echo $page;?></div>
				</div>
			</td>
		</tr>
	</table>
</div>
</body>
</html>