<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>频道栏目列表</title>
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
			频道列表
		</button>
		<button type="button" onClick="location.href='<?php echo U('add');?>'" class="button">
			<i class="icon-edit"></i>
			添加顶级频道
		</button>
		<button type="button" onClick="location.href='<?php echo U('updateCache');?>'" class="button">
			<i class="icon-eraser"></i>
			更新频道缓存
		</button>
	</div>
	<form action="<?php echo U('updateOrder');?>" method="POST">
		<table class="table table-bordered table-hover table-condensed table-responsive">
			<tr class="bg">
				<td width="80" align="center"><input type="checkbox" class="select_all" style="width:50px"></td>
				<td width="80">cid</td>
				<td width="80">排序</td>
				<td>频道名称</td>
				<td>类型</td>
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
        if (empty($cate)) {
            echo '';
        } else {
            $listId = 0;
            $listShowNum=0;
            $listNextId=0;
            foreach ($cate as $vo) {
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
                $hd['list'][vo]['last']= (count($cate)-1 <= $listId);
                //总数
                $hd['list'][vo]['total']++;
                //增加数
                $listId++;
                $listShowNum++;
                $listNextId+=1
                ?>
				<tr>
					<td align="center"><input type="checkbox" name="cid[]" value="<?php echo $vo['cid'];?>"/></td>
					<td><?php echo $vo['cid'];?></td>
					<td><input type="text" value="<?php echo $vo['catorder'];?>" style="width:80px;" name="list_order[<?php echo $vo['cid'];?>]"/></td>
					<td>
						    <?php if($vo['pid']==0){ ?>
							<strong><?php echo $vo['_name'];?></strong>
							<?php }else{ ?>
							<?php echo $vo['_name'];?>
						<?php } ?>
					</td>
					<td><?php echo $vo['cat_type_name'];?></td>
					<td width="300">
						<a href="<?php echo U('Index/Category/index',array('mid'=> $vo['mid'],'cid'=>$vo['cid']));?>" target="_blank">
							访问
						</a>
						<span>|</span>
						<a href="<?php echo U('add',array('pid'=> $vo['cid'],'mid'=>$vo['mid']));?>">
							添加子栏目
						</a>
		                <span>|</span>
		                <a href="<?php echo U('edit',array('cid'=>$vo['cid']));?>">
		                	<i class="icon-edit"></i>
		                    修改
		                </a>
		                <span>|</span>
		                <a href="javascript:;" class="dialogs" data-toggle="click" data-target="#mydialog<?php echo $vo['cid'];?>" data-mask="1" data-width="20%">
		                	<i class="icon-trash-o"></i>
		                    删除
		                </a>
		                <!-- 删除确认框 Start -->
						<div id="mydialog<?php echo $vo['cid'];?>">
							<div class="dialog radius-none bouncein">
								<div class="dialog-head">
									<span class="close icon-times"></span>
									<strong>删除确认对话框</strong>
								</div>
								<div class="dialog-body">
									是否确认删除该频道？
								</div>
								<div class="dialog-foot">
									<button class="button radius-none bg-sub" onClick="location.href='<?php echo U('del',array('cid'=>$vo['cid']));?>'">
										<i class="icon-check-square-o radius-none"></i>
										确 认
									</button>
									<button class="button radius-none dialog-close">
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
				<td colspan="7">
					<button type="button" onClick="select_all(0)" value='反选' class="button bg border">
						<i class="icon-arrows"></i>
						全 选
					</button>
					<button type="submit" class="button bg border">
						<i class="icon-arrows-v"></i>
						排 序
					</button>
				</td>
			</tr>
		</table>
	</form>
</div>
<script>
//全选
$("input.select_all").click(function ()
{
    $("[type='checkbox']").attr("checked", $(this).attr('checked') == 'checked');
})
//全选复选框
function select_all(state)
{
    if (state == 1)
    {
        $("[type='checkbox']").attr("checked", state);
    }
    else
    {
        $("[type='checkbox']").attr("checked", function ()
        {
            return !$(this).attr('checked')
        });
    }
}
</script>
</body>
</html>