<?php
include(INCLUDES."/header.php");
include(INCLUDES."/nav.php");
?>

<div class="main">
	<div class="main-content">
		<div class="page-wraper">
			<div class="page">
			
			<!-- 添加控件 -->
			<div class="pane">
				<div class="line">
					<h4>添加控件</h4>
				</div>
				<div class="line">
					<label>控件名（必填）</label>
				</div>
				<div class="line">
					<input class="textbox" type="text" name="widget_name" maxlength="64" />
				</div>
				<div class="line">
					<label>页面链接（必填）</label>
				</div>
				<div class="line">
					<input class="textbox" type="text" name="widget_link" maxlength="256" />
				</div>
				<div class="line">
					<label>控件高度</label>
				</div>
				<div class="line">
					<input class="textbox" type="text" name="widget_height" maxlength="64" />
				</div>
				<div class="line">
					<button class="button" id="btn-add">添加</button>
				</div>
			</div>
			
			<!-- 控件列表 -->
			<div class="pane">
				<div class="line">
					<h4>控件列表</h4>
				</div>
				<div class="line">
					<div class="block">名称</div>
					<div class="block">页面链接</div>
					<div class="block">高度</div>
				</div>
				<div class="list" id="list-widgets"></div>
				<div class="line">
					<button class="button-danger" id="btn-delete">删除</button>
				</div>
			</div>
			
			</div>
		</div>
		<div class="sidebar"></div>
	</div>
</div>

<!-- 脚本区域 -->
<script src="<?php echo INCLUDES."/administrator_widget.js"; ?>"></script>

<?php
include(INCLUDES."/footer.php");
?>
