<nav class="transition--fade">
  <div class="nav-bar" data-fixed-at="200">
    <div class="nav-module logo-module left">
      <a href="./">
        <img class="logo logo-dark" alt="logo" src="<?php echo $t_path;?>img/logo-dark.png" />
        <img class="logo logo-light" alt="logo" src="<?php echo $t_path;?>img/logo-light.png" />
      </a>
    </div>
    <div class="nav-module menu-module left">
      <?php echo navigation(0,'<li><a href="./">网站首页</a></li>',@$c_main,2,'menu','dropdown');?>
    </div>
    <!--end nav module-->
    <div class="nav-module right"></div>
    <div class="nav-module right">
      <a href="#" class="nav-function modal-trigger" data-modal-id="search-form">
        <i class="interface-search icon icon--sm"></i>
        <span>检索</span>
      </a>
    </div>
  </div>
  <!--end nav bar-->
  <div class="nav-mobile-toggle visible-sm visible-xs">
    <i class="icon-Align-Right icon icon--sm"></i>
  </div>
</nav>
<div class="modal-container search-modal" data-modal-id="search-form">
  <div class="modal-content bg-white imagebg" data-width="100%" data-height="100%">
    <div class="pos-vertical-center clearfix">
      <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 text-center">
        <form class="clearfix" action="search.php" method="post">
          <div class="input-with-icon">
            <i class="icon-Magnifi-Glass2 icon icon--sm"></i>
            <input type="search" name="keyword" placeholder="请输入关键词并点击回车键进行检索" />
          </div>
        </form>
      </div>
    </div>
  </div>
  <!--end of modal-content-->
</div>
<!--end of modal-container-->
