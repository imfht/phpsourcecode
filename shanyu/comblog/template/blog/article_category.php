<?php include 'public_head.php'; ?>
<?php 
    function getHtmlByTree($tree)
    {
        $list='<ul>';
        foreach ($tree as $v) {
            $list.='<li><a href="'.$v['url'].'">'.$v['title'].'<small>['.$v['count'].']</small></a></li>';
            if(!isset($v['_child'])) continue;
            $list.=getHtmlByTree($v['_child']);
        }
        $list.='</ul>';
        return $list;
    }
?>
<div class="article-category">
    <div class="title">
        文章分类
    </div>
    <div class="list">
        <?= getHtmlByTree($category_tree); ?>
    </div>
</div>
<?php include 'public_foot.php'; ?>