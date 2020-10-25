<?php include 'public_head.php'; ?>

    <div class="title">
        <a href="/admin?c=Tag&a=index">分类标签</a>
        /
        <a href="/admin?c=Tag&a=edit">修改</a>
    </div>
    <div class="form-group">
        <div class="container">
        <form class="form" action="/admin?c=Tag&a=update&id=<?= $_GET['id'] ?>" method="POST">
            <div class="form-group">
                <label>标签</label>
                <input class="form-control" type="text" required name="title" value="<?= $info['title'] ?>">
            </div>
            <button type="submit" class="btn btn-warning">修改</button>
        </form>
        </div>
    </div>

<?php include 'public_foot.php'; ?>