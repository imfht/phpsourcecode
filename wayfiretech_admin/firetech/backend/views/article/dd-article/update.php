<?php
/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-04-12 18:37:55
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-10 21:15:25
 */
 

use yii\helpers\Html;
use richardfan\widget\JSRegister;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\DdArticle */

$this->title = 'Update 文章: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '文章', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<?= $this->render('_tab') ?>

<div class="firetech-main">
    <div class="panel panel-default">
        <div class="box-body">
            <div class="dd-article-update">


                <?= $this->render('_form', [
                    'model' => $model,
                    'Helper' => $Helper,

                ]) ?>
            </div>
        </div>
    </div>
</div>

<?php JSRegister::begin([
    'key' => '3445'
]); ?>
<script>
    
$(function () {
      //分类
    $("#classsearch-cocate_id").change(function() {
        var cocateId = $(this).val(); //获取一级目录的值
        // $("#classsearch-course_id").html("<option value=\"\">选择二级分类</option>");//二级显示目录标签
        console.log(cocateId)
        if (cocateId > 0) {
            getCourse(cocateId); //查询二级目录的方法
        }
    });
    function getCourse(cocateId) {
        var href = "<?= Url::to(['article/dd-article-category/childcate'])?>"
        $.ajax({
            "type": "post",
            "url": href,
            "data": {
                parent_id: cocateId,
                type: "course"
            }, //所需参数和类型
            success: function(d) {
                var htmls = '';
                $.each(d, function(index, item) {
                    htmls += '<option value="' + item.id + '">' + item.title + '</option>';

                })
                console.log(htmls)
                $("#classsearch-course_id").append(htmls); //返回值输出
            }
        });
    }
})
</script>
<?php JSRegister::end(); ?>