<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>模板列表</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
</head>
<body>
	<div class="bg border margin-little-bottom padding-left padding-top padding-bottom">
	<button type="button" onClick="location.href='<?php echo U('index');?>'" class="button bg">
		<i class="icon-th-list"></i>
		模板列表
	</button>
</div>
<div class="container-layout" style="padding:10px 0px 0px 0px">
	<div class="line">
		        <?php
        //初始化
        $hd['list']['vo'] = array(
            'first' => false,
            'last'  => false,
            'total' => 0,
            'index' => 0
        );
        if (empty($style)) {
            echo '';
        } else {
            $listId = 0;
            $listShowNum=0;
            $listNextId=0;
            foreach ($style as $vo) {
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
                $hd['list'][vo]['last']= (count($style)-1 <= $listId);
                //总数
                $hd['list'][vo]['total']++;
                //增加数
                $listId++;
                $listShowNum++;
                $listNextId+=1
                ?>
			<div style="width:255px;" class="x2 fadein-top border padding-top padding-left margin-right    <?php if($vo['current']==1){ ?>bg<?php } ?>">
				<img src="<?php echo $vo['image'];?>" width="230" height="260" class="img-border radius-small text-center" />
				<h3 class="margin-little-bottom"><?php echo $vo['name'];?></h3>
				<p class="margin-left" style="margin-bottom:0px;">作者：<?php echo $vo['author'];?></p>
				<p class="margin-left" style="margin-bottom:0px;">E-mail：<?php echo $vo['email'];?></p>
				<p class="margin-left" style="margin-bottom:0px;">目录：<?php echo $vo['filename'];?></p>
				    <?php if($vo['current']==1){ ?>
					<button type="button" class="button bg-main margin-bottom margin-left">
						<span class="icon-spinner rotate"></span>
						正在使用
					</button>
					<?php }else{ ?>
					<button type="button" class="button bg-sub margin-bottom margin-left" onClick="location.href='<?php echo U('style',array('dirName'=> $vo['filename']));?>'">
						<span class="icon-spinner rotate"></span>
						点击使用
					</button>
				<?php } ?>
			</div>
		<?php }}?>
	</div>
</div>
</body>
</html>