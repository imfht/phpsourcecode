<?php


class TaskController extends \BaseController
{
    public function __construct()
    {
        /**
         * 对任务相关post或put请求进行数据过滤
         */
        \Libraries\MarkDownPurifier::purify(['description']);
    }
    /**
     * 返回项目中的任务.
     *
     * @param $project_id 待查询的项目id
     * @return \Illuminate\Http\JsonResponse
     * 这里区分分组(控制台)与不分组(思维导图)
     *
     * 分组分页
     * 当进入任务列表时返回 undo,doing,finished三种数据.
     * 当触发某一状态任务时，根据触发的状态返回对应undo,doing,finished中的一种.
     * 返回json格式:
     *   {
     *        'undo' => [
     *           {
     *              'total' => 10,
     *              'per_page' => 10,
     *              'current_page' => 2,
     *              'last_page' => 2,
     *              'from' => 1,
     *              'to' => 2,
     *              'data' =>
     *                  'id': 任务的id
     *                  'name': 任务的名称
     *                  'description': 任务的简介
     *                  'parent_id': 父任务的id
     *                  'project_id': 所属项目的id
     *                  'creater_id': 创建者的id
     *                  'last_man': 最后修改的人的id
     *                  'status_id': 任务状态的id
     *                  'expected_at': 任务预期的完成时间
     *                  'finished_at': 任务实际完成的时间
     *                  'created_at': 创建时间
     *                  'updated_at': 更新时间
     *                  'priority_id': 任务优先级的id
     *              },
     *              ......
     *          },
     *        'doing' => {
     *           //同上 undo 部分
     *        },
     *        'finished' => {
     *           //同上 undo 部分
     *        }
     *   }
     *
     * 不分组返回json格式:
     * [
     *   'id': 任务的id
     *   'name': 任务的名称
     *   'description': 任务的简介
     *   'parent_id': 父任务的id
     *   'project_id': 所属项目的id
     *   'creater_id': 创建者的id
     *   'last_man': 最后修改的人的id
     *   'status_id': 任务状态的id
     *   'expected_at': 任务预期的完成时间
     *   'finished_at': 任务实际完成的时间
     *   'created_at': 创建时间
     *   'updated_at': 更新时间
     *   'priority_id': 任务优先级的id
     * ]
     *
     */
    public function index($project_id)
    {
        $cond = Input::all();

        $respData = ProjectTask::getTasksByCondition($project_id, $cond);

        return Response::json($respData);

    }


    /**
     *  新建某一具体的任务.
     *  接受的请求包括以下数据:
     *    parent_id：父任务的id
     *    name：任务名称
     *    description：任务简介
     *    appointed_member：任务成员
     *
     * @param int $project_id 该任务所属于的项目的id
     * @return \Illuminate\Http\JsonResponse
     *
     *  返回的JSON格式（出错时）：
     *  {
     *   "errorMessages": {
     *     "name": ["第一个校验规则不同的错误信息", "第二个校验规则不通过的错误信息"],
     *     "description": ["第一个校验规则不同的错误信息", "第二个校验规则不通过的错误信息"],
     *      //...........
     *   }
     * }
     *  返回的JSON格式（成功时）:
     *  {
     *      "id": "新创建任务的id"
     *  }
     */
    public function store($project_id)
    {
        $postData = array_only(Input::all(), ['name', 'description', 'priority_id', 'parent_id']);


        $validator = $this->getStoreValidator($postData);

        if ($validator->passes()) {

            $taskData = $this->buildStoreDataArray($project_id);
            $newMemberIds = Input::get('appointed_member');
            $newTask = null;

            DB::transaction(function() use ($taskData, $newMemberIds, & $newTask){
                $newTask = ProjectTask::create($taskData);
                ProjectTask_Member::updateTaskMember($newTask['id'], $newMemberIds);
            });

            return Response::json([
               'id'=>$newTask['id'],
                'data' => $newTask->toArray()
            ]);
        } else {
            return Response::json([
                'error_messages' => $validator->messages()
            ], 403);
        }

    }

