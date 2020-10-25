<?php
namespace Core\Model;
use Core\Platform\Platform;
use Think\Model;

class Addon extends Model {
    protected $autoCheckFields = false;
    
    public static function run($params) {
        C('FRAME_ACTIVE', 'addons');
        $params['Addon'] = parse_name($params['Addon'], 1);
        $params['Entry'] = parse_name($params['Entry'], 1);
        $params['Controller'] = parse_name($params['Controller'], 1);
        $params['Action'] = parse_name($params['Action'], 1);
        $a = new Addon($params['Addon']);
        define('ADDON_NAME', $params['Addon']);
        define('ADDON_CURRENT_PATH', MB_ROOT . "addons/{$params['Addon']}/");
        C('ADDON_INSTANCE', $a);
        C('TMPL_PARSE_STRING.__ADDON_PUBLIC__', __SITE__ . "addons/{$params['Addon']}/static/");
        Addon::autoload();
        $class = "Addon\\{$params['Addon']}\\{$params['Entry']}\\Controller\\{$params['Controller']}Controller";
        if(class_exists($class)) {
            $instance = new $class($params, $a);
            $method = $params['Action'] . C('ACTION_SUFFIX');
            if(method_exists($instance, $method)) {
                call_user_func(array($instance, $method));
            } else {
                return error(-2, "访问的操作 {$params['Addon']}\\{$params['Entry']}\\{$params['Controller']}Controller\\{$params['Action']} 不存在.");
            }
        } else {
            return error(-1, "访问的控制器 {$params['Addon']}\\{$params['Entry']}\\{$params['Controller']}Controller 不存在.");
        }
    }
    public static function autoload() {
        spl_autoload_register(function($class){
            $pieces = explode('\\', $class, 2);
            if($pieces[0] == 'Addon' && !empty($pieces[1])) {
                $filename = MB_ROOT . 'addons/' . str_replace('\\', '/', $pieces[1]);
                $filename .= '.class.php';
                if(is_file($filename)) {
                    include $filename;
                }
            }
        });
    }
    
    const TYPE_APP      = 'app';
    const TYPE_ACTIVITY = 'activity';
    const TYPE_CRM      = 'crm';
    const TYPE_GAME     = 'game';
    const TYPE_TOOL     = 'tool';
    const TYPE_OTHER    = 'other';
    
    const ENTRY_BENCH   = 'bench';
    const ENTRY_CONTROL = 'control';
    const ENTRY_WANDER  = 'wander';
    const ENTRY_APP     = 'app';

    public static function types() {
        static $types;
        if(empty($types)) {
            $types['app'] = array(
                'name' => 'app',
                'title' => 'WebApp',
                'desc' => ''
            );
            $types['activity'] = array(
                'name' => 'activity',
                'title' => '营销及活动',
                'desc' => ''
            );
            $types['crm'] = array(
                'name' => 'crm',
                'title' => '客户关系',
                'desc' => ''
            );
            $types['game'] = array(
                'name' => 'game',
                'title' => '游戏',
                'desc' => ''
            );
            $types['tool'] = array(
                'name' => 'tool',
                'title' => '服务及工具',
                'desc' => ''
            );
            $types['other'] = array(
                'name' => 'other',
                'title' => '其他',
                'desc' => ''
            );
        }
        return $types;
    }
    
    public static function getAddons($type = '') {
        $condition = '';
        $pars = array();
        if(!empty($type)) {
            $condition = '`type`=:type';
            $pars[':type'] = $type;
        }

        $m = new Model();
        $addons = $m->table('__EX_ADDONS__')->where($condition)->bind($pars)->select();
        foreach($addons as &$addon) {
            $addon['icon'] = __SITE__ . "addons/{$addon['name']}/icon.png";
        }
        return $addons;
    }
    
