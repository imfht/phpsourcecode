<h2 class='contentTitle'><?php echo $title; ?></h2>
<div class='pageContent'>
	<form method='post' action='<?= URL('admin/updateadmin'); ?>' class='pageForm required-validate' onsubmit='return validateCallback(this,navTabAjaxDone)'>
		<div class='pageFormContent nowrap' layoutH='97'>
			<input type='hidden' name='id'  value='<?=$data['id']; ?>'/>
		<dl><dt>用户名：</dt>
				<dd>
					<input type='text' name='username' maxlength='255'   value='<?=$data['username']; ?>'/>
					<span class='info'></span>
				</dd>
		</dl>
		<dl><dt>密码：</dt>
				<dd>
					<input type='text' name='password' maxlength='255'   value='<?=$data['password']; ?>'/>
					<span class='info'></span>
				</dd>
		</dl>
		<dl><dt>角色：</dt>
				<dd>
					<input type='text' name='role' maxlength='255'   value='<?=$data['role']; ?>'/>
					<span class='info'></span>
				</dd>
		</dl>
		<dl><dt>登录时间：</dt>
				<dd>
					<input type='text' name='login_time' maxlength='11'   value='<?=$data['login_time']; ?>'/>
					<span class='info'></span>
				</dd>
		</dl>
		<dl><dt>创建时间：</dt>
				<dd>
					<input type='text' name='ctime' maxlength='11'   value='<?=$data['ctime']; ?>'/>
					<span class='info'></span>
				</dd>
		</dl>
			<div class='divider'></div>
		</div>
		<div class='formBar'>
			<ul>
				<input type='hidden' name='isPost' value='1' />
				<li><div class='buttonActive'><div class='buttonContent'><button type='submit'>提交</button></div></div></li>
				<li><div class='button'><div class='buttonContent'><button type='button' class='close'>取消</button></div></div></li>
			</ul>
		</div>
	</form>
</div>