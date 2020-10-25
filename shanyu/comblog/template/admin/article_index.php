<?php include 'public_head.php'; ?>

    <div class="title">
        <a href="/admin?c=Article&a=index">文章管理</a>
    </div>
    <div class="clearfix">
        <div class="search pull-left">
            <form class="form-inline" action="/admin?c=Article&a=index" method="POST">
            <input placeholder="标题" class="form-control" type="text" name="title" value="<?= isset($_POST['title'])?$_POST['title']:'' ?>">
            <input class="btn btn-default" type="submit" value="搜索">
            <a href="/admin?c=Article&a=index" class="btn btn-default">重置</a>
            </form>
        </div>
        <div class="action pull-right">
            <a href="/admin?c=Article&a=create" class="btn btn-success">添加</a>
        </div>
    </div>
    <div class="list-table">
        <table class="table">
            <tr>
                <th>标题</th>
                <th width="100">阅读</th>
                <th width="100">分类</th>
                <th width="150">时间</th>
                <th width="200">操作</th>
            </tr>
            <?php foreach ($articles['list'] as $k => $v) : ?>
            <tr>
                <td>
                    <a href="<?= $v['url'] ?>" target="_blank">
                    <?= $v['title'] ?>
                    </a>
                </td>
                <td><?= $v['view'] ?></td>
                <td><?= $v['category']['title'] ?></td>
                <td><?= $v['create_time'] ?></td>
                <td>
                    <a href="/admin?c=Article&a=edit&id=<?= $v['id'] ?>" class="btn btn-warning btn-sm">修改</a>
                    <a href="/admin?c=Article&a=destroy&id=<?= $v['id'] ?>" class="btn btn-danger btn-sm">删除</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="list-page">
        <?= $articles['page'] ?>
    </div>

<?php include 'public_foot.php'; ?>