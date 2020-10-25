<?php 
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
include 'Head.php';
include 'Action/Root_PageVI_Action.php';  
$Post_Code = mt_rand(0,1000000);
$_SESSION['Post_Code'] = $Post_Code;
 ?>
<div id="Wrapper2">
<aside id="sidebar-wrapper">
   <?php 
if ($Mark_Url_Action == '') { ?>
  <nav class="sidebar">
   <h1>新的</h1>
   <ul>
 <?php Root_Links('<li>','</li>'); ?>
   </ul>
  </nav>
 <?php } ?>
 <section class="sidebar">
  <h1>充实人生</h1>
  <nav class="sidebar">
  <ul>
  <span id="Text" style="color:green;"></span>
  </ul>
  </nav>
<script src="<?php __ROOT__('Js/life.js');?>"></script>
  </section>
 </aside>
<article id="contents">
  <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
  <input type="hidden" name="Post_VI_Action" value="<?php echo $Post_Code;?>"/>
  <?php if ($succeed) {   if ($page_state == 'publish') { ?>
  <div class="updated">页面已添加。 
 <a href="<?php  if ($Mark_Config_Action['write'] == 'open') {
  echo $Mark_Config_Action ['site_link'].$_SESSION['level'].'/'.$page_path.'.html';
 } else {
   echo $Mark_Config_Action ['site_link'].$_SESSION['level'].'/?'.$page_path.'/';
 }
  ?>" target="_blank">查看页面</a></div>
  <?php  } else { ?>
  <div class="updated">页面已保存到“草稿箱”。 <a href="Page.php?state=draft">打开草稿箱</a></div>
  <?php  }  } if (isset($error_msg)) { ?>
  <div class="updated"><?php  echo $error_msg; ?></div>
  <?php } ?>
  <div class="admin_page_name">
  <?php if ($page_path == ''){  echo "创建页面";}else {echo "编辑页面"; }?>
  </div>
  <div>
    <input name="title" type="text" class="edit_textbox" placeholder="在此输入标题" value="<?php echo htmlspecialchars(strip_tags($page_title)); ?>"/>
  </div>
  评论是否：
    <select name="can_comment" style="margin-right:16px;">
      <option value="1" <?php
if ($page_can_comment == '1') echo 'selected="selected";'; ?>>允许</option>
      <option value="0" <?php
if ($page_can_comment == '0') echo 'selected="selected";'; ?>>禁用</option>
    </select> 
    前台显示：
    <select name="index_show" style="margin-right:16px;">
      <option value="1" <?php
if ($page_show == '1') echo 'selected="selected";'; ?>>允许</option>
      <option value="0" <?php
if ($page_show == '0') echo 'selected="selected";'; ?>>禁用</option>
    </select> 
    <?php  if ($page_title == '') { ?>
    <input type="hidden" name="file" value="<?php echo $page_file; ?>"/>
    <input type="submit" name="save" value="添加" style="padding:6px 20px;"/><br />
    <?php } else {?>
    <input type="hidden" name="file" value="<?php echo $page_file; ?>"/>
    <input type="submit" name="save" value="修改" style="padding:6px 20px;"/><br />
    <?php } if ($page_title != '') { ?>
   <div style="float:left">
    添加时间：
    <select name="year">
      <option value="<?php echo substr($page_date, 0, 4);?>"><?php echo substr($page_date, 0, 4);?></option>
    </select> -
    <select name="month">
      <option value="<?php echo substr($page_date, 5, 2);?>"><?php echo substr($page_date, 5, 2);?></option>
    </select> -
    <select name="day">
      <option value="<?php echo substr($page_date, 8, 2);?>"><?php echo substr($page_date, 8, 2);?></option>
    </select>&nbsp;
    <select name="hourse">
      <option value="<?php echo substr($page_time, 0, 2);?>"><?php echo substr($page_time, 0, 2);?></option>
    </select> :
    <select name="minute">
      <option value="<?php echo substr($page_time, 3, 2);?>"><?php echo substr($page_time, 3, 2);?></option>
    </select> 
    </div>
    <?php } if ($page_title != '') { ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    添加状态：
    <select name="state" style="width:100px;">
     <option value="publish" <?php if ($page_state == 'publish') echo 'selected="selected"'; ?>>发布</option>
      <option value="draft" <?php  if ($page_state == 'draft') echo 'selected="selected"'; ?>>草稿</option>
    </select>
    <?php } ?>
     <div>
    <input name="path" type="text" class="edit_textbox" placeholder="在此输入页面路径，多级路径用半角斜杠(/)分割" value="<?php echo htmlspecialchars($page_path); ?>"/>
  </div>
  <div style="margin-bottom:20px;">
    <?php include 'Action/Root_VI_Action.php';   VI($page_content); ?>
  </div>
</form>
</article>
</div>
<?php include 'Footer.php'; ?>
</div>
</body>
</html>