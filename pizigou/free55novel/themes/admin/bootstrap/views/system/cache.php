<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - 缓存管理';
?>

    <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
      'id'=>'cache-form',
      'type'=>'horizontal',
      'enableClientValidation'=>true,
      'clientOptions'=>array(
        'validateOnSubmit'=>true,
      ),
//      'htmlOptions'=>array('class'=>'well'),
//      'htmlOptions'=>array('enctype' => 'multipart/form-data'),
    )); ?>


    <div class="control-group ">
        <label for="System_clearIndexCache" class="control-label">首页</label>
        <div class="controls">
            <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=> 'ajaxButton',
                'type'=> 'primary',
                'url' => $this->createUrl('system/clearCache', array('type'=> 'siteindex')),
                'ajaxOptions' => array(
                    'success' => 'js:function(data) {
                                alert(data);
                            }',
                ),
                'label'=> '清除首页缓存',
            )); ?>
            <span style="display: none" id="System_clearIndexCache_help" class="help-inline error"></span>
        </div>
    </div>

    <div class="control-group ">
        <label for="System_clearIndexCache" class="control-label">小说栏目</label>
        <div class="controls">
            <input type="text" value="填写小说栏目编号" id="System_clearBookCategoryCache" name="System_clearBookCategoryCache" onclick='if (this.value == "填写小说栏目编号") this.value="";' onblur='if (this.value == "") this.value = "填写小说栏目编号";'>
            <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=> 'ajaxButton',
                'type'=> 'primary',
                'url' => $this->createUrl('system/clearCache', array('type'=> 'bookcategory')),
                'ajaxOptions' => array(
                    'data' => array(
                        'id' => 'js:$(\'#System_clearBookCategoryCache\').val()',
                    ),
                    'success' => 'js:function(data) {
                                alert(data);
                            }',
                ),
                'label'=> '清除小说栏目缓存',
            )); ?>
            <span style="display: none" id="SystemBaseConfig_SiteIsUsedCache_em_" class="help-inline error"></span>
        </div>
    </div>

    <div class="control-group ">
        <label for="System_clearIndexCache" class="control-label">小说</label>
        <div class="controls">
            <input type="text" value="填写小说编号" id="System_clearBookCache" name="System_clearBookCache" onclick='if (this.value == "填写小说编号") this.value="";' onblur='if (this.value == "") this.value = "填写小说编号";'>
            <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=> 'ajaxButton',
                'type'=> 'primary',
                'url' => $this->createUrl('system/clearCache', array('type'=> 'book')),
                'ajaxOptions' => array(
                    'data' => array(
                        'id' => 'js:$(\'#System_clearBookCache\').val()',
                    ),
                    'success' => 'js:function(data) {
                                alert(data);
                            }',
                ),
                'label'=> '清除小说缓存',
            )); ?>
            <span style="display: none" id="SystemBaseConfig_SiteIsUsedCache_em_" class="help-inline error"></span>
        </div>
    </div>

    <div class="control-group ">
        <label for="System_clearIndexCache" class="control-label">章节</label>
        <div class="controls">
            <input type="text" value="填写章节编号" id="System_clearChapterCache" name="System_clearChapterCache" onclick='if (this.value == "填写章节编号") this.value="";' onblur='if (this.value == "") this.value = "填写章节编号";'>
            <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=> 'ajaxButton',
                'type'=> 'primary',
                'url' => $this->createUrl('system/clearCache', array('type'=> 'chapter')),
                'ajaxOptions' => array(
                    'data' => array(
                        'id' => 'js:$(\'#System_clearChapterCache\').val()',
                    ),
                    'success' => 'js:function(data) {
                                alert(data);
                            }',
                ),
                'label'=> '清除章节缓存',
            )); ?>
            <span style="display: none" id="SystemBaseConfig_SiteIsUsedCache_em_" class="help-inline error"></span>
        </div>
    </div>

    <div class="control-group ">
        <label for="System_clearIndexCache" class="control-label">新闻分类</label>
        <div class="controls">
            <input type="text" value="填写新闻分类编号" id="System_clearNewsCategoryCache" name="System_clearNewsCategoryCache" onclick='if (this.value == "填写新闻分类编号") this.value="";' onblur='if (this.value == "") this.value = "填写新闻分类编号";'>
            <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=> 'ajaxButton',
                'type'=> 'primary',
                'url' => $this->createUrl('system/clearCache', array('type'=> 'newscategory')),
                'ajaxOptions' => array(
                    'data' => array(
                        'id' => 'js:$(\'#System_clearNewsCategoryCache\').val()',
                    ),
                    'success' => 'js:function(data) {
                                alert(data);
                            }',
                ),
                'label'=> '清除新闻分类缓存',
            )); ?>
            <span style="display: none" id="SystemBaseConfig_SiteIsUsedCache_em_" class="help-inline error"></span>
        </div>
    </div>

    <div class="control-group ">
        <label for="System_clearIndexCache" class="control-label">新闻</label>
        <div class="controls">
            <input type="text" value="填写新闻编号" id="System_clearNewsCache" name="System_clearNewsCache" onclick='if (this.value == "填写新闻编号") this.value="";' onblur='if (this.value == "") this.value = "填写新闻编号";'>
            <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=> 'ajaxButton',
                'type'=> 'primary',
                'url' => $this->createUrl('system/clearCache', array('type'=> 'news')),
                'ajaxOptions' => array(
                    'data' => array(
                        'id' => 'js:$(\'#System_clearNewsCache\').val()',
                    ),
                    'success' => 'js:function(data) {
                                alert(data);
                            }',
                ),
                'label'=> '清除新闻缓存',
            )); ?>
            <span style="display: none" id="SystemBaseConfig_SiteIsUsedCache_em_" class="help-inline error"></span>
        </div>
    </div>

    <div class="control-group ">
        <label for="System_clearAllCache" class="control-label">全局</label>
        <div class="controls">
            <?php $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=> 'ajaxButton',
                'type'=> 'primary',
                'url' => $this->createUrl('system/clearCache', array('type'=> 'all')),
                'ajaxOptions' => array(
                    'success' => 'js:function(data) {
                                alert(data);
                            }',
                ),
                'label'=> '清除全部缓存',
            )); ?>
            <span style="display: inline" id="System_clearAllCache_help" class="help-inline error">警告：清除全部缓存，会严重影响性能，请慎用！</span>
        </div>
    </div>


<!--      <div class="form-actions">-->
<!--        --><?php //$this->widget('bootstrap.widgets.TbButton', array(
//                'buttonType'=>'submit',
//                'type'=>'primary',
//                'label'=>'确定',
//            )); ?>
<!--      </div>-->


    <?php $this->endWidget(); ?>

