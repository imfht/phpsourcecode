<?php

use application\core\utils\Env;
use application\core\utils\Org;
use application\modules\main\utils\Main;

?>
<link rel="stylesheet" href="<?php echo $assetUrl; ?>/css/report.css?<?php echo VERHASH; ?>">
<!-- Mainer -->
<div class="wrap">
    <div class="mc clearfix">
        
        <!-- Sidebar -->
        <div class="aside" id="aside">
            <div class="sbb sbbl sbbf">
                <div class="fill-ss">
                    <a  href="javascript:;" data-hash="index" class="btn btn-warning btn-block">
                        <i class="o-new"></i> 发起汇报
                    </a>
                </div>
                <ul class="nav nav-strip nav-stacked">
                    <li>
                        <a href="javascript:;" data-hash="send">
                            <i class="o-rp-send"></i>
                            我发出的
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" data-hash="receive">
                            <i class="o-rp-receive"></i>
                            我收到的
                        </a>
                    </li>
                    <?php if (isset($manager) || $set):?>
                        <li>
                            <a href="javascript:;" data-hash="manager/index">
                                <i class="o-rp-manager"></i>
                                管理模板
                            </a>
                        </li>
                    <?php endif;?>
                    <!-- <li>
                        <a href="<?php echo $this->createUrl('default/index'); ?>">
                            <i class="o-rp-appraise"></i>
                            个人统计
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $this->createUrl('default/index'); ?>">
                            <i class="o-rp-appraise"></i>
                            评阅统计
                        </a>
                    </li> -->
                </ul>
            </div>
        </div>
        <!-- Mainer right -->
        <div class="mcr" id="container">
            <!-- Mainer content -->
        </div>
    </div>
</div>
<script src='<?php echo $assetUrl; ?>/js/lang/zh-cn.js?<?php echo VERHASH; ?>'></script>
<script>
    Ibos.app.s('userid', <?php echo $userid; ?>)
</script>
