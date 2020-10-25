<div class="span12">
			
						
			<div class="row-fluid">

<div class="box">
<div class="box-content"><!-- Default panel contents -->
<form enctype="multipart/form-data" action="./index.php?advadmin/save" method="post"  class="form-horizontal">
<div class="control-group">
<label class="control-label" >
广告名称 </label><input type="text" name="title" value="{$adv.title}">
</div>

<div class="control-group">
<label class="control-label" >
位置 </label><select name="type" class="input-small">
{foreach from=$type item=l key=key}
<option value="{$key}" {if $adv.type==$key}selected{/if}>{$l}</option>
{/foreach}
</select>
</div>



<div class="control-group">
 <label class="control-label" >广告图片 </label>
  <input type="file" name="image">  自定义 最大不尺寸超过2000x2000  大小不超过800KB
</div>
<div class="control-group">

<label class="control-label" >连接网址 </label><input type="text" name="url" class="input-xlarge" value="{$adv.url}"> 

</div>
<div class="control-group">
<label class="control-label" >状态 
</label>
<input type="radio" name="status" value="0" {if $adv.status==0}checked {/if}>显示
<input type="radio" name="status"  value="-1" {if $adv.status==-1}checked {/if}>隐藏
</div>
<div class="control-group">
<label class="control-label" ></label>
<input type="hidden" name="id" value="{$adv.id}">
<input type="submit" class="btn" value="更新">
</div>
</form>
</div>
</div>
</div>
