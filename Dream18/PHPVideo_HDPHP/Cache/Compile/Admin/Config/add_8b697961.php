<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>配置项添加</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
</head>
<body>
	<div class="bg text-center border margin-little-bottom padding-left padding-top padding-bottom">
	配置项添加
</div>
<form action="http://localhost/PHPUnion/index.php?m=Admin&c=Config&a=add" method="POST">
	<table class="table table-bordered table-hover table-condensed">
		<tr>
			<td align="right" width="300">标题(中文)</td>
			<td width="300"><input type="text" name="title" /></td>
			<td>提示信息</td>
		</tr>
		<tr>
			<td align="right" >变量名(标识)</td>
			<td ><input type="text" name="name" /></td>
			<td>提示信息</td>
		</tr>
		<tr>
			<td align="right" >配置值</td>
			<td><input type="text" name="value" /></td>
			<td>提示信息</td>
		</tr>
		<tr>
			<td align="right">是否系统</td>
			<td>
				<label><input type="radio" name="system" value="2" checked />普通组</label>
				<label><input type="radio" name="system" value="1" />系统组</label>
			</td>
		</tr>
		<tr>
			<td align="right" >配置组</td>
			<td><select name="cid">
				        <?php
        //初始化
        $hd['list']['vo'] = array(
            'first' => false,
            'last'  => false,
            'total' => 0,
            'index' => 0
        );
        if (empty($configGroup)) {
            echo '';
        } else {
            $listId = 0;
            $listShowNum=0;
            $listNextId=0;
            foreach ($configGroup as $vo) {
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
                $hd['list'][vo]['last']= (count($configGroup)-1 <= $listId);
                //总数
                $hd['list'][vo]['total']++;
                //增加数
                $listId++;
                $listShowNum++;
                $listNextId+=1
                ?>
					<option value="<?php echo $vo['cid'];?>"><?php echo $vo['ctitle'];?></option>
				<?php }}?>
			</select></td>
			<td>提示信息</td>
		</tr>
		<tr>
			<td align="right" >类 型</td>
			<td>
				<label><input type="radio" name="type" value="text" checked />text</label> |
				<label><input type="radio" name="type" value="radio" />radio</label> |
				<label><input type="radio" name="type" value="textarea" />textarea</label> |
				<label><input type="radio" name="type" value="select" />select</label>
			</td>
			<td>提示信息</td>
		</tr>
		<tr>
			<td align="right" >参 数</td>
			<td><input type="text" name="info" /></td>
			<td>如：1|开启,0|关闭</td>
		</tr>
		<tr>
			<td align="right" >提示信息</td>
			<td><input type="text" name="message" /></td>
			<td>提示信息</td>
		</tr>
		<tr>
			<td align="right" >排序</td>
			<td><input type="text" name="sort" value="0" /></td>
			<td>提示信息</td>
		</tr>
		<tr>
			<td></td>
			<td colspan="2">
				<button type="submit" class="button bg-sub radius-none">
					<i class="icon-check-square-o"></i>
					提交保存
				</button>
				<button type="button" class="button radius-none" onClick="location.href='<?php echo U('webConfig');?>'">
					<i class="icon-th-list"></i>
					配置列表
				</button>
			</td>
		</tr>
	</table>
</form>
</body>
</html>