    public static function getAddon($name, $fromDefineFile = false) {
        if($fromDefineFile) {
            $path = MB_ROOT . "addons/{$name}/define.xml";
            if(is_file($path)) {
                $xml = file_get_contents($path);
                $addon = self::parseDefineFile($xml);
                if(is_error($addon)) {
                    return $addon;
                }
            } else {
                return error(-1, '不存在这个扩展或者扩展定义文件不存在');
            }
        } else {
            $condition = '`name`=:name';
            $pars = array();
            $pars[':name'] = $name;
            $m = new Model();
            $addon = $m->table('__EX_ADDONS__')->where($condition)->bind($pars)->find();
            if(empty($addon)) {
                return error(-1, '不存在这个扩展');
            }
        }
        if(!empty($addon)) {
            $addon['icon'] = __SITE__ . "addons/{$addon['name']}/icon.png";
        }
        return $addon;
    }
    
    private static function parseDefineFile($xml) {
        $dom = new \DOMDocument();
        if($dom->loadXML($xml)) {
            if($dom->schemaValidate(MB_ROOT . "source/Conf/define.xsd")) {
                $xpath = new \DOMXpath($dom);
                $xpath->registerNamespace('mb', 'http://www.microbuilder.cn');
                $addon = array();
                $addon['title'] = $xpath->evaluate('string(//mb:addon/mb:title)');
                $addon['name'] = $xpath->evaluate('string(//mb:addon/mb:name)');
                $addon['version'] = $xpath->evaluate('string(//mb:addon/mb:version)');
                $addon['require'] = $xpath->evaluate('string(//mb:addon/mb:require)');
                $addon['type'] = $xpath->evaluate('string(//mb:addon/mb:type)');
                $addon['description'] = $xpath->evaluate('string(//mb:addon/mb:description)');
                $addon['author'] = $xpath->evaluate('string(//mb:addon/mb:author)');
                $addon['url'] = $xpath->evaluate('string(//mb:addon/mb:url)');
                return $addon;
            } else {
                $err = error_get_last();
                if($err['type'] == 2) {
                    return error(-2, '扩展定义文件格式错误, 详细信息: ' . $err['message']);
                }
                return error(-2, '扩展定义文件格式错误');
            }
        } else {
            return error(-2, '扩展定义文件格式错误');
        }
    }
    
    private $addon;

    function __construct($addon) {
        parent::__construct();
        if(is_array($addon)) {
            $this->addon = $addon;
        } else {
            $condition = '`name`=:name';
            $pars = array();
            $pars[':name'] = strval($addon);
            $addon = $this->table('__EX_ADDONS__')->where($condition)->bind($pars)->find();
            if(empty($addon)) {
                trigger_error('扩展不存在', E_USER_ERROR);
            }
            $this->addon = $addon;
        }
    }
    
    public function U($entry, $url='', $vars='') {
        $addon = parse_name($this->addon['name']);
        $url = "/{$entry}/extend/{$addon}/{$url}";
        return U($url, $vars);
    }

    /**
     * 获取当前扩展对象
     * @return mixed
     */
    public function getCurrentAddon() {
        return $this->addon;
    }

    /**
     * 为当前扩展注册关键字
     *
     * @param string $keyword 关键字
     * @param int $resp       响应内容, 大于0是响应内容, 等于0是动态调用
     * @param string $match   匹配方式
     * @param string $extra   附加保存数据
     * @param int $order      优先级
     * @param string $remark  备注
     * @param int $original    原始处理器标识, 修改关键字的时候请传入原始处理器标识, 为0代表新增
     * @return error|string   处理器标识
     */
    public function registerKeyword($keyword, $resp = 0, $match = Processor::MATCH_EQUAL, $extra = '', $order = 0, $remark = '', $original = 0) {
        $matchs = array(
            Processor::MATCH_CONTAINS,
            Processor::MATCH_EQUAL,
            Processor::MATCH_REGEX
        );
        $match = in_array($match, $matchs) ? $match : Processor::MATCH_EQUAL;
        
        $exists = false;
        if(!empty($original)) {
            $condition = '`from`=:from AND `id`=:id';
            $pars = array();
            $pars[':from'] = $this->addon['name'];
            $pars[':id'] = $original;
            $proc = $this->table('__RP_PROCESSORS__')->where($condition)->bind($pars)->find();
            if(!empty($proc)) {
                $exists = true;
            }
        }
        
        $rec = array();
        $rec['msg_type'] = Platform::MSG_TEXT;
        $rec['msg_match'] = $match;
        $rec['msg_content'] = $keyword;
        $rec['resp_forward'] = $resp;
        $rec['resp_extra'] = $extra;
        $rec['from'] = $this->addon['name'];
        $rec['remark'] = $remark;
        $rec['orderlist'] = $order;
        $rec['status'] = 1;
        
        if($exists) {
            $ret = $this->table('__RP_PROCESSORS__')->data($rec)->where("`id`='{$original}'")->save();
        } else {
            $ret = $this->table('__RP_PROCESSORS__')->data($rec)->add();
        }
        if(empty($ret)) {
            return error(-2, '保存处理器失败');
        }
        return $this->getLastInsID();
    }

