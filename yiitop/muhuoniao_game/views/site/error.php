<?php
$this->pageTitle=Yii::app()->name . ' - Error';
$this->breadcrumbs=array(
	'Error',
);
?>


<div id="container">

 <img src="<?php echo Yii::app()->request->baseUrl; ?>/images/404.jpg"  class="404_BJ"/>
    
 <p class="cause"><a href="#"><?php echo CHtml::encode($message); ?></a></p>  
 <p class="fan"><a href="/"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/fan_H.jpg" /></a></p>
      
</div>
