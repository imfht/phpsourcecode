			<div id="main">	
				<div class="title">用户相册</div>
<!-- BEGIN memberrow -->
				<div class="module {memberrow.ROW_CLASS}">
					<b><a href="{memberrow.U_VIEWGALLERY}">{memberrow.USERNAME}</a></b><br/>
					创建日期：{memberrow.JOINED}<br/>
					上传图片: {memberrow.PICS}
				</div>
<!-- END memberrow -->
<!-- BEGIN no_pics -->
				<div class="row1">目前还有没任何人上传照片到相册</div>
<!-- END no_pics -->
				{PAGINATION}
			</div>