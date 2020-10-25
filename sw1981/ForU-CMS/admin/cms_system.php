<?php
$privilege = 'base';
include '../library/inc.php';

if ($act == 'edit') {
  null_back($_POST['s_domain'], '请填写域名');
  null_back($_POST['s_name'], '请填写网络名称');
  $data['s_domain'] = str_safe($_POST['s_domain']);
  $data['s_name'] = str_safe($_POST['s_name']);
  $data['s_seoname'] = str_safe($_POST['s_seoname']);
  $data['s_keywords'] = str_safe($_POST['s_keywords']);
  $data['s_description'] = str_safe($_POST['s_description']);
  $data['s_copyright'] = str_isafe($_POST['s_copyright']);
  $data['s_code'] = str_isafe($_POST['s_code']);
  $data['s_user'] = isset($_POST['s_user']) ? intval($_POST['s_user']) : 0;
  $data['s_comment'] = isset($_POST['s_comment']) ? intval($_POST['s_comment']) : 0;
  $data['s_state'] = intval($_POST['s_state']);
  if (CART) {
    $data['s_freight'] = intval($_POST['s_freight']);
  }
  $sql = "UPDATE system SET " . arr_update($data) . " WHERE id = 1";
  $dataops->ops($sql, '系统编辑', 1);
}
?>
<!DOCTYPE html>
<html class="no-js fixed-layout">
<head>
<?php include 'inc/inc_head.php';?>
</head>

<body>
<?php include 'inc/inc_header.php';?>

<div class="am-cf admin-main">
  <!-- content start -->
  <div class="admin-content">
    <div class="am-g am-g-fixed">
      <div class="am-u-sm-12 am-padding-top">
        <?php
        $res = $db->getRow("SELECT * FROM system WHERE id = 1");
        if ($row = $res) {
        ?>
        <section class="am-panel am-panel-default">
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">系统设置<span class="am-icon-chevron-down am-fr"></span></header>
          <form action="" method="post" class="am-form">

            <main class="am-tabs am-margin" data-am-tabs="{noSwipe: 1}">
              <ul class="am-tabs-nav am-nav am-nav-tabs">
                <li class="am-active"><a href="#tab1">基本信息</a></li>
                <li><a href="#tab2">SEO</a></li>
              </ul>

              <div class="am-tabs-bd">
                <section class="am-tab-panel am-fade am-in am-active" id="tab1">
                  <div class="am-form-group">
                    <label for="s_domain">域名</label>
                    <input id="s_domain" type="text" name="s_domain" value="<?php echo $row['s_domain'];?>" placeholder="请在域名前添加http://，如:http://www.163.com">
                  </div>

                  <div class="am-form-group">
                    <label for="s_name">网站名称</label>
                    <input id="s_name" type="text" name="s_name" value="<?php echo $row['s_name']; ?>">
                  </div>

                  <div class="am-form-group">
                    <label for="s_copyright">版权信息</label>
                    <textarea id="s_copyright" name="s_copyright" class="editor"><?php echo $row['s_copyright']?></textarea>
                  </div>

                  <div class="am-form-group">
                    <label for="s_code">第三方代码</label>
                    <textarea id="s_code" name="s_code"><?php echo $row['s_code']?></textarea>
                  </div>
                  <!--
                  <div class="am-form-group">
                    <label for="s_user">用户注册审核</label>
                    <select name="s_user" id="s_user">
                      <option value="0" <?php echo $row['s_user'] == 0 ? 'selected = "selected"' :'';?>>关闭</option>
                      <option value="1" <?php echo $row['s_user'] == 1 ? 'selected = "selected"' :'';?>>开启</option>
                    </select>
                  </div>

                  <div class="am-form-group">
                    <label for="s_comment">评论功能</label>
                    <select name="s_comment" id="s_comment">
                      <option value="0" <?php echo $row['s_comment'] == 0 ? 'selected = "selected"' :'';?>>关闭</option>
                      <option value="1" <?php echo $row['s_comment'] == 1 ? 'selected = "selected"' :'';?>>开启</option>
                    </select>
                  </div>
                  -->
                  <div class="am-form-group">
                    <label for="s_state">网站状态</label>
                    <select name="s_state" id="s_state">
                      <option value="0" <?php echo $row['s_state'] == 0 ? 'selected = "selected"' :'';?>>开启</option>
                      <option value="1" <?php echo $row['s_state'] == 1 ? 'selected = "selected"' :'';?>>关闭</option>
                    </select>
                  </div>

                  <?php if(CART){ ?>
                  <div class="am-form-group">
                    <label for="s_freight">运费</label>
                    <input id="s_freight" type="text" name="s_freight" value="<?php echo $row['s_freight']; ?>">
                  </div>
                  <?php } ?>
                </section>

                <section class="am-tab-panel am-fade" id="tab2">
                  <div class="am-form-group">
                    <label for="s_seoname">优化标题</label>
                    <input id="s_seoname" type="text" name="s_seoname" value="<?php echo $row['s_seoname']; ?>">
                  </div>

                  <div class="am-form-group">
                    <label for="s_keywords">关键字</label>
                    <textarea id="s_keywords" type="text" name="s_keywords"><?php echo $row['s_keywords']; ?></textarea>
                  </div>

                  <div class="am-form-group">
                    <label for="s_description">关键描述</label>
                    <textarea id="s_description" name="s_description"><?php echo $row['s_description']; ?></textarea>
                  </div>
                </section>

                <center>
                  <button type="submit" name="submit" class="am-btn am-btn-default">提交保存</button>
                  <input type="hidden" name="act" value="edit">
                </center>
                <br>
              </div>
            </main>
          </form>
        </section>
        <?php } ?>
      </div>
    </div>
  </div>
  <!-- content end -->
</div>

<?php include 'inc/inc_footer.php';?>

<!-- js -->
<script type="text/javascript">
$(function(){
  $('#save').click(function(){
    if ($('#s_domain').val() == ''){
      alert('请填写域名');
      $('#c_name').focus();
      return false;
    }
    if ($('#s_name').val() == ''){
      alert('请填写网络名称');
      $('#c_name').focus();
      return false;
    }
  });
});
</script>
</body>
</html>
