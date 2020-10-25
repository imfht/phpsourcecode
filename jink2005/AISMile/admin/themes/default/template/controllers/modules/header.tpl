{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

	<div class="toolbar-placeholder">
		<div class="toolbarBox toolbarHead">
	
			<ul class="cc_button">
				{if $add_permission eq '1'}
				<li>
					<a id="desc-module-new" class="toolbar_btn" href="#top_container" onclick="$('#module_install').slideToggle();" title="{l s='Add new module'}">
						<span class="process-icon-new-module" ></span>
						<div>{l s='Add new module'}</div>
					</a>
				</li>
				{/if}
			</ul>


			<div class="pageTitle">
				<h3><span id="current_obj" style="font-weight: normal;"><span class="breadcrumb item-0">Module</span> : <span class="breadcrumb item-1">{l s='List of modules'}</span></span></h3>
			</div>

		</div>
	</div>

{if $add_permission eq '1'}
	<div id="module_install" style="width:500px;margin-top:5px;{if !isset($smarty.post.downloadflag)}display: none;{/if}">
		<fieldset>
			<legend><img src="../img/admin/add.gif" alt="{l s='Add a new module'}" class="middle" /> {l s='Add a new module'}</legend>
			<p>{l s='The module must be either a zip file or a tarball.'}</p>
			<div style="float:left;margin-right:50px">
				<form action="{$currentIndex}&token={$token}" method="post" enctype="multipart/form-data">
					<label style="width: 100px">{l s='Module file'}</label>
					<div class="margin-form" style="padding-left: 140px">
						<input type="file" name="file" />
						<p>{l s='Upload a module from your computer.'}</p>
					</div>
					<div class="margin-form" style="padding-left: 140px">
						<input type="submit" name="download" value="{l s='Upload this module'}" class="button" />
					</div>
				</form>
			</div>
		</fieldset>
		<br />
	</div>
{/if}

