<?php
?>
<!--loadcss-->
<link rel="stylesheet" href="<?php echo STATICURL; ?>/js/app/assignment/assignment.popup.css?<?php echo VERHASH; ?>">
<link rel="stylesheet" href="<?php echo $assetUrl; ?>/css/assignment.css?<?php echo VERHASH; ?>">

<div class="assign-mainer" id="assign_mainer">
    <div class="assign-mainer--list">
        <!-- 列表 -->
        <div class="assign-mainer--wrap">
            <ul class="assign-list" id="assign_list">

            </ul>
        </div>
        <!-- 列表 -->
        <!-- 添加按钮-->
        <div class="assign-mainer--add toggole-form-btn" data-action="openTaskAddDialog" data-param='<?php echo $param ?>'>
            <i class="o-plus cbtn"></i>
            <span class="mlm">添加任务</span>
        </div>
        <!-- 添加按钮-->
    </div>
</div>
<!--页脚-->
<div class="assign-footer" id="assign_footer">
    <a href="<?php echo $this->createUrl('unfinished/index'); ?>" target="_blank">
        <i class="o-assign-link"></i>
        <span class="mlm">任务管理</span>
    </a>
</div>
<script src="<?php echo STATICURL; ?>/js/lib/webuploader/webuploader.js?<?php echo VERHASH; ?>"></script>
<script src="<?php echo STATICURL; ?>/js/lib/webuploader/handlers.js?<?php echo VERHASH; ?>"></script>
<script src="<?php echo STATICURL; ?>/js/app/ibos.charCount.js?<?php echo VERHASH; ?>"></script>
<script src="<?php echo $assetUrl; ?>/js/assignment.js?<?php echo VERHASH; ?>"></script>
<script src="<?php echo $assetUrl; ?>/js/lang/zh-cn.js?<?php echo VERHASH; ?>"></script>
<script src="<?php echo $assetUrl; ?>/js/assignment_unfinished_list.js?<?php echo VERHASH; ?>"></script>
<script>
    param = <?php echo $param ?>;
    Assignment.loadAssignPopupList(param);
</script>
