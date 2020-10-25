<!--- 表单数据 -->
<style type="text/css">
dd {text-indent: 8px;}
div.error {height: auto;}
</style>
<form action="index.php?step=checkdb&type=<?php echo $type;?>" method="post" id="checkdbform">
<input type="hidden" name="FORM_HASH" value="123" />
<div style="width: 700px; margin: auto;">
	<h1>3. 数据库信息</h1>
	<div class="div">
		<div class="header">数据库信息:</div>
		<div class="body">
			<dl>
				<dt>DB 类型</dt>
				<dd>
					<input type="radio" name="type" id="type_mysql" value="mysql"<?php echo $type == 'mysql'  ? 'checked="checked"' : '';?> <?php echo !$mysql_support ? 'disabled="disabled" onclick="alert(\'当前环境不支持MySQL\')"' : ''; ?> />MySQL
					<?php echo IN_SAE ? '<b>(SAE内置)</b>' : ''; ?>
					<input type="radio" name="type" id="type_pdo_mysql" value="pdo_mysql"<?php echo $type == 'pdo_mysql' ? 'checked="checked"' : '';?> <?php echo !$pdo_mysql_support ? 'disabled="disabled" onclick="alert(\'当前环境不支持PDO_MYSQL\')"' : ''; ?> />pdo_mysql
					<input type="radio" name="type" id="type_pdo_sqlite" value="pdo_sqlite"<?php echo $type == 'pdo_sqlite' ? 'checked="checked"' : '';?> <?php echo !$pdo_sqlite_support ? 'disabled="disabled" onclick="alert(\'当前环境不支持PDO_SQLITE\')"' : ''; ?> />pdo_sqlite
					<input type="radio" name="type" id="type_mongodb" value="mongodb"<?php echo $type == 'mongodb' ? 'checked="checked"' : '';?> <?php echo !$mongodb_support ? 'disabled="disabled" onclick="alert(\'当前环境不支持MongoDB\')"' : ''; ?> />MongoDB
				</dd>
				<?php if($type != 'pdo_sqlite') { ?>
				<dt>主机: </dt><dd><input type="text" size="24" name="host" value="<?php echo isset($_POST['host']) ? core::gpc('host', 'P') : $master['host'];?>" /><span class="grey"> 端口号格式：127.0.0.1:10123</span></dd>
				<dt>用户名: </dt><dd><input type="text" size="24" name="user" value="<?php echo isset($_POST['user']) ? core::gpc('user', 'P') : $master['user'];?>" /></dd>
				<dt>密码: </dt><dd><input type="text" size="24" name="pass" value="<?php echo isset($_POST['pass']) ? core::gpc('pass', 'P') : $master['password'];?>" /></dd>
				<dt>数据库: </dt><dd><input type="text" size="24" name="name" value="<?php echo isset($_POST['name']) ? core::gpc('name', 'P') : $master['name'];?>" /></dd>
				<?php } else {?>
				<dt>数据库文件: </dt><dd><input type="text" size="24" name="host" value="<?php echo isset($_POST['host']) ? core::gpc('host', 'P') : $master['host'];?>" /><span class="grey"> 为防止别人猜测到，请填写一个复杂文件名</span></dd>
				<?php }?>
				<?php if($type != 'mongodb') { ?>
					<dt>表前缀:   </dt><dd><input type="text" size="24" name="tablepre" value="<?php echo isset($_POST['tablepre']) ? core::gpc('tablepre', 'P') : $master['tablepre'];?>" /></dd>
				<?php }?>
			</dl>
		</div>
	</div>
	<h1>4. 管理员密码</h1>
	<div class="div">
		<div class="header">管理员密码:</div>
		<div class="body">
			<dl>
				<dt>管理员账户: </dt><dd><input type="text" size="24" name="adminuser" id="adminuser" value="admin" maxlength="16" /></dd>
				<dt>密码: </dt><dd><input type="password" size="24" name="adminpass" id="adminpass" value="" maxlength="16" /></dd>
				<dt>重复密码: </dt><dd><input type="password" size="24" name="adminpass2" id="adminpass2" value="" maxlength="16" /></dd>
			</dl>
		</div>
	</div>
	
	<h1>5. 时区</h1>
	<div class="div">
		<div class="header">选择时区:</div>
		<div class="body">
			<dl>
				<dt>时区: </dt>
				<dd>
					<?php echo $timeoffset_select;?>
				</dd>
			</dl>
		</div>
	</div>
	
	<p style="text-align: center;">
		<input type="button" value=" 上一步" onclick="history.back();"/>
		<input type="submit" value=" 下一步" name="formsubmit" id="formsubmit" /><!-- onclick="return window.confirm('请仔细确认数据库信息，系统将会以覆盖的方式安装！');" -->
	</p>
	<?php if(core::gpc('FORM_HASH', 'P')) { ?>
		<?php if($error) { ?>
			<div class="error"><?php echo $error;?></div>
		<?php } else {?>
			<script type="text/javascript">setTimeout(function() {window.location='?step=complete'}, 1000);</script>
		<?php }?>
	<?php }?>
</div>
</form>
<script type="text/javascript">
function getid(id) {
	return document.getElementById(id);
}

getid('type_mysql').onclick = function() {
	window.location = 'index.php?step=checkdb&type=mysql';
	//getid('db_div').style.display = '';
};
getid('type_pdo_mysql').onclick = function() {
	window.location = 'index.php?step=checkdb&type=pdo_mysql';
	//getid('db_div').style.display = '';
};
getid('type_mongodb').onclick = function() {
	window.location = 'index.php?step=checkdb&type=mongodb';
	//getid('db_div').style.display = 'none';
};
getid('type_pdo_sqlite').onclick = function() {
	window.location = 'index.php?step=checkdb&type=pdo_sqlite';
	//getid('db_div').style.display = 'none';
};

getid('formsubmit').onclick = function(e) {
	if(getid('adminuser').value == "") {
		alert('请输入管理员账户！');
		getid('adminuser').focus();
		return false;
	}
	if(getid('adminpass').value == "") {
		alert('请输入管理员密码！');
		getid('adminpass').focus();
		return false;
	}
	if(getid('adminpass').value != getid('adminpass2').value) {
		alert('两次输入的密码不一致');
		getid('adminpass2').focus();
		return false;
	}
	return true;
};
</script>