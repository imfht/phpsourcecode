<?php
//判断是否是搜索爬虫
function isSpider()
{
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (!empty($agent)) {
        $spiderSite = array(
            "TencentTraveler",
            "Baiduspider+",
            "BaiduGame",
            "Googlebot",
            "msnbot",
            "Sosospider+",
            "Sogou web spider",
            "ia_archiver",
            "Yahoo! Slurp",
            "YoudaoBot",
            "Yahoo Slurp",
            "MSNBot",
            "Java (Often spam bot)",
            "BaiDuSpider",
            "Voila",
            "Yandex bot",
            "BSpider",
            "twiceler",
            "Sogou Spider",
            "Speedy Spider",
            "Google AdSense",
            "Heritrix",
            "Python-urllib",
            "Alexa (IA Archiver)",
            "Ask",
            "Exabot",
            "Custo",
            "OutfoxBot/YodaoBot",
            "yacy",
            "SurveyBot",
            "legs",
            "lwp-trivial",
            "Nutch",
            "StackRambler",
            "The web archive (IA Archiver)",
            "Perl tool",
            "MJ12bot",
            "Netcraft",
            "MSIECrawler",
            "WGet tools",
            "larbin",
            "Fish search",
        );
        foreach ($spiderSite as $val) {
            $str = strtolower($val);
            if (strpos($agent, $str) !== false) {
                return true;
            }
        }
    } else {
        return false;
    }
}

/**
 * 当前路径
 * 返回指定栏目路径层级
 * @param $catid 栏目id
 * @param $symbol 栏目间隔符
 */
function catpos($catid, $symbol = ' > ')
{
    $category_arr = array();
    $category_arr = F('category_content');
    if (!isset($category_arr[$catid])) {
        return '';
    }

    $pos         = '';
    $siteurl     = C('SITE_URL');
    $arrparentid = array_filter(explode(',', $category_arr[$catid]['arrparentid'] . ',' . $catid));
    foreach ($arrparentid as $catid) {
        $pos .= '<a href="' . getUrl($catid) . '">' . $category_arr[$catid]['catname'] . '</a>' . $symbol;
    }
    return $pos;
}

//前台展示的url
function getUrl($catid, $id = null)
{
    $categorys = F('category_content');
    if ($catid && empty($id)) {
        return U('lists', ['catid' => $catid]);
        return '/' . $categorys[$catid]['catdir'];
    } elseif ($catid && $id) {
        return U('show', ['catid' => $catid, 'id' => $id]);
        return '/' . $categorys[$catid]['catdir'] . '/' . $id . '.html';
    }
}

/**
 * 生成SEO
 * @param $siteid       站点ID
 * @param $catid        栏目ID
 * @param $title        标题
 * @param $description  描述
 * @param $keyword      关键词
 */
function seo($catid = '', $title = '', $description = '', $keyword = '')
{
    $site = D('Site')->getSetting();

    if (!empty($title)) {
        $title = strip_tags($title);
    }

    if (!empty($description)) {
        $description = strip_tags($description);
    }

//    if (!empty($keyword)) $keyword = str_replace(' ', ',', strip_tags($keyword));
    $cat = array();
    if (!empty($catid)) {
        $categorys      = F('category_content');
        $cat            = $categorys[$catid];
        $cat['setting'] = string2array($cat['setting']);
    }
    // $seo['site_title'] = C('SITE_NAME');
    $seo['site_title']  = isset($site['site_title']) && !empty($site['site_title']) ? $site['site_title'] : $site['name'];
    $seo['keyword']     = !empty($keyword) ? $keyword : $site['keywords'];
    $seo['description'] = isset($description) && !empty($description) ? $description : (isset($cat['setting']['meta_description']) && !empty($cat['setting']['meta_description']) ? $cat['setting']['meta_description'] : (isset($site['description']) && !empty($site['description']) ? $site['description'] : ''));
    $seo['title']       = (isset($title) && !empty($title) ? $title . ' | ' : '') . (isset($cat['setting']['meta_title']) && !empty($cat['setting']['meta_title']) ? $cat['setting']['meta_title'] . ' | ' : (isset($cat['catname']) && !empty($cat['catname']) ? $cat['catname'] . ' | ' : ''));
    foreach ($seo as $k => $v) {
        $seo[$k] = str_replace(array("\n", "\r"), '', $v);
    }
    return $seo;
}

function ismobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }

    //此条摘自TPM智能切换模板引擎，适合TPM开发
    if (isset($_SERVER['HTTP_CLIENT']) && 'PhoneClient' == $_SERVER['HTTP_CLIENT']) {
        return true;
    }

    //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset($_SERVER['HTTP_VIA']))
    //找不到为flase,否则为true
    {
        return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
    }

    //判断手机发送的客户端标志,兼容性有待提高
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array(
            'nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile',
        );
        //从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    //协议法，因为有可能不准确，放到最后判断
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

/**
 * 栏目标签
 * @param $data
 */
function content_tag($action, $data){
    $content_tag_model = new \Lain\Phpcms\content_tag();
    //判断方法是否存在
    //
    return $content_tag_model->$action($data);
}

