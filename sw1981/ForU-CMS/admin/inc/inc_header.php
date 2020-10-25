<header class="am-topbar admin-header">
  <div class="am-topbar-brand">
    <a href="index.php?act=welcome"><span class="am-icon-home"></span> 管理首页 </a>
  </div>

  <button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-show-sm-only" data-am-collapse="{target: '#topbar-collapse'}"><span class="am-sr-only">导航切换</span> <span class="am-icon-bars"></span></button>

  <div class="am-collapse am-topbar-collapse" id="topbar-collapse">

    <ul class="am-nav am-nav-pills am-topbar-nav am-topbar-right admin-header-list">

      <li class="am-dropdown" data-am-dropdown>
        <a class="am-dropdown-toggle" data-am-dropdown-toggle href="javascript:;">
          <span class="am-icon-cogs"></span> 内容 <span class="am-icon-caret-down"></span>
        </a>
        <ul class="am-dropdown-content">
          <li><a href="cms_detail_add.php"><span class="am-icon-plus-square"></span> 添加详情 </a></li>
          <li><a href="cms_detail.php"><span class="am-icon-edit"></span> 管理详情 </a></li>
          <li class="am-divider"></li>
          <li><a href="cms_channel_add.php"><span class="am-icon-plus-square"></span> 添加频道 </a></li>
          <li><a href="cms_channel.php"><span class="am-icon-edit"></span> 管理频道 </a></li>
          <li class="am-divider"></li>
          <li><a href="cms_slideshow.php"><span class="am-icon-archive"></span> 管理幻灯 </a></li>
          <li><a href="cms_chip.php"><span class="am-icon-file-code-o"></span> 管理碎片 </a></li>
        </ul>
      </li>

      <li class="am-dropdown" data-am-dropdown>
        <a class="am-dropdown-toggle" data-am-dropdown-toggle href="javascript:;">
          <span class="am-icon-exchange"></span> 交互 <span class="am-icon-caret-down"></span>
        </a>
        <ul class="am-dropdown-content">
          <!-- <li><a href="cms_feedback.php"><span class="am-icon-comment"></span> 留言管理 </a></li> -->
          <li><a href="cms_mail.php"><span class="am-icon-envelope"></span> 邮件订阅 </a></li>
          <li class="am-divider"></li>
          <li><a href="cms_link.php"><span class="am-icon-link"></span> 友情链接 </a></li>
        </ul>
      </li>

      <li class="am-dropdown" data-am-dropdown>
        <a class="am-dropdown-toggle" data-am-dropdown-toggle href="javascript:;">
          <span class="am-icon-cogs"></span> 系统 <span class="am-icon-caret-down"></span>
        </a>
        <ul class="am-dropdown-content">
          <li><a href="cms_system.php"><span class="am-icon-cog"></span> 系统设置 </a></li>
          <li class="am-divider"></li>
          <li><a href="cms_admin.php"><span class="am-icon-user"></span> 管理员</a></li>
          <li><a href="cms_role.php"><span class="am-icon-graduation-cap"></span> 权限管理 </a></li>
          <li class="am-divider"></li>
          <li><a href="cms_addon.php"><span class="am-icon-plug"></span> 插件管理 </a></li>
          <li><a href="cms_template.php"><span class="am-icon-laptop"></span> 模板管理 </a></li>
          <!-- <li><a href="cms_lang.php"><span class="am-icon-globe"></span> 语言管理 </a></li> -->
          <li><a href="cms_database.php"><span class="am-icon-database"></span> 数据库管理 </a></li>
          <li><a href="cms_log.php"><span class="am-icon-book"></span> 日志管理 </a></li>
          <li class="am-divider"></li>
          <!-- <li><a href="cms_rewrite.php"><span class="am-icon-exchange"></span> 伪静态 </a></li> -->
          <li><a href="javascript:if(confirm('是否进行sitemap文件生成?')) window.open('../sitemap.php?act=xml');"><span class="am-icon-cubes"></span> sitemap </a></li>
          <!-- <li><a href="javascript:if(confirm('清理无用的上传文件?')) window.location.href='index.php?act=clearUploadfile';"><span class="am-icon-trash"></span> 清理文件 </a></li> -->
          <!--<li><a href="javascript:if(confirm('是否进行百度主动推送?')) window.location.href='index.php?act=baiduSend';"><span class="am-icon-exchange"></span> 百度主动推送 </a></li>-->
        </ul>
      </li>

      <li><a href="../" target="_blank"><span class="am-icon-desktop"></span> 预览 </a></li>
      <li><a href="index.php?act=logout"><span class="am-icon-power-off"></span> 退出 </a></li>
      <li class="am-hide-sm-only"><a href="javascript:;" id="admin-fullscreen"><span class="am-icon-arrows-alt"></span> <span class="admin-fullText">全屏</span> </a></li>
    </ul>

  </div>
</header>
