<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2016/12/13 22:25
// +----------------------------------------------------------------------
// | TITLE: 表格模板
// +----------------------------------------------------------------------
?>

<!-- /section:basics/navbar.layout -->
<div class="main-container" id="main-container">

    <!-- /section:basics/sidebar -->
    <div class="main-content">
        <div class="main-content-inner">
            <!-- #section:basics/content.breadcrumbs -->


            <!-- /section:basics/content.breadcrumbs -->
            <div class="page-content">

                <!-- /section:settings.box -->
                <div class="page-header">


                </div><!-- /.page-header -->


                <div class="profile-user-info profile-user-info-striped">


                    <?php
                    array_walk($info,function ($val,$keys){
                    echo ' <div class="profile-info-row">';
                    echo '  <div class="profile-info-name"> '.$keys.' </div>';
                    echo ' <div class="profile-info-value">';
                    echo '  <span class="editable" >'.$val.'</span>';
                    echo ' </div>';
                    echo '  </div>';
                    })
                    ?>

                </div>


            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->



</div><!-- /.main-container -->

