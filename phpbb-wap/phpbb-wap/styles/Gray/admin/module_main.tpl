			<div id="main">
				<div class="title">{L_TITLE}</div>
				<form action="{S_ACTION}" method="post">
<!-- BEGIN switch_edit_module -->
						<input type="hidden" name="id" value="{MODULE_ID}">
						<input type="hidden" name="param" value="{MODULE_PARAM}" />
<!-- END switch_edit_module -->
					<div class="module bm-gray">
						<label>模块的名称：</label>
						<p>如果不是指向普通模块，模块名称不能留空</p>
						<div><input type="text" name="name" value="{MODULE_NAME}" /></div>
					</div>
					<div class="module bm-gray">
						<label>模块的类型：</label>
						<div>
							{MODULE_TYPE}
						</div>
					</div>
<!-- BEGIN switch_create_module -->
					<div class="module bm-gray">
						<label>选择论坛分类：</label>
						<p>注意:此项仅对子论坛模块生效</p>
						<div>{SELECT_FORUM_CAT}</div>		
					</div>
<!-- END switch_create_module -->
<!-- BEGIN switch_edit_module -->
					<div class="module bm-gray">
						<label>指向现有子论坛</label>
						<p>如果上面 “模块的类型” 选项指向的不是子论坛模块此选项不会生效</p>
						<div>{SELECT_FORUM}</div>
					</div>
<!-- END switch_edit_module -->
					<div class="module bm-gray">
						<div><input type="checkbox" name="br"{MODULE_BR_CHECK} /> 模块后使用&lt;br /&gt;进行换行</div>
					</div>
					<div class="module bm-gray">
						<div><input type="checkbox" name="hide"{MODULE_HIDE_CHECK} /> 隐藏模块</div>
					</div>
					<div class="module bm-gray">
						<label>模块的排序：</label>
						<div><input type="text" name="sort" size="4" value="{MODULE_SORT}" /></div>
					</div>
<!-- BEGIN switch_edit_module -->
					<div class="module bm-gray">
						<div><input type="checkbox" name="delete" /> 删除模块</div>
					</div>
<!-- END switch_edit_module -->
					<div class="center"><input type="submit" name="submit" value="提交" /></div>
				</form>
				<p>【<a href="{U_BACK_MODULE}">返回上级</a>】</p>
				<p>【<a href="{U_INDEX_MODULE}">页面编辑首页</a>】</p>
			</div>