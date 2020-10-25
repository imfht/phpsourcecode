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
function smarty_function_list ($params, $template)
{
    global $newsService;
    global $data;
    $limitNum = isset($params['num']) ? $params['num'] : 10;
    $flag = isset($params['flag']) ? $params['flag'] : 0;

    if ($newsService == null) {
        $newsService = new NewsService(1, 1);
    }

    $where = array(
            'cid' => $params['cid'],
            'p' => 1,
            'num' => $limitNum,
            'flag' => $flag
    );

    $mark = $params['cid'] . '-' . $flag;
    $cache = new CacheService();
    if ($cache->isCache($mark)) {
        $data[$mark] =unserialize($cache->getCache($mark));
    } else {
        $news = $newsService->listing($where);
        $data[$mark] = $news['newslist'];
        $cache->setCache(serialize($news['newslist']), $mark);
    }

    $template->assign($params['assign'], $data[$mark]);
}

?>
