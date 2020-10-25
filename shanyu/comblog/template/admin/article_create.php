<?php include 'public_head.php'; ?>

    <div class="title">
        <a href="/admin?c=Article&a=index">文章管理</a>
        /
        <a href="/admin?c=Article&a=create">添加</a>
    </div>
    <div class="form-group">
        <div class="container">
        <form class="form" action="/admin?c=Article&a=store" method="POST">
            <div class="form-group">
                <label>标题</label>
                <input class="form-control" type="text" required name="title">
            </div>
            <div class="form-group">
                <label>分类</label>
                <select class="form-control" name="cid" required>
                    <?php foreach($category as $k =>$v): ?>
                    <option value="<?= $k ?>"><?= $v['title'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>正文</label>
                <div id="epiceditor" style="height:350px;"><textarea name="markdown" id="markdown" class="hide"></textarea></div>
                <script src="/assets/addons/epiceditor/js/epiceditor.min.js"></script>
                <script>
                var editorOpt={container: 'epiceditor',textarea: 'markdown',clientSideStorage:false,basePath: '/assets/addons/epiceditor'};
                var editor = new EpicEditor(editorOpt).load();
                </script>
            </div>
            <div class="form-group">
                <label>标签</label>
                <br>
                <?php foreach ($tag as $k => $v): ?>
                <label><input type="checkbox" name="tags[]" value="<?= $v['id'] ?>" > <?= $v['title']?></label>
                <?php endforeach ?>
            </div>
            <div class="form-group">
                <label>简介</label>
                <textarea name="description" rows="2" class="form-control" ></textarea>
            </div>
            <button type="submit" class="btn btn-success">添加</button>
        </form>
        </div>
    </div>

<?php include 'public_foot.php'; ?>