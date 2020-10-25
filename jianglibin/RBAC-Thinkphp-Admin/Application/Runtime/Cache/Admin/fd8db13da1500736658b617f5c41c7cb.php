<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <title>
        会员管理
    </title>
    <link rel="stylesheet" type="text/css" href="/ar/Public/static/h-ui/css/H-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="/ar/Public/static/h-ui.admin/css/H-ui.admin.css" />
    <link rel="stylesheet" type="text/css" href="/ar/Public/static/h-ui.admin/skin/default/skin.css" id="skin" />
    <link rel="stylesheet" type="text/css" href="/ar/Public/static/h-ui.admin/css/style.css" />
    <link rel="stylesheet" type="text/css" href="/ar/Public/lib/Hui-iconfont/1.0.7/iconfont.css" />
    <link rel="stylesheet" type="text/css" href="/ar/Public/lib/icheck/icheck.css" />

    
    <script type="text/javascript" src="/ar/Public/lib/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="/ar/Public/js/jquery.icheck.min.js"></script>

    <script type="text/javascript" src="/ar/Public/lib/layer/2.1/layer.js"></script>
    <script type="text/javascript" src="/ar/Public/static/h-ui/js/H-ui.js"></script>
    <script type="text/javascript" src="/ar/Public/static/h-ui.admin/js/H-ui.admin.js"></script>
    <script type="text/javascript" src="/ar/Public/laydate/laydate.dev.js"></script>
    <script type="text/javascript" src="/ar/Public/lib/layer/2.1/layer.js"></script>
    <script type="text/javascript" src="/ar/Public/lib/laypage/1.2/laypage.js"></script>
    <link rel="stylesheet" type="text/css" href="/ar/Public/ichartjs1.2/samples/css/demo.css" />
    <script type="text/javascript" src="/ar/Public/ichartjs1.2/ichart.1.2.min.js"></script> 
    
</head>


  <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 会员管理 <span class="c-gray en">&gt;</span> 会员列表 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
    <form action="<?php echo U('User/userList');?>" method="get">
	<div class="text-c"> 注册时间：
		<input type="text" onClick="laydate()" placeholder="开始时间" name="start_time" class="input-text Wdate" style="width:150px;">
		-
		<input type="text" onClick="laydate()" placeholder="结束时间" name="end_time" class="input-text Wdate" style="width:150px;">
		<input type="text" class="input-text" style="width:250px" placeholder="输入会员ID、用户名、电话" id="" name="user">
		<button type="submit" class="btn btn-success radius"><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
	</div>
        </form>
	<div class="mt-20">
	<table class="table table-border table-bordered table-hover table-bg table-sort">
		<thead>
			<tr class="text-c">
				<th width="25"><input type="checkbox" name="" value=""></th>				
				<th width="100">用户名</th>
				<th width="40">性别</th>
				<th width="90">手机</th>
				<th width="150">情感状态</th>
                                <th width="150">生日</th>
				<th width="">居住地</th>
				<th width="130">注册时间</th>
				<th width="70">用户经验值</th>
			</tr>
		</thead>
		<tbody>
                    <?php if(is_array($user)): $i = 0; $__LIST__ = $user;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="text-c">
				<td><input type="checkbox" value="" name=""></td>				
				<td><u style="cursor:pointer" class="text-primary" onclick="member_show('会员详情','<?php echo U('User/userShow',array('uid'=>$vo['uid']));?>','<?php echo ($vo["uid"]); ?>','360','600')"><?php echo ($vo["username"]); ?></u></td>
                                <td>
                                    <?php switch($vo["gender"]): case "0": ?>保密<?php break;?>
                                        <?php case "1": ?>男<?php break;?>
                                        <?php case "2": ?>女<?php break; endswitch;?>                                   
                                </td>
				<td><?php echo ($vo["mobile"]); ?></td>
				<td>
                                    <?php switch($vo["emotion_status"]): case "0": ?>保密<?php break;?>
                                        <?php case "1": ?>单身<?php break;?>
                                        <?php case "2": ?>恋爱中<?php break;?>
                                        <?php case "2": ?>已婚<?php break; endswitch;?>
                                </td>
                                <td class="text-l"><?php echo ($vo["birthyear"]); ?>-<?php echo ($vo["birthmonth"]); ?>-<?php echo ($vo["birthday"]); ?></td>
				<td class="text-l"><?php echo ($vo["resideprovince"]); echo ($vo["residecity"]); echo ($vo["residearea"]); ?></td>
				<td><?php echo (date("Y-m-d H:i:s",$vo["add_dateline"])); ?></td>
				<td class="td-status"><span class="label label-success radius">&nbsp;&nbsp;<?php echo ($vo["experience"]); ?>&nbsp;&nbsp;</span></td>				
			</tr><?php endforeach; endif; else: echo "" ;endif; ?>                               
		</tbody>
	</table>
            <div class="pager"><?php echo ($page); ?></div>
	</div>
</div>


</html>


<script type="text/javascript">
/*用户-查看*/
function member_show(title,url,id,w,h){
	layer_show(title,url,w,h);
}
</script>