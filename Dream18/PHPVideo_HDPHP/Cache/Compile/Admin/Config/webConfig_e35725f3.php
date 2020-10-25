<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>配置项列表</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
</head>
<body>
	<div class="container-layout">
		<div class="margin-top margin-bottom padding-left padding-top padding-bottom">
		<!-- tab标签 Box -->
		<div class="tab">
			<!-- tab标签 Start -->
			<div class="tab-head bg border">
				<ul class="tab-nav padding-top">
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
						<li    <?php if($vo['select']==1){ ?>class="active"<?php } ?>>
						<a href="#<?php echo $vo['cname'];?>" style="outline:none;"><?php echo $vo['ctitle'];?></a>
					</li>
					<?php }}?>
				</ul>
			</div>
			<!-- tab标签 End -->

			<!-- Tab标签 Content -->
			<div class="tab-content" >
				<form method="POST" id="form">
					        <?php
        //初始化
        $hd['list']['c'] = array(
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
            foreach ($data as $c) {
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
                $hd['list'][c]['index']++;
                //第1个值
                $hd['list'][c]['first']=($listId == 0);
                //最后一个值
                $hd['list'][c]['last']= (count($data)-1 <= $listId);
                //总数
                $hd['list'][c]['total']++;
                //增加数
                $listId++;
                $listShowNum++;
                $listNextId+=1
                ?>
						<div class="tab-body" style="padding-top:1px;">
							<div class="tab-panel    <?php if($c['select']==1){ ?>active<?php } ?>" id="<?php echo $c['cname'];?>">
								<!-- table Start -->
								<table class="table table-bordered table-hover table-condensed">
									<tr>
										<td width="50">排序</td>
										<td width="150">标题</td>
										<td width="300">配置值</td>
										<td width="300">变量名</td>
										<td>描述</td>
										<td width="150">操作</td>
									</tr>
									        <?php
        //初始化
        $hd['list']['vo'] = array(
            'first' => false,
            'last'  => false,
            'total' => 0,
            'index' => 0
        );
        if (empty($c['_config'])) {
            echo '';
        } else {
            $listId = 0;
            $listShowNum=0;
            $listNextId=0;
            foreach ($c['_config'] as $vo) {
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
                $hd['list'][vo]['last']= (count($c['_config'])-1 <= $listId);
                //总数
                $hd['list'][vo]['total']++;
                //增加数
                $listId++;
                $listShowNum++;
                $listNextId+=1
                ?>
										<tr>
											<td><input type="text" name="sort" value="<?php echo $vo['sort'];?>" style="width:50px" /></td>
											<td><?php echo $vo['title'];?></td>
											<td><?php echo $vo['_html'];?></td>
											<td>{$hd.config.<?php echo $vo['name'];?>}</td>
											<td><?php echo $vo['message'];?></td>
											<td>
												<a href="javascript:;" class="dialogs" data-toggle="click" data-target="#mydialog<?php echo $vo['id'];?>" data-mask="1" data-width="20%">
													<span class="icon-times"></span>
													删除
												</a>
												<!-- 删除确认框 Start -->
												<div id="mydialog<?php echo $vo['id'];?>">
													<div class="dialog radius-none bouncein">
														<div class="dialog-head">
															<span class="close icon-times"></span>
															<strong>删除确认对话框</strong>
														</div>
														<div class="dialog-body">
															<span class="icon-bitbucket"></span>
															确定删除该数据？
														</div>
														<div class="dialog-foot">
															<button class="button radius-none bg-sub" onClick="location.href='<?php echo U('del',array('id'=> $vo['id']));?>'">
																<i class="icon-check-square-o"></i>
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
										<td class="text-center" colspan="7">
											<button type="button" id="editButton" class="button bg-sub radius-none">
												<i class="icon-check-square-o"></i>
												提交保存
											</button>
											<button type="button" id="delbutton" class="button radius-none" onClick="location.href='<?php echo U('add');?>'">
												<i class="icon-edit"></i>
												添加配置
											</button>
										</td>
									</tr>
								</table>
								<!-- table End -->
							</div>
						</div>
					<?php }}?>
				</form>
				<script>
					$('#editButton').click(function(){
						$.post("<?php echo U('webConfig');?>", $('#form').serialize(), function(data){
							if (data.status == true) {
								alert(data.message);
							} else {
								alert(data.message);
							}
						}, 'json')
						return false;
					});
				</script>
			</div>
			<!-- Tab标签 Content End -->
		</div>
		<!-- tab标签 Box End -->
	</div>
</div>
<!-- 标签页切换 -->
<script type="text/javascript">
$('.tab-content').find('.tab-body').hide();
$('.tab-content').find('.tab-body').eq(0).show();
$('.tab-nav').find('li').click(function(){
	var index = $(this).index();
	$('.tab-content').find('.tab-body').hide();
	$('.tab-content').find('.tab-body').eq(index).show();

})
</script>
</body>
</html>