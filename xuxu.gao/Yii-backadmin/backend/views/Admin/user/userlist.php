<?php
    use backend\assets\AppAsset;
    use yii\helpers\Url;
    AppAsset::addCss($this,'@web/public/vendors/bootgrid/jquery.bootgrid.min.css');
    AppAsset::addScript($this,'@web/public/vendors/bootgrid/jquery.bootgrid.min.js');
    AppAsset::addScript($this,'@web/custom/user.js');
?>
<div class="card">
    <div class="card-header">
        <h2>用户列表</h2>
    </div>
    <table id="data-table-command" class="table table-striped table-vmiddle">
        <thead>
        <tr>
            <th data-column-id="id" data-type="numeric">ID</th>
            <th data-column-id="username">用户名</th>
            <th data-column-id="email" data-order="desc">邮箱</th>
            <th data-column-id="commands" data-formatter="commands" data-sortable="false">操作</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>
