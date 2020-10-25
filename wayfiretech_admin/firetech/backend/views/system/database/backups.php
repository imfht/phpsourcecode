<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-06-23 21:07:35
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-24 01:07:33
 */
 
use common\helpers\Auth;
use common\widgets\adminlte\VuemainAsset;
use diandi\admin\components\Helper;
use richardfan\widget\JSRegister;
use yii\helpers\Url;
VuemainAsset::register($this);
$this->registerJs($this->render('_script.js'));
$this->title = '数据备份';
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>
<?= $this->render('_tab') ?>
<div class="firetech-main" id="backups">
    <div class="panel panel-default">
          <div class="panel-body table-responsive">
                           <table class="table table-hover">
                            <thead>
                            <tr>
                                <th><input type="checkbox" checked="checked" class="check-all"></th>
                                <th>表备注</th>
                                <th>表名</th>
                                <th>类型</th>
                                <th>记录总数</th>
                                <th>数据大小</th>
                                <th>编码</th>
                                <!-- <th>创建时间</th>-->
                                <th>备份状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody id="list">
                            <?php foreach ($models as $model) { ?>
                                <tr name="<?= $model['name'] ?>">
                                    <td><input type="checkbox" name="table[]" checked="checked" value="<?= $model['name'] ?>"></td>
                                    <td><?= $model['comment'] ?></td>
                                    <td><?= $model['name'] ?></td>
                                    <td><?= $model['engine'] ?></td>
                                    <td><?= $model['rows'] ?></td>
                                    <td><?= Yii::$app->formatter->asShortSize($model['data_length'], 0) ?></td>
                                    <td><?= $model['collation'] ?></td>
                                    <!-- <td>--><?php //$model['create_time'] ?><!--</td>-->
                                    <td id="<?= $model['name'] ?>">未备份</td>
                                    <td>
                                        <!-- 权限校验 -->
                                        <?php if (Helper::checkRoute('/sys/data-base/optimize')) { ?>
                                            <a href="#" class="btn btn-white table-list-optimize">优化表</a>
                                        <?php } ?>
                                        <!-- 权限校验 -->
                                        <?php if (Helper::checkRoute('/sys/data-base/repair')) { ?>
                                            <a href="#" class="btn btn-white table-list-repair">修复表</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                  
          </div>
    </div>
    

    <el-dialog
        :title="logmessage"
        :visible.sync="dialogVisible"
        width="80%"
        :before-close="handleClose"
        >
        <el-progress :text-inside="true" :stroke-width="26" :percentage="percentage"></el-progress>
        <span slot="footer" class="dialog-footer text-center">
            备份文件名称:{{filename}}
            <el-button type="primary" @click="dialogsubmit" v-show="achieveStatus">确 定</el-button>
        </span>
</el-dialog>

    <nav class="navbar navbar-default navbar-fixed-bottom text-center" role="navigation" style="padding-top:6px;">
                <div class="btn-group m-l-n-sm">
                    <el-button-group>
                        <el-button type="primary" size="medium"   v-show="<?= Helper::checkRoute('/sys/data-base/export')?>" @click="Export">立即备份</el-button>
                        <el-button type="primary" size="medium"   v-show="<?= Helper::checkRoute('/sys/data-base/repair')?>" @click="repair">修复表</el-button>
                        <el-button type="primary" size="medium"   v-show="<?= Helper::checkRoute('/sys/data-base/optimize')?>" @click="optimize">优化表</el-button>
                    </el-button-group>
                </div>
    </nav>
</div>