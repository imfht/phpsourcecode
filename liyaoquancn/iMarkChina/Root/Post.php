<?php 
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
include 'Head.php';
include 'Action/Root_Post_Action.php';
?> 
<script type="text/javascript">
function do_filter()
{
  var tag = document.getElementById('tag');
  var date = document.getElementById('date');
  
  location.href = '?state=<?php
echo $state; ?>&tag=' + tag.value + '&date=' + date.value;
}
function goto_page(e)
{
  var evt = e || window.event;
  var eventSrc = evt.target||evt.srcElement;

  if ((e.keyCode || e.which) == 13) {
    location.href = '?state=<?php
echo $state; ?>&date=<?php
echo $filter_date; ?>&tag=<?php
echo $filter_tag; ?>&page=' + eventSrc.value;
  }
}
</script>
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
  <header>
      <?php if (isset($message)) {  echo $message; } ?>
<div class="admin_page_name">日志管理<a class="link_button" href="Post_VI.php">撰写日志</a></div>
<div class="post_mode_link">
<a href="?state=publish" class="link_button <?php if ($state == 'publish') echo 'current'; ?>">已发布</a>
<a href="?state=draft" class="link_button <?php if ($state == 'draft') echo 'current'; ?>">草稿箱</a>
<a href="?state=delete" class="link_button <?php if ($state == 'delete') echo 'current'; ?>">回收站</a>
</div>
  </header>
  <div class="table_list post_list">
<table colspan="0" rowspan="0" cellpadding="0" cellspacing="0" id="list">
  <thead>
    <tr>
    <td style="width:20px"></td>
    <td>标题</td><td style="width:25%">标签</td><td style="width:15%">日期</td>
    </tr>
  </thead>
  <tbody>
  <?php
for ($i = 0; $i < $post_count; $i++) {
    if ($i < ($page_num - 1) * 10 || $i >= ($page_num * 10)) continue;
    $post_id = $post_ids[$i];
    $post = $Mark_Posts_Action[$post_id]; ?>
    <tr>
      <td></td>
      <td>
        <a class="row_name" href="Post_VI.php?id=<?php echo $post_id; ?>"><?php echo htmlspecialchars($post['title']); ?></a>
        <div class="row_tool">
          <a class="link_button" href="Post_VI.php?id=<?php echo $post_id; ?>">编辑</a>
          <?php if ($state == 'delete') { ?>
          <a class="link_button" href="?revert=<?php  echo $post_id; ?>&state=<?php  echo $state; ?>&date=<?php echo $filter_date; ?>&tag=<?php echo $filter_tag; ?>">还原</a>
          <a class="link_button" href="?delete=<?php  echo $post_id; ?>&state=<?php echo $state; ?>&date=<?php  echo $filter_date; ?>&tag=<?php  echo $filter_tag; ?>">删除</a>
          <?php } else { ?>
          <a class="link_button" href="?delete=<?php echo $post_id; ?>&state=<?php echo $state; ?>&date=<?php echo $filter_date; ?>&tag=<?php echo $filter_tag; ?>">回收</a>
          <?php  } ?>
          <a class="link_button" href="<?php if ($Mark_Config_Action['write'] == 'open') {
           echo $_SESSION['level'].'/post-'.$post_id.'.html';
          } else {
            echo $_SESSION['level'].'/?post/'.$post_id;
          }
           ?>" target="_blank">查看</a>
        </div>
      </td>
      <td><?php  $tags = $post['tags']; $tag_count = count($tags); for ($Mark = 0; $Mark < $tag_count; $Mark++) { $tag = $tags[$Mark];?>
      <a href="?state=<?php echo $state; ?>&date=<?php echo $filter_date; ?>&tag=<?php echo htmlspecialchars($tag); ?>">
      <?php echo Getstr(htmlspecialchars($tag),13); ?></a><?php if ($Mark < $tag_count - 1) echo ',&nbsp;'; }?></td>
      <td><?php echo htmlspecialchars($post['date']); ?></td>
    </tr>
  <?php } ?>
  </tbody>
</table>
</div>
<div class="table_list_tool">
  <span>
    <select id="date">
      <option value="">显示所有日期</option>
      <?php foreach ($date_array as $date_name) { ?>
      <option value="<?php echo $date_name; ?>" <?php if ($filter_date == $date_name) echo ' selected="selected"'; ?>>
      <?php echo $date_name; ?></option>
      <?php } ?>
    </select>
    <select id="tag">
      <option value="">显示所有标签</option>
      <?php foreach ($tags_array as $tag_name) { ?>
      <option value="<?php echo $tag_name; ?>" <?php if ($filter_tag == $tag_name) echo ' selected="selected"'; ?>>
      <?php echo Getstr($tag_name,13); ?></option>
      <?php } ?>
    </select>
    <input type="submit" value="筛选" onclick="do_filter();"/>
  </span>
  <span class="pager">
    共 <?php echo $post_count; ?> 篇日志&nbsp;&nbsp;
    <a class="link_button" href="?state=<?php echo $state; ?>&amp;date=<?php echo $filter_date; ?>&amp;tag=<?php echo $filter_tag; ?>">&laquo;</a>
    <a class="link_button" href="?state=<?php echo $state; ?>&amp;date=<?php echo $filter_date; ?>&amp;tag=<?php echo $filter_tag; ?>&amp;page=<?php echo $prev_page; ?>">&lsaquo;</a>
    第 <input type="text" value="<?php echo $page_num; ?>" id="page_input_2"/> 页,共 <?php echo $last_page; ?> 页
    <a class="link_button" href="?state=<?php echo $state; ?>&amp;date=<?php echo $filter_date; ?>&amp;tag=<?php echo $filter_tag; ?>&amp;page=<?php echo $next_page; ?>">&rsaquo;</a>
    <a class="link_button" href="?state=<?php echo $state; ?>&amp;date=<?php echo $filter_date; ?>&amp;tag=<?php echo $filter_tag; ?>&amp;page=<?php echo $last_page; ?>">&raquo;</a>
  </span>
  <script type="text/javascript">
  document.getElementById('page_input_1').onkeydown = goto_page;
  </script>
</div>
</article>
</div>
<?php include 'Footer.php'; ?>
</div>
</body>
</html>