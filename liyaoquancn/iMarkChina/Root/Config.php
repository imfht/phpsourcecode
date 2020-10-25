<?php 
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
include 'Head.php';
include 'Action/Root_Config_Action.php';
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
<header>
    <h1>站点设置</h1>
  </header>
 <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
  <?php if ($display_info) { echo $display_info; } ?>
  <div class="admin_page_name"></div>
  <div class="small_form small_form2">
  <div class="field_body"><input class="button" type="submit" name="save" value="保存设置" /></div>
    <div class="field">
      <div class="label">网站标题</div>
      <input class="textbox" type="text" name="site_name" value="<?php echo htmlspecialchars($site_name); ?>" />
    </div>
        <div class="clear"></div>
       <div class="field">
      <div class="label">网站副标题</div>
      <input class="textbox" type="text" name="nametwo" value="<?php echo htmlspecialchars($nametwo); ?>" />
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">每页日志数</div>
      <input class="textbox" type="text" name="site_mumber" value="<?php echo htmlspecialchars($site_mumber); ?>" />
    </div>
  <div class="clear"></div>
    <div class="field">
      <div class="label">网站风格</div>
             <select name="style">
      <option value="<?php echo htmlspecialchars($style ); ?>" elected="selected";><?php echo htmlspecialchars($style ); ?></option>
      <?php ShowDir($path);?>
    </select>
    </div>
        <div class="clear"></div>
    <div class="field">
      <div class="label">伪静态</div>
        <select name="write">
        <option value="open"<?php if ($write == 'open') echo  ' selected="selected";';?>>开启</option>
      <option value="close"<?php if ($write == 'close') echo  ' selected="selected";';?>>关闭</option>
    </select>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">友情链接</div>
        <select name="fdlinks">
        <option value="open"<?php if ($fdlinks == 'open') echo  ' selected="selected";';?>>开启</option>
      <option value="close"<?php if ($fdlinks == 'close') echo  ' selected="selected";';?>>关闭</option>
    </select>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">建站时间</div>
      <input class="textbox" type="text" name="runyear"  style="width:30px;height:20px" placeholder="年" value="<?php echo htmlspecialchars($runyear); ?>" />
        <input class="textbox" type="text" name="runmon"  style="width:30px;height:20px" placeholder="月"  value="<?php echo htmlspecialchars($runmon); ?>" />
         <input class="textbox" type="text" name="runday"  style="width:30px;height:20px" placeholder="日" value="<?php echo htmlspecialchars($runday); ?>" />
          <input class="textbox" type="text" name="runhour"  style="width:30px;height:20px" placeholder="时" value="<?php echo htmlspecialchars($runhour); ?>" />
           <input class="textbox" type="text" name="runmin"  style="width:30px;height:20px" placeholder="分" value="<?php echo htmlspecialchars($runmin); ?>" />
            <input class="textbox" type="text" name="runsec"  style="width:30px;height:20px" placeholder="秒" value="<?php echo htmlspecialchars($runsec); ?>" />
    </div>
  <div class="clear"></div>
    <div class="field">
      <div class="label">网站关键词</div>
      <input class="textbox" type="text" name="site_key" value="<?php echo htmlspecialchars($site_key); ?>" />
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">网站描述</div>
      <input class="textbox" type="text" name="site_desc" value="<?php echo htmlspecialchars($site_desc); ?>" />
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">网站地址</div>
      <input class="textbox" type="text" name="site_link" value="<?php echo htmlspecialchars($site_link); ?>" />
      <div class="info"></div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">站长昵称</div>
      <input class="textbox" type="text" name="user_nick" value="<?php echo htmlspecialchars($user_nick); ?>" />
      <div class="info"></div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">后台路径</div>
      <input class="textbox" type="text" name="root_link" value="<?php echo htmlspecialchars($root_link); ?>" />
      <div class="info">修改后需要通过FTP修改文件名(Root)。</div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">后台入口文件</div>
      <input class="textbox" type="text" name="root_file" value="<?php echo htmlspecialchars($root_file); ?>" />
      <div class="info">修改后需要通过FTP修改文件名(root.php)。</div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">后台帐号</div>
      <input class="textbox" type="text" name="user_name" value="<?php echo htmlspecialchars($user_name); ?>" />
      <div class="info"></div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">后台密码</div>
      <input class="textbox" type="password" name="user_pass" />
      <div class="info"></div>
    </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label">评论代码</div>
      <textarea rows="5" class="textbox" name="comment_code"><?php echo htmlspecialchars($comment_code); ?></textarea>
       </div>
       <div class="clear"></div>
    <div class="field">
      <div class="label">版权信息</div>
      <textarea rows="5" class="textbox" name="copy_right"><?php echo htmlspecialchars($copy_right); ?></textarea>
       </div>
    <div class="clear"></div>
    <div class="field">
      <div class="label"></div>
      <div class="info"></div>
    </div>
    <div class="clear"></div>
  </div>
</form>
</article>
</div>
<?php include 'Footer.php'; ?>
</div>
</body>
</html>