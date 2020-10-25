<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php?m=system&s=managechannel">操作员后台</a>→ <a href="./index.php?m=system&s=managehtml">html管理</a>→ <a href="./?m=system&s=managehtml&a=help" title="在线帮助">在线帮助</a> </div>
<table width="100%" border="0" cellpadding="4" cellspacing="1" class="admintb">
    <tr class="adtbtitle">
      <td width="70%">
	  <h3>静态Html缓存管理器</h3>
      <a href="javascript:history.back(1);" title="返回静态Html缓存管理器首页" class="creatbt">返回管理</a>
      <input type="submit" name="submit" value="点击一键生成全部静态" class="savebt" style="width:150px;" id="creatHTML">
	  </td>
	</tr>
</table>
<style>
.space {width: 98%;height: 11px;line-height: 11px;padding: 1px;border: 1px solid #CCC;float: left; margin:4px 0 0 12px; display:inline;}
.space #using { width:0;height:11px; line-height:11px; background:-webkit-gradient(linear, 0 100%, 0 0, from(#669900), to(#66CC00)); background:-moz-linear-gradient(top, #66CC00, #669900);  filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#66CC00', endColorstr='#669900');
-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#66CC00', endColorstr='#669900')"; }
.jindu { margin:5px 0 0 12px; float:left; display:inline;}
.htmltip { width:97%;height:300px; overflow:auto; float:left; margin:5px 0 0 12px; line-height:30px; display:inline; border:1px solid #ccc; padding:0 0 0 10px;}
</style>
<br />
<div class="space">
	<div id="using"></div>
</div>
<p id="jindu" class="jindu">生成进度：0%</p>
<br />
<div id="content" class="htmltip"></div>
<?php 
$sql = 'SELECT * FROM '.TB_PREFIX.'menu';
$rs = $db->get_results($sql);
$cs=0;
foreach($rs as $o)
{
	$cs++;
	//生成栏目下的相关数据
	$temptype = array('list','list','jobs','guestbook','picture','poll','product','video');
	if(in_array($o->type,$temptype))
	{
		$sql2 = 'SELECT * FROM '.TB_PREFIX.$o->type.' WHERE channelId ='.$o->id;
		$rsone = $db->get_results($sql2);
		if($rsone)
		{
			
			if($o->type =='poll')
			$o->type = 'product';
			eval('$syscount = '.$o->type.'Count;');
			if($o->type == 'linkers')
			$syscount = 100;
			if(count($rsone)>$syscount)
			{
					$counts = ceil(count($rsone)/$syscount); //取整
					for($i=1;$i<=$counts;$i++)
					{
						
						$cs++;
					}
			}
			foreach($rsone as $v)
			{
				
				$cs++;
			}
		}
	}
}
?>
<script language="javascript" type="text/javascript">
var i=0;
$(document).ready(function(){
	$('#creatHTML').click(function(){gohtml()});
})
function gohtml()
{
	$.ajax({
		type:"POST",
		url:"index.php?m=system&s=managehtml&a=htmlCount",
		data:"cs="+i,
		timeout:"5000",
		async  :true,
		cache:false,                                
		success: function(html){
			i++;
			$('#content').append(html.split('+')[0]);
			var Obj=document.getElementById("content");
			Obj.scrollTop=Obj.scrollHeight;
			if(parseInt((100/<?php echo $cs?>)*i)<=100)
			{
				$("#using").css({width:(100/<?php echo $cs?>)*i+"%"});
				$("#jindu").html("生成进度："+parseInt((100/<?php echo $cs?>)*i)+"%");
			}
			if(i<=<?php echo $cs?>)
				gohtml();
		},
		error:function(){	
		}
	});
}
</script> 