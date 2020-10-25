<?php
$show_menu = function($node, $menu_url, $children = [])
                use($curr_url, $urlpre, $urlext)
{
    $menu_url = trim($menu_url, '/');
    $curr_url = trim($curr_url, '/');
    $is_match = hp_starts_with($curr_url, $menu_url);
    if ($node['is_file']) {
        $li_class = $is_match ? ' class="active"' : '';
        return <<<EOD
<li{$li_class}>
<a href="{$urlpre}{$menu_url}{$urlext}">{$node['title']}</a>
</li>
EOD;
    } else if (isset($node['nodes'])) {
        $children = implode("\n", $children);
        $li_class = $is_match ? ' class="open"' : '';
        return <<<EOD
<li{$li_class}>
<a href="#" class="aj-nav folder">{$node['title']}</a>
<ul class="nav nav-list">
$children
</ul>
</li>
EOD;
    }
};

$menus = \Docx\Utility\FileSystem::traverse($organiz['nodes'], $show_menu, '');

?>
<!-- For Mobile -->
<div class="responsive-collapse">
    <button type="button" class="btn btn-sidebar" id="menu-spinner-button">
    <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
    </button>
</div>

<div id="sub-nav-collapse" class="sub-nav-collapse">
    <!-- Navigation -->
    <ul class="nav nav-list">
    <?=implode("\n", $menus)?>
    </ul>
    <div class="well well-sidebar">
        <div><a href="#" id="toggleCodeBlockBtn" onclick="toggleCodeBlocks();">外置代码框</a></div>
        <?php
        if ($page_type !== 'html'):
            echo '<div><a href="' . $urlpre . 'admin/staticize/">生成静态页</a></div>';
            echo '<div><a href="' . $urlpre . 'admin/publish/">Git发布</a></div>';
        endif;
        ?>
    </div>
    <div class="well well-sidebar">
        <!-- Links -->
        <?php
        if ($options['links']):
        foreach($options['links'] as $link_name => $link_url):
            echo '<div><a href="' . $link_url . '" target="_blank">' . $link_name . '</a></div>';
        endforeach;
        endif;
        ?>
    </div>
</div>