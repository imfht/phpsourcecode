<?php require_once VIEWPATH . "$theme_id/inc/header.inc.php";?>
<div id="main" class="main layui-clear">
    <?php if (isset($article)): ?>
        <h2 class="page-title t_orange">追加内容，为保持内容上下文连贯性，文章发布后不可编辑，只可追加内容。</h2>
    <?php else: ?>
        <h2 class="page-title">发布</h2>
    <?php endif;?>
    <div class="layui-form layui-form-pane">
        <?php if (isset($article)): ?>
            <form method="post" onsubmit="return article_append(this);">
        <?php else: ?>
            <form method="post" onsubmit="return article_add(this);">
        <?php endif;?>
            <div class="layui-form-item">
                <label for="article_title" class="layui-form-label">标题</label>
                <div class="layui-input-block">
                    <?php if (isset($article)): ?>
                        <input type="text" id="article_title" name="title" value="<?=$article['article_title']?>" autocomplete="off" class="layui-input" disabled title="标题不可编辑">
                    <?php else: ?>
                        <input type="text" id="article_title" name="title" autocomplete="off" class="layui-input">
                    <?php endif;?>
                </div>
            </div>
            <div id="article_rich_editor"></div>
            <?php if (!isset($article)): ?>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">所在类别</label>
                    <div class="layui-input-block">
                        <select id="article_type" name="article_type">
                            <option>请选择分类</option>
                            <option value="1"<?=isset($article) && $article['article_type'] == 1 ? ' selected' : ''?>>问答</option>
                            <option value="2"<?=isset($article) && $article['article_type'] == 2 ? ' selected' : ''?>>讨论</option>
                            <option value="3"<?=isset($article) && $article['article_type'] == 3 ? ' selected' : ''?>>头条</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label for="article_title" class="layui-form-label">标签</label>
                <div class="layui-input-block">
                    <input type="text" id="article_topics" name="article_topics" autocomplete="off" class="layui-input" placeholder="多个标签用空格或英文,隔开，最多可添加5个标签">
                </div>
            </div>
            <?php endif;?>
            <div class="layui-form-item">
                <?php if (isset($article)): ?>
                    <button class="layui-btn">立即追加</button>
                <?php else: ?>
                    <button class="layui-btn">立即发布</button>
                <?php endif;?>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
var article_id = <?=isset($article) ? $article['id'] : 'null'?>;
var article_type = <?=isset($article) ? $article['article_type'] : 'null'?>;

//创建富文本编辑器
$(function(){
    //发布文章
    if(article_id == null){
        $('#article_title').focus();
        create_rich_editor('article_rich_editor', '', '请输入发布内容，可以 #话题名称# 这样插入话题。', 360, false);
    }
    else{
        create_rich_editor('article_rich_editor', '', '请输入追加内容', 360, true);
    }
});
</script>
<?php require_once VIEWPATH . "$theme_id/inc/footer.inc.php";?>