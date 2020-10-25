<?php
/**
 * 博客模板
 *
 * @author chengxuan <i@chengxuan.li>
 */
namespace Model\Theme;
class Main extends \Model\Abs {
    
    /**
     * 表名
     *
     * @var string
     */
    protected static $_table = 'theme_main';
    
    /**
     * 每个人最多多少个模板
     * 
     * @var int
     */
    const TOTAL_LIMIT = 100;
    
    /**
     * 获取指定用户的主题 
     * 
     * @param int     $uid          要操作的UID
     * @param boolean $show_default 是否获取默认模板
     * 
     * @return \array
     */
    static public function userTpls($uid = false, $show_default = true) {
        $uid || $uid = \Model\User::validateLogin();
        $where_uid = $show_default ? [$uid, 0] : $uid;
        $db = self::db()->wAnd(['user_id'=>$where_uid])->order(['user_id' => SORT_ASC, 'id' => SORT_DESC]);
        return $db->fetchAll();
    }
    
    /**
     * 统计用户的主题数
     * 
     * @param string $uid 要操作的UID
     * 
     * @return \mixed
     */
    static public function countUserTpl($uid = false) {
        $uid || $uid = \Model\User::validateAuth($uid);
        return self::db()->wAnd(['user_id'=>$uid])->fetchOne('COUNT(*)');
    }
    
    /**
     * 创建一个模板
     * 
     * @param int    $alias_id 关联模板ID
     * @param string $name     模板名称
     * @param int    $user_id  用户UID（默认为当前用户UID）
     * @param string $pic      模板图片
     * 
     * @throws \Exception\Msg
     * 
     * @return int
     */
    static public function create($alias_id, $name, $uid = '', $pic = '') {
        $uid || $uid = \Model\User::validateLogin();
        
        //判断名称是否为空
        if(!$name) {
            throw new \Exception\Msg('请输入新模板名称');
        }
        
        //获取旧模板数据
        if($alias_id) {
            $data = self::show($alias_id);
            if(empty($data)) {
                throw new \Exception\Msg('源主题不存在');
            }
            
            //已经有alias_id的，并且当前模板没数据，使用原来的，避免产生过多的链
            if($data['alias_id']) {
                $resource = Resource::showByTpl($data['id']);
                $resource || $alias_id = $data['alias_id'];
            }
        }
        
        $total_number = self::countUserTpl($uid);
        if($total_number >= self::TOTAL_LIMIT) {
            throw new \Exception\Msg(sprintf(_('主题总数不能超过%s个'), $total_number));
        }
        
        $db = self::db();
        if($db->wAnd(['user_id' => $uid, 'name' => $name])->fetchRow()) {
            throw new \Exception\Msg(sprintf(_('主题名称已存在:%s'), $name));
        }
        
        $db->insert(array(
            'alias_id'    => $alias_id,
            'name'        => $name,
            'user_id'     => $uid,
            'pic'         => $pic, 
            'create_time' => date('Y-m-d H:i:s'),
        ));
        $id = $db->lastId();

        return $id;
    }

    
    /**
     * 删除一个模板
     * 
     * @param int    $id  主键ID
     * @param string $uid 用户UID
     * 
     * @return \int
     */
    static public function delete($id, $uid = false) {
        $uid || $uid = \Model\User::validateAuth($uid);
        return self::db()->wAnd(['id' => $id, 'user_id' => $uid])->delete(true);
    }
    
}
