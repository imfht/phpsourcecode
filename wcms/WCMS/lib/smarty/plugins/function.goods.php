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
static $buyService;
$data=array();
function smarty_function_goods ($params, $template)
{
    global $buyService;
    global $data;
    $num = isset($params['num']) ? $params['num'] : 10;

    if ($buyService == null) {
        $buyService = new BuyService();
    }

    $data=$buyService->getRecommendGoodsByCid($params['cid'], $num);
    $template->assign($params['assign'],$data);
}

?>
