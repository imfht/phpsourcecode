<?php
/**
 * 扩展功能访问入口
 */
namespace App\Controller;
use Core\Model\Addon;
use Think\Controller;

class ExtendController extends Controller {
    public function _empty() {
        $pieces = explode('/', __INFO__, 5);
        if(count($pieces) >= 4 && $pieces[0] == 'extend') {
            $params = array();
            $params['Entry'] = 'App';
            list($action, $params['Addon'], $params['Controller'], $params['Action'], $params['Stuff']) = $pieces;
            unset($_GET[$params['Controller']]);
            $ret = Addon::run($params);
            if(is_error($ret)) {
                $this->error($ret['message']);
            }
            return;
        }
    }
}