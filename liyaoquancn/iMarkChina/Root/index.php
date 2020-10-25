<?php /*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
include 'Head.php'; ?>
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
    <h1>Successful Landing Welcome Home ! </h1>
  </header>
  <p> 感谢你的使用,你可以... </p>
        <p> <a href="Post_VI.php" class="btn btn-primary btn-large">发布一篇日志</a> 
        <a href="Page_VI.php" class="btn btn-primary btn-large">创建一个页面</a> </p>
           <br />日志:
    <div class="progress">
      <span class="red" style="width:<?php Mark_The_Styel_Post(); ?>%;">
      <span><?php Mark_The_Styel_Post(); ?>%</span></span>
    </div>
     月分:
    <div class="progress">
     <span class="green" style="width:<?php Mark_The_Styel_Date(); ?>%;">
     <span><?php Mark_The_Styel_Date(); ?>%</span></span>
    </div>
    标签:
    <div class="progress">
    <span class="orange" style="width: <?php Mark_The_Styel_Tags(); ?>%;">
    <span><?php Mark_The_Styel_Tags(); ?>%</span></span>
    </div>
</article>
</div>
<?php include 'Footer.php'; ?>
</div>
</body>
</html>