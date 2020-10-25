<?php
namespace app\helpers;

use yii\base\NotSupportedException;
use yii\helpers\Html;
use app\filters\AccessFilter;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\models\User;
use app\models\ProjectMember;
use app\models\Project;

/**
 * 项目工具类
 * @author dungang
 *
 */
class MiscHelper
{

    const SWITCH_PROJECT_ID = '_project_id';
    const SWITCH_PROJECTS = '_joined_projects';
    
    public static function isAdmin(){
        return \Yii::$app->user->identity->is_admin;
    }
    
    
    public static function goBackButton(){
        return Html::button('<i class="glyphicon glyphicon-chevron-left"></i> 返回',['class' => 'btn btn-warning','onclick'=>'window.history.back()']);
    }
    
    public static function getProjectId(){
        return \Yii::$app->session->get(self::SWITCH_PROJECT_ID);
    }
    
    public static function getProject($project_id=null){
        $id = $project_id?:self::getProjectId();
        //echo $id;
        $projects = self::loadUserJoinedProjects();
        //print_r($projects[$id]);die;
        if($id && isset($projects[$id])){
            return $projects[$id];
        }
        return null;
    }

    public static function initUserDefultProject(){
        $session = \Yii::$app->session;
        $project_id = $session->get(self::SWITCH_PROJECT_ID);
        $projects = self::loadUserJoinedProjects();
        //$projects = $session->get(self::SWITCH_PROJECTS);
        //如果session中没有存储用户参与的项目
        // if( $projects== null) {
        //     $projects = $this->loadUserJoinedProjects();
        //     $session->set(self::SWITCH_PROJECTS,$projects);
        // }
        //如果当前的会话项目id为空 或者 默认的项目id不存在会员参与的项目中
        //默认读取第一个作为会话项目
        if($project_id == null || isset($projects[$project_id])==false) {
            $project_ids = array_keys($projects);
            $project_id =  array_shift($project_ids);
            $session->set(self::SWITCH_PROJECT_ID,$project_id);
        }
        return $project_id;
    }

    
    /**
     * 加载用户的项目
     * id:ProjectId
     * name: ProjectName
     * position: user position In project
     */
    public static function loadUserJoinedProjects()
    {
        // $key = 'projects:user:' . \Yii::$app->user->id;
        // return \Yii::$app->cache->getOrSet($key,function(){
            $q = Project::loadJoinedProjectsQuery('id,name,position');
            return $q->indexBy('id')->all();
        //});
    }
    
    public static function betweenDayWithTimestamp($field,$date) {
        if($date) {
            $start = strtotime($date);
            $end  = 24 * 3600 + $start;
            return ['between',$field,$start, $end];
        }
        return [];
    }
    
    /**
     * 根据项目id查询股东的id和名称的信息
     * @param int $project_id
     * @return array|array[]
     */
    public static function orgMemberNames($project_id){
        $query  = new Query();
        $membs =  $query->select('id,name')->from(['m'=>User::tableName(),'om'=>ProjectMember::tableName()])
        ->where('m.id=om.mem_id')
        ->andWhere(['om.project_id'=>$project_id])->all();
        return ArrayHelper::map($membs, 'id', 'name');
    }
    
    public static function memberNames($ids) {
        $query  = new Query();
        $membs =  $query->select('id,name')->from(User::tableName())
        ->where(['id'=>$ids])->all();
        return ArrayHelper::map($membs, 'id', 'name');
    }
    
    /**
     * 用户加入的项目
     * @return NULL|string[][][]|mixed[][][]
     */
    public static function switchProjectMenuItems(){
        $rootItem = null;
        $switchProjects = [];
        $_joined_projects = self::loadUserJoinedProjects();
        $_project_id = \Yii::$app->session->get(self::SWITCH_PROJECT_ID);
        
        if(isset($_joined_projects) && is_array($_joined_projects)) {
            foreach($_joined_projects as $project_id => $org) {
                if($project_id == $_project_id) {
                    $rootItem = [
                        'label'=>$org['name'],
                        'url'=>['/space','id'=>$org['id']]
                    ];
                } 
                //else {
                    $switchProjects[] = [
                        'label'=>$org['name'],
                        'url'=>['/switch-project','id'=>$org['id']]
                    ];
                //}
            }
            if($rootItem == null) {
                $rootItem = array_shift($switchProjects);
            }
            $rootItem['items'] = $switchProjects;
        }
        return $rootItem;
    }

    
    /**
     *
     * @param string $text
     * @param array $url
     * @param array $options
     * @return string
     */
    public static function orgLinkButton($text, $url, $options)
    {
        if (is_array($url)) {
            return self::hasProjectPermission($url[0]) ? Html::a($text, $url, $options) : '';
        }
        throw new NotSupportedException('argment url be must an array!');
    }
    
    /**
     *
     * @param array $action
     * @param string $content
     * @param array $options
     * @throws NotSupportedException
     * @return string
     */
    public static function orgSubmitButton( $action,  $content = '',  $options = [])
    {
        if (is_array($action)) {
            return self::hasProjectPermission($action[0]) ? Html::SubmitButton($content, $options) : '';
        }
        throw new NotSupportedException('argment url be must an array!');
    }

    
    public static  function hasProjectPermission($route)
    {
        /* @var AccessFilter $aclFilter */
        $aclFilter = \Yii::$app->controller->getBehavior(AccessFilter::ID);
        return $aclFilter->hasPermission($route);
    }
    
      /**
     * 以关联数组形式的返回参数值
     *
     * @param string $text
     * @return mixed[]
     */
    public static function parseText2Assoc($text)
    {
        $assoc = [];
        $lines = \explode("\n", trim($text));
        foreach ($lines as $line) {
            if (($line = trim($line, '\n\r\s'))) {
                $kv = explode(':', $line);
                $assoc[$kv[0]] = $kv[1];
            }
        }
        return $assoc;
    }

        /**
     * 把返回的数据集转换成Tree
     *
     * @param array $list
     *            要转换的数据集
     * @param string $pk
     *            主键
     * @param string $pid
     *            parent标记字段
     * @param string $child
     *            子节点字段
     * @param
     *            int
     * @return array
     * @author gang.dun <dungang@huluwa.cc>
     */
    public static function listToTree($list, $pk = 'id', $pid = 'pid', $child = 'items', $root = 0)
    {
        // 创建Tree
        $tree = array();
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] = &$list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][] = &$list[$key];
                    }
                }
            }
        }
        return $tree;
    }
}

