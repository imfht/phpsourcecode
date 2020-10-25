<?php
/**
 * 管理中心欢迎页
 */
namespace Control\Controller;
use Core\Model\Addon;
use Think\Controller;
use Think\Model;

class ExtendController extends Controller {
    public function _empty() {
        $pieces = explode('/', __INFO__, 6);
        if(count($pieces) >= 5 && $pieces[0] == 'control' && $pieces[1] == 'extend') {
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
        $entries = $a->getEntries(Addon::ENTRY_CONTROL);

        $this->assign('entity', $a->getCurrentAddon());
        $this->assign('entries', $entries);
        C('FRAME_ACTIVE', 'extend');
        C('FRAME_CURRENT', U('control/extend/' . ACTION_NAME));
        $this->display('Extend/addon');
    }

    public function listAction() {
        $types = Addon::types();
        foreach($types as &$type) {
            $type['entities'] = Addon::getAddons($type['name']);
        }
        
        $t = I('get.t');
        if(empty($t)) {
            $t = Addon::TYPE_APP;
        }
        $this->assign('t', $t);
        $this->assign('types', $types);
        C('FRAME_ACTIVE', 'extend');
        C('FRAME_CURRENT', U('control/extend/list'));
        $this->display();
    }
    
    public function installAction() {
        $a = I('get.addon');
        if(!empty($a)) {
            $this->doInstall($a);
            exit;
        }
        $type = I('get.t') == 'nocompat' ? 'nocompat' : 'compat';
        $extends = Addon::getAddons();
        $names = coll_neaten($extends, 'name');
        $addons = array();
        $path = MB_ROOT . 'addons/';
        if (is_dir($path)) {
            if ($handle = opendir($path)) {
                while (false !== ($addonpath = readdir($handle))) {
                    if($addonpath != '.' && $addonpath != '..' && !in_array($addonpath, $names)) {
                        $addons[] = $addonpath;
                    }
                }
            }
        }
        $entities = array();
        if(!empty($addons)) {
            foreach($addons as $addon) {
                $define = Addon::getAddon($addon, true);
                if(is_error($define)) {
                    $define['name'] = $addon;
                    $entities['nocompat'][] = $define;
                } else {
                    $entities['compat'][] = $define;
                }
            }
        }

        $this->assign('types', Addon::types());
        $this->assign('type', $type);
        $this->assign('entities', $entities);
        C('FRAME_ACTIVE', 'extend');
        C('FRAME_CURRENT', U('control/extend/install'));
        $this->display();
    }
    
    private function doInstall($addon) {
        $define = Addon::getAddon($addon, true);
        if(is_error($define)) {
            $this->error($define['message']);
        } else {
            $rec = coll_elements(array('name', 'type', 'title', 'version', 'description', 'author', 'url'), $define, '');
            $m = new Model();
            $ret = $m->table('__EX_ADDONS__')->data($rec)->add();
            if(!empty($ret)) {
                Addon::autoload();
                $class = "Addon\\{$addon}\\Api\\Application";
                if(class_exists($class)) {
                    $instance = new $class();
                    if(!empty($instance)) {
                        $instance->addon = new Addon($addon);
                        if(method_exists($instance, 'install')) {
                            $ret = $instance->install();
                        }
                    }
                }
                if(is_error($ret)) {
                    $this->error('扩展未能安装成功, 请完全卸载后重试, 或者联系扩展开发商. 扩展提供的详细错误信息为: ' . $ret['message']);
                } else {
                    $this->success('扩展安装成功');
                }
            }
            exit;
        }
    }
    
    public function uninstallAction() {
        $a = I('get.addon');
        if(empty($a)) {
            $this->error('访问错误');
        }
        $addon = new Addon($a);
        Addon::autoload();
        $class = "Addon\\{$a}\\Api\\Application";
        if(class_exists($class)) {
            $instance = new $class();
            if(!empty($instance)) {
                $instance->addon = $addon;
                if(method_exists($instance, 'install')) {
                    $instance->uninstall();
                }
            }
        }
        $condition = '`name`=:name';
        $pars = array();
        $pars[':name'] = $a;
        $m = new Model();
        $m->table('__EX_ADDONS__')->where($condition)->bind($pars)->delete();
        $m->table('__EX_ADDON_ENTRIES__')->where('`addon`=:name')->bind($pars)->delete();
        $m->table('__RP_PROCESSORS__')->where('`from`=:name')->bind($pars)->delete();
        $m->table('__RP_REPLIES__')->where('`from`=:name')->bind($pars)->delete();
        $this->success('扩展卸载成功');
    }
}