<!-- NavBar -->
<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo $this->getUrlManager()->getUrl('index', 'site', 'system'); ?>">Trotri</a>
    </div>
    <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
<?php echo $this->menus; ?>
      </ul>
      <ul class="nav navbar-nav pull-right">
        <?php if ($this->is_login) : ?>
        <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->user_name; ?> <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="<?php echo $this->getUrlManager()->getUrl('logout', 'account', 'users'); ?>"><?php echo $this->logout; ?></a></li>
          </ul>
        </li>
        <?php endif; ?>
        <li><a href="index.php" target="_blank"><?php echo $this->getView()->CFG_SYSTEM_URLS_INDEX; ?></a></li>
      </ul>
    </div><!-- /.nav-collapse -->
  </div><!-- /.container -->
</div><!-- /.navbar -->
<!-- /NavBar -->