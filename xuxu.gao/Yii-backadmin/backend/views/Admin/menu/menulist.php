<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AppAsset;
AppAsset::addCss($this,'@web/public/vendors/bootgrid/jquery.bootgrid.min.css');
AppAsset::addScript($this,'@web/public/vendors/bootgrid/jquery.bootgrid.min.js');
AppAsset::addScript($this,'@web/custom/menu.js');
?>
<div class="card">
    <div class="card-header">
        <h2>菜单列表</h2>
    </div>
    <table id="data-table-command" class="table table-striped table-vmiddle">
        <thead>
        <tr>
            <th data-column-id="treeson" data-formatter="showson" data-sortable="false"></th>
            <th data-column-id="id" data-type="numeric" data-order="desc">ID</th>
            <th data-column-id="name">菜单名称</th>
            <th data-column-id="url">地址</th>
            <th data-column-id="slug">权限</th>
            <th data-column-id="created_at">创建时间</th>
            <th data-column-id="updated_at">更新时间</th>
            <th data-column-id="commands" data-formatter="commands" data-sortable="false">操作</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>
