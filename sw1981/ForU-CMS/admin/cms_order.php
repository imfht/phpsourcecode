<?php
$privilege = 'order';
include '../library/inc.php';
include_once LIB_PATH . 'cls.page.php';

if (isset($_POST['execute'])) {
  null_back(@$_POST['id'], '请至少选中一项！');
  $id = $s = '';
  foreach ($_POST['id'] as $value) {
    $id .= $s . $value;
    $s = ',';
  }
  switch ($_POST['execute_method']) {
    case 0:
      $sql = "UPDATE order SET o_state = 0 WHERE id in (" . $id . ")";
      break;
    case 1:
      $sql = "UPDATE order SET o_state = 1 WHERE id in (" . $id . ")";
      break;
    case 2:
      $sql = "UPDATE order SET o_state = 2 WHERE id in (" . $id . ")";
      break;
    case 3:
      $sql = "UPDATE order SET o_state = 3 WHERE id in (" . $id . ")";
      break;
    case 5:
      $sql = "UPDATE order SET o_state = 5 WHERE id in (" . $id . ")";
      break;
    case 6:
      $sql = "UPDATE order SET o_state = 6 WHERE id in (" . $id . ")";
      break;
    case 7:
      $sql = "UPDATE order SET o_state = 7 WHERE id in (" . $id . ")";
      break;
    case 8:
      $sql = "UPDATE order SET o_state = 8 WHERE id in (" . $id . ")";
      break;
    case 9:
      $sql = "UPDATE order SET o_state = 9 WHERE id in (" . $id . ")";
      break;
    case 'delete':
      $sql = "DELETE FROM order WHERE id IN (" . $id . ")";
      admin_log('批量信息删除');
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

        <section class="am-panel am-panel-default">
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">订单管理<span class="am-icon-chevron-down am-fr"></span></header>
          <main class="am-panel-bd am-collapse am-in am-scrollable-horizontal" id="collapse-panel-1">
            <form method="get" class="am-form">
              <div class="am-g">
                <div class="am-u-md-12">
                  <div class="am-input-group am-input-group-sm">
                    <input id="key" class="am-form-field" type="text" name="key" placeholder="订单号" />
                    <span class="am-input-group-btn"><button type="submit" id="search" class="am-btn">检索</button></span>
                  </div>
                </div>
              </div>
            </form>
            <hr>
            <form action="" method="post">
            <table class="am-table am-table-striped am-table-bordered am-table-compact admin-content-table am-text-nowrap">
              <thead>
              <tr>
                <th>ID</th><th>订单号</th><th>物品</th><th>价值</th><th>日期</th><th>状态</th><th>操作</th>
              </tr>
              </thead>
              <tbody>
                <?php
                if (!empty($_GET['order'])) {
                  $order = "ORDER BY id ASC";
                } else {
                  $order = "ORDER BY id DESC";
                }
                $where = "WHERE id>0" . (!empty($_GET['key'])?" AND o_sn LIKE '%" . $_GET['key'] . "%'":"");
                $pager = new Page(20);
                $pager->handle($db->getOne("SELECT COUNT(*) FROM order $where"));
                $res = $db->getAll("SELECT * FROM order $where $order LIMIT " . $pager->page_start . "," . $pager->page_size);
                if (!empty($res)) {
                  foreach($res as $row){
                ?>
                <tr>
                  <td><input type="checkbox" name="id[]" value="<?php echo $row['id'];?>" /></td>
                  <td><?php echo $row['o_sn'] ?></td>
                  <td><?php echo $row['o_qty'];?></td>
                  <td><?php echo $row['o_cost'];?></td>
                  <td><?php echo local_date('y-m-d', $row['o_date']);?></td>
                  <td><?php echo $row['o_state'];?></td>
                  <td><a href="cms_order_edit.php?id=<?php echo $row['id']?>" class="am-btn am-btn-default am-btn-xs"><span class="am-icon-pencil"></span></a></td>
                </tr>
                <?php
                  }
		            }
                ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="7">
                      <input type="button" id="check_all" value="全选" />
                      <input type="button" class="form_button" id="check_none" value="不选" />
                      <input type="button" class="form_button" id="check_invert" value="反选" />
                      <select id="execute_method" name="execute_method">
                        <option value="">请选择操作</option>
                        <option value="0">未付款</option>
                        <option value="1">已付款</option>
                        <option value="2">等待收货</option>
                        <option value="3">确认收货</option>
                        <option value="5">撤销</option>
                        <option value="6">退款中</option>
                        <option value="7">退款完成</option>
                        <option value="8">交易完成</option>
                        <option value="9">已撤销</option>
                        <option value="delete">删除</option>
                      </select>
                      <input type="submit" id="execute" name="execute" onclick="return confirm('确定要执行吗')" value="执行" />
                  </td>
                </tr>
              </tfoot>
            </table>
            </form>
            <div data-am-page="{pages:<?php echo $pager->page_sum;?>,curr:<?php echo $pager->page_current;?>,jump:'?page=%page%'}"></div>
          </main>
        </section>

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
    if ($('#execute_method').val() == ''){
      alert('请选择一项要执行的操作！');
      return false;
    };
    if ($('input[name="id[]"]').val() = ''){
      alert('请至少选择一项！');
      return false;
    };
  });
  //频道转移验证
  $('#shift').click(function(){
    if ($('#shift_target').val() == ''){
      alert('请选择要转移到的频道！');
      return false;
    };
    if ($('input[name="id[]"]').val() = ''){
      alert('请至少选择一项！');
      return false;
    };
  });
  //搜索验证
  $('#search').click(function(){
    if ($('#key').val() == ''){
      alert('请输入要查找的关键字');
      $('#key').focus();
      return false;
    };
  });
});
</script>
</body>
</html>
