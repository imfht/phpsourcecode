<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Insert title here</title>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/metinfo.css" />
<script type="text/javascript" src="__PUBLIC__/js/jquery-1.8.0.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/cookie.js"></script>
<script type="text/javascript">
	var ua = false;
	var loading = "<img src='__PUBLIC__/images/loading.gif' />";
	$(function() {
		$('table tr').each(function(i, obj) {
			$(obj).find('td').eq(0).addClass("text");
			$(obj).find('td').eq(1).addClass("input");
			$(obj).find('td').eq(1).children('input').addClass('text nonull');
			$(obj).find('td').eq(1).children('textarea').addClass('textarea gen');
		});
		$('input[name=seid]').eq(0).attr('checked', true);
	})
	
	function updatePrice(keyid,seid){
		var obj = $('#keyprice_'+keyid);
		obj.html("<input type='text' id='input_"+keyid+"' class='ajaxinput' value=''>");
		$('#input_'+keyid).focus().blur(function(){
			var value = $(this).val();
			if(value.length < 1){
				alert('非法参数');
				$(this).focus();
				return false;
			}
			$.ajax({
				type:"POST",
				url:"<?php echo U('Keyword/ajax');?>",
				cache:false,
				data:{
					action : "updateprice",
					keyid:keyid,
					keyprice:value,
					seid:seid,
				},
				dataType:"json",
				beforeSend:function(){
					$('#keyprice_'+keyid).html(loading);
				},
				success:function(data) {
					if(data.status == 1){
						$('#keyprice_'+keyid).html("<span>"+data.data+"</span>");
					}else if(data.status == 0){
						alert(data.info);
						$('#keyprice_'+keyid).html("<span>"+data.info+"</span>");
					}	
				}
			});
		});
	}
	
	function getSort(keyname,keyid,weburl,webid,sortid,index) {
		var size = $('#lists tr :not(:first)').size() - 1;
		$.ajax({
			type:"POST",
			url:"<?php echo U('Keyword/ajax');?>",
			cache:false,
			data:{
				action : "getsort",
				keyid:keyid,
				keyname:keyname,
				weburl:weburl,
				webid:webid,
				sortid:sortid,
			},
			dataType:"json",
			beforeSend:function(){
				$('#keysort_'+keyid).html(loading);
			},
			success:function(data) {
				if(data.status == 1){
					$('#keysort_'+keyid).html(data.data);
				}else if(data.status == 0){
					//alert(data.info);
					$('#keysort_'+keyid).html('超时');
				}	
			},
			complete:function(msg){
				var next = parseInt(index)+1;
				//alert(size);
				if(next >= size){
					$('#updateall').attr('disabled',false);
					ua = false;
				}
				if(ua && next < size){
					updateall(next,true);
				}
			}
		});
	}
	
	function cost(keyid,seid,keyprice){
		var keysort = $('#keysort_'+keyid).text().trim();
		$.ajax({
			type:"POST",
			url:"<?php echo U('Keyword/ajax');?>",
			cache:false,
			data:{
				action : "cost",
				keyid:keyid,
				seid:seid,
				keyprice:keyprice,
				keysort:keysort,
			},
			dataType:"json",
			beforeSend:function(){
				//$('#keysort_'+keyid).html(loading);
			},
			success:function(data) {
				if(data.status == 1){
					$('#keyprice_'+keyid).next().html(data.data);
				}else if(data.status == 0){
					alert(data.info);
					//$('#keysort_'+keyid).html(data.info);
				}	
			},
			complete:function(){
				//$('#keysort_'+keyid).html();
			}
		});
	}
	function updateall(index){
		var i = index?index:0;
		ua = true;
		$('#lists tr:not(:first)').eq(i).find('td:last a').trigger('click');
		$('#updateall').attr('disabled',true);
	}
</script>
<style>
	.indent20{text-indent: 20px;}
	.ajaxinput{width:50px;}
</style>
</head>

