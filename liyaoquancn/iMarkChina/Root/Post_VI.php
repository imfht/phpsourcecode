<?php 
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
include 'Head.php';
include 'Action/Root_PostVI_Action.php';
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
  <?php if ($succeed) { if ($post_state == 'publish') { ?>
  <div class="updated">日志已添加。 
  <?php
  if ($Mark_Config_Action['write'] == 'open') {
     echo "<a href=\"".$Mark_Config_Action['site_link'].$_SESSION['level']."/post-".$post_id.".html\" target=\"_blank\">查看日志</a>";
  }else{
    echo "<a href=\"".$Mark_Config_Action['site_link'].$_SESSION['level']."/?post/".$post_id."\" target=\"_blank\">查看日志</a>";
  }
  ?>
</div>
  <?php  } else { ?>
  <div class="updated">日志已保存到"草稿箱"。 <a href="Post.php?state=draft">打开草稿箱</a></div>
  <?php } } if (isset($error_msg)) { ?>
  <div class="updated"><?php echo $error_msg;  ?></div>
  <?php } ?>
  <div class="admin_page_name">
  <?php if ($post_id == '') echo "撰写日志"; else echo "编辑日志"; ?>
  </div>
  <div>
    <input name="title" type="text" class="edit_textbox" placeholder="在此输入标题" 
    value="<?php echo htmlspecialchars($post_title); ?>"/>
  </div>
 评论是否：
    <select name="can_comment" style="margin-right:16px;">
      <option value="1" <?php if ($post_can_comment == '1') echo 'selected="selected";';?>>允许</option>
      <option value="0" <?php if ($post_can_comment == '0') echo 'selected="selected";';?>>禁用</option>
    </select><input type="hidden" name="id" value="<?php echo $post_id; ?>"/>
    <?php if ($post_id == '') { ?>
    <input type="submit" name="save" value="添加" style="padding:6px 20px;"/><br /> 
    <?php } else { ?>
    <input type="submit" name="save" value="修改" style="padding:6px 20px;"/><br /> 
    <?php } if ($post_id != '') { ?>
   <div style="float:left">
    添加时间：
    <select name="year">
      <option value="<?php echo substr($post_date, 0, 4);?>"><?php echo substr($post_date, 0, 4);?></option>
    </select> -
    <select name="month">
      <option value="<?php echo substr($post_date, 5, 2);?>"><?php echo substr($post_date, 5, 2);?></option>
    </select> -
    <select name="day">
      <option value="<?php echo substr($post_date, 8, 2);?>"><?php echo substr($post_date, 8, 2);?></option>
    </select>&nbsp;
    <select name="hourse">
      <option value="<?php echo substr($post_time, 0, 2);?>"><?php echo substr($post_time, 0, 2);?></option>
    </select> :
    <select name="minute">
      <option value="<?php echo substr($post_time, 3, 2);?>"><?php echo substr($post_time, 3, 2);?></option>
    </select> 
    </div>
     <?php }  if ($post_id != '') { ?>
     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 添加状态：
    <select name="state" style="width:100px;">
    <option value="publish" <?php if ($post_state == 'publish') echo 'selected="selected"';?>>发布</option>
   <option value="draft" <?php  if ($post_state == 'draft') echo 'selected="selected"';?>>草稿</option>
</select>
<?php } ?>
<div>
<input name="tags" type="text" class="edit_textbox" placeholder="在此输入标签，多个标签之间用逗号分隔" 
value="<?php echo htmlspecialchars(implode('，', $post_tags));?>"/>
</div>
<div style="margin-bottom:20px;">
<?php include 'Action/Root_VI_Action.php'; VI($post_content); ?>
  </div>
</form>
</article>
</div>
<?php include 'Footer.php'; ?>
</div>
</body>
</html>