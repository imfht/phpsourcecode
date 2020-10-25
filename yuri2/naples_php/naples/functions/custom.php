<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/30
 * Time: 14:38
 */

//自定义全局函数声明处

// helper functions
// -----------------------------------------------------------------------------
// get html dom from file
// $maxlen is defined in the code as PHP_STREAM_COPY_ALL which is defined as -1.
/**
 * @return simple_html_dom
 */
function file_get_html($url, $use_include_path = false, $context=null, $offset = -1, $maxLen=-1, $lowercase = true, $forceTagsClosed=true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN=true, $defaultBRText=DEFAULT_BR_TEXT, $defaultSpanText=DEFAULT_SPAN_TEXT)
{
    // We DO force the tags to be terminated.
    $dom = new simple_html_dom(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);
    // For sourceforge users: uncomment the next line and comment the retreive_url_contents line 2 lines down if it is not already done.
    $contents = file_get_contents($url, $use_include_path, $context, $offset);
    // Paperg - use our own mechanism for getting the contents as we want to control the timeout.
    //$contents = retrieve_url_contents($url);
    if (empty($contents) || strlen($contents) > MAX_FILE_SIZE)
    {
        return false;
    }
    // The second parameter can force the selectors to all be lowercase.
    $dom->load($contents, $lowercase, $stripRN);
    return $dom;
}

// get html dom from string
/**
 * @return simple_html_dom
 */
function str_get_html($str, $lowercase=true, $forceTagsClosed=true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN=true, $defaultBRText=DEFAULT_BR_TEXT, $defaultSpanText=DEFAULT_SPAN_TEXT)
{
    $dom = new simple_html_dom(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);
    if (empty($str) || strlen($str) > MAX_FILE_SIZE)
    {
        $dom->clear();
        return false;
    }
    $dom->load($str, $lowercase, $stripRN);
    return $dom;
}

// dump html dom tree
/**
 * @return string
 */
function dump_html_tree($node, $show_attr=true, $deep=0)
{
    $node->dump($node);
}
