<?php include 'application/views/admin/public/head.php'?>
<style type="text/css">
<!--
body {
	margin-left: 3px;
	margin-top: 0px;
	margin-right: 3px;
	margin-bottom: 0px;
}
.STYLE1 {
	color: #e1e2e3;
	font-size: 12px;
}
.STYLE6 {color: #000000; font-size: 12; }
.STYLE10 {color: #000000; font-size: 12px; }
.STYLE19 {
	color: #344b50;
	font-size: 12px;
}
.STYLE21 {
	font-size: 12px;
	color: #3b6375;
}
.STYLE22 {
	font-size: 12px;
	color: #295568;
}
-->
</style>
</head>
<body>
	<table width="100%" border="0" class="table_list" cellpadding="0" cellspacing="1" bgcolor="#a8c7ce">
    <caption>参加活动查看</caption>
	<tr>
		<td width="50%" align="center" style="color:red;">活动名称</td>
		<td width="20%" align="center" style="color:red;">报名时间</td>
		<td width="10%" align="center" style="color:red;">活动报名人数</td>
		<td width="20%" align="center" style="color:red;">管理</td>
	</tr>
	<?php foreach($baoming as $baoming):?>
	<tr>
		<td width="50%" align="center"><?php 
			$aid = $baoming['aid'];
			$conn = mysql_connect("localhost","root",$this->db->password);
			mysql_select_db("zj");
			mysql_query("set names utf8");
			$query = mysql_query("select title from activity where id='$aid'");
			$row = mysql_fetch_row($query);
			echo $row[0];
		?></td>
		<td width="20%" align="center"><?php echo $baoming['baomingtime'];?></td>
		<td width="10%" align="center">
		<?php 
			$query2 = mysql_query("select count(*) from baoming where aid='$aid'");
			$row = mysql_fetch_row($query2);
			echo $row[0];
			mysql_close($conn);
		?>
		</td>
		<td width="20%" align="center">
		 <a class="blues" href="<?php echo site_url('admin/baomingLook/'.$baoming['aid']);?>">查看该活动的报名情况</a>
		</td>
	</tr>
	<?php endforeach;?>
</div>
</body>
</html>
