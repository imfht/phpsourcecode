<?php
/**
 * 发布任务管理器
 *
 * @package Model
 * @author chengxuan <i@chengxuan.li>
 */
namespace Model\Publish;
class Task extends \Model\Abs {


    /**
     * 继承后设置表名可直接通过self::db获取数据库操作对象
     *
     * @var string
     */
    protected static $_table = 'publish_task';
    
    /**
     * 主键ID
     *
     * @var string
     */
    protected static $_primary_key = 'id';

    /**
     * 发布首页
     *
     * @var integer
     */
    const TYPE_HOME = 1;

    /**
     * 发布分类下的文章列表
     *
     * @var integer
     */
    const TYPE_CATEGORY_ARTICLE_LIST = 2;
    
    /**
     * 发布侧边栏
     * 
     * @var integer
     */
    const TYPE_SIDEBAR = 3;
    
    /**
     * 最多发布页数
     * 
     * @var int
     */
    const PUBLISH_PAGE_MAX = 20;
    
    /**
     * 创建发布任务
     * 
     * @param int   $type     类别
     * @param array $metadata 元数据
     * 
     * @return \mixed
     */
    static public function create($type, $connection_id, array $metadata = array()) {
        $uid = \Model\User::validateLogin();
        $data = array(
            'uid'           => $uid,
            'type'         => $type,
            'connection_id' => $connection_id,
            'metadata' => \Comm\Json::encode($metadata),
            'update_time' => date('Y-m-d H:i:s'),
        );
        return self::db()->insert($data);
    }
    
    /**
     * 批量创建发布任务
     * 
     * @param array $datas
     * 
     * @throws \Exception\Msg
     * 
     * @return \boolean
     */
    static public function createBatch(array $datas) {
        $uid = \Model\User::validateLogin();
        $update_time = date('Y-m-d H:i:s');
        $fields = array('uid', 'type', 'connection_id', 'metadata', 'update_time');
        
        $bath_datas = array();
        foreach($datas as $value) {
            if(empty($value['type'])) {
                throw new \Exception\Msg('类型错误');
            }
            $connection_id = isset($value['connection_id']) ? $value['connection_id'] : 0;
            $metadata = isset($value['metadata']) ? \Comm\Json::encode($value['metadata']) : '{}';
            $bath_datas[] = array(
                $uid, $value['type'], $connection_id, $metadata, $update_time,
            );
        }
        
        return self::db()->insertBatch($fields, $bath_datas, true);
    }
    
    /**
     * 根据用户获取一堆数据
     * 
     * @param int $uid   用户UID
     * @param int $limit 限制获取多少条
     */
    static public function showListByUser($uid, $limit) {
        $db = self::db()->wAnd(['uid' => $uid])->order('id', SORT_ASC);
        $result = $db->limit($limit)->fetchAll();
        foreach($result as $key => $value) {
            $metadata = $value['metadata'] ? json_decode($value['metadata'], true) : array();
            $result[$key]['metadata'] = $metadata;
        }
        return $result;
    }
    
    /**
     * 执行发布任务
     * 
     * @throws \Exception\Msg
     * 
     * @return \mixed true执行完成，array需要继续执行
     */
    static public function execute() {
        $uid = \Model\User::validateLogin();
        $tasks = self::showListByUser($uid, 2);
        $tasks_count = count($tasks);
        if(empty($tasks)) {
            throw new \Exception\Msg(_('发布任务为空'));
        }
        $task = reset($tasks);
        unset($tasks);
        
        switch($task['type']) {
            //首页
            case self::TYPE_HOME:
                $execute_result = self::_executeHome($task);
                break;
            
            //分类文章列表
            case self::TYPE_CATEGORY_ARTICLE_LIST :
                $execute_result = self::_executeCategoryArticleList($task);
                break;
            
            //发布侧边栏
            case self::TYPE_SIDEBAR :
                \Model\Publish::sidebar();
                $execute_result = true;
                break;
                
            //其它情况，异常
            default :
                self::destory($task['id']);
                throw new \Exception\Msg(_('发布任务异常')); 
        }
        
        
        if($execute_result === true) {
            //彻底完成了
            self::destory($task['id']);
        } elseif (is_array($execute_result)) {
            //还需要继续执行
            $update_data = array(
                'metadata'    => \Comm\Json::encode($execute_result['metadata']),
                'update_time' => date('Y-m-d H:i:s'),
            );
            self::db()->wAnd([self::$_primary_key => $task['id']])->upadte($update_data);
        } else {
            //异常
            throw new \Exception\Msg(_('执行任务异常'));
        }
        
        $result['finished'] = $tasks_count <=1 && $execute_result === true;
        $result['response'] = $execute_result;
        $result['task'] = $task;
        return $result;
    }
    
    /**
     * 执行分类下文章列表发布任务
     * 
     * @param array $task
     * 
     * @return \mixed true执行完成，array需要继续执行
     */
    static protected function _executeCategoryArticleList(array $task) {

        $category_id = $task['connection_id'];
        $metadata = $task['metadata'];
        $since_id = empty($metadata['since_id']) ? PHP_INT_MAX : $metadata['since_id'];
        $p = empty($metadata['p']) ? 0 : $metadata['p'];
        ++$p;
        
        //超过了最大发布页数
        if($p > self::PUBLISH_PAGE_MAX) {
            return true;
        }
        
        //处理分页
        $blog = \Model\Blog::show();
        $limit = isset($blog['data']['page_count']) ? $blog['data']['page_count'] : 50;
        $total = \Model\Counter\Article::get($category_id);
        $pager = new \Comm\Pager($total, $limit, $p);
        
        $category = \Model\Category::show($category_id);
        $articles = \Model\Article::showByCategorySince($category_id, $since_id, $limit);
        if($articles) {
            //发布内容
            \Model\Publish::categoryArticleList($category, $articles, $pager, $blog);
            
            //更新元数据
            $end = end($articles);
            $metadata['since_id'] = $end['id'];
            $metadata['p'] = $p;
            $result = array('metadata' => $metadata);
        } else {
            $result = true;
        }
        
        return $result;
    }
    
    /**
     * 执行首页发布任务
     *
     * @param array $task
     *
     * @return \mixed true执行完成，array需要继续执行
     */
    static protected function _executeHome(array $task) {
        $blog = \Model\Blog::show($task['uid']);
        $limit = isset($blog['data']['page_count']) ? $blog['data']['page_count'] : 50;
        
        $metadata = $task['metadata'];
        $p = empty($metadata['p']) ? 1 : $metadata['p'];
        
        
        //超过了最大发布页数
        if($p > self::PUBLISH_PAGE_MAX) {
            return true;
        }
        
        //获取总数
        $total = \Model\Counter\Article::get(0);
        $pager = new \Comm\Pager($total, $limit, $p);
        $articles = \Model\Article::showUserList($pager);
        
        if(!empty($articles['result'])) {
            //渲染模板，发布至GITHUB
            \Model\Publish::home($articles, $pager, $blog);
        
            //更新元数据
            $metadata['p'] = ++$p;
            $result = array('metadata' => $metadata);
        } else {
            $result = true;
        }
        
        return $result;
    }
}
