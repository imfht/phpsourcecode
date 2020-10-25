<?php 
include(INCLUDES."/header.php");
include(INCLUDES."/nav.php");

$text_styles = array(
  LEVEL_SUCCESS=>'text-success',
  LEVEL_INFO=>'text-info',
  LEVEL_WARNING=>'text-warning',
  LEVEL_DANGER=>'text-danger'
);
?>

<?php 
$text_style = $text_styles[$level];
?>

<div class="main">
    <div class="main-content">
      <!-- 显示信息 -->
      <div class="row <?php echo $text_style; ?>">
        <?php echo $content; ?>
      </div>

      <!-- 页面跳转计时器 -->
      <?php
      if(isset($jump)){
        echo "<div class=\"row text-info\">
            <span id=\"time-counter\">5</span>
            秒后自动跳转，如未跳转，请点击<a id=\"jump\" href=\"$jump\">这里</a>
        </div>";
      }
      ?>

  </div>
</div>
<?php
include(INCLUDES."/footer.php");
?>
