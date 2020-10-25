<?php

/**
 * Class ProjectController
 *
 * 此控制器采用Laravel框架中资源控制器的编写形式，对应于项目管理的数据交互.
 *
 */
class ProjectController extends \BaseController
{
    public function __construct()
    {
        /*
        * 关于项目管理中的权限校验问题，在过滤器accessProject中完成
        */
        $this->beforeFilter('accessProject');

        /**
         * 对项目相关post或put请求进行数据过滤
         */
        \Libraries\MarkDownPurifier::purify(['introduction']);
    }

    /**
     * 显示当前用户的所有项目，包括用户所创建的，以及用户所参与的.
     * 这里涉及到分页但是对于每种分页情况，返回的数据格式是一致的.
     * [
     *   {
     *     'total' => 10,
     *     'per_page' => 10,
     *     'current_page' => 2,
     *     'last_page' => 2,
     *     'from' => 1,
     *     'to' => 2,
     *     'data' => [
     *       {
     *         "id":1,
     *         "name":"example",
     *         "cover":"example.jgp",
     *         "introduction":"example and  example",
     *         "creater_id":1,
     *         "created_at":"-0001-11-30 00:00:00",
     *         "updated_at":"-0001-11-30 00:00:00"
     *       }
     *     ]
     *   },
     *   ......
     * ]
     * @return Response
     */
    public function index()
    {
        $currUser = Auth::user();
        $resp['create'] = $currUser['createProjects']->toArray();
        $resp['join'] = $currUser['joinProjects']->toArray();
        $resp['all'] = array_merge($resp['create'], $resp['join']);
        shuffle($resp['all']);
        $pagination = Paginate::paginateArray($resp[Input::get('option', 'all')]);


        return Response::json($pagination);
    }
    /**
     * 处理新创建项目的操作， 提交的数据格式如下：
     *  name: 项目名称,
     *  introduction: 项目介绍
     *  cover: 项目封面图
     *  memberList: [
     *   {
     *    'user_id':  '成员的用户id',
     *    'role_id':  '成员的角色id'
     *   },
     *   //注意这是个数组，如果有的话
     * ]
     *
     * 返回JSON的参数：
     *  errorMessage: 当密码不成功修改时，返回表示错误信息的对象，
     *  id: 当成功创建项目的时候,返回新创建项目的id, 示范如下：
     *
     *  {
     *    "id": "新创建的项目id",
     *    "error": "相关的错误信息"
     *
     * @return Response
     */
    public function store()
    {
        //项目封面暂时只能选择，不能自定义
        $addData = array_only(Input::all(), ['name', 'introduction', 'cover']);
        $addData['creater_id'] = Auth::user()['id'];


        $validator = $this->getStoreValidator($addData);

        $status = 200;

        if ($validator->passes()) {
            try {

                DB::beginTransaction();
                $newProject = Project::create($addData);
                $this->addMembers(Input::get('memberList'), $newProject['id']);
                DB::commit();

                $res['id'] = $newProject['id'];
            } catch (Exception $err) {
                DB::rollBack();
                $res['errorMessages'] = ['data'=>'抱歉！内部数据操作失败'];
                $status = 500;
            }

        } else {
            $res['errorMessages'] = $this->changeValidatorMessageToString($validator->messages());
            $status = 403;
        }

        return Response::json($res, $status);
    }

    /**
     * 添加项目成员　
     * @param $memberList
     * @param $projectId
     */
    private function addMembers($memberList, $projectId)
    {
        foreach($memberList as $currMember){
            Project_Member::create([
                'project_id'=>$projectId,
                'member_id'=>$currMember['user_id'],
                'role_id'=>$currMember['role_id']
            ]);
        }
    }

    /**
     * 类内部使用, 创建用于校验新建项目数据的校验器.
     *
     * @param array $addData 待新建项目数据的关联数组
     * @return \Illuminate\Validation\Validator
     */
    private function getStoreValidator($addData)
    {
        $verifiedRules = [
            'name' => 'required',
            'introduction' => 'required'
        ];

        return Validator::make($addData, $verifiedRules);
    }


    /**
     * 显示某一个项目的具体信息.
     *
     * 返回JSON的参数格式：
     * {
     * 	"baseInfo": {
     * 	  "id: "项目id",
     * 	  "name": "项目名称",
     *    "cover" : "项目的封面图片",
     *    "introduction": "项目简介",
     *    "creater_id": "创建者id",
     *    "created_at": "创建时间",
     *    "updated_at": "上一次更新时间"
     * 	},
     *  "editable": true/false,
     *  "creater": {
     * 	  "id": "创建者id",
     *    "username": "创建者的用户名",
     *    "email": "创建者的电子邮件地址",
     *    "created_at": "创建者的注册时间",
     *  },
     *  members: [
     *   成员１的信息(格式结构同 creater ),
     *   成员２的信息,
     * 	]
     * }
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $currProject = Project::findOrFail($id);
        $projectInfo['baseInfo'] = $currProject->toArray();
        $projectInfo['creater'] = $currProject->creater->toArray();
        $projectInfo['members'] = $currProject->members->toArray();
        $projectInfo['editable'] = Project::checkManagerOrCreater(Auth::user(), $currProject); //用户是否具备项目信息的编辑权限
        return Response::json($projectInfo);
    }

    /**
     * 处理项目编辑，并更新相关数据.
     *
     * 返回的JSON格式参数(仅当操作失败时返回）：
     * {
     *   "error": "相关的错误信息"
     * }
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $updateData = Input::all();

        Project::findOrFail($id)->update($updateData);

        return Response::make('', 200);
    }



    /**
     * 删除指定的项目.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        Project::findOrFail($id)->delete();
        return Response::make('', 200);
    }

}
