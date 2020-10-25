<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-11-9
 * Time: 上午10:11
 */
use Illuminate\Database\Eloquent\Builder;

class ProjectTask extends Eloquent
{

    /**
     * 获取任务创建者
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creater()
    {
        return $this->belongsTo('User', 'creater_id', 'id');
    }

    /**
     * 获取任务负责人
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function handler()
    {
        return $this->belongsTo('User', 'handler_id', 'id');
    }


    /**
     * 获取某一任务的子任务
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subTask()
    {
        return $this->hasMany('ProjectTask', 'parent_id', 'id');
    }

    /**
     * 获取某一任务的父级任务
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentTask()
    {
        return $this->belongsTo('ProjectTask', 'parent_id', 'id');
    }

    /**
     * 获取任务成员
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function taskMember()
    {
        return $this->belongsToMany('User', 'projectTask_member', 'task_id', 'member_id');
    }

    /**
     * 获取任务成员列表id
     * @param $taskId
     * @return array
     */
    public static function getTaskMemberIds($taskId)
    {
        return ProjectTask_Member::where('task_id', $taskId)->select('member_id')->get()->toArray();
    }

    /**
     * 获取任务状态
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function taskStatus()
    {
        return $this->hasOne('ProjectTaskStatus', 'id', 'status_id');
    }

    /**
     * 获取任务优先级别
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function taskPriority()
    {
        return $this->hasOne('ProjectTaskPriority', 'id', 'priority_id');
    }


    /**
     * 获取项目中某一任务
     * @param $projectId
     * @param $taskId
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function findTaskInProject($projectId, $taskId)
    {
        return static::where('project_id', $projectId)->where('id', $taskId)->first();
    }

    /**
     * 获取项目中某一任务
     * @param $projectId
     * @param $taskId
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public static function findTaskInProjectOrFail($projectId, $taskId)
    {
        return static::where('project_id', $projectId)->where('id', $taskId)->firstOrFail();
    }

    /**
     * 构造任务查询构造器
     * @param $project_id
     * @param $options
     * @return \Illuminate\Pagination\Paginator
     */
    public static function getTasksByCondition($project_id, $options)
    {
        if ( isset($options['group']) ){
            return static::groupByStatus($project_id, $options);
        } else {

            $tmpBuilder = static::getBaseBuilder($project_id);

            if(isset($options['status'])) {
                $buildeFun = $options['status'].'TasksBuilder';
                if (method_exists('ProjectTask',$buildeFun)) {
                    $tmpBuilder = static::$buildeFun($tmpBuilder);
                }
            }

            if (isset($options['priority_id'])) {
                $tmpBuilder = $tmpBuilder->where('priority_id', $options['priority_id']);
            }

            return Paginate::paginateBuilder( $tmpBuilder )->toArray();
        }
    }

    /**
     * 任务列表分组，这里有两种选择：
     * 按偏移量 offset 获取 (当对任务状态更改时采用)
     * 按分页 per_page 获取
     *
     * @param $project_id
     * @param $options
     * @return null
     */
    protected static function groupByStatus($project_id, $options)
    {
        $status = ['undo', 'doing', 'finished'];
        if ( isset( $options['status']) ) {
            $status = [ $options['status'] ];
        }

        $taskData = null;

        //判断是使用Laravel风格的分页，还是通过自定义的offset和size实现任意的分页获取
        $useOffsetAndSize = isset($options['offset']) && isset($options['size']);

        foreach ( $status as $currStatus ) {
            $tmpBuilder = static::getBaseBuilder($project_id);
            $method = $currStatus . 'TasksBuilder';
            $tmpBuilder = static::$method( $tmpBuilder );

            if ( isset( $options['priority_id'] ) ) {
                $tmpBuilder = $tmpBuilder->where('priority_id', $options['priority_id']);
            }

            if( $useOffsetAndSize ){
                $taskData[ $currStatus ] = $tmpBuilder->skip($options['offset'])->take($options['size'])->get();
            }else{
                $taskData[ $currStatus ] = Paginate::paginateBuilder( $tmpBuilder )->toArray();
            }

        }

        return $taskData;
    }

    /**
     * 获取基本的查询构造器，已构造成查询指定项目的任务
     *
     * @param $projectId　项目的id
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected static function getBaseBuilder($projectId)
    {
        return ProjectTask::where('project_id', $projectId);
    }

    /**
     * 获取任务状态为 undo
     * @param Illuminate\Database\Eloquent\Builder $builder
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected static function undoTasksBuilder(Builder $builder)
    {
        return $builder->where('status_id', ProjectTaskStatus::where('name', 'undo')->firstOrFail()['id']);
    }

    /**
     * 获取任务状态为 doing
     * @param Illuminate\Database\Eloquent\Builder $builder
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected static function doingTasksBuilder(Builder $builder)
    {
        return $builder->where('status_id', ProjectTaskStatus::where('name', 'doing')->firstOrFail()['id']);
    }

    /**
     * 获取任务状态为 finished
     * @param Illuminate\Database\Eloquent\Builder $builder
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected static function finishedTasksBuilder(Builder $builder)
    {
        return $builder->where('status_id', ProjectTaskStatus::where('name', 'finished')->firstOrFail()['id']);
    }



    protected $table = 'projectTasks';

    protected $guarded = ['id'];
}