    /**
     * 返回新建任务所使用的数据校验器.
     *
     * @param $postData
     * @return \Illuminate\Validation\Validator
     */
    protected function getStoreValidator($postData)
    {
        $rules = [
            'name' => 'required | max:50',
            'description' => 'required | max:255',
            'priority_id'=>'required | exists:projectTaskPriorities,id',
            'parent_id' => 'exists:projectTasks,id'

        ];

        return Validator::make($postData, $rules);
    }

    /**
     * 创建新建项目的数据操作时所使用的数据关联数组
     *
     * @param int $projectId  当前的项目id
     * @return array
     */
    protected function buildStoreDataArray($projectId)
    {
        $taskData = array_only(Input::all(), ['name', 'description', 'expected_at', 'priority_id']);

        $taskData['parent_id'] = Input::get('parent_id', null);
        $taskData['project_id'] = $projectId;

        $taskData['creater_id'] = Auth::user()['id'];
        $taskData['last_man'] = $taskData['creater_id'];
        $taskData['handler_id'] = Input::get('handler_id', $taskData['creater_id']);

        return $taskData;
    }


    /**
     * 返回某一具体任务的信息.
     *
     * @param $project_id 任务所属于的项目的id
     * @param $id 待查询的任务的id
     * @return \Illuminate\Http\JsonResponse
     *
     * 返回的JSON格式：
     * {
     * 	  	"baseInfo": {
     * 	  	 "id": "任务id",
     * 	     "parent_id" : "父任务的id，顶级任务的值为null",
     * 	     "description": "任务简介",
     * 	     "creater_id": "创建者id",
     * 	     "created_at": "创建时间",
     * 	     "updated_at": "上一次更新时间"
     * 	  	},
     *       "parentTask": {
     *          //注意此字段仅在当前任务为子任务时才有值，否则为null,
     *         "id": "任务id",
     *         "parent_id": "父任务的id，顶级任务的值为null",
     *         "description": "任务简介",
     *         "creater_id": "创建者id",
     *         "created_at": "创建时间",
     *         "updated_at": "上一次更新时间"
     *       },
     *      "editable"： "该任务能否被编辑",
     * 	    "creater": {
     * 	  	 "id": "创建者id",
     * 	     "username": "创建者的用户名",
     * 	     "email": "创建者的电子邮件地址",
     * 	     "created_at": "创建者的注册时间",
     * 	   },
     *      "handler": {
     * 	  	 "id": "负责人id",
     * 	     "username": "负责人的用户名",
     * 	     "email": "负责人的电子邮件地址",
     * 	     "created_at": "负责人的注册时间",
     * 	   },
     *     "taskStatus": {
     *       "id": "状态id",
     *       "name": "状态名称",
     *       "label": "状态外部显示标签",
     *     },
     *     "taskPriority": {
     *       "id": "任务优先级id",
     *       "name": "任务优先级名称",
     *       "label": "任务优先级的外部显示标签"
     *     },
     * 	   "appointed_member": [
     * 	    成员１的信息(格式结构同 creater ),
     * 	    成员２的信息,
     * 	  	],
     *        "sub_task": [
     *         子任务1的信息: {
     *           "id": "任务的id",
     *           "name": "任务名称"
     *          }
     *         子任务2的信息（格式结构同 子任务1）,
     *         .....
     *         ]
     * 	  }
     */
    public function show($project_id, $id)
    {
        $currTask = ProjectTask::findTaskInProjectOrFail($project_id, $id);
        $baseInfo = $currTask->toArray();

        $relations = ['creater', 'handler', 'parentTask', 'sub_task', 'taskStatus', 'taskPriority'];
        $respArrayData = $this->getSectionalValuesFromModel($currTask, $relations);

        $respArrayData['appointed_member'] = $currTask['taskMember'];
        $respArrayData['baseInfo'] = $baseInfo;
        $respArrayData['editable'] = $this->checkTaskEditable($currTask);

        return Response::json($respArrayData);

    }

