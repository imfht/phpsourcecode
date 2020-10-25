<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\PformBackcover;
?>

<div class="customer-pform-form">
<br><br>

<?php 
    $pform_backover = PformBackcover::find(['pform_uid' => $form_uid])->one();
    if(!empty($pform_backover) && !empty($pform_backover->content))
    {
?>
    <?= $pform_backover->content ?>
<?php 
    } else {
?>
<center>
<h1 style="color: green">
恭喜！
</h1>
<h3>您的信息已成功提交。</h3>
</center>
<?php } ?>

</div>