<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-17 10:50:22
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-17 10:53:40
 */

use richardfan\widget\JSRegister;
use yii\helpers\Html;

?>

    <div class="row">
        <div class="form-group">
            <div class="col-sm-3 text-right required">
                <label class="control-label">经度(lng)</label></div>
            <div class="col-sm-9">
                <?= Html::textInput('lng', $lng, ['class' => 'form-control', 'id' => 'rfMapLng']); ?>
                <div class="help-block"></div>
            </div>
        </div>
        <div class="form-group required">
            <div class="col-sm-3 text-right">
                <label class="control-label">纬度(lat)</label></div>
            <div class="col-sm-9">
                <?= Html::textInput('lat', $lat, ['class' => 'form-control', 'id' => 'rfMapLat']); ?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>


<?php  JSRegister::begin([
    'id'=>'maps'
])?>
<script>
    var boxId = "<?= $boxId;?>";
    // 选择
    $('#mapConfirm').click(function () {
        var lat = $('#rfMapLat').val();
        var lng = $('#rfMapLng').val();

        if (!lng) {
            rfWarning('请填写经度(lng)');
            return;
        }

        if (!lat) {
            rfWarning('请填写纬度(lat)');
            return;
        }

        var data = {lat: lat, lng: lng};
        $(document).trigger('select-map-' + boxId, [boxId, data]);
        // 关闭 model
        $('#rfMapClose').trigger('click');
        console.log(data);
    });

</script>
<?php  JSRegister::end(); ?>
