<?php if (!defined('SHUIPF_VERSION')) exit(); ?>
<Admintemplate file="Common/Head"/>
<body class="J_scroll_fixed">
<div class="wrap J_check_wrap">
  <Admintemplate file="Common/Nav"/>
  <div class="h_a">模型属性</div>
  <form action="{:U("add")}" method="post" class="J_ajaxForm" >
    <div class="table_full">
      <table width="100%"  class="table_form">
        <tr>
          <th width="120">模型名称：</th>
          <td class="y-bg"><input type="text" class="input" name="name" id="name" size="30" value="" /></td>
        </tr>
        <tr>
          <th>模型表键名：</th>
          <td class="y-bg"><input type="text" class="input" name="tablename" id="tablename" size="30" value="" /></td>
        </tr>
        <tr>
          <th>描述：</th>
          <td class="y-bg"><input type="text" class="input" name="description" id="description" value=""  size="30"/></td>
        </tr>
      </table>
    </div>
    <div class="">
      <div class="btn_wrap_pd">
        <button class="btn btn_submit mr10 J_ajax_submit_btn" type="submit">添加</button>
      </div>
    </div>
  </form>
</div>
<script src="{$config_siteurl}statics/js/common.js"></script>
</body>
</html>