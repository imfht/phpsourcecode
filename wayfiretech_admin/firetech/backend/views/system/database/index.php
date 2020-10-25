<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-06-04 19:00:54
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-23 20:52:29
 */

use common\widgets\adminlte\VuemainAsset;
use richardfan\widget\JSRegister;
use yii\widgets\ActiveForm;

VuemainAsset::register($this);

$this->title = '数据库备份';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>
<div class="firetech-main" id="databackup">
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">数据库</h3>
                </div>
            
                <div class="box-body">
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-primary" type="button" @click="backup">数据库备份</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php JSRegister::begin([
    'key'=>'data'
])?>

<script>
new Vue({
    el: '#databackup',
    data: function () {
        return {
            pos: [],
        }
    },
    created: function () {
        let that = this;
       
        console.log('全局设置是否可以',window.sysinfo)
    },
    methods: {
      backup() {
            let that = this
            console.log(that)
            that.tableData7=[]
            //Lambda写法
            that.$http.post('backup', {}).then((response) => {
              console.log('response',response)
              // return false;
                //响应成功回调
                if (response.data.code == 200) {  
                 

                }
            }, (response) => {
                //响应错误回调
                console.log('错误了',response)
            });
    }
  }
})
</script>

<?php JSRegister::end(); ?>
