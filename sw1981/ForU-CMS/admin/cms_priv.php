<?php
$privilege = 'priv';
include '../library/inc.php';

// 获取管理员权限
$rid = intval($_GET['id']);
$res = $GLOBALS['db']->getOne("SELECT r_priv FROM role WHERE id = " . $rid);
$priv = explode(',', $res);

// 更新权限至数据库
if ($act == 'update') {
  $_POST['rid'] = intval($_POST['rid']);

  foreach ($_POST as $key=>$val) {
    if ($val!='' && $key!='rid' && $key!='act') {
      $arr[$key] = $val;
    }
  }
  $priv = implode(',', $arr); //转为priv字串
  $sql = "UPDATE role SET r_priv = '$priv' WHERE id = " . $_POST['rid'];
  $dataops->ops($sql, '权限编辑[' . $_POST['rid'] . ']', 1, '', 'id=' . $_POST['rid']);
}
?>
<!DOCTYPE html>
<html class="no-js fixed-layout">
<head>
<?php include 'inc/inc_head.php';?>
<style>
  th { background: #EEE;}
  label{ padding:5px 20px; cursor: pointer; margin-bottom: 0;}
  input[type=checkbox], input[type=radio]{ margin: 4px 5px 0 0;}
</style>
</head>

<body>
<?php include 'inc/inc_header.php';?>

<div class="am-cf admin-main">
  <!-- content start -->
  <div class="admin-content">
    <div class="am-g am-g-fixed">
      <div class="am-u-sm-12 am-padding-top">

        <section class="am-panel am-panel-default">
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">权限管理<span class="am-icon-chevron-down am-fr"></span></header>
          <form action="" method="post">
          <main class="am-panel-bd am-collapse am-in am-scrollable-horizontal" id="collapse-panel-1">
            <table class="am-table am-table-bordered am-table-bordered am-table-compact am-text-nowrap">
              <tbody>
                <tr>
                  <th><label><input type="checkbox" name="cms" id="cms" value="">内容管理</label></th>
                  <td>
                    <?php echo getChannelPriv(0, $rid, $priv);?>
                    <hr>
                    <?php
                    $cms = $_lang['priv']['cms'];
                    foreach ($cms as $key=>$val) {
                      echo '<label for="' . $key . '"><input type="checkbox" name="' . $key . '" id="' . $key . '" value="' . $key . '" ' . (in_array($key, $priv) ? 'checked="checked"' : '') . '>' . $val . '</label>';
                    }
                    ?>
                  </td>
                </tr>
                <tr>
                  <th><label><input type="checkbox" name="int" id="int" value="">交互管理</label></th>
                  <td>
                    <?php
                    $int = $_lang['priv']['interaction'];
                    foreach ($int as $key=>$val) {
                      echo '<label for="' . $key . '"><input type="checkbox" name="' . $key . '" id="' . $key . '" value="' . $key . '" ' . (in_array($key, $priv) ? 'checked="checked"' : '') . '>' . $val . '</label>';
                    }
                    ?>
                  </td>
                </tr>
                <tr>
                  <th><label><input type="checkbox" name="sys" id="sys" value="">系统设置</label></th>
                  <td>
                    <?php
                    $sys = $_lang['priv']['system'];
                    foreach ($sys as $key=>$val) {
                      echo '<label for="' . $key . '"><input type="checkbox" name="' . $key . '" id="' . $key . '" value="' . $key . '" ' . (in_array($key, $priv) ? 'checked="checked"' : '') . '>' . $val . '</label>';
                    }
                    ?>
                  </td>
                </tr>
              </tbody>
            </table>
            <center>
              <button type="submit" name="submit" id="save" class="am-btn am-btn-default">提交保存</button>
              <button type="reset" class="am-btn am-btn-default">放弃保存</button>
              <input type="hidden" name="act" value="update">
              <input type="hidden" name="rid" value="<?php echo str_safe($rid);?>">
            </center>
          </main>
          </form>
        </section>

      </div>
    </div>
  </div>
  <!-- content end -->
</div>

<?php include 'inc/inc_footer.php';?>

<!-- js -->
<script>
$(function(){
  $('#cms,#int,#sys').click(function(){
    var p = $(this).parent().parent().parent();
    var v = $(this).attr('status');
    if (v==1) {
      p.find('input:checkbox').prop('checked', false);
      $(this).attr('status','0');
    }else{
      p.find('input:checkbox').prop('checked', true);
      $(this).attr('status','1');
    }
  });
  $('.common_table th>label>input').click(function(){
    var p = $(this).parent().parent().parent().parent();
    var v = $(this).attr('status');
    if (v==1) {
      p.find('input:checkbox').prop('checked', false);
      $(this).attr('status','0');
    }else{
      p.find('input:checkbox').prop('checked', true);
      $(this).attr('status','1');
    }
  });
})
</script>
</body>
</html>
