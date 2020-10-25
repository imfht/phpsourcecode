<?php include 'public_head.php'; ?>

    <div class="title">
        <a href="/admin?c=Tag&a=index">分类标签</a>
    </div>
    <div class="clearfix">
        <div class="action pull-right">
            <a href="/admin?c=Tag&a=create" class="btn btn-success">添加</a>
        </div>
    </div>
    <div class="list-table">
        <table class="table">
            <tr>
                <th>标签</th>
                <th width="200">操作</th>
            </tr>
            <?php foreach ($list as $k => $v) : ?>
            <tr>
                <td>
                    <?= $v['title'] ?>
                </td>
                <td>
                    <a href="/admin?c=Tag&a=edit&id=<?= $v['id'] ?>" class="btn btn-warning btn-sm">修改</a>
                    <a href="/admin?c=Tag&a=destroy&id=<?= $v['id'] ?>" class="btn btn-danger btn-sm">删除</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

<?php include 'public_foot.php'; ?>