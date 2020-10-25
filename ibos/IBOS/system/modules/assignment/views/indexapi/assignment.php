<?php

use application\core\utils\Ibos;
use application\core\utils\StringUtil;
use application\modules\assignment\utils\Assignment as AssignmentUtil;

?>
<link rel="stylesheet" href="<?php echo $assetUrl; ?>/css/assignment.css?<?php echo VERHASH; ?>">
<!--我负责的 todo 修改为今天到期的-->
<?php if ($tab == 'today'): ?>
    <?php if (!empty($todayData)): ?>
        <table class="table table-underline">
            <?php foreach ($todayData as $today): ?>
                <tr>
                    <td width="40">
                        <span class="avatar-circle avatar-circle-small">
                            <img class="mbm" src="<?php echo $today['designee']['avatar_small']; ?>" alt="">
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo Ibos::app()->urlManager->createUrl('assignment/default/show', array('assignmentId' => $today['assignmentid'])); ?>"
                           class="xcm">
                            <?php echo StringUtil::cutStr($today['subject'], 40); ?>
                        </a>
                        <div class="fss">
                            <?php echo $today['designee']['realname']; ?>
                            <?php echo $today['st']; ?> -- <?php echo $today['et']; ?>
                            <?php if (TIMESTAMP > $today['endtime']): ?>
                                <i class="om-am-warning mls" title="<?php echo $lang['Expired']; ?>"></i>
                            <?php elseif ($today['remindtime'] > 0): ?>
                                <i class="om-am-clock mls" title="<?php echo $lang['Has been set to remind']; ?>"></i>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td width="60">
                        <span
                            class="pull-right am-tag am-tag-<?php echo AssignmentUtil::getCssClassByStatus($today['status']) ?>">
                            <?php if ($today['status'] == 0): ?>
                                <?php echo $lang['Unreaded']; ?>
                            <?php elseif ($today['status'] == 1): ?>
                                <?php echo $lang['Ongoing']; ?>
                            <?php elseif ($today['status'] == 4): ?>
                                <?php echo $lang['Has been cancelled']; ?>
                            <?php endif; ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="mbox-base">
            <div class="fill-hn xac">
                <a href="<?php echo Ibos::app()->urlManager->createUrl('assignment/unfinished/index'); ?>"
                   class="link-more">
                    <i class="cbtn o-more"></i>
                    <span class="ilsep"><?php echo $lang['Show more assignment']; ?></span>
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="am-empty"></div>
    <?php endif; ?>
    <!--我指派的 todo 修改为明天到期的-->
<?php elseif ($tab == 'tomorrow'): ?>
    <?php if (!empty($tomorrowData)): ?>
        <table class="table table-underline">
            <?php foreach ($tomorrowData as $tomorrow): ?>
                <tr>
                    <td width="40">
                        <span class="avatar-circle avatar-circle-small">
                            <img class="mbm" src="<?php echo $tomorrow['charge']['avatar_small']; ?>" alt="">
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo Ibos::app()->urlManager->createUrl('assignment/default/show', array('assignmentId' => $tomorrow['assignmentid'])); ?>"
                           class="xcm">
                            <?php echo StringUtil::cutStr($tomorrow['subject'], 40); ?>
                        </a>
                        <div class="fss">
                            <?php echo $tomorrow['charge']['realname']; ?>
                            <?php echo $tomorrow['st']; ?> -- <?php echo $tomorrow['et']; ?>
                            <?php if (TIMESTAMP > $tomorrow['endtime']): ?>
                                <i class="om-am-warning mls" title="<?php echo $lang['Expired']; ?>"></i>
                            <?php elseif ($tomorrow['remindtime'] > 0): ?>
                                <i class="om-am-clock mls" title="<?php echo $lang['Has been set to remind']; ?>"></i>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td width="60">
                        <span
                            class="pull-right am-tag am-tag-<?php echo AssignmentUtil::getCssClassByStatus($tomorrow['status']) ?>">
                            <?php if ($tomorrow['status'] == 0): ?>
                                <?php echo $lang['Unreaded']; ?>
                            <?php elseif ($tomorrow['status'] == 1): ?>
                                <?php echo $lang['Ongoing']; ?>
                            <?php elseif ($tomorrow['status'] == 4): ?>
                                <?php echo $lang['Has been cancelled']; ?>
                            <?php endif; ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="mbox-base">
            <div class="fill-hn xac">
                <a href="<?php echo Ibos::app()->urlManager->createUrl('assignment/unfinished/index'); ?>"
                   class="link-more">
                    <i class="cbtn o-more"></i>
                    <span class="ilsep"><?php echo $lang['Show more assignment']; ?></span>
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="am-empty"></div>
    <?php endif; ?>
<?php endif; ?>
