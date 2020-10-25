<?php	defined('APP_PATH') OR exit('No direct script access allowed'); ?>
<div class="row" >
	<div class="col-lg-12">
		<h4>位置：微信公众号管理</h4>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="panel-group" id="accordion">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h4 class="panel-title">
		        		<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
		          			<h4 class="panel-title">增加公众号:点击这里</h4>
						</a>
					</h4>
				</div>
				<div id="collapseOne" class="panel-collapse collapse <?php echo count($form_error)>0?'in':'';?>">
					<div class="panel-body" >
						<?php echo count($form_error)>0?'<div class="form-group text-danger col-sm-offset-2">有错误，请检查、消除错误后再提交。</div>':'';?>
						<form class="form-horizontal" role="form" method="post" action="<?=$public?>index/index/add">
							<?php echo $ecms_hashur['form']?>
							<div class="form-group">
								<label for="name" class="control-label col-sm-2 text-right">公众号名称：</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" id="name" name="name" value="{$form.name ?? ''}" placeholder="请输入公众号名称，用于在本系统中区分"/>
								</div>
								<?php echo isset($form_error['name']) ?'<div class="col-sm-4" style="color:red">'.$form_error['name'].'</div>' :'';?>
							</div>
							<div class="form-group">
								<label for="type" class="control-label col-sm-2 text-right">类型：</label>
								<div class="col-sm-6">
									<select name="type" id="type" class="form-control">
										<option value="">请选择</option>
										<option value="1" {$form.type ==1?'selected':''}>1认证服务号</option>
										<option value="2" {$form.type ==2?'selected':''}>2未认证服务号</option>
										<option value="3" {$form.type ==3?'selected':''}>3认证订阅号</option>
										<option value="4" {$form.type ==4?'selected':''}>4未认证订阅号</option>
									</select>
								</div>
								<?php echo isset($form_error['type'])?'<div class="col-sm-4" style="color:red">'.$form_error['type'].'</div>' :'';?>
							</div>
							<div class="form-group">
								<label for="way_of_key" class="control-label col-sm-2 text-right">消息加密：</label>
								<div class="col-sm-6">
									<select name="way_of_key" id="way_of_key" size="1" class="form-control">
										<option value="">请选择</option>
										{/* 此处必须用php原生标签判断，因为默认模板标签默认先用empty($form.way_of_key)判断，则出错……*/}
										<option value="0" <?php echo isset($form['way_of_key']) ?($form['way_of_key']==='0'?'selected':''):'';?>>明文模式</option>
										<option value="1" {$form.way_of_key ==1?'selected':''}>兼容模式</option>
										<option value="2" {$form.way_of_key ==2?'selected':''}>加密模式</option>
									</select>
								</div>
								<?php echo isset($form_error['way_of_key'])?'<div class="col-sm-4" style="color:red">'.$form_error['way_of_key'].'</div>' :'';?>
							</div>
							<div class="form-group">
								<label for="encoding_aes_key" class="control-label col-sm-2 text-right">EncodingAesKey：</label>
								<div class="col-sm-6">
									<input type="text" name="encoding_aes_key" id="encoding_aes_key" 
										title="该密码很重要，是加密、解密的钥匙，理论上可随意生成，只需与微信公众号平台保持一致即可，也可随时更换，防止泄密" 
										placeholder="请保持与微信后台一直，明文状态下可不填,填写后注意保密" value="{$form.encoding_aes_key ?? ''}" class="form-control" />
								</div>
								<?php echo isset($form_error['encoding_aes_key']) ?'<div class="col-sm-4" style="color:red">'.$form_error['encoding_aes_key'].'</div>' :'';?>
							</div>
							<div class="form-group">
								<label for="app_id" class="control-label col-sm-2 text-right">AppID：</label>
								<div class="col-sm-6">
									<input name="app_id" id="app_id" type="text"  placeholder="请保持与微信后台一直，并注意保密" 
										title="该数据密级较低，可以公开，但不可与密钥同时公开" value="{$form.app_id ?? ''}" class="form-control"/>
								</div>
								<?php echo isset($form_error['app_id']) ?'<div class="col-sm-4" style="color:red">'.$form_error['app_id'].'</div>' :'';?>
							</div>
							<div class="form-group">
								<label for="app_secret" class="control-label col-sm-2 text-right">AppSecret：</label>
								<div class="col-sm-6">
									<input type="text" name="app_secret" title="绝密，不可公开，否则账号极不安全" id="app_secret" placeholder="请保持与微信后台一直，并注意保密" 
										value="{$form.app_secret ?? ''}" class="form-control"/>
								</div>
								<?php echo isset($form_error['app_secret']) ?'<div class="col-sm-4" style="color:red">'.$form_error['app_secret'].'</div>' :'';?>
							</div>
							<div class="form-group">
								<label for="token" class="control-label col-sm-2 text-right">Token：</label>
								<div class="col-sm-6">
									<input type="text" name="token" id="token" type="text" title="绝密，不可公开，否则账号极不安全" placeholder="请保持与微信后台一直，并注意保密" 
										value="{$form.token ?? ''}" class="form-control"/>
								</div>
								<?php echo isset($form_error['token']) ?'<div class="col-sm-4" style="color:red">'.$form_error['token'].'</div>' :'';?>
							</div>
							<div class="form-group">
								<label for="active" class="control-label col-sm-2 text-right">默认：</label>
								<div class="col-sm-6">
									<label class="checkbox-inline">
										是<input name="active" type="radio" value="1" {$form.active ==1?'checked="checked"':''}/>
									</label>
									<label class="checkbox-inline">
										否<input name="active" type="radio" value="0" {$form.active ==1?'':'checked="checked"'}/>
									</label>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-6 col-sm-offset-2">
									<input name="submit" type="submit" class="form-control btn btn-success" value="提交" />
								</div>
							</div>
						</form>
					</div>
		   		</div>
			</div>
		</div>
	</div>
