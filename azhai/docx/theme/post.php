<?php $this->extendTpl($theme_dir . '/base.php'); ?>

<?php $this->blockStart('content'); ?>
<div class="content-page">
    <article>
        <div class="page-header sub-header clearfix">
            <h1><?php
                echo $page['title'] . "\n";
                if ($page_type !== 'html'):
                    $edit_doc_url = $urlpre . 'admin/' . $curr_url . '/';
                    echo '<a id="editThis" href="' . $edit_doc_url . '" class="btn html-hidden">编辑文档</a>';
                endif;
            ?></h1>
            <span style="float: left; font-size: 10px; color: gray;">
            <?php
            foreach($page['tags'] as $i => $tag):
                echo ($i > 0) ? ', ' : '标签：';
                echo $tag;
            endforeach;
            ?>
            </span>
            <span style="float: right; font-size: 10px; color: gray;">
            <!--a href="<?=$urlpre .'author/'. hp_slugify($page['author']);?>"-->
            <?=$page['author']?><!--/a--> 写于 <?php echo hp_zhdate($options['date_format'], $page['date']); ?>
            </span>
        </div>

        <?=$page['htmldoc']?>
    </article>
</div>
<?php $this->blockEnd(); ?>

<?php $this->blockStart('scripts'); ?>
<!-- hightlight.js -->
<script src="<?=$assets_url?>/js/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>
<?php $this->blockEnd(); ?>

