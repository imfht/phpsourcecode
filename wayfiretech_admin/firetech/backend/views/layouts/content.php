<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-28 16:00:03
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-26 23:15:24
 */
use common\widgets\adminlte\Alert;
use richardfan\widget\JSRegister;

?>
<section class="content" id="APP" style="background:#e8e8e8;">
<?= Alert::widget(); ?>
    
    <?= $content; ?>
  
    <div id="global_dialog"  >
        <el-dialog
                :title="title"
                :visible.sync="dialogVisible"
                width="70%"
                height="80%"
                top="50px"
                @open="openbefore"
                custom-class="globaldialog"
                :before-close="handleClose">
            <el-row  class="dialogLoad" v-if="loading" v-loading="loading"  element-loading-text="拼命加载中" element-loading-spinner="el-icon-loading">
            </el-row>    
            <iframe  :src="url"    @load="getTi()" frameborder="0" width="100%" height="600px"></iframe>

            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="dialogVisible = false">关闭</el-button>
            </span>
        </el-dialog>
    </div>
</section>


<?php JSRegister::begin([
    'id' => '1',
]); ?>
<script>
    var parentPageid = $(window.parent.document).find(".tab-pane.active").data('pageid');
    var src = $(window.parent.document).find("#iframe_" + parentPageid).attr('src');

    if (window.location.pathname != src) {
        $('.backMenu').show();
    }
</script>
<?= JSRegister::end(); ?>