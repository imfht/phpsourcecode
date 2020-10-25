<?php

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsFunction
 */
/**
 * Smarty {counter} function plugin
 *
 * Type: function<br>
 * Name: counter<br>
 * Purpose: print out a counter value
 *
 * @author Monte Ohrt <monte at ohrt dot com>
 * @link http://www.smarty.net/manual/en/language.function.counter.php {counter}
 *       (Smarty online manual)
 * @param array $params
 *            parameters
 * @param Smarty_Internal_Template $template
 *            template object
 * @return string null
 */
static $newsService;
static $data = array();

function smarty_function_content ($params, $template)
{
    global $newsService;
    global $data;
    // 获取制定分类的内容 不支持扩展字段
    if (isset($params['id'])) {
        
        if ($newsService == null) {
            $newsService = new NewsService(1, 1);
        }
        
        $cache = new CacheService();
        $mark = $params['id'];
        if ($cache->isCache($mark)) {
            $con['content'] = unserialize($cache->getCache($mark));
        } else {
            $con = $newsService->getCon($params['id']);
            $cache->setCache(serialize($con['content']), $mark);
        }
        
        $template->assign($params['assign'], $con['content']);
    }
}

?>