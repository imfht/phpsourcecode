<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;
$AdminUser = new \backend\models\AdminUser();
?>


<!-- /section:basics/navbar.layout -->
<div class="main-container" id="main-container">

    <!-- /section:basics/sidebar -->
    <div class="main-content">
        <div class="main-content-inner">

            <!-- /section:basics/content.breadcrumbs -->
            <div class="page-content">

                <!-- /section:settings.box -->
                <div class="page-header">
                    <h1>
                        角色管理
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            角色列表
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <a href="<?= Url::toRoute('admin-user/create') ?>" class="btn">
                        <i class="ace-icon fa fa-pencil align-top bigger-125"></i>
                        新增
                    </a>

                </div>

                <div class="hr hr-18 dotted hr-double"></div>

                <div class="row">
                    <div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->
                        <div class="row">
                            <div class="col-xs-12">
                                <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>

                                        <th><?= $AdminUser->getAttributeLabel('id') ?></th>
                                        <th><?= $AdminUser->getAttributeLabel('role_id') ?></th>
                                        <th><?= $AdminUser->getAttributeLabel('username') ?></th>
                                        <th class="hidden-480"><?= $AdminUser->getAttributeLabel('mobile') ?></th>
                                        <th class="hidden-480"><?= $AdminUser->getAttributeLabel('email') ?></th>
                                        <th class="hidden-480">  <?= $AdminUser->getAttributeLabel('status') ?></th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>

                                    <tbody>

                                    <?php
                                    foreach ($model as $k => $v) {
                                        echo '<tr>';

                                        echo '<td><a href="#">' . $v->id . '</a></td>';
                                        echo '<td>' . $v->adminRole['name'] . '</td>';
                                        echo '<td>' . $v->username . '</td>';
                                        echo '<td>' . $v->mobile . '</td>';
                                        echo '<td>' . $v->email . '</td>';
                                        echo '<td>' . $v->enumeration('status', $v->status). '</td>';

                                        echo ' <td>';
                                        if ($v->id != 1)
                                            echo '  <div class="inline position-relative">
                                                    <button class="btn btn-minier btn-primary dropdown-toggle" data-toggle="dropdown" data-position="auto">
                                                        <i class="ace-icon fa fa-cog icon-only bigger-110"></i>
                                                    </button>

                                                    <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
                                              
                                                        <li>
                                                            <a href="' . Url::to(['update', 'id' => $v->id]) . '" class="tooltip-success" data-rel="tooltip" title="Edit">
																			<span class="green">
																				<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
																				修改
																			</span>
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a data-id="' . $v->id . '" data-url="' . Url::to(['delete', 'id' => $v->id]) . '"  class="tooltip-error delete" data-rel="tooltip" title="Delete">
																			<span class="red">
																				<i class="ace-icon fa fa-trash-o bigger-120"></i>
																				删除
																			</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>';
                                        echo '   </td>';
                                        echo ' </tr>';

                                    }

                                    ?>
                                    </tbody>
                                </table>

                                <div class="row">
                                    <?php echo LinkPager::widget([
                                        'pagination' => $pages,
                                    ]); ?>
                                </div>

                            </div><!-- /.span -->
                        </div><!-- /.row -->

                        <div class="hr hr-18 dotted hr-double"></div>

                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->


</div><!-- /.main-container -->

<script>
    $('.delete').bind('click', function () {
        var id = $(this).attr('data-id');
        var url = $(this).attr('data-url');

        $.get(url, function (data) {
            if (data.code == 200) {
                alert('删除成功');
                //  window.location=location.href;
            }
        });
    })


</script>