<body>
	<div class="metinfotop">
		<div class="position">简体中文：网站后台 > <a href="<?php echo U('Keyword/lists');?>">关键词管理</a></div>
		<div class="return"><a href="">&lt;&lt;返回</a></div>
	</div>
	<div class="clear"></div>
	<table cellpadding="2" cellspacing="1" class="table" >
		<tr>
			<td colspan="8" class="centle" style=" height:20px; line-height:20px; font-weight:normal; padding-left:10px;">
				<div style="float:left;">
				<a href="<?php echo U('Keyword/add');?>">+添加关键词</a>
				<span style="font-weight:normal; color:#999; padding-left:10px;">排序数值越大越靠前</span>
				</div>
				<div class="formright">
				<form method="POST" style="position:relative; top:2px;" name="filterform" action="" target="_self">
				&nbsp;搜索引擎
				<select name="recommend" id="recommend" onChange="handle_form('filterform')">
					<option value="1" >百度</option>
					<option value="0" >谷歌</option>
				</select>
				&nbsp;网站
				<select name="top" id="shaix-top" onChange="handle_form('filterform')">
					<option value="1" >www.youku.com</option>
					<option value="0" >www.tudou.com</option>
				</select>
				</form>
				<form method="POST" name="search" action="" target="_self">
					<input name="title" type="text" class="text" id="searchtext" value="请输入关键字。" />				
					<input type="submit" name="searchsubmit" value="搜索" class="submitmi" />
				</form>
				<input type="button" value="一键查询本页" class="submitmi" id="updateall" onclick="updateall()" />
				</div>
			</td>
		</tr>
	</table>
	<table cellpadding="2" cellspacing="1" class="table" id="lists">
		<tr>
			<td class="list alignleft">关键词名称</td>
			<td class="list alignleft">所属网站</td>
			<td class="list">搜索引擎</td>
			<td class="list">排名(100以内)</td>
			<td class="list">价格</td>
			<td class="list">扣款</td>
			<td class="list">日期</td>
			<td class="list">指数</td>
			<td class="list">相关数</td>
			<td class="list"><?php if($_REQUEST["keyid"] == ''): ?>操作<?php endif; ?></td>
		</tr>
		<?php if(is_array($keylist)): $i = 0; $__LIST__ = $keylist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr bgcolor="#ECECEC">
			<td class="list-text alignleft indent20"><a href="<?php echo U('Keyword/lists?keyid='); echo ($vo["keyid"]); ?>"><?php echo ($vo["keyname"]); ?></a></td>
			<td class="list-text alignleft indent20"><a href="<?php echo ($vo["weburl"]); ?>" target="_blank" >访问</a>&nbsp;&nbsp;<?php echo ($vo["webname"]); ?></td>
			<td class="list-text"><?php echo (($vo["cnname"])?($vo["cnname"]):百度); ?></td>
			<td class="list-text" id="keysort_<?php echo ($vo["keyid"]); ?>"><?php echo (($vo["keysort"])?($vo["keysort"]):未更新); ?></td>
			<td class="list-text" id="keyprice_<?php echo ($vo["keyid"]); ?>" ondblclick="updatePrice(<?php echo ($vo["keyid"]); ?>,<?php echo (($vo["seid"])?($vo["seid"]):1); ?>)"><span title="双击更改"><?php echo (($vo["keyprice"])?($vo["keyprice"]):'双击填写'); ?></span></td>
			<td class="list-text" id="cost_<?php echo ($vo["sortid"]); ?>"><?php if($vo["cost"] == ''): ?><a onclick="cost(<?php echo ($vo["keyid"]); ?>,<?php echo ($vo["seid"]); ?>,<?php echo ($vo["keyprice"]); ?>)">扣款</a><?php else: echo ($vo["cost"]); endif; ?></td>
			<td class="list-text"><?php echo (date("Y-m-d",($vo["sortdate"])?($vo["sortdate"]):time())); ?></td>
			<td class="list-text"><?php echo (($vo["keyindex"])?($vo["keyindex"]):0); ?></td>
			<td class="list-text"><?php echo (($vo["keyabout"])?($vo["keyabout"]):0); ?></td>
			<td class="list-text"><?php if($_REQUEST["keyid"] == ''): ?><a onclick="getSort('<?php echo ($vo["keyname"]); ?>',<?php echo ($vo["keyid"]); ?>,'<?php echo ($vo["weburl"]); ?>',<?php echo ($vo["webid"]); ?>,<?php echo (($vo["sortid"])?($vo["sortid"]):0); ?>,<?php echo ($i-1); ?>)">更新</a><?php endif; ?></td>
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		<tr bgcolor="#ECECEC"><td colspan="10" style="text-align:center"><?php echo ($page); ?></td></tr>
	</table>
</body>
</html>