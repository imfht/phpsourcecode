<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('e0');

if (empty($_GET)) die;

$item_id = intval($_GET['item_id']);
$user_id = $auth['id'];

$rs = dt_query("SELECT * FROM finance_item_invest WHERE item_id = $item_id AND user_id = $user_id");
$i_i = mysql_fetch_array($rs);
if (empty($i_i)) die('e0');

$rs = dt_query("SELECT name, kind, credit_s FROM finance_item WHERE id = $item_id");
$item = mysql_fetch_array($rs); 
if (empty($item)) die('e0');

?>

<?php require_once('finance/inc/item_kind.php'); ?>

<hr style="border-top:1px solid #ccc">

<div class="well finance-item-add">
	<div class="row clearfix">

		<div class="col-md-12 column">
			<h3><span class="glyphicon glyphicon-edit"></span> 修改投资意向</h3>
			<hr class="solid">
			<div class="alert alert-info">
				<b>注意：</b>
				<ol>
					<li>您真在进行修改意向操作的是<?php echo $item_id ?>号融资项目“<?php echo $item['name'] ?>”</li>
					<li>此项目为“<?php echo $item_kind[$item['kind']]['name'] ?>”项目</li>
					<li>此项目信用评分为：<?php echo $item['credit_s'] ?></li>
				</ol>
			</div>
			<div class="col-md-11 column">
				<form id="item_edit_invest" class="form-horizontal">
					<fieldset>
						<!-- i_i_id -->
						<input type="hidden" name="i_i_id" value="<?php echo $i_i['id'] ?>"/>

						<!-- Text input-->
						<div class="form-group">
							<label class="col-md-2 control-label">意向投资额</label>  
							<div class="col-md-7">
								<input name="money" type="text" placeholder="500000" class="form-control input-md" required="" value="<?php echo $i_i['money'] ?>">
								<span class="help-block">您愿意为这个项目投入的资金量。</span>
							</div>
						</div>

						<!-- Text input-->
						<div class="form-group">
							<label class="col-md-2 control-label">联系电话</label>  
							<div class="col-md-7">
								<input name="phone" type="text" placeholder="158****6894" class="form-control input-md" required="" value="<?php echo $i_i['phone'] ?>">
								<span class="help-block">我们的专业人员通过这个电话与您联系。</span>
							</div>
						</div>

						<!-- token -->
						<input type="hidden" name="token" value="<?php echo get_token() ?>"/>

						<!-- Button -->
						<div class="form-group">
							<label class="col-md-2 control-label"></label>
							<div class="col-md-2">
								<a class="btn btn-primary btn-block" href="javascript:do_item_edit_invest()">保存修改</a>
							</div>
							<div class="col-md-5">
							</div>
						</div>

					</fieldset>
				</form>	


			</div>
		</div>
	</div>
</div>


<script type="text/javascript">

function do_item_edit_invest() {
	$.post('?c=finance&a=do_item_edit_invest', $("form#item_edit_invest").serialize(), function(rs){
		if ('s0' != rs) { 
			alert(rs);
			return;
		} 
		alert('修改成功！');
		$('div#i_i_edit').html('');
	});
}

</script>
