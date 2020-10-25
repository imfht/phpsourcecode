<?php
/**
 * @Author: Wang chunsheng  &#60;2192138785@qq.com&#62;
 * @Date:   2020-04-29 20:01:06
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-11 15:02:51
 */
 

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->searchModelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="panel panel-info">
      <div class="panel-heading">
            <h3 class="panel-title">搜索</h3>
      </div>
      <div class="panel-body">
           
    

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    


<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-search">

    <?= "<?php " ?>$form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
            <?php if ($generator->enablePjax): ?>
                    'options' => [
                        'data-pjax' => 1
                    ],
            <?php endif; ?>
                ]); ?>

<?php
$count = 0;
foreach ($generator->getColumnNames() as $attribute) {
    if (++$count < 6) {
    
        echo  "<div class='col-xs-12 col-sm-6 col-md-4 col-lg-4'>\n\n";
        echo "    <?= " . $generator->generateActiveSearchField($attribute) . " ?>\n\n";
        echo  "</div>\n\n";
    
    } else {
        echo  "<div class='col-xs-12 col-sm-6 col-md-4 col-lg-4'>\n\n";
    
        echo "    <?php // echo " . $generator->generateActiveSearchField($attribute) . " ?>\n\n";
        echo  "</div>\n\n";

   
    }
}
?>

</div>


<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<div class="form-group">
        <?= "<?= " ?>Html::submitButton(<?= $generator->generateString('搜索') ?>, ['class' => 'btn btn-primary']) ?>
        <?= "<?= " ?>Html::resetButton(<?= $generator->generateString('重置') ?>, ['class' => 'btn btn-outline-secondary']) ?>
    </div>

</div>

  
    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
</div>
</div>
