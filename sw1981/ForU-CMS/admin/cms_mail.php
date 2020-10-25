<?php
$privilege = 'subscribe';
include '../library/inc.php';
include_once LIB_PATH . 'cls.page.php';

if (@$_POST['size']) {
  setcookie('cms[mail_size]', intval($_POST['size']), time() + COOKIE_EXPIRE);
  href('cms_mail.php');
}
$size = @$_COOKIE['cms']['mail_size'] ? intval($_COOKIE['cms']['mail_size']) : 20;
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

        <section class="am-panel am-panel-default">
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">订阅信息<span class="am-icon-chevron-down am-fr"></span></header>
          <main class="am-panel-bd am-collapse am-text-break am-in" id="collapse-panel-1">
            <div class="am-g">

              <div class="am-u-sm-8">
                <form class="am-form" method="post" action="">
                  <div class="am-input-group am-input-group-sm">
                    <select name="size" id="size" class="am-form-field">
                      <option value="20" <?php echo $size==20?'selected="selected"':'';?>>每页20条</option>
                      <option value="40" <?php echo $size==40?'selected="selected"':'';?>>每页40条</option>
                      <option value="100" <?php echo $size==100?'selected="selected"':'';?>>每页100条</option>
                      <option value="200" <?php echo $size==200?'selected="selected"':'';?>>每页200条</option>
                      <option value="500" <?php echo $size==500?'selected="selected"':'';?>>每页500条</option>
                    </select>
                    <span class="am-input-group-btn">
                      <button class="am-btn am-btn-default" type="submit">切换</button>
                    </span>
                  </div>
                </form>
              </div>
              <div class="am-u-sm-4">
                <a href="../ajax.php?act=mail" class="am-btn am-btn-default am-btn-sm am-fr">数据导出</a>
              </div>
            </div>
            <hr>
            <?php
            $pager = new Page($size);
            $pager->handle($db->getOne("SELECT COUNT(*) FROM subscribe"));
            $res = $db->getAll("SELECT sub_mail FROM subscribe ORDER BY id DESC LIMIT " . $pager->page_start . "," . $pager->page_size);
            echo '<textarea class="am-form-field" rows="15" id="doc-ta-1" onClick="select();">';
            foreach ($res as $key=>$val) {
              if ($key==0) {
                echo $val['sub_mail'];
              } else {
                echo ',' . $val['sub_mail'];
              }
            }
            echo '</textarea>';
            ?>
            <div data-am-page="{pages:<?php echo $pager->page_sum;?>,curr:<?php echo $pager->page_current;?>,jump:'?page=%page%'}"></div>
          </main>
        </section>

      </div>
    </div>
  </div>
  <!-- content end -->
</div>

<?php include 'inc/inc_footer.php';?>
</body>
</html>
