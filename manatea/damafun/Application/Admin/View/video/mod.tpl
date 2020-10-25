<{include file="public/header.tpl"}>
<body>
<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>编辑视频</h3>
<form enctype="multipart/form-data" method="post" action="<{$smarty.const.__CONTROLLER__}>/update">
<input type="hidden" name='id' value="<{$data.id}>">
	<div class="form-group">
	<label for="InputName">选择分类</label>
  <{$select}>
   </div>
  <div class="form-group">
    <label for="InputVName">视频名称</label>
    <input type="text" class="form-control" id="InputVName" name="name" value="<{$data.name}>">
  </div>
  <div class="form-group">
    <label for="InputVName">视频点击量：<{$data.hot}></label>
  </div>
    <div class="form-group">
    <label for="InputVName">视频评论数：<{$data.comnumber}></label>
  </div>
    <div class="form-group">
    <label for="InputVName">上传时间：<{$data.ptime|date_format:"%Y-%m-%d %H:%M:%S"}></label>
  </div>
  <div class="form-group">
   <img src="<{$smarty.const.APP_RES}>/uploads/images/<{$data.pic}>">

  </div>
  <div class="form-group">
    <label for="InputName">描述</label>
    <textarea class="form-control" name="desn" rows="3"><{$data.desn}></textarea>
  </div>

  <button type="submit" class="btn btn-default">Submit</button>
</form>
</div>
</div>
</body>
<{include file="public/footer.tpl"}>