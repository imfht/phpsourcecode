<?php

use application\core\utils\Env;
use application\core\utils\Module;
use application\modules\statistics\core\StatConst;
use application\modules\statistics\utils\StatCommon;
use application\modules\report\core\ReportType as ICReportType;

?>

<div class="aside" id="aside">
    <div class="sbb sbbl sbbf">
        <div class="fill-ss">
            <a href="<?php echo $this->createUrl('default/add'); ?>" class="btn btn-warning btn-block">
                <i class="o-new"></i> 发起汇报
            </a>
        </div>
        <ul class="nav nav-strip nav-stacked">
            <li class="active">
                <a href="<?php echo $this->createUrl('default/index'); ?>">
                    <i class="o-rp-personal"></i>
                    我发出的
                </a>
            </li>
            <li>
                <a href="<?php echo $this->createUrl('manager/receive'); ?>">
                    <i class="o-rp-appraise"></i>
                    我收到的
                </a>
            </li>
            <li>
                <a href="<?php echo $this->createUrl('review/index'); ?>">
                    <i class="o-rp-appraise"></i>
                    管理模板
                </a>
            </li>
            <li>
                <a href="<?php echo $this->createUrl('review/index'); ?>">
                    <i class="o-rp-appraise"></i>
                    个人统计
                </a>
            </li>
            <li>
                <a href="<?php echo $this->createUrl('review/index'); ?>">
                    <i class="o-rp-appraise"></i>
                    评阅统计
                </a>
            </li>
        </ul>
    </div>
</div>