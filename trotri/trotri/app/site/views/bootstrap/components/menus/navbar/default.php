<?php $view = $this->getView(); ?>

<!-- NavBar -->
<div class="blog-masthead">
  <div class="container">
    <ul class="nav navbar-nav blog-nav">
<?php echo $this->menus; ?>
    </ul>
    <ul class="nav navbar-nav blog-nav pull-right">
      <?php if ($view->is_login) : ?>
      <li class="dropdown">
        <a href="#" class="dropdown-toggle blog-nav-item" data-toggle="dropdown"><?php echo $view->member_name; ?> <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <li><a href="<?php echo $this->getUrlManager()->getUrl('repwdoldpwd', 'show', 'member'); ?>"><?php echo $view->CFG_SYSTEM_GLOBAL_REPWD; ?></a></li>
          <li><a href="<?php echo $this->getUrlManager()->getUrl('social', 'show', 'member'); ?>"><?php echo $view->CFG_SYSTEM_GLOBAL_SOCIAL; ?></a></li>
        </ul>
      </li>
      <li><a class="blog-nav-item" href="<?php echo $this->getUrlManager()->getUrl('logout', 'show', 'member'); ?>"><?php echo $view->CFG_SYSTEM_GLOBAL_LOGOUT; ?></a></li>
      <?php else : ?>
      <li><a class="blog-nav-item" href="<?php echo $this->getUrlManager()->getUrl('login', 'show', 'member'); ?>"><?php echo $view->CFG_SYSTEM_GLOBAL_LOGIN; ?></a></li>
      <li><a class="blog-nav-item" href="<?php echo $this->getUrlManager()->getUrl('reg', 'show', 'member'); ?>"><?php echo $view->CFG_SYSTEM_GLOBAL_REG; ?></a></li>
      <?php endif; ?>
    </ul>
  </div>
</div>
<!-- /NavBar -->