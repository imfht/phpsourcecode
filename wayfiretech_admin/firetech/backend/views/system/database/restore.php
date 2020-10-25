<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-06-23 21:08:55
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-24 09:37:04
 */
use common\widgets\adminlte\VuemainAsset;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '数据还原';
$this->params['breadcrumbs'][] = ['label' => $this->title];
VuemainAsset::register($this);
$this->registerJs($this->render('_script.js'));
?>
<?= $this->render('_tab'); ?>

<div class="firetech-main" id="backups">
    <div class="row">
        <div class="col-sm-12">
            <div class="nav-tabs-custom">
                    <template>
                        <el-table
                            :data="tableData"
                            style="width: 100%">
                            <el-table-column label="备份名称" prop="backname"></el-table-column>
                            <el-table-column label="卷数" prop="part"></el-table-column>
                            <el-table-column label="压缩" prop="compress"></el-table-column>
                            <el-table-column label="数据大小" prop="size"></el-table-column>
                            <el-table-column label="备份时间" prop="time"></el-table-column>
                            
                            <el-table-column
                            align="right"  label="操作">
                                <template slot-scope="scope">
                                    <el-button
                                    size="mini"
                                    @click="handleEdit(scope.$index, scope.row)">还原</el-button>
                                    <el-button
                                    size="mini"
                                    type="danger"
                                    @click="handleDelete(scope.$index, scope.row)">删除</el-button>
                                </template>
                            </el-table-column>
                        </el-table>
                    </template>
            </div>
        </div>
    </div>
</div>