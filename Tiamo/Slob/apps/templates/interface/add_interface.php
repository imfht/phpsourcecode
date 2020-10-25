<h2 class='contentTitle'><?php echo $title; ?></h2>
<div class='pageContent'>
	<form method='post' action='<?= URL('interfaces/addinterface'); ?>' class='pageForm required-validate' onsubmit='return validateCallback(this,navTabAjaxDone)'>
		<div class='pageFormContent nowrap' layoutH='97'>
			
					<dl><dt>接口名称：</dt>
							<dd>
								<input type='text' name='name' maxlength='60' class='required' />
								<span class='info'></span>
							</dd>
					</dl>
					<dl><dt>接口所属用户：</dt>
							<dd>
								<input type='text' name='own_uid' maxlength='11'  />
								<span class='info'></span>
							</dd>
					</dl>
					<dl><dt>：</dt>
							<dd>
								<input type='text' name='create_time' maxlength='11' class='required' />
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