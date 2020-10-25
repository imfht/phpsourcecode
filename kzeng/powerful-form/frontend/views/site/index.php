<?php

/* @var $this yii\web\View */
use yii\helpers\Html;

$this->title = '超级表单';

$slogan = [
    "帮你轻松收集和管理客户数据",
    "高效  简单  灵活  人人可用",
    "真诚的倾听您的需求",
];

?>

<div id="dowebok">
    <div class="section section1">
        
        <div class="jumbotron">

            <h1>超级表单</h1>

            <p class="lead" style="color:#fff"><?= $slogan[rand(0,sizeof($slogan)-1)] ?></p>

            <br><br><br>
            <p>
                <a href="<?= Yii::$app->request->hostInfo ?>/admin" class="btn btn-lg btn-success">现在就去免费创建一个表单
                <span class="glyphicon glyphicon-arrow-right"></span>
                </a>
            </p>

        </div>
    </div>
<!--     <div class="section section2"></div> -->
<!--     <div class="section section3"></div>
    <div class="section section4"></div> -->
</div>

<script>
$(function(){
    $('#dowebok').fullpage();
});
</script>

