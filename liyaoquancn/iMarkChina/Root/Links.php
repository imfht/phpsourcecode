<?php 
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
include 'Head.php';
include 'Action/Root_Links_Action.php';
?>
<script type="text/javascript">
function do_filter()
{
  var date = document.getElementById('date');
  
  location.href = '?state=<?php
echo $state; ?>&date=' + date.value;
}
function goto_page(e)
{
  var evt = e || window.event;
  var eventSrc = evt.target||evt.srcElement;

  if ((e.keyCode || e.which) == 13) {
    location.href = '?state=<?php
echo $state; ?>&date=<?php
echo $filter_date; ?>&page=' + eventSrc.value;
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
   <?php if (isset($message)) { echo $message;  } ?>
<div class="admin_page_name">链接管理<a class="link_button" href="Links_VI.php">创建链接</a></div>
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
    <td>标题</td><td style="width:25%">链接</td><td style="width:15%">日期</td>
    </tr>
  </thead>
  <tbody>
  <?php for ($i = 0; $i < $Links_count; $i++) {if ($i < ($Links_num - 1) * 10 || $i >= ($Links_num * 10)) continue;$Links_id = $Links_ids[$i];$links = $Mark_Links_Action[$Links_id]; ?>
    <tr> <td></td><td>
<a class="row_name" href="Links_VI.php?file=<?php echo $links['file']; ?>"><?php echo htmlspecialchars($links['title']); ?></a>
        <div class="row_tool">
          <a class="link_button" href="Links_VI.php?file=<?php echo $links['file']; ?>">编辑</a>
          <?php if ($state == 'delete') { ?>
          <a class="link_button" href="?revert=<?php echo $Links_id; ?>&state=<?php echo $state; ?>&date=<?php echo $filter_date; ?>">还原</a>
          <a class="link_button" href="?delete=<?php echo $Links_id; ?>&state=<?php echo $state; ?>&date=<?php echo $filter_date; ?>">删除</a>
          <?php } else { ?>
          <a class="link_button" href="?delete=<?php  echo $Links_id; ?>&state=<?php echo $state; ?>&date=<?php echo $filter_date; ?>">回收</a>
          <?php } ?>
        </div>
      </td>
      <td><a href="<?php echo htmlspecialchars($links['url']); ?>"  target="_blank"><?php echo htmlspecialchars($links['url']); ?></a></td>
      <td><?php echo htmlspecialchars($links['date']); ?></td>
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
      <option value="<?php
    echo $date_name; ?>" <?php
    if ($filter_date == $date_name) echo ' selected="selected"'; ?>><?php
    echo $date_name; ?></option>
      <?php } ?>
    </select>
    <input type="submit" value="筛选" onclick="do_filter();"/>
  </span>
  <span class="pager">
    共 <?php echo $Links_count; ?> 个链接&nbsp;&nbsp;
    <a class="link_button" href="?state=<?php echo $state; ?>&date=<?php echo $filter_date; ?>">&laquo;</a>
    <a class="link_button" href="?state=<?php echo $state; ?>&date=<?php echo $filter_date; ?>&page=<?php echo $prev_page; ?>">&lsaquo;</a>
    第 <input type="text" value="<?php echo $Links_num; ?>" id="page_input_1"/> 页,共 <?php echo $last_page; ?> 页<a class="link_button" href="?state=<?php echo $state; ?>&date=<?php echo $filter_date; ?>&page=<?php echo $next_page; ?>">&rsaquo;</a>
    <a class="link_button" href="?state=<?php echo $state; ?>&date=<?php echo $filter_date; ?>&page=<?php echo $last_page; ?>">&raquo;</a>
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