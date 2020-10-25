<?php

function shorten_white_space($content)
{
    $content = preg_replace('/\s+/', ' ', $content);
    return $content;
}

function parse_url_link($content)
{
    $content = preg_replace("#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie",
        "'<a class=\"label label-badge\" href=\"$1\" target=\"_blank\"><i class=\"icon-link\" title=\"$1\"></i></a>$4'", $content
    );
    return $content;
}

function parse_content($content)
{
    return $content;
}