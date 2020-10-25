<?php $this->extendTpl($theme_dir . '/base.php'); ?>

<?php $this->blockStart('content'); ?>
<div class="content-page">
    <article>
        <div class="page-header sub-header clearfix">
            <h1><?php 
                echo $page['title'] . "\n";
                $view_doc_url = $urlpre . $curr_url . '/';
                echo '<a id="editThis" href="' . $view_doc_url . '" class="closeEditor btn btn-warning">关闭</a>';
            ?></h1>
            <span style="float: left; font-size: 10px; color: gray;">
            <?php foreach($page['tags'] as $i => $tag):
                                echo ($i > 0) ? ', ' : '标签：'; ?>
            <!--a href="<?=$urlpre .'tag/'. hp_slugify($tag);?>"--><?=$tag;?><!--/a-->
            <?php endforeach; ?>
            </span> 
            <span style="float: right; font-size: 10px; color: gray;">
            <!--a href="<?=$urlpre .'author/'. hp_slugify($page['author']);?>"--><?=$page['author']?><!--/a--> 写于 <?php echo hp_zhdate($options['date_format'], $page['date']); ?> 
            </span>
        </div>
        
        <form method="POST">
        <div class="navbar navbar-inverse navbar-default navbar-fixed-bottom" role="navigation">
            <div class="navbar-inner"> <a href="javascript:;" class="save_editor btn btn-primary navbar-btn pull-right">保存到文件</a> </div>
        </div>
        <textarea id="metatext_editor" name="metatext" style="display:none" 
                rows="<?php echo substr_count($page['metatext'], "\n") + 1; ?>" cols="80"><?=$page['metatext']?></textarea>
        <textarea id="markdown_editor" name="markdown"><?=$page['markdown']?></textarea>
        <div id="htmldoc_editor" name="markdown" class="pen hinted" placeholder="im a placeholder"><?=$page['htmldoc']?></div>
        </form>
        
        <div class="clearfix"></div>
    </article>
</div>
<?php $this->blockEnd(); ?>

<?php $this->blockStart('scripts'); ?>
<link rel="stylesheet" href="<?=$assets_url?>/css/pen.css">
<!-- hightlight.js -->
<script src="<?=$assets_url?>/js/pen.js"></script>
<script src="<?=$assets_url?>/js/markdown.js"></script>
<script src="<?=$assets_url?>/js/demarcate.min.js"></script>
<script type="text/javascript">
$(function(){
    var marked = $('#htmldoc_editor');
    var pen = new Pen({editor: marked[0], class: "hinted"});
    marked.addClass('hinted');
    pen.rebuild();
    $('.save_editor').click(function(){
        var marktext = demarcate.demarcate(marked);
        $('#markdown_editor').val(marktext);
        document.forms[0].submit();
    });
});
</script>
<?php $this->blockEnd(); ?>