    /**
     * 检查当前用户是否具有修改任务信息的权限
     *
     * 只有任务的创建者或者项目的管理员（包括项目的创建者）具备修改的权限
     *
     *testUpdate
     * @param \Illuminate\Database\Eloquent\Model $currentTask Task模型实例
     * @return bool
     */
    protected function checkTaskEditable(\Illuminate\Database\Eloquent\Model $currentTask)
    {
        if( $currentTask['creater_id'] == Auth::user()['id'] ){
            return true;
        } else {
            return Project::checkManagerOrCreater(Auth::user(), $currentTask['project_id']);
        }
    }



    /**
     * 更改某一任务的信息.
     *
     * 接受的请求包括以下数据
     *      name: 任务名称
     *      description: 任务简介
     *      appointed_member{ "add": [], "delete": [] }: 要更新的任务成员的id
     *      status: 任务状态
     *      finished_at: 任务完成时间
     *      expected_at: 任务期望的完成时间
     *      parent_id: 当前任务的父任务id
     *      handler_id: 当前任务负责人id
     *
     * @param $project_id 该任务所属于的项目的id
     * @param $id   该任务的id
     * @return \Illuminate\Http\JsonResponse
     *
     * 返回的JSON格式：
     * {
     * 	    "errorMessages": {
     *	      "name": ["第一个校验规则不同的错误信息", "第二个校验规则不通过的错误信息"],
     * 	 	    "description": ["第一个校验规则不同的错误信息", "第二个校验规则不通过的错误信息"],
     * 	  	  //.........
     * 	   	}
     * 	 }
     */
    public function update($project_id, $id)
    {
        $targetTask = ProjectTask::findTaskInProjectOrFail($project_id, $id);

        $putData = Input::all();

        $validator = $this->getUpdateValidator($putData);

        if ($validator->passes()) {
            $putData['last_man'] = Auth::user()['id'];
            $taskMember = Input::get('appointed_member');


            if ( isset($putData['parent_id'])) {
                if ( $putData['parent_id'] != 0 && !$this->checkHandler($targetTask['handler_id'], $putData['parent_id']) ) {
                    $respData['status'] = 'reselectHandler';
                    $parentTask = ProjectTask::findOrFail($putData['parent_id']);
                    $respData['memberList'] = $parentTask['taskMember']->toArray();
                    array_push($respData['memberList'], $parentTask['handler']->toArray());
                    return Response::json($respData);
                }
                if ( $putData['parent_id'] == 0 ) {
                    $putData['parent_id'] = null;
                }
            }

            DB::transaction(function() use ($targetTask, $putData, $taskMember){
                //appoint_member并不是数据库中对应的字段
                unset($putData['appointed_member']);
                $targetTask->update($putData);
                ProjectTask_Member::updateTaskMember($targetTask['id'], $taskMember);
            });

            return Response::make('', 200);
        } else {
            return Response::json([
                'error_messages' => $validator->messages()
            ], 401);
        }

    }

    /**
     * 判断新父任务成员列表是否存在指定负责人
     * @param $handler_id
     * @param $newParent_id
     * @return bool
     */
    public function checkHandler($handler_id, $newParent_id)
    {
        $taskMemberIds = ProjectTask::getTaskMemberIds($newParent_id);
        $parentTask = ProjectTask::findOrFail($newParent_id);
        array_push($taskMemberIds, $parentTask['handler_id']);
        if (in_array($handler_id, $taskMemberIds)) {
            return true;
        }
        return false;
    }

    /**
     * 返回更新任务信息时所使用的数据过滤器.
     *
     * @param $putData
     * @return \Illuminate\Validation\Validator
     */
    protected function getUpdateValidator($putData)
    {
        return Validator::make($putData, [
            'name'=>'max:50',
            'description'=>'max:255',
            'status_id' => 'exists:projectTaskStatus,id'
        ]);
    }


    /**
     * 删除某一个任务.
     *
     * @param $project_id   该任务所属于的项目的id
     * @param $id   该任务的id
     * @return \Illuminate\Http\JsonResponse
     *
     * 返回的JSON格式：
     * {
     *   "error": "相关的错误信息"
     * }
     */
    public function destroy($project_id, $id)
    {
        ProjectTask::findTaskInProjectOrFail($project_id, $id)->delete();

        return Response::make('', 200);
    }
}