<?php
use yii\helpers\Url;
use backend\models\AdminRole;
$AdminRole  = new \backend\models\AdminRole;
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
                    <a href="<?=  Url::toRoute('admin-role/create')?>" class="btn">
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

                                        <th><?=$AdminRole->getAttributeLabel('id')?></th>
                                        <th><?=$AdminRole->getAttributeLabel('name')?></th>
                                        <th class="hidden-480"><?=$AdminRole->getAttributeLabel('code')?></th>

                                        <th>
                                            <?=$AdminRole->getAttributeLabel('des')?>
                                        </th>
                                        <th class="hidden-480">  <?=$AdminRole->getAttributeLabel('status')?></th>

                                        <th>操作</th>
                                    </tr>
                                    </thead>

                                    <tbody>

                                    <?php
                                    foreach ( $model as $k=>$v){
                                        echo  '<tr>';

                                        echo   '<td><a href="#">'.$v->id.'</a></td>';
                                        echo    '<td>'.$v->name.'</td>';
                                        echo    '<td>'.$v->code.'</td>';
                                        echo    '<td>'.$v->des.'</td>';
                                        echo    '<td>'.AdminRole::status_to_str($v->status ) .'</td>';
                                        echo    '<td>';
                                        if ($v->id != 1 ){
                                            echo   ' 
                                            <div class="inline position-relative">
                                                    <button class="btn btn-minier btn-primary dropdown-toggle" data-toggle="dropdown" data-position="auto">
                                                        <i class="ace-icon fa fa-cog icon-only bigger-110"></i>
                                                    </button>

                                                    <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
                                              
                                                        <li>
                                                            <a href="'.Url::to(['admin-role/update','id'=>$v->id]).'" class="tooltip-success" data-rel="tooltip" title="Edit">
																			<span class="green">
																				<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
																				修改
																			</span>
                                                            </a>
                                                        </li>
                                                         <li>
                                                            <a href="'.Url::to(['set-rule','id'=>$v->id]).'" class="tooltip-info" data-rel="tooltip" title="Edit">
																			<span class="blue">
																				<i class="ace-icon fa fa-cog bigger-120"></i>
																				设置权限
																			</span>
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a data-id="'.$v->id.'" data-url="'.Url::to(['admin-role/delete','id'=>$v->id]).'"  class="tooltip-error delete" data-rel="tooltip" title="Delete">
																			<span class="red">
																				<i class="ace-icon fa fa-trash-o bigger-120"></i>
																				删除
																			</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                        ';
                                        }
                                        echo '</td>';

                                        echo ' </tr>';

                                    }

                                    ?>
                                    </tbody>
                                </table>
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
    $('.delete').bind('click',function () {
        var id= $(this).attr('data-id');
        var url= $(this).attr('data-url');

        $.get(url, function(data) {
            if(data.code==200){
                alert('删除成功');
                window.location=location.href;
            }
        });
    })
    


</script>