    /**
     * 为当前扩展注册接管
     *
     * @param int $resp
     * @param string $extra
     * @param int $order
     * @param string $remark
     * @param int $original
     * @return error|string 成功返回处理器标识, 失败返回错误信息
     */
    public function registerTakeOver($resp = 0, $extra = '', $order = 0, $remark = '', $original = 0) {
        $condition = '`from`=:from AND `msg_match`=:match AND `order`=:order';
        $pars = array();
        $pars[':from'] = $this->addon['name'];
        $pars[':match'] = Processor::MATCH_TAKEOVER;
        $pars[':order'] = $order;
        $rec = $this->table('__RP_PROCESSORS__')->where($condition)->bind($pars)->find();
        if(!empty($rec)) {
            return error(-1, '这个优先级的接管操作已经定义, 请检查');
        }

        $exists = false;
        if(!empty($original)) {
            $condition = '`from`=:from AND `id`=:id';
            $pars = array();
            $pars[':from'] = $this->addon['name'];
            $pars[':id'] = $original;
            $proc = $this->table('__RP_PROCESSORS__')->where($condition)->bind($pars)->find();
            if(!empty($proc)) {
                $exists = true;
            }
        }

        $rec = array();
        $rec['msg_type'] = '';
        $rec['msg_match'] = Processor::MATCH_TAKEOVER;
        $rec['msg_content'] = '';
        $rec['resp_forward'] = $resp;
        $rec['resp_extra'] = $extra;
        $rec['from'] = $this->addon['name'];
        $rec['remark'] = $remark;
        $rec['orderlist'] = $order;
        $rec['status'] = 1;

        if($exists) {
            $ret = $this->table('__RP_PROCESSORS__')->data($rec)->where("`id`='{$original}'")->save();
        } else {
            $ret = $this->table('__RP_PROCESSORS__')->data($rec)->add();
        }
        if(empty($ret)) {
            return error(-2, '注册项注册失败');
        } else {
            return $this->getLastInsID();
        }
    }

    /**
     * 为当前扩展注册特殊类型
     *
     * @param string $msgType 消息类型
     * @param string $params  消息参数
     * @param int $resp
     * @param string $extra
     * @param int $order
     * @param string $remark
     * @param int $original
     * @return error|string 成功返回处理器标识, 失败返回错误信息
     */
    public function registerType($msgType, $params = '', $resp = 0, $extra = '', $order = 0, $remark = '', $original = 0) {
        $exists = false;
        if(!empty($original)) {
            $condition = '`from`=:from AND `id`=:id';
            $pars = array();
            $pars[':from'] = $this->addon['name'];
            $pars[':id'] = $original;
            $proc = $this->table('__RP_PROCESSORS__')->where($condition)->bind($pars)->find();
            if(!empty($proc)) {
                $exists = true;
            }
        }
        
        $rec = array();
        $rec['msg_type'] = $msgType;
        $rec['msg_match'] = '';
        $rec['msg_content'] = $params;
        $rec['resp_forward'] = $resp;
        $rec['resp_extra'] = $extra;
        $rec['from'] = $this->addon['name'];
        $rec['remark'] = $remark;
        $rec['orderlist'] = $order;
        $rec['status'] = 1;

        if($exists) {
            $ret = $this->table('__RP_PROCESSORS__')->data($rec)->where("`id`='{$original}'")->save();
        } else {
            $ret = $this->table('__RP_PROCESSORS__')->data($rec)->add();
        }
        if(empty($ret)) {
            return error(-2, '注册项注册失败');
        } else {
            return $this->getLastInsID();
        }
    }

