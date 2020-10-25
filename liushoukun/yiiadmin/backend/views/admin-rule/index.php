<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2016/12/13 22:25
// +----------------------------------------------------------------------
// | TITLE: 权限列表
// +----------------------------------------------------------------------
use yii\helpers\Url;
use yii\widgets\LinkPager;
$AdminRule = new \backend\models\AdminRule();
?>


<div class="main-container" id="main-container">

    <!-- /section:basics/sidebar -->
    <div class="main-content">
        <div class="main-content-inner">

            <!-- /section:basics/content.breadcrumbs -->
            <div class="page-content">

                <!-- /section:settings.box -->
                <div class="page-header">
                    <h1>
                        权限管理
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            权限列表
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <a href="<?= Url::toRoute('create') ?>" class="btn">
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

                                        <th><?= $AdminRule->getAttributeLabel('id') ?></th>
                                        <th><?= $AdminRule->getAttributeLabel('title') ?></th>
                                        <th><?= $AdminRule->getAttributeLabel('route') ?></th>
                                        <th><?= $AdminRule->getAttributeLabel('type') ?></th>
                                        <th><?= $AdminRule->getAttributeLabel('order') ?></th>
                                        <th><?= $AdminRule->getAttributeLabel('tips') ?></th>
                                        <th><?= $AdminRule->getAttributeLabel('is_show') ?></th>
                                        <th><?= $AdminRule->getAttributeLabel('status') ?></th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>

                                    <tbody>

                                    <?php
                                    foreach ($model as $k => $v) {
                                        echo '<tr>';

                                        echo '<td><a href="#">' . $v->id . '</a></td>';
                                        echo '<td>' . $v->title . '</td>';
                                        echo '<td>' . $v->route . '</td>';
                                        echo '<td>' . $v->enumeration('type', $v->type) . '</td>';
                                        echo '<td>' . $v->order . '</td>';
                                        echo '<td>' . $v->tips . '</td>';
                                        echo '<td>' . $v->enumeration('is_show', $v->is_show) . '</td>';
                                        echo '<td>' . $v->enumeration('status', $v->status) . '</td>';
                                        echo ' <td>';
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


