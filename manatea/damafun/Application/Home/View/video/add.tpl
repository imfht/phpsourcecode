<{include file="public/header.tpl"}>
<div class="container">
<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>用户视频上传</h3>
<form enctype="multipart/form-data" method="post" action="<{$smarty.const.__CONTROLLER__}>/upload" onsubmit="$('.btn-upload').button('loading');">
	<div class="form-group">
	<label for="InputName">选择分类</label>
  <{$select}>
   </div>
   <div class="form-group">
    <label for="InputVName">视频名称</label>
    <input type="text" class="form-control" id="InputVName" name="name" required >
  </div>
  <div class="form-group">
    <label for="InputName">上传人账号</label>
    <input type="text" class="form-control" id="InputName"  value="<{$smarty.session.user.name}>" disabled>
    <input type="hidden" name="uid" value="<{$smarty.session.user.id}>">
  </div>
  <div class="form-group">
    <label for="InputVideo">视频上传</label>
    <input type="file" id="InputVideo" name="path">
    <p class="help-block">文件上传最大限制为100M,建议mp4格式,目前支持mp4,avi,flv,wmv格式,视频转码时间较长，请您耐心等待</p>
  </div>
  <div class="form-group">
    <label for="InputName">描述</label>
    <textarea class="form-control" name="desn" rows="3"></textarea>
  </div>

  <button type="submit"  class="btn btn-default btn-upload">上传</button>
</form>
</div>
</div>
</div>
<{include file="public/footer.tpl"}>