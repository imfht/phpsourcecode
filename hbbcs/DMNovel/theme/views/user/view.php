<?php include VIEWPATH . "header.php" ?>

    <div class="col-md-8 col-md-offset-2" id="userTabs">
        <div class="btn-group pull-right" style="z-index: 999;">
            <button class="btn btn-default" data-addtab="story" data-url="<?= site_url('/admin/story') ?>" data-title="发布小说">
                <i class="icon-book"></i>
            </button>
            <button class="btn btn-default" data-addtab="chapter_list" data-url="<?= site_url('/admin/chapter/list/') ?>" data-title="章节列表">
                <i class="icon-list-alt"></i>
            </button>
            <button class="btn btn-default" data-addtab="chapter" data-url="<?= site_url('/admin/chapter/') ?>" data-title="增加章节">
                <i class="icon-file-text-alt"></i>
            </button>
        </div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" <?= $active ? '' : 'class="active"' ?>>
                <a href="#profile" role="tab" data-toggle="tab">我的属性</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="profile">
                <div class="row" id="userMenu">
                    <div class="col-md-3">
                        <div class="list-group">
                            <a href="javascript:void(0);" class="list-group-item active" data-profile="bookmark">
                                我的书架
                            </a>
                            <a href="javascript:void(0);" class="list-group-item" data-profile="avatar">
                                我的头像
                            </a>
                            <a href="javascript:void(0);" class="list-group-item" data-profile="modify">
                                密码邮箱
                            </a>

                        </div>
                    </div>

                    <div class="col-md-9">

                    </div>
                </div>
            </div>

        </div>

    </div>

    <link rel="stylesheet" type="text/css" media="screen" href="<?= THEMEPATH ?>/css/bootstrap.addtabs.css"/>

    <script src="<?= THEMEPATH ?>/js/bootstrap.addtabs.min.js"></script>

    <script type="text/javascript">
        $(function () {
            $('#userTabs').addtabs({iframeHeight: $(document).height() - 157,iframeUse:false});

            $('#userMenu .col-md-9').load('<?=site_url('user/view/bookmark')?>')

            $('#userMenu .list-group a').click(function () {
                var profile=$(this).data('profile');
                $('#userMenu .list-group .active').removeClass('active');
                $(this).addClass('active');
                $('#userMenu .col-md-9').load('<?=site_url('user/view/')?>/'+profile);
            });
        })
    </script>

<?php include VIEWPATH . "footer.php" ?>