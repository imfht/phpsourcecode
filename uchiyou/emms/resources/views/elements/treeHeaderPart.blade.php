<div id="manual-edit">
	<div id="tree-root" style="width: 200px;">
		<div class="nav-item-left">
			<i class="fa fa-th-large"></i> 
			@if(session('company')!=null)
			{{ session('company')->name }}
			@endif
		</div>
		<div class="nav-item-right">
			<button data-target="#create-new" data-toggle="dropdown"
				aria-haspopup="true" aria-expanded="false"
				id="create_directory_button" title="创建目录">
				<i class="fa fa-plus"></i>
			</button>
		</div>
		<div class="nav-item-content" id="sidebar"
			style="height: 100%; overflow: auto"></div>

	</div>

	<div id="jsTreeContainer"></div>
</div>
<hr>
<div id="showNodeInfo"></div>