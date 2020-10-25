<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-01 11:01:01
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-07 11:22:31
 */
use common\helpers\ImageHelper;
use richardfan\widget\JSRegister;

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

   
<?php JSRegister::begin([
    'key'=>'_tab'
]) ?>
<script>
    new Vue({
        el: '#_tab',
        data: function() {
            return { 
                items:<?= $items ?>,
                activeIndex:'menu0',
            }
        },
        created: function () {
            let that  = this; 
            console.log(that.items)
            
            that.items.forEach((item,key)=>{
                console.log(key,item)
                if(item.active){
                    that.activeIndex = 'menu'+key
                }
            })
            console.log('items',that.items)
        },
        methods:{
            handleSelect(key, keyPath) {
                let that = this
                that.items.forEach((item,k)=>{
                    console.log(k,item)
                    if('menu'+k == key){
                        window.location.href = item.url
                    }
                })
                console.log(key, keyPath,this.activeIndex);
            },
            reloadpage(){
                window.location.reload()
            },
            historypage(){
                history.go(-1);
            }
        }
    })
</script>
<?php JSRegister::end();?>