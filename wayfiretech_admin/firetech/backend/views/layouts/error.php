<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = '';
?>
<div class="row">
   
    <div class="col-md-8 col-xs-offset-2" style="margin-top: 50px;height:100px;">
      <div class="box box-solid box-warning">
        <div class="box-header">
          <h3 class="box-title">错误提示</h3>
        </div>
        <div class="box-body text-center">
        <div class="row">
            <icon class="glyphicon glyphicon-remove fa-4x text-warning">
           </icon>
        </div>
        <div class="row">
             <h2 class="text-warning">
         
             <?php echo $message['msg']; ?>
                   </h2>
                             
        </div>
          
        </div>
        <div class="box-footer">
            <div class="col-xs-6 ">
                <a href="javascript:history.go(-1);" class="btn btn-warning">点击这里返回上一页</a>
            </div>
            <div class="col-xs-6">
                <a href="{php echo url('home/welcome/system', array('page' => 'home'))}" class="btn btn-default">首页</a>
        </div>
					
        </div>
      </div>
    </div>
   
    	
  </div>





			<script type="text/javascript">
				// setTimeout(function () {
				// 	location.href = "{$redirect}";
				// }, 5000);
			</script>
