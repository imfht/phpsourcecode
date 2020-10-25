<?php
session_start();
require_once('../include/common.inc.php');
require_once('../include/common.func.php');
include_once ("../common/admin.session.php"); 
?>
<!DOCTYPE HTML>
<html>
    <head>
		<title>后台管理-订单列表</title>
		<meta http-equiv=content-type content="text/html; charset=utf8">
		<script src="/assets/jquery/jquery-1.11.1.min.js"></script>
		<link href="/assets/bootstrap/bootstrap.min.css" rel="stylesheet">
		<script src="/assets/bootstrap/bootstrap.min.js"></script>
		<link href="/assets/datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
		<script src="/assets/datetimepicker/js/bootstrap-datetimepicker.min.js" charset="UTF-8"></script>
		<script src="/assets/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
		<!--[if lt IE 9]>
			<script src="/assets/html5shiv/html5shiv.min.js"></script>
			<script src="/assets/respond/respond.min.js"></script>
		<![endif]-->
		<link href="/assets/Flat-UI/css/flat-ui.css" rel="stylesheet">
		<link href="/public/css/hongwei.css" rel="stylesheet"/>
		<link href="/public/css/website.css" rel="stylesheet"/>
	</head>
	<body class="pdt-70">
		<!-- 菜单 -->
		<?php
		require_once "admin_menu.php";
 ?>
		
		<!-- 主体 -->
		<div class="table-responsive l-98b">  
			<div class="panel panel-default">
				<div class="panel-heading">
					后台管理 &gt;&gt; 订单列表
				</div>
				<div class="panel-body ">
					<form action="" method="get" class="form-inline">
						<!--search start-->
					 	<div class="form-group">
					    	<label class="sr-only" for="ordersn">订单号</label>
					    	<input type="text" class="form-control" id="ordersn" name="ordersn" placeholder="请输入订单号" value="<?=$_REQUEST['ordersn']?>">
					  	</div>
					  	<div class="form-group">
					    	<label class="sr-only" for="truename">收货人</label>
					    	<input type="text" class="form-control" id="truename" name="truename" placeholder="请输入收货人" value="<?=$_REQUEST['truename']?>">
					  	</div>
					  	<div class="form-group">
					    	<select class="form-control" name="delivery">
					    		 <option value="">请选择配送方式</option>
							  	 <option value="0" <?php if($_REQUEST['delivery'] === '0')echo ' selected="selected"';?>>快递配送</option>
							  	 <option value="1" <?php if($_REQUEST['delivery'] == '1')echo ' selected="selected"';?>>校园配送</option>
							</select>
					  	</div>
					  	<div class="form-group">
					    	<select class="form-control" name="orderstatus">
					    		 <option value="">请选择支付状态</option>
							  	 <option value="0" <?php if($_REQUEST['orderstatus'] === '0')echo ' selected="selected"';?>>未支付</option>
							  	 <option value="1" <?php if($_REQUEST['orderstatus'] == '1')echo ' selected="selected"';?>>已支付</option>
							  	 <option value="2" <?php if($_REQUEST['orderstatus'] == '2')echo ' selected="selected"';?>>已发货</option>
							</select>
					  	</div>
					  	
					  	<div class="form-group">
			                 <input class="form-control pay_form_datetime" size="22" type="text" value="<?=$_REQUEST['paystarttime']?>" placeholder="下单开始时间" name="paystarttime" >-<input class="form-control pay_form_datetime" size="22" type="text" value="<?=$_REQUEST['payendtime']?>" placeholder="下单结束时间" name="payendtime">
			                 <script type="text/javascript">
								$(".pay_form_datetime").datetimepicker({format: 'yyyy-mm-dd hh:ii',language: 'zh-CN',autoclose: true});
							</script> 
			            </div>
						<div class="form-group">
			                 <input class="form-control payed_form_datetime" size="22" type="text" value="<?=$_REQUEST['payedstarttime']?>" placeholder="付款开始时间" name="payedstarttime" >-<input class="form-control payed_form_datetime" size="22" type="text" value="<?=$_REQUEST['payedendtime']?>" placeholder="付款结束时间" name="payedendtime">
			                 <script type="text/javascript">
								$(".payed_form_datetime").datetimepicker({format: 'yyyy-mm-dd hh:ii',language: 'zh-CN',autoclose: true});
							</script> 
			            </div>

					  	<button type="submit" class="btn btn-primary">搜索</button>
						<!--search end-->
						
						<table class="table table-striped table-bordered mt-15">
							<thead>
							<tr>
								<th width="50">ID</th>
								<th>订单号</th>
								<th>书籍金额</th>
								<th>运费</th>
								<th>总金额</th>
								<th>下单人</th>
								<th>下单人类型</th>
								
								<th>下单时间</th>
								<th>付款时间</th>
							
								<th>支付方式</th>
								<th>配送方式</th>
								<th>订单状态</th>
								<th>操作</th>
							</tr>
							</thead>
							<tbody>
								<?php
								$pageParams['ordersn']=isset($_REQUEST['ordersn'])?$_REQUEST['ordersn']:'';
								$pageParams['delivery']=($_REQUEST['delivery'] || $_REQUEST['delivery']=='0')?$_REQUEST['delivery']:'';
								$pageParams['orderstatus']=($_REQUEST['orderstatus'] || $_REQUEST['orderstatus']=='0')?$_REQUEST['orderstatus']:'';
								$pageParams['truename']=isset($_REQUEST['truename'])?$_REQUEST['truename']:'';
								$pageParams['paystarttime']=isset($_REQUEST['paystarttime'])?$_REQUEST['paystarttime']:'';
								$pageParams['payendtime']=isset($_REQUEST['payendtime'])?$_REQUEST['payendtime']:'';
								$pageParams['payedstarttime']=isset($_REQUEST['payedstarttime'])?$_REQUEST['payedstarttime']:'';
								$pageParams['payedendtime']=isset($_REQUEST['payedendtime'])?$_REQUEST['payedendtime']:'';
								
								if($pageParams['ordersn']){
									$sql.=" and  a.ordersn like '%".$pageParams['ordersn']."%' ";
								}
								if($pageParams['delivery'] || $pageParams['delivery']=='0'){
									$sql.=" and  a.delivery = ".$pageParams['delivery'];
								}
								if($pageParams['orderstatus'] || $pageParams['orderstatus']=='0'){
									$sql.=" and  a.orderstatus = ".$pageParams['orderstatus'];
								}
								if($pageParams['truename']){
									$sql.=" and  b.truename like '%".$pageParams['truename']."%' ";
								}
								if($pageParams['paystarttime'] && $pageParams['payendtime']){
									$sql.=" and  a.paytime > '".strtotime($pageParams['paystarttime'])."' and a.paytime < '".strtotime($pageParams['payendtime'])."'";
								}
								if($pageParams['payedstarttime'] && $pageParams['payedendtime']){
									$sql.=" and  a.payedtime > '".strtotime($pageParams['payedstarttime'])."' and a.payedtime < '".strtotime($pageParams['payedendtime'])."'";
								}
								//权限
								if($adminrole == 0){
									$sql.=" and  b.schoolprov='".$adminprov."'";
								}else if($adminrole ==1 ){
									$sql.=" and  b.cid<>1 and b.schoolprov='".$adminprov."' and b.schoolcity='".$admincity."' ";
								}else if($adminrole ==2){
									$sql.=" and  b.cid<>1 and b.schoolprov='".$adminprov."' and b.schoolcity='".$admincity."' and b.schooldist='".$admindist."' ";
								}
								$sql="select a.* ,b.cid,b.schoolprov,b.schoolcity,b.schooldist,b.school,b.schoolclass,b.address from n_orders as a left join n_users as b on a.userid=b.id where 1=1 ".$sql." order by a.orderid desc";
								//echo $sql;
								$pageArr=PageData("select count(*) from (".$sql.") t",$pageParams);
								$result=$mysql->getAll($sql." limit ".$pageArr["pageStart"].",".$pageArr["pageSize"]);
								foreach ($result as $record){
								?>
						
								<tr>
									<td><?php echo $record['orderid']; ?></td>
									<td><?php echo $record['ordersn']; ?></td>
									
									
									<td><?php echo number_format($record['total_pricemoney'], 2); ?></td>
									<td><?php echo number_format($record['freight'], 2); ?></td>
									<td><?php echo number_format($record['total_fee'], 2); ?></td>
									
									<td><?php echo $record['usertruename']; ?></td>
									<td><?php
									if ($record['cid'] == 3) {echo "<span class='btn btn-success'>学生</span>";
									} else if ($record['cid'] == 1) {echo "<span class='btn btn-info'>社会人士</span>";
									} else if ($record['cid'] == 2) {echo "<span class='btn btn-warning'>学校</span>";
									}
										?></td>
									
									<td><?php echo date("Y-m-d H:i", $record['paytime']); ?></td>
									<td><?php if($record['orderstatus']>0){echo date("Y-m-d H:i", $record['payedtime']);} ?></td>
								
									
									<td>
										<?php
										if ($record['payment'] == 0) {echo "支付宝";
										} else if ($record['payment'] == 1) {echo "转账汇款";
										} else if ($record['payment'] == 2) {echo "银联支付";
										}
										?>									</td>
									<td>
										<?php
										if ($record['delivery'] == 0) {echo "快递配送";
										} else if ($record['delivery'] == 1) {echo "校园配送";
										}
										?>									</td>
									<td>
										<?php
										if ($record['orderstatus'] == 0) {
											echo "<span class='btn btn-danger'>未支付</span>";
										} else if ($record['orderstatus'] == 1) {
											echo "<span class='btn btn-primary'>已支付</span>";
										} else if ($record['orderstatus'] == 2) {
											echo "<span class='btn btn-primary'>已发货</span>";
										}
										?>									</td>
									<td>
										<? if(in_array('3-0-1', $arr_private)){?><a href='admin_orders_edit.php?id=<?php echo $record[orderid]; ?>' class="btn btn-primary" target="_blank">查看</a><? } ?>
										<? if(in_array('3-0-2', $arr_private) && $record['orderstatus']==1){?><a href='admin_orders_deal.php?id=<?php echo $record[orderid]; ?>' class="btn btn-primary">发货</a><? } ?>									</td>
								</tr>
								<tr>
								<td colspan="3" align="">
								收货信息：<br/><a href="admin_users_edit.php?id=<?php echo $record['userid']; ?>" target="_blank"><?php echo $record['usertruename']; ?></a>,<?
											if ($adminrole == 0) {echo $record['userphone'] . ',';
											}
 ?><? if($record['delivery']==0) echo $record['schoolprov'].'/'.$record['schoolcity'].'/'.$record['schooldist'].'/'.$record['address']; else if($record['delivery']==1) echo  $record['schoolprov'].'/'.$record['schoolcity'].'/'.$record['schooldist'].'/'.$record['school'].'/'.$record['schoolclass']?>
								</td>
								<td colspan="3" align="">
								支付宝付款单号：<br/><?php if($record['alipayordersn']==0) {echo '未支付';} else {echo $record['alipayordersn'];} ?>
								</td>
								<td colspan="1" align="right" style="background:#eeeeee;">
								数量(<?php echo $record[carttotal]; ?>本):
								</td>
								<td colspan="6" align="">
								<?php 
									$rowgoods=$record[order_goods];
									$rowgoods_every = explode(",",$rowgoods);
									$arrlength=count($rowgoods_every);
									for($x=0;$x<$arrlength;$x++) {
										$rowgoods_everydetails=explode("|",$rowgoods_every[$x]);
										$sqlbooks ="select title from n_goodsbase where id=$rowgoods_everydetails[0]";
										$query22 = $mysql->query($sqlbooks);
										
										$resbooks = $mysql->fetch($query22);
										
								?>
								<span>ID：</span><?php echo $rowgoods_everydetails[0]; ?>，
								<span>名称：</span><?php echo $resbooks[title]; ?>，
								<span>数量：</span><?php echo $rowgoods_everydetails[1]; ?>，
								<span>价格：</span><?php echo $rowgoods_everydetails[2]; ?><br/>
								<?php
							 } 
							 ?>
								</td>
								</tr>
		
	
								<?php
								}
								?>
								<tr>
									<th colspan="13"><?=$pageArr["html"]?></th>
								</tr>
							</tbody>
						</table>
					</form>
				</div>
			</div>
		</div>
		
		
	</body>
</html>									