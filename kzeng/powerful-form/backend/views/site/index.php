<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '超级表单';
?>
<div class="site-index">

    <div class="jumbotron">
        <h2>人人可用的在线表单工具</h2>

        <p class="lead">超级表单帮您收集和管理日常工作中的数据，把几小时的工作节约至零。</p>
        <br><br>
        <p>
            <?= Html::a("官网首页 <span class='glyphicon glyphicon-globe'></span>", 'http://'.$_SERVER['HTTP_HOST'], ['class' => 'btn btn-lg btn-primary']) ?>
                 &nbsp;&nbsp; &nbsp;&nbsp;
            <?= Html::a("免费使用 <span class='glyphicon glyphicon-send'></span>", ['pform/index'], ['class' => 'btn btn-lg btn-success']) ?>
        </p>

    </div>

    <div class="body-content">

        <div class="row">
        
        </div>

    </div>
</div>