    /**
     * 清除注册的处理器
     * @param string $id
     * @return bool
     */
    public function unRegister($id) {
        $id = intval($id);
        $ret = $this->table('__RP_PROCESSORS__')->where("`id`='{$id}'")->delete();
        if(empty($ret)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 获取当前扩展的菜单
     * @param string $type 菜单入口位置
     * @return mixed
     */
    public function getEntries($type) {
        $condition = '`addon`=:addon AND `type`=:type';
        $pars = array();
        $pars[':addon'] = $this->addon['name'];
        $pars[':type'] = $type;

        $entries = $this->table('__EX_ADDON_ENTRIES__')->where($condition)->bind($pars)->order('`id`')->select();
        if(!empty($entries)) {
            foreach($entries as &$entry) {
                if(stripos($entry['url'], 'http://') === 0 || stripos($entry['url'], 'https://')) {
                } else {
                    $entry['url'] = $this->U($type, $entry['url']);
                }
            }
        }
        return $entries;
    }

    private function pasteEntry($type, $title, $url, $description) {
        $condition = '`addon`=:addon AND `type`=:type AND `title`=:title';
        $pars = array();
        $pars[':addon'] = $this->addon['name'];
        $pars[':type'] = $type;
        $pars[':title'] = $title;

        $entry = $this->table('__EX_ADDON_ENTRIES__')->where($condition)->bind($pars)->find();
        if(!empty($entry)) {
            return error(-1, '这个名称已经存在, 不能使用');
        } else {
            $rec = array();
            $rec['addon'] = $this->addon['name'];
            $rec['type'] = $type;
            $rec['title'] = $title;
            $rec['url'] = $url;
            $rec['description'] = $description;
            $ret = $this->table('__EX_ADDON_ENTRIES__')->data($rec)->add();
            if(empty($ret)) {
                return error(-2, '注册菜单入口失败');
            } else {
                return $this->getLastInsID();
            }
        }
    }

    /**
     * 为当前模块增加工作台菜单入口
     *
     * @param string $title 菜单名称
     * @param string $url   菜单URL
     * @param string $description 说明信息
     * @return error|string 成功返回编号, 失败返回error
     */
    public function pasteBenchEntry($title, $url, $description = '') {
        return $this->pasteEntry(self::ENTRY_BENCH, $title, $url, $description);
    }

    /**
     * 为当前模块增加控制中心菜单入口
     *
     * @param string $title 菜单名称
     * @param string $url   菜单URL
     * @param string $description 说明信息
     * @return error|string 成功返回编号, 失败返回error
     */
    public function pasteControlEntry($title, $url, $description = '') {
        return $this->pasteEntry(self::ENTRY_CONTROL, $title, $url, $description);
    }

    /**
     * 为当前模块增加手机页面访问入口
     *
     * @param string $title 入口名称
     * @param string $url   入口URL
     * @param string $description 说明信息
     * @return error|string 成功返回编号, 失败返回error
     */
    public function pasteAppEntry($title, $url, $description = '') {
        return $this->pasteEntry(self::ENTRY_APP, $title, $url, $description);
    }

    /**
     * 为当前模块增加游客页面访问入口
     *
     * @param string $title 入口名称
     * @param string $url   入口URL
     * @param string $description 说明信息
     * @return error|string 成功返回编号, 失败返回error
     */
    public function pasteWanderEntry($title, $url, $description = '') {
        return $this->pasteEntry(self::ENTRY_WANDER, $title, $url, $description);
    }
    
    public function unPaste($id) {
        $id = intval($id);
        $ret = $this->table('__EX_ADDON_ENTRIES__')->where("`id`='{$id}'")->delete();
        if(empty($ret)) {
            return false;
        } else {
            return true;
        }
    }

    public function pay($order) {
        $order['addon'] = $this->addon['name'];
        $m = new Pay();
        $ret = $m->saveLog($order);
        if(is_error($ret)) {
            return $ret;
        }
        return U('cash/desk', array('p' => $ret)) . '?showwxpaytitle=1';
    }
}
