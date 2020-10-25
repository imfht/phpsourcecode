<?php
include '../library/inc.php';
?>
<!DOCTYPE html>
<html class="no-js fixed-layout">
<head>
<?php include 'inc/inc_head.php';?>
</head>

<body>
<?php include 'inc/inc_header.php';?>

<div class="am-cf admin-main">
  <!-- content start -->
  <div class="admin-content">
    <div class="am-g am-g-fixed">
      <div class="am-u-sm-12 am-padding-top">

        <section class="am-panel am-panel-default">
          <header class="am-panel-hd am-cf" data-am-collapse="{target: '#collapse-panel-1'}">伪静态规则文件生产<span class="am-icon-chevron-down am-fr"></span></header>
          <main class="am-panel-bd am-collapse am-nbfc am-in" id="collapse-panel-1">
            <div class="am-u-sm-6">
              <a href="../ajax.php?act=rewrite_apache" class="am-btn am-btn-default">APACHE</a>
              <p class="am-form-help">默认会在网站根目录生成.htaccess规则文件</p>
            </div>
            <div class="am-u-sm-6">
              <a href="../ajax.php?act=rewrite_nginx" class="am-btn am-btn-default">NGINX</a>
              <p class="am-form-help">默认会在网站根目录生成.nginx规则文件</p>
            </div>
            <div class="am-u-sm-6">
              <a href="../ajax.php?act=rewrite_isapi" class="am-btn am-btn-default">HTTPD</a>
              <p class="am-form-help">默认会在网站根目录生成httpd.ini规则文件</p>
            </div>
            <div class="am-u-sm-6">
              <a href="../ajax.php?act=rewrite_dotnet" class="am-btn am-btn-default">DOTNET</a>
              <p class="am-form-help">默认会在网站根目录生成web.config规则文件</p>
            </div>
          </main>
        </section>

      </div>
    </div>
  </div>
  <!-- content end -->
</div>

<?php include 'inc/inc_footer.php';?>

</body>
</html>
