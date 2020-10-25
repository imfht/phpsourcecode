<?php

/* @var $this yii\web\View */

$this->title = '商品管理';
?>
<div class="firetech-main">
    <?php if ($plugins == 'wxapp') { ?>
        小程序
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-envelope-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Messages</span>
                        <span class="info-box-number">1,410</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-flag-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bookmarks</span>
                        <span class="info-box-number">410</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-files-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Uploads</span>
                        <span class="info-box-number">13,648</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-star-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Likes</span>
                        <span class="info-box-number">93,139</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
        </div>
    <?php } elseif ($plugins == 'sysai') { ?>
        系统

        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-envelope-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Messages</span>
                        <span class="info-box-number">1,410</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-flag-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bookmarks</span>
                        <span class="info-box-number">410</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-files-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Uploads</span>
                        <span class="info-box-number">13,648</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-star-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Likes</span>
                        <span class="info-box-number">93,139</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
        </div>
    <?php } elseif ($plugins == 'aimember') { ?>
        店滴ai会员

        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-envelope-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Messages</span>
                        <span class="info-box-number">1,410</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-flag-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bookmarks</span>
                        <span class="info-box-number">410</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-files-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Uploads</span>
                        <span class="info-box-number">13,648</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-star-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Likes</span>
                        <span class="info-box-number">93,139</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
        </div>
    <?php } elseif ($plugins == 'marketing') { ?>
        营销

        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-envelope-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Messages</span>
                        <span class="info-box-number">1,410</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-flag-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bookmarks</span>
                        <span class="info-box-number">410</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-files-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Uploads</span>
                        <span class="info-box-number">13,648</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-star-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Likes</span>
                        <span class="info-box-number">93,139</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
        </div>
    <?php } elseif ($plugins == 'wxapp') { ?>
        小程序

        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-envelope-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Messages</span>
                        <span class="info-box-number">1,410</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-flag-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bookmarks</span>
                        <span class="info-box-number">410</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-files-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Uploads</span>
                        <span class="info-box-number">13,648</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-star-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Likes</span>
                        <span class="info-box-number">93,139</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
        </div>
    <?php } elseif ($plugins == 'plugins') { ?>
        扩展

        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-envelope-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Messages</span>
                        <span class="info-box-number">1,410</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-flag-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bookmarks</span>
                        <span class="info-box-number">410</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-files-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Uploads</span>
                        <span class="info-box-number">13,648</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-star-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Likes</span>
                        <span class="info-box-number">93,139</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
        </div>
    <?php } elseif ($plugins == 'member') { ?>
        会员

        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-envelope-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Messages</span>
                        <span class="info-box-number">1,410</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-flag-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bookmarks</span>
                        <span class="info-box-number">410</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-files-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Uploads</span>
                        <span class="info-box-number">13,648</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-star-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Likes</span>
                        <span class="info-box-number">93,139</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
        </div>
    <?php } ?>
</div>