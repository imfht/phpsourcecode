<?php
// 获取域名后缀
function get_domain_suffix($domain) {
    include_once(dirname(__FILE__)."/domains_db.inc.php");

    $domain = strtolower(trim($domain));
    $tmp = explode(".", $domain);
    $len = count($tmp);
    if($len < 2) {
        return "";
    }
    $s1 = $tmp[$len-1];
    $s2 = $tmp[$len-2];

    $db = include(dirname(__FILE__)."/domains_db.inc.php");
    foreach($db as $v) {
        if(".$s2.$s1" == $v[0] || ".$s1" == $v[0]) {
            return trim($v[0], ".");
        }
    }

    return "";
}

// 获取顶级域名后缀
function get_domain_top_suffix($domain) {
    $suffix = get_domain_suffix($domain);
    $tmp = explode(".", $suffix);
    return end($tmp);
}

// 获取域名punycode代码
function get_domain_punycode($domain) {
    include_once(dirname(__FILE__)."/idna_convert.class.php");
    $idna = new idna_convert();
    return $idna->encode($domain);
}

