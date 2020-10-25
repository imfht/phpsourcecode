<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-01 11:01:01
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-16 13:07:03
 */
use common\helpers\ImageHelper;

?>
<!-- Left side column. contains the logo and sidebar -->
   <aside class="main-sidebar ">
       <!-- sidebar: style can be found in sidebar.less -->
       <section class="sidebar">
           <!-- Sidebar user panel -->
           <div class="user-panel">
               <div class="pull-left image">
                   <img src="<?= ImageHelper::tomedia(Yii::$app->user->identity->avatar,'avatar.jpg'); ?>" class="img-circle" alt="User Image">
               </div>
               <div class="pull-left info">
                   <p><?= Yii::$app->user->identity->username; ?></p>
                   <a  data-toggle="modal" href='#selectStore-id'>
                       <i class="fa fa-edit"></i> 
                       <span id="bloc-left-name">点我选择商户</span>
                    </a>
               </div>
           </div>
           <!-- sidebar menu: : style can be found in sidebar.less -->
           <ul class="sidebar-menu">

           </ul>
       </section>
       <!-- /.sidebar -->
   </aside>

   <?= $this->render('selectStore.php'); ?>