<?php

use application\core\utils\Ibos;
use application\core\utils\Module;

?>
<!doctype html>
<!-- <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> -->
<html lang="en">
<head>
  <meta charset="<?php echo CHARSET; ?>">
  <title><?php echo $lang['Home page']; ?></title>
  <!-- load css -->
  <link rel="stylesheet" href="<?php echo STATICURL; ?>/css/base.css?<?php echo VERHASH; ?>">
  <!-- IE8 fixed -->
  <!--[if lt IE 9]>
  <link rel="stylesheet" href="<?php echo STATICURL; ?>/css/iefix.css?<?php echo VERHASH; ?>">
  <![endif]-->
  <!-- private css -->
  <link rel="stylesheet" href="<?php echo $assetUrl; ?>/css/index.css?<?php echo VERHASH; ?>">
  <link rel="stylesheet" href="<?php echo STATICURL; ?>/js/lib/artDialog/skins/ibos.css?<?php echo VERHASH; ?>">
</head>
<body>
  <script>
  var adjustSidebarWidth = function () {
    document.body.className = (window.innerWidth || document.documentElement.clientWidth) > 1150 ? "db-widen" : "";
  }
  adjustSidebarWidth();
  window.onresize = adjustSidebarWidth;
  </script>

  <div class="header">
    <div class="logo" id="logo">
      <h2 class="logo-bg">IBOS</h2>
    </div>
    <div class="hdbar clearfix" id="bar">
      <div class="user-info pull-right">
        <span class="user-name">
          <a href="<?php echo Ibos::app()->user->space_url; ?>" target="_blank"><img width="30"
            height="30"
            class="radius msep"
            src="<?php echo Ibos::app()->user->avatar_middle; ?>"
            title="<?php echo Ibos::app()->user->realname; ?>">
          </a>
          <?php echo Ibos::app()->user->realname; ?>
        </span>
        <a href="<?php echo Ibos::app()->urlManager->createUrl('/'); ?>" target="_blank"
          class="msep"><?php echo Ibos::lang('Return to home page'); ?>
        </a>
        <a href="<?php echo $this->createUrl('default/logout', array('formhash' => FORMHASH)); ?>"><?php echo $lang['Logout']; ?>
        </a>
      </div>
    </div>
  </div>

  <div class="mainer" id="mainer">
    <div class="aside" id="aside">
      <div class="main-nav">
        <ul id="main_nav">
          <?php $i = 0; ?>
          <?php foreach ($cateConfig as $cate => $config): ?>
            <li <?php if (empty($i)): ?>class="active" <?php endif; $i++; ?>>
              <a class="main-link" href="<?php echo $config['url']; ?>" target="main" data-href="#db_<?php echo $config['id']; ?>_list" id="db_<?php echo $config['id']; ?>">
                <i class="icon db-<?php echo $config['id']; ?>"></i>
                <span class="text"><?php echo $lang[$config['lang']]; ?></span>
              </a>
              <?php $routeA = $routes[$cate] ?>
              <ul class="sub-nav" id="db_<?php echo $cateConfig[$cate]['id']; ?>_list"
                <?php if ($i !== 1): ?>style="display:none;"<?php endif; ?>>
                <?php foreach ($routeA as $route => $config): ?>
                  <?php if ($config['config']['isShow']): ?>
                    <?php if ($route == 'module/manager'): ?>

                      <?php foreach ($moduleMenu as $id => $menu): ?>
                        <li>
                          <?php if (Module::getIsEnabled($menu['m'])):?>
                              <?php if ($menu['m'] == 'crm'): ?>
                                  •&nbsp;&nbsp;<a class="sub-link" href="<?php echo Ibos::app()->urlManager->createUrl('crm/dashboard/preferences'); ?>"
                                                  target="main" title="<?php echo 'CRM'; ?>">
                                      <?php echo 'CRM'; ?>
                                  </a>
                                  <?php else: ?>
                                  •&nbsp;&nbsp;<a class="sub-link" href="<?php echo Ibos::app()->urlManager->createUrl($menu['m'] . '/' . $menu['c'] . '/' . $menu['a']); ?>"
                                                  target="main" title="<?php echo $menu['name']; ?>">
                                      <?php echo $menu['name']; ?>
                                  </a>
                              <?php endif;?>
                          <?php endif;?>
                        </li>
                      <?php endforeach; ?>
                            <li>
                                •&nbsp;&nbsp;<a class="sub-link" href="<?php echo Ibos::app()->urlManager->createUrl('dashboard/wxsync/app'); ?>"
                                                target="main" title="<?php echo $lang['Application Center'];?>">
                                    <?php echo $lang['Application Center'];?>
                                </a>
                            </li>
                            <?php if (ENGINE != 'SAAS'): ?>
                            <li>
                                •&nbsp;&nbsp;<a class="sub-link" href="<?php echo Ibos::app()->urlManager->createUrl('dashboard/module/manager'); ?>"
                                                target="main" title="<?php echo $lang['Module manager'];?>">
                                    <?php echo $lang['Module manager'];?>
                                </a>
                            </li>
                            <?php endif;?>
                    <?php else: ?>
                      <li <?php if ($i === 1): ?>class="active"<?php endif; ?>>
                        <?php $i++; ?>
                        <a class="sub-link" href="<?php echo $config['url']; ?>" target="main"
                          title="<?php echo $lang[$config['config']['lang']]; ?>">
                          •&nbsp;&nbsp;<?php echo $lang[$config['config']['lang']]; ?>
                        </a>
                      </li>
                    <?php endif;?>
                  <?php endif; ?>
                <?php endforeach; ?>
              </ul>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <div class="mc" id="mc">
      <iframe src="<?php echo $def; ?>" width="100%" height="100%" frameborder="0" name="main" id="main"></iframe>
    </div>
  </div>

  <!-- load js -->
  <script src="<?php echo STATICURL; ?>/js/src/core.js?<?php echo VERHASH; ?>"></script>
  <script src="<?php echo STATICURL; ?>/js/lib/artDialog/artDialog.min.js?<?php echo VERHASH; ?>"></script>
  <script src="<?php echo STATICURL; ?>/js/src/base.js?<?php echo VERHASH; ?>"></script>
  <script src="<?php echo STATICURL; ?>/js/src/common.js?<?php echo VERHASH; ?>"></script>
  <script src="<?php echo $assetUrl; ?>/js/frame.js?<?php echo VERHASH; ?>"></script>
  <script>
  $(function () {
    var refer = U.getUrlParam().refer;
    if (refer !== "") {
      var $referElem = $('#sub_nav [href="' + unescape(refer) + '"]');
      var $subMenu = $referElem.closest("ul");
      var $nav = $('[data-href="#' + $subMenu.attr("id") + '"]');
      $nav.click();
      $referElem.click();
    }

    $(document).on("click", "a[target='main']", function () {
      var title = '<?php echo $lang['Admin control']; ?> -' + $(this).text();
      document.title = title;
    })
  });
  </script>
</body>
</html>
