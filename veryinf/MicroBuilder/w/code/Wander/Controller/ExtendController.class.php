<?php
/**
 * 管理中心欢迎页
 */
namespace Wander\Controller;
use Core\Model\Addon;
use Think\Controller;
use Think\Model;

class ExtendController extends Controller {
    public function _empty() {
        $pieces = explode('/', __INFO__, 6);
        if(count($pieces) >= 5 && $pieces[0] == 'wander' && $pieces[1] == 'extend') {
            $params = array();
            list($params['Entry'], $action, $params['Addon'], $params['Controller'], $params['Action'], $params['Stuff']) = $pieces;
            unset($_GET[$params['Controller']]);
            $ret = Addon::run($params);
            if(is_error($ret)) {
                $this->error($ret['message']);
            }
            return;
        }
        $this->error('访问错误');
    }
}