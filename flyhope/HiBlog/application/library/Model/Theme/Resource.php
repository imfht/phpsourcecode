<?php
/**
 * 博客模板资源
 *
 * @author chengxuan <i@chengxuan.li>
 */
namespace Model\Theme;
class Resource extends \Model\Abs {

    /**
     * 表名
     *
     * @var string
     */
    protected static $_table = 'theme_resource';
    
    /**
     * 检查模板是否是保护的不允许删除的
     * 
     * @var string
     */
    protected static $_protected_resource = ',article,sidebar,home,article-list,';

    /**
     * 每个主题下最多的模板数
     * 
     * @var int
     */
    const RESOURCE_COUNT_MAX = 100;
    
    /**
     * 通过TPL_ID获取模板资源
     * 
     * @param int    $tpl_id 模板ID
     * 
     * @return array
     */
    static public function showByTpl($tpl_id) {
        $where = ['tpl_id' => $tpl_id];
        return self::db()->wAnd($where)->fetchAll();
    }
    
    /**
     * 根据模板展示资源内容
     * 
     * @param \array $theme
     * 
     * @return \array
     */
    static public function showByTheme(array $theme) {
        $result = self::showByTpl($theme['id']);
        if($result) {
            $locked = false;
        } else {
            $result = self::showByTpl($theme['alias_id']);
            $locked = true;
        }
        $response = array(
            'resource' => \Comm\Arr::hashmap($result, 'resource_name'),
            'locked'   => $locked,
        );
        return $response;
    }
    
    /**
     * 通过模板ID和名称获取资源
     * 
     * @param int    $tpl_id 模板ID
     * @param string $name   资源名称
     * 
     * @return array
     */
    static public function showByName($tpl_id, $name) {
        $where = ['tpl_id' => $tpl_id, 'resource_name' => $name];
        return self::db()->wAnd($where)->fetchRow();
    }
    
    /**
     * 解锁复制模板资源内容
     * 
     * @param int    $to_tpl_id   目标模板ID
     * @param string $uid         当前登录用户
     * 
     * @return int
     */
    static public function unlock($to_tpl_id, $uid = false) {
        $to_tpl_id = (int)$to_tpl_id;
        $theme = self::validateAuth($to_tpl_id, $uid);
        $update_time = date('Y-m-d H:i:s');
        
        $table = self::db()->showTable();
        $mysql = new \Comm\Db\Mysql();
        $sql = "INSERT IGNORE INTO {$table} (tpl_id, resource_name, content, update_time) SELECT {$to_tpl_id}, resource_name, content, '{$update_time}' FROM {$table} WHERE tpl_id = ?";
        return $mysql->executeSql($sql, [$theme['alias_id']]);
    }
    
    /**
     * 更新一条模板资源数据
     * 
     * @param int    $tpl_id        模板ID
     * @param string $content       模板内容
     * @param int    $uid           当前登录用户UID
     * 
     * @param string $uid
     */
    static public function update($id, $content, $uid = false) {
        $resource = self::show($id);
        if(!$resource) {
            throw new \Exception\Msg('指定模板不存在');
        }
        self::validateAuth($resource['tpl_id'], $uid);
        $data = array(
            'content'       => $content,
            'update_time'   => date('Y-m-d H:i:s'),
        );
        return self::db()->wAnd(['id'=>$id])->upadte($data);
    }
    
    /**
     * 增加一个主题的模板资源
     * 
     * @param int    $tpl_id        模板ID
     * @param string $resource_name 模板资源名称
     * 
     * @throws \Exception\Msg
     * 
     * @return \boolean
     */
    static public function addResource($tpl_id, $resource_name) {
        $theme_main = self::validateAuth($tpl_id);
        
        if(!$resource_name) {
            throw new \Exception\Msg(_('模板资源名称不能为空'));
        }
        
        $resources = self::showByTpl($tpl_id);
        if(count($resources) > self::RESOURCE_COUNT_MAX) {
            throw new \Exception\Msg(sprintf(_('每个主题下最多允许模板%u个'), self::RESOURCE_COUNT_MAX));
        }
        
        $data = array(
            'tpl_id'        => $tpl_id,
            'resource_name' => $resource_name,
            'content'       => '',
            'update_time'   => date('Y-m-d H:i:s'),
        );
        return self::db()->insert($data);
    }
    
    /**
     * 检查指定权限用户有无操作权限
     * 
     * @param int $tpl_id 模板ID
     * @param int $uid    当前登录用户UID
     * 
     * @return array TPL-Main数据
     */
    static public function validateAuth($tpl_id, $uid = false) {
        $uid || $uid = \Model\User::validateLogin();
        $tpl_main = Main::show($tpl_id);
        $validate_uid = isset($tpl_main['user_id']) ? $tpl_main['user_id'] : 0;
        \Model\User::validateAuth($validate_uid, $uid);
        return $tpl_main;
    }
    
    /**
     * 判断给定的资源名称是不是保护的
     * 
     * @param string $resource_name 资源名称
     * 
     * @return void
     */
    static public function isProtected($resource_name) {
        return strpos(self::$_protected_resource, ",{$resource_name},") !== false;
    }
    
    /**
     * 删除一个模板资源
     * 
     * @param int $id
     * @throws \Exception\Msg
     * 
     * @return \boolean
     */
    static public function destroy($id) {
        $uid = \Model\User::validateLogin();
        $resource = self::show($id);
        if(!$resource) {
            throw new \Exception\Msg('指定模板不存在');
        }
        self::validateAuth($resource['tpl_id'], $uid);
        
        return parent::destory($id);
    }
    
    
}
