			<div id="main">
				<div class="title">您当前的Logo为</div>
				<p><img src="{LOGO}" /></p>
				<form action="{S_UPLOAD_ACTION}" method="post" enctype="multipart/form-data">
					<p>上传说明：允许jpg、gif、png图片上传，图片大小不能超过2M</p>
					<input type="file" name="logo" /> 
					<input type="submit" name="submit" value="上传" />
				</form>
				<p>【<a href="{U_BACK_MODULE}">返回上级</a>】</p>
				<p>【<a href="{U_INDEX_MODULE}">页面编辑首页</a>】</p>
			</div>