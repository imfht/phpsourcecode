<?php
/*
 * Template Lite plugin
 */
function tpl_modifier_mip($html){
    $html = preg_replace(
        array(
            '@<\s*img[^>]+src=(["\']?)(.*?)\\1[^>]*?>@is',
            '@<a[^>]+href=(["\']?)(.*?)\\1[^>]*?>(.*?)</a>@is',
            '@<video[^>](.*?)>(.*?)</video>@is',
        ),
        array(
            '<mip-img popup layout="responsive" src="$2"></mip-img>',
            '<mip-link href="$2">$3</mip-link>',
            '<mip-video $1>$2</mip-video>'
        ),
        $html
    );
	$html = preg_replace(
		array(
			"/<img\/>/",
			"/<p>(\r\n|\s+)*<br\s*\/>(\r\n|\s+)*<\/p>/",
			"/\s+style=.[^>]*/",
			"/<p\><br\/\><\/p\>/",
			"/<img>/"
		) ,
		'' , $html
	);

    return $html;
}
