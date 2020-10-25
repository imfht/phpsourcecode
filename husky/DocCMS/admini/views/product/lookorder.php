<style type="text/css">
<!--
.wrapL{
width:19.8%; float:left;
}
.wrapR{
width:80%; float:left;
}
.menuText a{
text-decoration:none;
}
.menuText a:hover{
text-decoration:none;
}
.orderde td{ line-height:35px; border-right:1px solid #fff;border-bottom:1px solid #fff; font-size:12px;}
.orderdetitle{ background:#ccc;}
.orderdelist{ background:#ededed;}
.orderde a{ color:#03c; margin-right:5px;}
-->
</style>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb"> 
  <tr class="adtbtitle">
    <td><h3>订单明细：</h3><a href="javascript:history.back(1)" class="creatbt">返回</a> </td>
    <td align="right"><span id="msg" style="color:#FF0000"></span></td>
  </tr>
  <tr>
    <td colspan="3" bgcolor="#FFFFFF">
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="orderde">
	  <tr align="center" class="orderdetitle">
	    <td width="40">序号</td>
	    <td width="80">姓名</td>
	    <td width="120">联系电话</td>
	    <td width="120">日期</td>
	    <td width="35%">地址</td>
	    <td width="100">订单状态</td> 
	    <td width="80">操作</td>
	  </tr>
 
  <?php
		global $db,$request;
		$sql="SELECT * FROM `".TB_PREFIX."product_order` WHERE id=".intval($request['id']);
		$v=$db->get_row($sql, ARRAY_A);
		if(!empty($v))
		{
			$remark = $v['remark'];
			?>
			 <tr align="center" class="orderdelist">
			    <td><?php echo $v['id'];?></td>
			    <td><?php echo $v['customer'];?></td>
			    <td><?php echo $v['m_tel'];?></td>    
			    <td><?php echo $v['dtTime'];?></td>
			    <td><?php echo $v['address'];?></td>
			    <td><?php echo !$v['stauts']?'未处理':($v['stauts']==1?'已处理':'已取消');?></td>
			    <td><a href="index.php?a=cancelorder&p=<?php echo $request['p'];?>&id=<?php echo $v['id'];?>">取消</a>&nbsp;&nbsp;
					<a href="index.php?a=dealorder&p=<?php echo $request['p'];?>&id=<?php echo $v['id'];?>">处理</a>&nbsp;&nbsp; 
					<a href="index.php?a=delorder&p=<?php echo $request['p'];?>&id=<?php echo $v['id'];?>">删除</a></td>
			  </tr>
			<?php
		}
		else
		{
			echo '暂无。';
		}
		?>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="orderde">
  <tr align="center" class="orderdetitle">
    <td width="10%">序号</td>
    <td width="20%">缩略图</td>
    <td width="40%">产品</td>
    <td width="15%">订购数量</td>
    <td width="15%">查看产品</td>
  </tr>
 
  <?php
		$product=unserialize($v['orederinfo']);
		if(!empty($product))
		{
			foreach($product as $k=>$v){
				$num+=$v['num'];
			?>
	<tr align="center" class="orderdelist">
    <td><?php echo $v['id'];?></td>
    <td><img src="<?php echo ispic($v['smallPic']);?>" width="30" height="30" /></td>
    <td><?php echo $v['title'];?></td>    
    <td><?php echo $v['num'];?></td>
    <td><a href="/index.php?p=<?php echo $v['channelId'];?>&a=view&r=<?php echo $v['id'];?>" target="_blank">浏览</a></td>
  </tr>
  	
			<?php	
			}
		}
		else
		{
			echo '暂无。';
		}
		?>
	<tr align="center" class="orderdelist">
    <td colspan="2" > 共<?php echo $num; ?>件商品</td>
	 <td  bgcolor="#FFFFFF">处理内容（订单号，订单状态等）： <form action="index.php?a=dealorder&p=<?php echo $request['p'];?>&id=<?php echo $request['id'];?>" method="post"><textarea style="width:500px; height:100px" name="remark"><?php echo $remark;?></textarea> <input type="submit" value=" 保 存 "></form></td>
	</tr>
</table>
    	
	  </td>
  </tr>
</table>
