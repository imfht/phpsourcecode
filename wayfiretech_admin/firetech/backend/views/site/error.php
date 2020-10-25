<?php
/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-04-11 12:27:56
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-29 22:01:15
 */
 

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<section class="content">

    <div class="error-page">
        <h2 class="headline text-info"><i class="fa fa-warning text-yellow"></i></h2>

        <div class="error-content">
            <h3><?= $name ?></h3>

            <p>
                <?= nl2br(Html::encode($message)) ?>
            </p>

            <p>
                上述错误发生在Web服务器处理您的请求时。如果您认为这是服务器错误，请与我们联系。谢谢你！同时，您可以返回指示板或尝试使用搜索表单。
                <a href='javascript:void(0);' onclick="javascript:history.back(-1);">返回上一页</a>
            </p>
        </div>
    </div>

</section>
