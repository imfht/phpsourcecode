<?php
/*
 * Template Lite plugin
 */
function tpl_output_mip(&$html,&$tpl){

    $html = preg_replace(
        array(
            '@on(\w+)=(["\']?)+\\1@is',
            '@style=(["|\']?)+\\1@is',
            /*
            '@<script[^>]*>.*?</script>@is',
            '@<style[^>]*>.*?</style>@is',
            '@<link.*?rel=(["|\']?)stylesheet\\1.*?>@is',
            '@<link.*?type=(["|\']?)text/css\\1.*?>@is',
            */
        ),
        '',$html
    );

    $html = preg_replace(
        array(
            '@<!doctype.*?>@is',
            '@<html.*?>@is',
            '@<img[^>]+src=(["\']?)(.*?)\\1[^>]*?>@is',
            '@<a[^>]+href=(["\']?)(.*?)\\1[^>]*?>(.*?)</a>@is',
            '@<video[^>](.*?)>(.*?)</video>@is',
        ),
        array(
            '<!DOCTYPE html>',
            '<html mip>',
            '<mip-img layout="responsive" src="$2"></mip-img>',
            '<mip-link href="$2">$3</mip-link>',
            '<mip-video $1>$2</mip-video>'
        ),
        $html
    );
}
