<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body>
<div class="wrap J_check_wrap" id="loadhtml">
  <Admintemplate file="Common/Nav"/>
  <div class="h_a">说明</div>
  <div class="prompt_text">
    <ul>
      <li>模块管理可以很好的扩展网站运营中所需功能！</li>
      <li><font color="#FF0000">安装新模块后，需要刷新后台界面才能看到对应模块菜单。</font></li>
    </ul>
  </div>
  <div class="h_a">搜索</div>
  <div class="search_type  mb10">
    <form action="{:U('index')}" method="post">
      <div class="mb10">
        <div class="mb10"> <span class="mr20"> 模块名称：
          <input type="text" class="input length_2" name="keyword" style="width:200px;" value="{$keyword}" placeholder="请输入关键字...">
          <button class="btn">搜索</button>
          </span> </div>
      </div>
    </form>
  </div>
  <div class="loading">loading...</div>
</div>
<script src="{$config_siteurl}statics/js/common.js"></script>
<script>
$(function(){
	$.get('{:U("index")}',{ page:'{$page}',keyword:'{$keyword}',r:Math.random() },function(data){
		$('#loadhtml').append(data);
		$('div.loading').hide();
	});
});
</script>
</body>
</html>