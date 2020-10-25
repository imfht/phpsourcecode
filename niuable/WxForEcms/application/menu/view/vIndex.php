<?php defined('APP_PATH') OR exit('Not allow to access'); ?>

<div class="row" >
	<div class="col-lg-12">
		<h4>位置：{$title}</h4>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<form class="form-inline" id="view-form" action="{$public}menu/index/index" method="post">
			{$ecms_hashur['form']??''}
			<table class="table table-bordered table-hover text-center">
			    <tr align="left">
			    	<td>
			    		<input type="checkbox" value="1" name="def" id="def" {$def?'checked':''} onclick="javascript:this.form.submit();">
			    		<label for="def" class="control-label">&nbsp;只看默认公众号</span>
			    	</td>
			   		<td>
			   			<div class="form-inline">
			   				<div class="form-group">
								<input name="search" type="search" class="form-control" value="{$search??''}" placeholder="请输入搜索内容">
							</div>
							<div class="form-group">
								<input name="doSearch" type="submit" value="搜索" class="btn btn-warning">&nbsp;
								 
							</div>
							<p class="form-control-static"><?php echo (isset($search) && (!empty($search) || $search==='0'))?'正在搜索：'.$search:'';?></p>
			   			</div>
			   		</td>
			   	</tr>
			</table>
		</form>
		<form class="form-inline" id="editor_form" action="{$public}menu/index/editor">
			{$ecms_hashur['form']??''}
			<input type="hidden" name="site" id='site' value="" />
			<input type="hidden" name="operation_form" id='operation_form' value="" />
			<input type="hidden" id="editor_type" name="editor_type" value="" />
			<table class="table table-bordered table-hover text-center">
			    <tr>
			      <td width="5%">选择</td>
				  <td width="5%">序号</td>
			      <td width="8%">ID</td>
				  <td width="15%">标题</td>
			      <td width="15%">更新时间</td>
			      <td width="8%">用于公众号</td>
			  	  <td width="">操作</td>
			    </tr>
				{volist name="list" id="v" key="k" empty="<tr><td colspan=7>暂无数据</td></tr>"}
			    	<tr>
			            <td><input name="ids[]" type="checkbox" id="ids_{$k}" value="{$k}" {$aid==$v.aid?'':"disabled"}/></td>
						<td>{$k}</td>
			            <td>
			            	<input type="hidden" name="id[{$k}]" value="{$v.id}" >
			            	{$v['id']??''}
			            </td>
			            <td style="word-break:break-all;">{$v.title??''}</td>
			            <td>{$v.update_time??''}</td>
			            <td>{$v['aid']??''}</td>
			            <td>
			            	<a href="{$public}menu/index/up2Wx/id/{$v.id}?{$ecms_hashur['href']??''}" {$aid==$v['aid']?'':'disabled'} class="btn btn-warning">上传</a>
			            	<a href="{php} if($aid==$v['aid']){ {/php}
			            			{$public}menu/index/toEditor/id/{$v.id}?{$ecms_hashur.href??''}
			            		{php}}else{ {/php}
			            			javascript:void(0);
			            		{php}}{/php}" 
			            		{$aid==$v['aid']?'':'disabled'} class="btn btn-success">编辑</a>
			             	<input type="button" value="删除" class="btn btn-danger" 
			             		onclick="editorInput('{$k}','site');editorInput('oneDelete','editor_type');editorInput('editor_form','operation_form');editorModal('确定要残忍删除吗？');" 
			             		data-toggle="modal" data-target="#myModal" <?=$aid==$v['aid']?'':"disabled"?>/>
			            </td>
			    	</tr> 
				{/volist}
			        <tr>
			            <td>
				            <label><input type="checkbox" onclick="checkall(form, 'ids')" name="all" /><br />全选</label>
			            </td>
			            <td colspan="6">
			            	<input type="button" class="btn btn-danger" 
			            		onclick="editorInput('sDelete','editor_type');editorInput('editor_form','operation_form');editorModal('确定要残忍删除？');"  
			            		value='删除所选' data-toggle="modal" data-target="#myModal"/>
			            	<a href="{$public}menu/index/addView?{$ecms_hashur['href']??''}" class="btn btn-success">新增菜单</a>
			            </td>
			        </tr>
				<?php if(isset($page)){?>
					<tr align="left">
						<td colspan="7">
							<?php echo $page;?>			
						</td>
					</tr>
				<?php }?>		       
			</table>
		</form>
	</div>
</div>