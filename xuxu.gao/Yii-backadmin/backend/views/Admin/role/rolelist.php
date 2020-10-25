<?php
use backend\assets\AppAsset;
use yii\helpers\Url;
AppAsset::addCss($this,'@web/public/vendors/bootgrid/jquery.bootgrid.min.css');
AppAsset::addScript($this,'@web/public/vendors/bootgrid/jquery.bootgrid.min.js');
AppAsset::addScript($this,'@web/custom/role.js');
?>
<div class="card">
    <div class="card-header">
        <h2>角色列表</h2>
    </div>
    <table id="data-table-command" class="table table-striped table-vmiddle">
        <thead>
        <tr>
            <th data-column-id="name">角色名称</th>
            <th data-column-id="description">描述</th>
            <th data-column-id="created_at" data-order="desc">创建时间</th>
            <th data-column-id="updated_at">更新时间</th>
            <th data-column-id="commands" data-formatter="commands" data-sortable="false">操作</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>