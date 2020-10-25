<?php include 'public_head.php'; ?>

    <div class="title">
        <a href="/admin?c=ArticleCategory&a=index">文章分类</a>
        /
        <a href="/admin?c=ArticleCategory&a=edit">修改</a>
    </div>
    <div class="form-group">
        <div class="container">
        <form class="form" action="/admin?c=ArticleCategory&a=update&id=<?= $_GET['id'] ?>" method="POST">
            <div class="form-group">
                <label>标题</label>
                <input class="form-control" type="text" required name="title" value="<?= $info['title'] ?>">
            </div>
            <div class="form-group">
                <label>父级分类</label>
                <select class="form-control" name="cid" required>
                    <option value="0">顶级栏目</option>
                    <?php foreach($category as $k =>$v): ?>
                    <option value="<?= $k ?>" <?php if($k == $info['pid']): ?>selected<?php endif; ?> ><?= $v['title'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-warning">修改</button>
        </form>
        </div>
    </div>

<?php include 'public_foot.php'; ?>