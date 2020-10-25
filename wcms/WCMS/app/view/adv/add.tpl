<div class="span12">
			
						
			<div class="row-fluid">

<div class="box">
<div class="box-content"><!-- Default panel contents -->
<form enctype="multipart/form-data" action="./index.php?advadmin/sub" method="post"  class="form-horizontal">
<div class="control-group">
<label class="control-label" >
广告名称 </label><input type="text" name="title">
</div>

<div class="control-group">
<label class="control-label" >
位置 </label><select name="type" class="input-small">
{foreach from=$type item=l key=key}
<option value="{$key}">{$l}</option>
{/foreach}
</select>
</div>


<div class="control-group">
 <label class="control-label" >广告图片 </label>
  <input type="file" name="image"> 自定义 最大不尺寸超过2000x2000  大小不超过800KB
</div>
<div class="control-group">

<label class="control-label" >连接网址 </label><input type="text" name="url" class="input-xlarge"> 

</div>
<div class="control-group">
<label class="control-label" >状态 
</label>
<input type="radio" name="status" checked value="0">显示
<input type="radio" name="status"  value="-1">隐藏
</div>
<div class="control-group">
<label class="control-label" ></label>
<input type="submit" class="btn " value="添加">
</div>
</form>
</div>
</div>
</div>
