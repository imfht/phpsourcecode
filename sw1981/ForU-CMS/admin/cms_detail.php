<?php
$privilege = 'detail';
include '../library/inc.php';
include_once LIB_PATH . 'cls.page.php';

if (isset($_POST['execute'])) {
  null_back(@$_POST['id'], '请至少选中一项！');
  $id = $s = '';
  foreach ($_POST['id'] as $value) {
    $id .= $s.$value;
    $s = ',';
  }
  switch ($_POST['execute_method']) {
    case 'srec':
      $sql = "UPDATE detail SET d_rec = 1 WHERE id in ($id)";
      break;
    case 'crec':
      $sql = "UPDATE detail SET d_rec = 0 WHERE id in ($id)";
      break;
    case 'shot':
      $sql = "UPDATE detail SET d_hot = 1 WHERE id in ($id)";
      break;
    case 'chot':
      $sql = "UPDATE detail SET d_hot = 0 WHERE id in ($id)";
      break;
    case 'spop':
      $sql = "UPDATE detail SET d_popup = 1 WHERE id in ($id)";
      break;
    case 'cpop':
      $sql = "UPDATE detail SET d_popup = 0 WHERE id in ($id)";
      break;
    case 'enable':
      $sql = "UPDATE detail SET d_enable = 1 WHERE id in ($id)";
      break;
    case 'disable':
      $sql = "UPDATE detail SET d_enable = 0 WHERE id in ($id)";
      break;
    case 'delete':
      $sql = "DELETE FROM detail WHERE id IN ($id)";
      admin_log('批量信息删除');
      break;
    default:
      alert_back('请选择要执行的操作');
  }
  $dataops->ops($sql, '', 1);
}
if ( isset($_POST['shift']) ) {
  null_back($_POST['id'], '请至少选中一项！');
  $s = '';
  foreach ($_POST['id'] as $value) {
    $id .= $s . $value;
    $s = ',';
  }
  null_back($_POST['shift_target'], '请选择要转移到的频道');
  $sql = "UPDATE detail SET d_parent = " . $_POST['shift_target'] . " WHERE id IN ($id)";
  $dataops->ops($sql, '信息转移[' . $id . ']', 1);
}

$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
$page = !empty($_GET['page']) ? intval($_GET['page']) : 1;
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
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">内容管理<span class="am-icon-chevron-down am-fr"></span></header>
          <main class="am-panel-bd am-collapse am-in am-scrollable-horizontal" id="collapse-panel-1">
            <form method="get" class="am-form">
              <div class="am-g">
                <div class="am-u-md-4">
                  <select onchange="location.href='cms_detail.php?cid='+this.options[this.selectedIndex].value;" class="am-input-sm">
                    <option value="0">全部</option>
                    <?php
                    echo channel_select_list($cids,0,$cid,0);
                    if (isset($_GET['key'])) {
                      echo '<option selected="selected" >搜索结果</option>';
                    }
                    ?>
                  </select>
                </div>
                <div class="am-u-md-8">
                  <div class="am-input-group am-input-group-sm">
                    <input id="key" class="am-form-field" type="text" name="key" placeholder="名称查找" />
                    <input type="hidden" name="cid" value="<?php echo str_safe($cid);?>">
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
                <th>ID</th><th>排序</th><th>名称</th><th>频道</th><th>属性</th><th>日期</th><th>操作</th>
              </tr>
              </thead>
              <tbody>
                <?php
                $where = "WHERE id > 0" . (!empty($_GET['key']) ? " AND d_name LIKE '%" . $_GET['key'] . "%'" : "");
                if ($cid!=0) {
                  $where .= " AND d_parent IN (" . get_channel($cid, 'c_sub') . ")";
                } else {
                  if ($cids) {
                    $where .= " AND d_parent IN ($cids)";
                  }
                }
                $pager = new Page(20);
                $pager->handle($db->getOne("SELECT COUNT(*) FROM detail $where"));
                $res = $db->getAll("SELECT * FROM detail $where ORDER BY id DESC LIMIT " . $pager->page_start . "," . $pager->page_size);
                if (!empty($res)) {
                foreach($res as $row){
                ?>
                <tr>
                  <td><input type="checkbox" name="id[]" value="<?php echo $row['id'];?>" /></td>
                  <td><?php echo $row['d_order'];?></td>
                  <td align="left"><?php echo '<a href="../detail.php?id=' . $row['id'] . '" target="_blank">' . $row['d_name'] . '</a>';?></td>
                  <td><?php echo get_channel($row['d_parent'], 'c_name');?></td>
                  <td>
                    <?php
                      echo $row['d_rec'] == 1 ? '<span class="am-badge am-badge-success">荐</span>':'';
                      echo $row['d_hot'] == 1 ? '<span class="am-badge am-badge-danger">热</span>':'';
                      echo $row['d_ifslideshow'] == 1 ? '<span class="am-badge am-badge-primary">图</span>':'';
                    ?>
                  </td>
                  <td><?php echo local_date('y-m-d', $row['d_date']);?></td>
                  <td><a href="cms_detail_edit.php?id=<?php echo $row['id'];?>&page=<?php echo $page;?>" class="am-btn am-btn-default am-btn-xs"><span class="am-icon-pencil"></span></a></td>
                </tr>
                <?php
                  }
		}
                ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="7">
                    <div class="am-fl">
                      <input type="button" id="check_all" value="全选" />
                      <input type="button" class="form_button" id="check_none" value="不选" />
                      <input type="button" class="form_button" id="check_invert" value="反选" />
                      <select id="execute_method" name="execute_method">
                        <option value="">请选择操作</option>
                        <option value="srec">设为推荐</option>
                        <option value="crec">取消推荐</option>
                        <option value="shot">设为热门</option>
                        <option value="chot">取消热门</option>
                        <option value="enable">设为生效</option>
                        <option value="disable">设为失效</option>
                        <option value="delete">删除选中</option>
                      </select>
                      <input type="submit" id="execute" name="execute" onclick="return confirm('确定要执行吗')" value="执行" />
                    </div>
                    <div class="am-fr">
                      <select id="shift_target" name="shift_target" style="width:150px;">
                        <option value="">请选择目标频道</option>
                        <?php echo channel_select_list(0, 0, 0, 0);?>
                      </select>
                      <input type="submit" id="shift" name="shift" onclick="return confirm('确定要转移吗');" value="转移" />
                    </div>
                  </td>
                </tr>
              </tfoot>
            </table>
            </form>
            <div data-am-page="{pages:<?php echo $pager->page_sum;?>,curr:<?php echo $pager->page_current;?>,jump:'?cid=<?php echo str_safe($cid);?>&page=%page%'}"></div>
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
    if ($('#execute_method').val() == '') {
      alert('请选择一项要执行的操作！');
      return false;
    };
    if ($('input[name="id[]"]').val() = '') {
      alert('请至少选择一项！');
      return false;
    };
  });
  //频道转移验证
  $('#shift').click(function(){
    if ($('#shift_target').val() == '') {
      alert('请选择要转移到的频道！');
      return false;
    };
    if ($('input[name="id[]"]').val() = '') {
      alert('请至少选择一项！');
      return false;
    };
  });
  //搜索验证
  $('#search').click(function(){
    if ($('#key').val() == '') {
      alert('请输入要查找的关键字');
      $('#key').focus();
      return false;
    };
  });
});
</script>
</body>
</html>
