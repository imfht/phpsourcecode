<?php 
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
include 'Head.php';
include 'Action/Root_LinksVI_Action.php';  
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
  <?php if ($succeed) {   if ($links_state == 'publish') { ?>
  <div class="updated">链接已添加。 </div>
  <?php  } else { ?>
  <div class="updated">页面已保存到“草稿箱”。 <a href="Links.php?state=draft">打开草稿箱</a></div>
  <?php  }  } if (isset($error_msg)) { ?>
  <div class="updated"><?php  echo $error_msg; ?></div>
  <?php } ?>
  <div class="admin_page_name">
  <?php if ($links_path == ''){  echo "创建链接";}else {echo "编辑链接"; }?>
  </div>
  <div>
    <input name="title" type="text" class="edit_textbox" placeholder="在此输入标题" value="<?php echo htmlspecialchars($links_title); ?>"/>
     <input name="content" type="text" class="edit_textbox" placeholder="在此输入链接，必需以(http://)开头" value="<?php echo htmlspecialchars($links_content); ?>"/>
  </div>
    <?php  if ($links_title == '') { ?>
    <input type="hidden" name="file" value="<?php echo $links_file; ?>"/>
    <input type="submit" name="save" value="添加" style="padding:6px 20px;"/><br /><br />
    <?php } else {?>
    <input type="hidden" name="file" value="<?php echo $links_file; ?>"/>
    <input type="submit" name="save" value="修改" style="padding:6px 20px;"/><br /><br />
    <?php } if ($links_title != '') { ?>
   <div style="float:left">
    添加时间：
    <select name="year">
      <option value="<?php echo substr($links_date, 0, 4);?>"><?php echo substr($links_date, 0, 4);?></option>
    </select> -
    <select name="month">
      <option value="<?php echo substr($links_date, 5, 2);?>"><?php echo substr($links_date, 5, 2);?></option>
    </select> -
    <select name="day">
      <option value="<?php echo substr($links_date, 8, 2);?>"><?php echo substr($links_date, 8, 2);?></option>
    </select>&nbsp;
    <select name="hourse">
      <option value="<?php echo substr($links_time, 0, 2);?>"><?php echo substr($links_time, 0, 2);?></option>
    </select> :
    <select name="minute">
      <option value="<?php echo substr($links_time, 3, 2);?>"><?php echo substr($links_time, 3, 2);?></option>
    </select> 
    </div>
    <?php } if ($links_title != '') { ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    添加状态：
    <select name="state" style="width:100px;">
     <option value="publish" <?php if ($links_state == 'publish') echo 'selected="selected"'; ?>>发布</option>
      <option value="draft" <?php  if ($links_state == 'draft') echo 'selected="selected"'; ?>>草稿</option>
    </select>
    <?php } ?>
     <div>
    <input name="path" type="hidden" class="edit_textbox" value="<?php echo htmlspecialchars($links_path); ?>"/>
  </div>
</form>
</article>
</div>
<?php include 'Footer.php'; ?>
</div>
</body>
</html>