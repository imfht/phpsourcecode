<?php
/**
 * 扩展功能访问入口
 */
namespace Bench\Controller;
use Core\Model\Addon;
use Think\Controller;

class ExtendController extends Controller {
    public function _empty() {
        $pieces = explode('/', __INFO__, 6);
        if(count($pieces) >= 5 && $pieces[0] == 'bench' && $pieces[1] == 'extend') {
            $params = array();
            list($params['Entry'], $action, $params['Addon'], $params['Controller'], $params['Action'], $params['Stuff']) = $pieces;
            unset($_GET[$params['Controller']]);
            $ret = Addon::run($params);
            if(is_error($ret)) {
                $this->error($ret['message']);
            }
            return;
        }
        
        $name = parse_name(ACTION_NAME, 1);
        $a = new Addon($name);
        $entries = $a->getEntries(Addon::ENTRY_BENCH);
        
        $this->assign('entity', $a->getCurrentAddon());
        $this->assign('entries', $entries);
        C('FRAME_ACTIVE', 'extend');
        C('FRAME_CURRENT', U('bench/extend/' . ACTION_NAME));
        $this->display('Extend/addon');
    }
}