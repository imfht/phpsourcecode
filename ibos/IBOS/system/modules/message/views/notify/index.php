<?php

use application\core\utils\Convert;
use application\core\utils\Ibos;
use application\modules\user\model\User;

?>

<link rel="stylesheet" href="<?php echo $assetUrl; ?>/css/message.css?<?php echo VERHASH; ?>">
<div class="mc clearfix">
    <!-- Sidebar goes here-->
    <?php echo $this->getSidebar(array('lang' => $lang)); ?>
    <!-- Mainer right -->
    <div class="mcr">
        <div class="page-list message-page-list" id="remind_list">
            <div class="page-list-header page-list-header__nav">
                <ul class="nav nav-skid">
                    <li <?php if($isread == 0): ?>class="active"<?php endif;?>>
                        <a href="javascript:;" data-action="toggleQueryParam" data-param='{"type": "isread", "value": "0"}'><?php echo $lang['Unread']?> ( <?php echo $unreadCount; ?> )</a>
                    </li>
                    <li <?php if($isread == 1): ?>class="active"<?php endif;?>>
                        <a href="javascript:;" data-action="toggleQueryParam" data-param='{"type": "isread", "value": "1"}'><?php echo $lang['Read']?> ( <?php echo $readCount; ?> )</a>
                    </li>
                </ul>
            </div>
            <div class="page-list-header page-list-header__query">
                <div class="module-query" id="module_list_wrap">
                    <div class="control-group">
                        <label class="control-label xwb"><?php echo $lang['Belongs to module']; ?></label>
                        <div class="controls clearfix">
                            <ul class="module-query--list">
                                <li <?php if($module == ""): ?>class="active"<?php endif;?>>
                                    <a href="javascript:;" data-action="toggleQueryParam" data-param='{"type": "module", "value": ""}'><?php echo $lang['There is no limit']; ?></a>
                                </li>
                                <?php foreach ($modulelist as $key => $val) {?>
                                    <li <?php if($module == $key): ?>class="active"<?php endif;?>>
                                        <a href="javascript:;" data-action="toggleQueryParam" data-param='{"type": "module", "value": "<?php echo $key;?>"}'><?php echo $val;?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="clearfix">
                    <div class="pull-left">
                        <label class="checkbox btn"><input type="checkbox" data-name="remind"></label>
                        <?php if($isread == 0): ?>
                            <button type="button" class="btn btn-primary mls" data-action="markNoticeRead"><?php echo $lang['Set read']; ?></button>
                        <?php endif; ?>
                        <button type="button" class="btn mls" data-action="removeNotices"><?php echo $lang['Delete']; ?></button>
                    </div>
                    <a class="module-query--toggle" data-action="toggleModuleList"><?php echo $lang['Select module']; ?></a>
                    <div class="search pull-right">
                        <input type="text" placeholder="<?php echo $lang['Search notily content']; ?>" id="notify_manage_search" name="keyword" nofocus="" value="<?php echo $search;?>">
                        <a href="javascript:;">search</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-list-mainer">
            <?php if (!empty($list)): ?>
            <ul class="main-list main-list-hover msg-pm-list">
                <?php foreach ($list as $key => $data): ?>
                <li class="main-list-item" id="remind_<?php echo $data['id']; ?>">
                    <div class="msg-box clearfix">
                        <label class="checkbox checkbox-inline pull-left">
                            <input type="checkbox" name="remind" value="<?php echo $data['id']; ?>">
                        </label>
                        <div class="avatar-box pull-left posr mls">
                            <img width="50" src="<?php echo Ibos::app()->assetManager->getAssetsUrl($data['module']) . '/image/icon.png'; ?>" alt="">
                        </div>
                        <?php if ($data['isalarm'] == 0): ?>
                            <div class="main-list-item-body pull-left">
                                <div>
                                    <strong>
                                        <?php echo empty($allmodule[$data['module']]['name']) ? $data['module'] : $allmodule[$data['module']]['name']; ?>
                                    </strong>
                                    <span class="tcm mls"><?php echo Convert::formatDate($data['ctime'], 'u'); ?></span>
                                </div>
                                <ul class="clist">
                                    <li>
                                        <?php if (empty($data['url'])): ?>
                                            <?php echo CHtml::encode($data['title']); ?>
                                        <?php else: ?>
                                            <a href="<?php echo $this->createUrl('notify/jump', array('id' => $data['id'], 'url' => $data['url'])); ?>"><?php echo $data['title']; ?></a>
                                        <?php endif; ?>
                                    </li>
                                </ul>
                            </div>
                        <?php else: ?>
                        <div class="main-list-item-body pull-left">
                            <div>
                                <strong>
                                    <?php if (empty($data['url'])): ?>
                                        <?php echo CHtml::encode($data['title']); ?>
                                    <?php else: ?>
                                        <a href="<?php echo $this->createUrl('notify/jump', array('id' => $data['id'], 'url' => $data['url'])); ?>"><?php echo $data['title']; ?></a>
                                    <?php endif; ?>
                                </strong>
                                <span class="tcm mls"><?php echo Convert::formatDate($data['ctime'], 'u'); ?></span>
                            </div>
                            <ul class="clist">
                                <li>
                                    <?php echo CHtml::encode($data['body']); ?>
                                </li>
                                <?php if(!empty($data['senduid']) && is_numeric($data['senduid']) ) :?>
                                <li>
                                    <i class="o-msg-remembrancer"></i>
                                    <span class="mlm"><?php echo User::model()->fetchRealnameByUid($data['senduid']) ?></span>
                                </li>
                                <?php endif;?>
                            </ul>
                        </div>
                    <?php endif; ?>
                        <a href="javascript:;" title="<?php echo $lang['Delete']; ?>" class="cbtn o-trash mls pull-right" data-action="removeNotice" data-param='{"id": "<?php echo $data['id']; ?>"}'></a>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
                <div class="no-data-tip"></div>
            <?php endif; ?>
        </div>
        <div class="page-list-footer">
            <?php $this->widget('application\core\widgets\Page', array('pages' => $pages)); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        Ibos.app.s('queryParam', {"module":"<?php echo $module; ?>","search":"<?php echo $search; ?>","isread":"<?php echo $isread; ?>"});
    });
</script>
<script src='<?php echo $assetUrl; ?>/js/message_notify_index.js?<?php echo VERHASH; ?>'></script>
