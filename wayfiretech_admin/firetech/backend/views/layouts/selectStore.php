<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-12 19:57:00
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-09 09:04:56
 */
use common\helpers\ImageHelper;
use richardfan\widget\JSRegister;
use yii\bootstrap\Modal;
use yii\helpers\Url;

?>
<style>
    .store-box{
        border: 1px solid #ccc;
        border-radius: 4px;
        cursor:pointer; 
    }
    .store-active{
        border:1px solid #5191fd!important;
    }
</style>
<?php
Modal::begin([
    'header' => '选择我的商户',
    'toggleButton' => false,
    'options' => [
        'id' => 'selectStore-id',
        'style' => 'padding:0px;',
    ],
    'size' => 'modal-lg',
    'footer' => '
    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
    <button type="button" class="btn btn-primary avatar-save" data-dismiss="modal">保存</button>',
]);
?>
 
    <ul class="nav nav-tabs">
       <?php foreach (Yii::$app->params['userBloc'] as $key => $value): ?> 
            <li   class="<?= $key == 0 ? 'active' : ''; ?> "><a href="#store-<?= $value['bloc_id']; ?>" data-toggle="tab"> <?= $value['business_name']; ?> </a></li>
        <?php endforeach; ?>
    </ul>
    <div class="nav-tabs-custom">                               
        <div class="tab-content storeModal" style="height:200px;overflow: auto;">
            <?php foreach (Yii::$app->params['userBloc'] as $key => $value): ?> 
               <?php if ($value['store']): ?>
                <section id="store-<?= $value['bloc_id']; ?>" class="tab-pane <?= $key == 0 ? 'active' : ''; ?>">
                        <?php foreach ($value['store'] as $k => $store): ?> 
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="info-box store-box <?= $store['store_id'] == Yii::$app->params['store_id'] ? 'store-active' : ''; ?>" 
                                        data-store_id="<?= $store['store_id']; ?>"
                                        data-bloc_id="<?= $store['bloc_id']; ?>"
                                        data-store_name="<?= $store['name']; ?>"
                                        >
                                        <span class="info-box-icon" style="background-color: #ef000000 !important;">
                                            <img src="<?= ImageHelper::tomedia($store['logo']); ?>" alt="" height="75px">
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text"> <?= $store['name']; ?></span>
                                            <span class="info-box-text"><?= $store['address']; ?></span>
                                        </div>
                                    </div>
                                </div>
                        <?php endforeach; ?>
                        </section>
                    <?php else: ?>
                        <section id="store-<?= $value['bloc_id']; ?>" class="tab-pane <?= $key == 0 ? 'active' : ''; ?>">
                            该公司暂无商户
                        </section>
                
                <?php endif; ?>
                
            <?php endforeach; ?>
         
        </div>
    </div>
        
<?php Modal::end(); ?>

<?php JSRegister::begin([
        'id' => 'icons',
    ]);
?>
<script>
    let storeSeleted = localStorage.getItem("bloc"); 
    if(storeSeleted){
        $('#bloc-left-name').text(JSON.parse(storeSeleted).store_name);        
    }
    $('.storeModal').on('click', '.store-box', function() {
        $('#bloc-left-name').text($(this).data('store_name'));  
        let  obj = JSON.stringify($(this).data());
        localStorage.setItem("bloc",obj);
        $('.store-box').removeClass('store-active')
        $(this).addClass('store-active')
    });
    
    $('#selectStore-id').on('hide.bs.modal', function () {
        // 执行一些动作...
        $.ajax({
                type:"POST",
                url:"<?= Url::to('system/settings/set-cache'); ?>",
                dataType:"json",
                data:{'bloc':localStorage.getItem("bloc")},
                success:function(data){
                    closeOtherTabs();
                    var pageId = getActivePageId();
                    refreshTabById(pageId)
                },
                error:function(jqXHR){
                   alert("发生错误："+ jqXHR.status);
                }
        });
    })
</script>

<?php JSRegister::end();
?>