<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-17 09:13:04
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-02 07:36:08
 */

use richardfan\widget\JSRegister;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="row" id="<?= $boxId; ?>">
    <div class="col-lg-12">
        <div class="input-group">
            <?= Html::textInput('address-all', $address, [
                'class' => 'form-control',
                'disabled' => true,
                'placeholder' => '请选择经纬度'
            ]); ?>
            <!-- <span class="input-group-btn"><button type="button" class="btn btn-default  map-edit">编辑</button></span> -->
            <span class="input-group-btn"><button type="button" class="btn btn-default  map-select"  data-toggle="modal" href='#modal-id'>地图选择</button></span>
        </div>
    </div>
<style>
    .modal-body{
        min-height: 400px;
    }
</style>
<?php Modal::begin([
            'header' => '',
           'toggleButton' => false,
           'options'=>[
               'id'=>'modal-id',
               'style'=>'padding:0px;'
           ],
           'size' => 'modal-lg',
           'footer'=>' <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
           <button class="btn btn-primary confirm" data-dismiss="modal">确定</button>',   
        ]); ?>
        
        <?= $this->renderAjax($type,[
            'lng' => $value['lng'],
            'lat' => $value['lat'],
            'zoom' => 12,
            'boxId' => $boxId,
            'defaultSearchAddress' => $defaultSearchAddress,
        ]); ?>
   
    <?= Html::hiddenInput($name . '[lng]', $value['lng'], ['class' => 'mapLng']); ?>
    <?= Html::hiddenInput($name . '[lat]', $value['lat'], ['class' => 'mapLat']); ?>
<?php Modal::end(); ?>
</div>
        
<?php  JSRegister::begin([
    'id'=>'maps'
])?>
<script>
    var boxId = "<?= $boxId; ?>";
    $(document).on('select-map-' + boxId, function(e, boxId, data){
        if (data.lng == 'undefined' || data.lng == undefined) {
            return;
        }

        var str = data.lng + "," + data.lat;
        console.log(str,boxId,data);
        $('#' + boxId).find('.input-group input').val(str);
        $('#' + boxId).find('.mapLng').val(data.lng);
        $('#' + boxId).find('.mapLat').val(data.lat);
    });
</script>
<?php  JSRegister::end(); ?>