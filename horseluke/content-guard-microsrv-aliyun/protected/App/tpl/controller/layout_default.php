<?php 
use SCH60\Kernel\KernelHelper;
?>
<!DOCTYPE html>
<html lang="zh-cn">
  <head>
  <?php KernelHelper::render('widget/common/indexHead', array('title' => (isset($title) ? $title : ''), 'parentTitle' =>  isset($parentTitle) ? $parentTitle : '')) ?>
  
  </head>
  
  <body >
  
  <?php KernelHelper::render('widget/common/nav') ?>
  
    <div id="mainContent"><?=$content?></div>
    
    <?php KernelHelper::render('widget/common/indexFooter') ?>
    
  </body>
  
</html>