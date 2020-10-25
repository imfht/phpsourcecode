<?php
include '../library/inc.php';
?>
<!DOCTYPE html>
<html class="js fixed-layout">
<head>
<?php include 'inc/inc_head.php';?>
</head>

<body>
<?php include 'inc/inc_header.php';?>

<div class="am-cf admin-main">
  <!-- content start -->
  <div class="admin-content">
    <div class="am-g am-g-fixed">

      <div class="am-cf am-padding">
        <strong class="am-text-primary am-text-lg">管理员: <?php echo getUserToken();?></strong>
      </div>

      <?php echo hook('adminIndex');?>

    </div>
  </div>
  <!-- content end -->
</div>

<?php include 'inc/inc_footer.php';?>
</body>
</html>
