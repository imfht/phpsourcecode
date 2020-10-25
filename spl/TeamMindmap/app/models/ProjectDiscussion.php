<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-12-25
 * Time: 上午12:57
 */

class ProjectDiscussion extends Eloquent
{

//    public static function paginate( array $arrayData, $inputConf, $defaultConf)
//    {
//        $pageConf = static::arrayUpdate($inputConf, $defaultConf);
//
//        $paginator = Paginator::make($arrayData, $defaultConf['perPage'], $pageConf['perPage']);
//
//        return $paginator;
//    }

    /**
     * 基于筛选条件过滤讨论，并返回过滤后的结果集合.
     *
     * @param int $projectId 项目id
     * @param array $inputCond 前端传递过来的筛选条件
     * @param array $defaultCond 默认的筛选项
     * @param int $per_page 分页时，每页显示的列表数
     * @return array
     */
    public static function getDiscussionByCond($projectId, $inputCond, $defaultCond)
    {
        $methodSet = static::buildMethodSet($inputCond, $defaultCond);

        $builder = ProjectDiscussion::with('creater')
            ->where('project_id', $projectId);

        //使用筛选方法进行筛选
        foreach ( $methodSet as $method ) {
            $builder = static::$method($builder);
        }

        return Paginate::paginateBuilder( $builder );
    }

    /**
     * 根据前端传递过来的条件和默认值，构建对应的方法调用集合.
     *
     * @param $inputCond
     * @param $defaultCond
     * @return array
     */
    protected static function buildMethodSet($inputCond, $defaultCond)
    {

        $config = static::getFilterMethodSet('cond');
        $methodSet = static::arrayUpdate($inputCond, $defaultCond);

        $resp = [];
        foreach ($methodSet as $filter => $order) {
            array_push($resp, $config[$filter][$order]);
        }

        return $resp;
    }

    protected static function arrayUpdate($fromArray, $toArray)
    {
        foreach($fromArray as $item=>$value){
            if( isset($toArray[ $item ] ) ){
                $toArray[ $item ] = $value;
            }
        }

        return $toArray;
    }

    /**
     * 对open字段的筛选，筛选结果为：不进行筛选
     * @param \Illuminate\Database\Eloquent\Builder　$buidler　查询构造器
     * @return mixed　
     */
    private static function statusGetAll($buidler)
    {
        return $buidler;
    }

    /**
     * 对open字段的筛选，筛选结果为：只获取已开启的讨论
     * @param \Illuminate\Database\Eloquent\Builder　$builder　查询构造器
     * @return mixed
     */
    private static function statusGetOpen($builder)
    {
        return $builder->where('open', 1);
    }

    /**
     * 对open字段的筛选，筛选结果为：只获取已关闭的讨论
     * @param \Illuminate\Database\Eloquent\Builder $builder 查询构造器
     * @return mixed
     */
    private static function statusGetClose($builder)
    {
        return $builder->where('open', 0);
    }

    /**
     * 与用户相关的筛选，筛选结果为：不进行筛选
     * @param \Illuminate\Database\Eloquent\Builder　$builder　查询构造器
     * @return mixed
     */
    private static function optionGetAll($builder)
    {
        return $builder;
    }

    /**
     * 与用户相关的筛选，筛选结果为：只获取请求用户关注的讨论
     * @param \Illuminate\Database\Eloquent\Builder $builder 查询构造器
     * @return mixed
     */
    private static function optionGetCreate($builder)
    {
        return $builder->where('creater_id', Auth::user()['id']);
    }

    /**
     * 与用户相关的筛选，筛选结果为：只获取用户创建的讨论
     * @param \Illuminate\Database\Eloquent\Builder　$builder　查询构造器
     * @return mixed
     */
    private static function optionGetFollow($builder)
    {
        return $builder->leftJoin('projectDiscussion_follower',
            'projectDiscussions.id', '=',
            'projectDiscussion_follower.projectDiscussion_id')
            ->where('projectDiscussion_follower.follower_id', Auth::user()['id'])
            ->select([
                'projectDiscussions.id AS id',
                'projectDiscussions.project_id AS project_id',
                'projectDiscussions.title AS title',
                'projectDiscussions.content AS content',
                'projectDiscussions.open AS open',
                'projectDiscussions.creater_id AS creater_id',
                'projectDiscussions.created_at AS created_at',
                'projectDiscussions.updated_at AS updated_at'
            ]);
    }

    public function creater()
    {
        return $this->hasOne('User', 'id', 'creater_id');
    }

    public function followers()
    {
        return $this->belongsToMany('User', 'projectDiscussion_follower', 'projectDiscussion_id', 'follower_id');
    }

    public function projectTasks()
    {
        return $this->belongsToMany('ProjectTask', 'projectDiscussion_projectTask', 'projectDiscussion_id', 'projectTask_id');
    }

    public function project()
    {
        return $this->hasOne('Project', 'id', 'project_id');
    }

    public static function getFilterMethodSet($option)
    {
        $rtn = [];
        foreach(static::$filterMethodSet as $currentItemName=>$currentItemOpts){
            $rtn[ $currentItemName ] = $currentItemOpts[ $option ];
        }

        return $rtn;
    }


    protected static $filterMethodSet = [
        'open'=>[
            'cond'=>['statusGetAll', 'statusGetOpen', 'statusGetClose'],
            'label'=>['全部的', '开启的', '关闭的']],
        'user'=>[
            'cond'=>['optionGetAll', 'optionGetFollow', 'optionGetCreate'],
            'label'=>['所有人的', '请求我关注的', '我所创建的']
        ]
    ];

    protected $guarded = ['id'];

    protected $table = 'projectDiscussions';
}