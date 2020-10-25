<?php $this->header(); ?>
<header class="am-topbar admin-header am-header-fixed">
    <div class="am-topbar-brand">
        <strong><?= $sitetile ?></strong> <small></small>
    </div>
    <ul class="am-nav am-nav-pills am-topbar-nav am-topbar-right admin-header-list">
        <li>
            <a href="javascript:;" data-am-modal="{target: '#my-alert'}">
                <span class="am-icon-street-view"></span> <?= $_SESSION['admin']['user_name']; ?>, 您好
            </a>
        </li>
        <li>
            <a href="http://www.pescms.com/d/v/12/43.html" target="_blank">
                配置示例接口
            </a>
        </li>
        <li>
            <a href="javascript:;" class="sidebar-control">
                <span class="am-icon-expand"></span> 隐藏侧栏
            </a>
        </li>
    </ul>
    <script>
        $(function () {
            $('.sidebar-control').on('click', function () {
                if ($('.am-offcanvas-bar').hasClass("admin-offcanvas-bar") == true) {
                    $(this).html('<span class="am-icon-compress"></span> 显示侧栏')
                    $('.am-offcanvas-bar').removeClass("admin-offcanvas-bar")
                } else {
                    $(this).html('<span class="am-icon-expand"></span> 隐藏侧栏')
                    $('.am-offcanvas-bar').addClass("admin-offcanvas-bar")
                }
            });
            $(".update-password").on("click", function(){
                $("#update-password").submit();
            })
        });
    </script>
</header>
<div class="am-cf admin-main">
    <!-- sidebar start -->
    <div class="admin-sidebar am-offcanvas" id="admin-offcanvas">
        <div class="am-scrollable-vertical am-offcanvas-bar admin-offcanvas-bar" style="overflow-y: auto;">
            <ul class="am-list admin-sidebar-list">
                <?php foreach ($menu as $topkey => $topValu) : ?>
                    <li class="admin-parent">
                        <a class="am-cf" data-am-collapse="{target: '#<?= $topValu['menu_id'] ?>'}"><span class="<?= $topValu['menu_icon'] ?>"></span> <?= $topValu['menu_name']; ?><span class="am-icon-angle-right am-fr am-margin-right"></span></a>
                        <?php if (!empty($topValu['menu_child'])): ?>
                            <ul class="am-list am-collapse admin-sidebar-sub" id="<?= $topValu['menu_id'] ?>">
                                <?php foreach ($topValu['menu_child'] as $key => $value) : ?>
                                    <li><a href="<?= $label->url($value['menu_url']); ?>" class="am-cf menu-link"><span class="<?= $value['menu_icon'] ?>"></span> <?= $value['menu_name']; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
                <li><a href="<?= $label->url(GROUP . '-Index-logout'); ?>"><span class="am-icon-sign-out"></span> 注销</a></li>
            </ul>

        </div>
    </div>
    <!-- sidebar end -->
    <!-- content start -->
    <div class="admin-content" style="padding-top:2px;">
        <iframe id="iframe_default" src="" style="width: 100%; height: 100%;" data-id="default" frameborder="0" scrolling="yes"></iframe>
    </div>
</div>
<a class="am-icon-btn am-icon-th-list am-show-sm-only admin-menu" data-am-offcanvas="{target: '#admin-offcanvas'}"></a>

<div class="am-modal am-modal-alert" tabindex="-1" id="my-alert">
  <div class="am-modal-dialog">
    <div class="am-modal-hd">修改管理员密码</div>
    <div class="am-modal-bd">
        <form class="am-form am-form-horizontal" id="update-password" action="<?= $label->url(GROUP.'-Index-updatePassword') ?>" method="POST">
            <div class="am-form-group">
                <label class="am-u-sm-2 am-form-label">密码</label>
                <div class="am-u-sm-10">
                    <input type="text" name="password" placeholder="新密码">
                </div>
            </div>
        </form>
    </div>
    <div class="am-modal-footer">
      <span class="am-modal-btn update-password">更新密码</span>
    </div>
  </div>
</div>
<?php $this->footer(); ?>