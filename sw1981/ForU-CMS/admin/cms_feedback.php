<?php
$privilege = 'qa';
include '../library/inc.php';
include_once LIB_PATH . 'cls.page.php';

if (isset($_POST['execute'])) {
  null_back($_POST['id'], '请至少选中一项！');
  $s = '';
  foreach ($_POST['id'] as $value) {
    $id .= $s . $value;
    $s = ',';
  }
  switch ($_POST['execute_method']) {
    case 'sok':
      $sql = "UPDATE feedback SET f_ok = 1 WHERE id IN (" . $id . ")";
      break;
    case 'cok':
      $sql = "UPDATE feedback SET f_ok = 0 WHERE id IN (" . $id . ")";
      break;
    case 'delete':
      $sql = "DELETE FROM feedback WHERE id IN (" . $id . ")";
      admin_log('反馈删除[' . $id . ']');
      break;
    default:
      alert_back('请选择要执行的操作');
  }
  $dataops->ops($sql, '', 1);
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

        <div class="am-panel am-panel-default">
          <div class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">留言管理<span class="am-icon-chevron-down am-fr"></span></div>
          <div class="am-panel-bd am-collapse am-in am-scrollable-horizontal" id="collapse-panel-1">
            <form action="" method="post">
            <table class="am-table am-table-striped am-table-bordered am-table-compact admin-content-table am-text-nowrap">
              <thead>
              <tr>
                <th>ID</th><th>状态</th><th>留言人</th><th>联系电话</th><th>留言日期</th><th>回复</th>
              </tr>
              </thead>
              <tbody>
                <?php
                $pager = new Page(20);
                $pager->handle($db->getOne("SELECT COUNT(*) FROM feedback"));
                $res = $db->getAll("SELECT * FROM feedback ORDER BY id DESC LIMIT " . $pager->page_start . "," . $pager->page_size);
                if (!empty($res)) {
                  foreach ($res as $row) {
                    if ($row['f_ok'] == 0) {
                      $temp_str = '<span style="color:red">未审</span>';
                    } else {
                      $temp_str = '已审';
                    }
                    echo '<tr><td><input class="form_checkbox" type="checkbox" name="id[]" value="' . $row['id'] . '" /></td><td>' . $temp_str . '</td><td>' . $row['f_name'] . '</td><td>' . $row['f_tel'] . '</td><td>' . local_date('Y-m-d',$row['f_date']) . '</td><td><a href="cms_feedback_answer.php?id=' . $row['id'] . '">回复</a></td></tr>';
                  }
                }
                ?>
                <tr>
                  <td colspan="6">
                    <button type="button" class="form_button" id="check_all">全选</button>
                    <button type="button" class="form_button" id="check_none">不选</button>
                    <button type="button" class="form_button" id="check_invert">反选</button>
                    <select name="execute_method" id="execute_method">
                      <option value="">请选择操作</option>
                      <option value="sok">审核留言</option>
                      <option value="cok">取消审核</option>
                      <option value="delete">删除选中</option>
                    </select>
                    <button type="submit" class="form_button" id="execute" name="execute" onclick="return confirm('确定要执行吗')">执行</button>
                  </td>
                </tr>
              </tbody>
            </table>
            </form>
            <div data-am-page="{pages:<?php echo $pager->page_sum;?>,curr:<?php echo $pager->page_current;?>,jump:'?page=%page%'}"></div>
          </div>
        </div>

      </div>
    </div>
  </div>
  <!-- content end -->
</div>

<?php include 'inc/inc_footer.php';?>

<!-- js -->
<script type="text/javascript">
  $(function(){
    $('#check_all').click(function(){
      $('input[name="id[]"]:checkbox').prop('checked',true);
    });
    $('#check_none').click(function(){
      $('input[name="id[]"]:checkbox').prop('checked',false);
    });
    $('#check_invert').click(function(){
      $('input[name="id[]"]:checkbox').each(function(){
        this.checked = !this.checked;
      });
    });
    //操作执行验证
    $('#execute').click(function(){
      if ($('#execute_method').val() == '') {
        alert('请选择一项要执行的操作！');
        return false;
      };
      if ($('input[name="id[]"]').val() = '') {
        alert('请至少选择一项！');
        return false;
      };
    });
  });
</script>
</body>
</html>
