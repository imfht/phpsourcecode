<?php include 'public_head.php'; ?>

    <div class="title">
        <a href="/admin?c=ArticleCategory&a=index">文章分类</a>
        /
        <a href="/admin?c=ArticleCategory&a=create">添加</a>
    </div>
    <div class="form-group">
        <div class="container">
        <form class="form" action="/admin?c=ArticleCategory&a=store" method="POST">
            <div class="form-group">
                <label>标题</label>
                <input class="form-control" type="text" required name="title">
            </div>
            <div class="form-group">
                <label>父级分类</label>
                <select class="form-control" name="cid" required>
                    <option value="0">顶级栏目</option>
                    <?php foreach($category as $k =>$v): ?>
                    <option value="<?= $k ?>"><?= $v['title'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success">添加</button>
        </form>
        </div>
    </div>

<?php include 'public_foot.php'; ?>