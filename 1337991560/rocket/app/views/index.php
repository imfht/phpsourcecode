<div class="content">
    {{ $article->content }}
    <?php
    $array = ['name' => '小民'];
    $html = "<span>{name}</span>";
    $html = variable_replace($html, $array);
    ?>
    {{ $html }}
</div>