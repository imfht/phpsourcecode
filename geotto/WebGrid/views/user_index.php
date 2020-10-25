<?php
include(INCLUDES."/header.php");

if(isset($desktops)){
  $str_desktops = implode(',', $desktops);
}
else{
  $str_desktops = "none";
}

$background = STORAGE."/backgrounds/";
$background .= isset($_SESSION['background'])?$_SESSION['background']:"default.png";
?>
<div class="main" id="frame">
  <!-- 菜单栏 -->
  <div class="menu">
    <div class="menu-items">
        <?php 
            if(isset($user_name)){
                echo "<div class=\"menu-item\">
                    尊敬的
                    <a href=\"index.php?fun=me\" target=\"_blank\" style=\"text-decoration: underline;color: lightblue;\">{$user_name}</a>
                    ，您好
                </div>";
                echo "<div class=\"menu-item\" id=\"logout\">注销</div>";
            }else{
                echo "<div class=\"menu-item\" id=\"open-login-dialog\">登录</div>";
            }
        ?>
        <div class="menu-item" id="open-register-tab">
            <a  href="index.php?fun=register" target="_blank">注册</a>
        </div>
        <div class="menu-item" id="view-sites">浏览网址</div>
        <div class="menu-item" id="open-man">用户手册</div>
        <div class="menu-item" id="open-about">网站信息</div>
    </div>
    <div class="icon-menu">
      <img src="<?php echo INCLUDES."/img/menu.png"; ?>" />
    </div>
  </div>
    
    <!-- 桌面 -->
    <div id="desktop-container">
        <div id="desktop-tools">
            <div class="desktop-tool" id="del-desktop" title="删除当前桌面">
                <img src="<?php echo INCLUDES."/img/delete.png"; ?>" />
            </div>
            <div class="desktop-tool" id="add-desktop" title="添加新桌面">
                <img src="<?php echo INCLUDES."/img/plus.png"; ?>" />
            </div>
            <div class="desktop-tool" id="last-desktop" title="上一个桌面">
				<img src="<?php echo INCLUDES."/img/last.png"; ?>" />
            </div>
            <div class="desktop-tool" id="next-desktop" title="下一个桌面">
				<img src="<?php echo INCLUDES."/img/next.png"; ?>" />
            </div>
        </div>
        <div id="desktop"></div>
    </div>

    <!-- widgets -->
    <div class="list" id="widget-container">
        <div id="widgets">
        </div>
        <div id="add-widget" title="添加控件">
			<img src="<?php echo INCLUDES."/img/plus.png"; ?>" />
        </div>
    </div>
    
</div>
  
  <!-- 登录对话框 -->
<div class="dialog" id="dialog-login">
	<div class="header">
		<div class="tab">登录</div>
		<div class="closeit">
			<img src="<?php echo INCLUDES."/img/close.png"; ?>" />
		</div>
	</div>
	<div class="pane" id="pane-login">
		<div class="line">
			<label>用户名</label>
		</div>
		<div class="line">
			<input class="textbox" id="txt-name" type="text" name="name" maxlength="64"/>
		</div>
		<div class="line">
			<label>密码</label>
		</div>
		<div class="line">
			<input class="textbox" id="txt-password" type="password" name="password" maxlength="16"/>
		</div>
		<div class="line">
			<label>			
				<input id="chk-keep" type="checkbox" name="keep_signed" value="1" />
				保持登录状态
			</label>
			<button class="button" id="btn-login">登录</button>
		</div>
	</div>
</div>

<!-- 网址导航 -->
<div class="dialog" id="dialog-view-sites">
  <div class="header">
    <div class="tab">分类</div>
    <div id="search-pane">
      <input class="textbox" id="textbox-search" placeholder="搜索" type="text" name="search" value="" />
    </div>
  </div>
  <div class="pane">
    <div class="pane" id="pane-sites">
      <?php
      //显示分类
      if($categories != null){
	foreach($categories as $category){
	  echo "<div class=\"item item-category\">
		{$category['name']}
		<input type=\"hidden\" name=\"category\" value=\"{$category['id']}\" />
	    </div>";
	}
      }
      ?>
    </div>
    <div class="list" id="list-sites"></div>
    <div class="pane" id="pane-tools">
      <div class="button button-danger" id="button-close">关闭</div>
      <div class="button button-success" id="button-add">添加</div>
    </div>
  </div>
</div>

<!-- 添加控件窗口 -->
<div class="dialog" id="dialog-addwidget">
	<div class="header">
		<div class="tab">添加控件</div>
		<div class="closeit">
			<img src="<?php echo INCLUDES."/img/close.png"; ?>" />
		</div>
	</div>
	<div class="pane">
		<div class="list" id="list-widgets"></div>
		<div class="line">
			<button class="button" id="btn-addwidget">添加</button>
		</div>
	</div>
</div>

<!-- 脚本区域 -->
<link href="<?php echo INCLUDES."/user_index.css"; ?>" type="text/css" rel="stylesheet" />
<link href="<?php echo INCLUDES."/user_index_login.css"; ?>" type="text/css" rel="stylesheet" />
<link href="<?php echo INCLUDES."/user_index_viewsites.css"; ?>" type="text/css" rel="stylesheet" />
<script src="<?php echo INCLUDES."/jquery.contextmenu.r2.packed.js"?>"></script>
<script src="<?php echo INCLUDES."/user_index.js"?>"></script>
<script src="<?php echo INCLUDES."/user_index_login.js"; ?>"></script>
<script src="<?php echo INCLUDES."/user_index_viewsites.js"; ?>"></script>
<input type="hidden" name="str_desktops" value="<?php echo $str_desktops; ?>" />
<input type="hidden" name="current_desktop" value="-1" />
<input type="hidden" name="background" value="<?php echo $background; ?>" />
<input type="hidden" name="signed" value="<?php echo (isset($_SESSION['id']))?"true":"false"; ?>" />

<?php
include(INCLUDES."/footer.php");
?>