</div>
<?php if(count($form_error)>0){}else{?>
<div class="row" >
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">正在管理的微信公众号</div>
			<div class="panel-body">
				<form action="<?=$public?>index/index">
					{$ecms_hashur['form']?? ''}
		    		<div class="form-inline">
						<div class="form-group">
							<input name="search" type="search" class="form-control" value="{$search ?? ''}" placeholder="请输入搜索内容">
						</div>
						<div class="form-group">
							<input name="dosearch" type="submit" value="搜索" class="btn btn-warning">&nbsp;
							<?php echo isset($search)?'正在搜索：'.$search:'';?>
						</div>
					</div>
				</form>
			</div>
		</div>
			<!-- <div class="panel-body">-->
				{$form_error2 ?? ''}
				<form id="editor_form" action="<?=$public?>index/index/editor">
					<?php echo $ecms_hashur['form']?>
					<table class="table table-bordered table-hover text-center">
					    <tr>
							<td width="4%">选择</td>
					    	<td width="3%">ID</td>
					    	<td width="10%">名称</td>
							<td width="8%">类型</td>
					    	<td width="12%">AppID</td>
					    	<td width="13%">AppSecret</td>
							<td width="5%">加密方式</td>
					    	<td width="14%">EncodingAesKey</td>
					    	<td width="6%">Token</td>
							<td width="5%">是否默认</td>
					  		<td>操作</td>
					    </tr>	
						{volist name="list" id="v" key="j" empty="<tr><td colspan=11>暂时没有数据</td></tr>" }
					    	<tr>
					            <td><input name="ids[]" type="checkbox" id="ids_{$j}" value="{$j}" /></td>
					            <td><input name="id[]" type="hidden" value="<?php echo $v['id']?>"/><?php echo $v['id']?></td>
					            <td><input name="name[]" type="text" class="form-control" value="<?php echo $v['name']?>" placeholder="本系统“代称”，必填"/></td>
					            <td><select name="type[]" >
					                  <option value="1" <?php echo $v['type']==1?'selected="selected"':''?>>认证服务号</option>
					                  <option value="2" <?php echo $v['type']==2?'selected="selected"':''?>>未认证服务号</option>
					                  <option value="3" <?php echo $v['type']==3?'selected="selected"':''?>>认证订阅号</option>
					                  <option value="4" <?php echo $v['type']==4?'selected="selected"':''?>>未认证订阅号</option>
					                  <!--<option value="5" <?php echo $v['type']==5?'selected="selected"':''?>>认证企业号</option>
					                  <option value="6" <?php echo $v['type']==6?'selected="selected"':''?>>未认证企业号</option>-->
					            </select></td>
					            <td><input name="app_id[]" type="text" class="form-control" style="text-align:center" value="<?php echo $v['app_id']?>" placeholder="请注意保密，必填"/></td>
					            <td><input name="app_secret[]" type="text" class="form-control" style="text-align:center" value="<?php echo $v['app_secret']?>" placeholder="请注意保密，必填"/></td>
								<td>
								<select name="way_of_key[]" >
					                  <option value="0" <?php echo $v['way_of_key']==0?'selected="selected"':''?>>明文</option>
					                  <option value="1" <?php echo $v['way_of_key']==1?'selected="selected"':''?>>兼容</option>
					                  <option value="2" <?php echo $v['way_of_key']==2?'selected="selected"':''?>>加密</option>
					            </select></td>
					            <td><input name="encoding_aes_key[]" type="text" class="form-control" style="text-align:center" 
					            	value="<?php echo $v['encoding_aes_key']?>" placeholder="明文状态下可不填"/></td>
					            <td><input name="token[]" type="text" class="form-control" style="text-align:center" value="<?php echo $v['token']?>" /></td>
					            <td>
						            <select name="active[]" >
						                <option value="1" {$v.active==1?:''}>是</option>
						                <option value="0" {$v.active==1?'':'selected="selected"'}>否</option>
						            </select>
					            </td>
					            <td colspan="4">
					             <input type="button" value="更新" class="btn btn-primary" onclick="setSite({$j});
						             editorInput('editor_form','operation_form');editorInput('update','editorType');editorModal('<font color=red>确定要更新吗？</font>');" 
					             	data-toggle="modal" data-target="#myModal"/>
					             <input type="button" value="删除" class="btn btn-danger" 
					             	onclick="setSite({$j});editorInput('editor_form','operation_form');
						             	editorInput('oneDelete','editorType');editorModal('<font color=red>确定要删除该公众号吗？</font>')" 
					             	data-toggle="modal" data-target="#myModal"/>
					             <input name="holdfile[{$j}]" type="checkbox" value="1" id="holdfile"/>保留附件
					            </td>
					    	</tr> 
						{/volist}
						<input type="hidden" id="editorType" name="editorType" value=""/>
						<input type="hidden" id="site" name="site" value=""/>
						<input type="hidden" id="operation_form" name="operation_form" value="">
				        <tr>
				            <td>
					            <label><input type="checkbox" onclick="checkall(form, 'ids')" name="all" /><br />全选</label>
				            </td>
				            <td colspan="10" align="center">
				            	<input type="button" value='删除所选' class="btn btn-danger" onclick="editorInput('editor_form','operation_form');
				            		editorInput('sDelete','editorType');editorModal('<font color=red>确定要批量删除吗？</font>');" 
						             	data-toggle="modal" data-target="#myModal" />&nbsp;
				            	<label><input name="s_holdfile" type="checkbox" value="1" id="s_holdfile"/>全部保留附件</label>
				            </td>
				        </tr>
						{if condition="!empty($page)"}
							<tr>
								<td colspan="15">{$page}</td>
							</tr>
						{/if}
					</table>
				</form>
			<!-- </div>
		</div> -->
		
		<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">
			<tr>
				<td>说明：</td>
			</tr>
			<tr>
				<td>1、“默认公众号”的意义：本插件中，‘消息管理’、‘附件管理’、‘群发管理’等菜单均是对默认公众号进行操作</td>
			</tr>
			<tr>
				<td>2、如需获得授权，请参考《使用手册》:<a href="//www.niuable.cn" target="_blank">https://www.niuable.cn</a></td>
			</tr>
		    <tr>
				<td>3、使用本插件有任何疑问、建议，欢迎进入QQ群交流：333213594。</td>
			</tr>
		</table>
	</div>
</div>
<?php }?>