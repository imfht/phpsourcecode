<link href="<?php echo $this->getAssetUrl(); ?>/css/approval.css" type="text/css" rel="stylesheet"/>
<div class="ct sp-ct">
    <div class="clearfix">
        <h1 class="mt"><?php echo $lang['Common Setting'];?> > <?php echo $lang['Verfiy Definition'];?></h1>
    </div>
    <div>
        <form id="process_setting_form" action="" method="post" class="form-horizontal process-setting-form">
            <div class="ctb ps-type-title">
                <div class="alert trick-tip">
                    <div class="trick-tip-title">
                        <i></i>
                        <strong><?php echo $lang['Function Introduction'] ?></strong>
                    </div>
                    <div class="trick-tip-content">
                        <ul>
                            <li>在信息公告、通知公告、会议管理中可调用已创建的审批流程</li>
                            <li>审批顺序：一级审批＞二级审批＞三级审批＞四级审批＞五级审批</li>
                            <li>若同级审批存在多个审批人，仅需其中一人通过审批，即可进入下级审批</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div>
                <ul class="process-list clearfix">
                    <?php if (empty($source)): ?>
                      <?php foreach ($approvals as $approval): ?>
                    <li data-id="<?php echo $approval['id']; ?>">
                        <div class="fill-nn approval-box">
                            <div class="clearfix mbs apploval-flow-title">
                                <div class="pull-left fsm xcn"><?php echo $approval['name']; ?></div>
                                <div class="pull-right">
                                    <a href="<?php echo $this->createUrl('approval/edit', array('id' => $approval['id'])); ?>"
                                       title="编辑" target="_self" class="o-edit"></a>
                                    <a href="javascript:;" title="删除" class="o-trash"></a>
                                </div>
                            </div>
                            <div class="process-step">
                                <div class="process-step-list">
                                    <div class="step-list mb">
                                        <?php foreach ($approval['levels'] as $level => $levelInfo): ?>
                                        <div class="step-content">
                                            <div class="step-icon">
                                                <i class="<?php echo $levelInfo['levelClass']; ?>"></i>
                                            </div>
                                            <div class="related-person"
                                                 title="<?php echo $levelInfo['title']; ?>">
                                                <?php echo !empty($levelInfo['show']) ? $levelInfo['show'] : '未设置审核人'; ?>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="approve-description">
                                    <span>免审核人</span>
                                    <p title="<?php echo $approval['desc']; ?>"
                                       class="description-content tcm"><?php echo !empty($approval['free']['show']) ? $approval['free']['show'] : '未设置免审核人'; ?></p>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                    <?php endif;?>
                    <li class="item-add">
                        <div class="fill-nn approval-box">
                            <a href="<?php echo $this->createUrl('approval/add'); ?>" class="process-item-add">
                                <i class="process-add-icon"></i>
                                <p class="">新建审批流程</p>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </form>
    </div>
</div>
<script src="<?php echo $this->getAssetUrl(); ?>/js/db_approval.js"></script>
