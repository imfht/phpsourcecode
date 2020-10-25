			<div id="main">
<!-- BEGIN create_class -->
				<div class="mouule">
					<form action="{S_CREATE_ACTION}" method="post">
						<input type="text" name="classname" value="" />
						<input type="submit" value="创建专题" />
					</form>
				</div>
<!-- END create_class -->
				<div class="title">专题列表</div>
<!-- BEGIN class_list -->
				<div class="module {class_list.ROW_CLASS}">
					{class_list.NUMBER}、<a href="{class_list.U_CLASS}">{class_list.CLASS_NAME}</a>
	<!-- BEGIN is_mod -->
					【<a href="{class_list.is_mod.U_EDIT_CLASS}">编辑</a> . <a href="{class_list.is_mod.U_DELETE_CLASS}">删除</a>】
	<!-- END is_mod -->
				</div>
<!-- END class_list -->
<!-- BEGIN not_class -->
				<div class="module">还没有任何专题</div>
<!-- END not_class -->
				{PAGINATION}
				<div>【<a href="{U_VIEW_FORUM}">返回上级</a>】</div>
				{PAGE_JUMP}
			</div>