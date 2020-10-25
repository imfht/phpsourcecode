<?php

class MemberController extends \BaseController
{
    /**

    * 默认控制器动作，获取当前项目的所有参与者（包括项目创建者和创建者）.
    * 这里通过模型Project的动态属性获取成员和创建者.
    *
    * @param int $projectId 成员所在的项目的id
    * @return Response
    *
    * 返回的JSON参数格式：
    * {
    * 	"creater":{
    * 		"id":1,
    * 		"username":"admin","email":"admin@example.com"
    * 	},
    * 	"members":[{
    * 		"id":2,
    * 		"username":"spatra",
    *       "email":"spatra@sp.com",
    *       "role_id": "role_id"
    * 	},
    * 	//....注意，members是个数组
    * ]
    * }
    */
    public function index($projectId)
    {
        $targetProject = Project::findOrFail($projectId);

        $creater = $targetProject['creater']->toArray();

        $members = Project_Member::getMembersFromProject($projectId);

        return Response::json([
            'creater'=>$creater,
            'members'=>$members,
            'editable'=>$this->checkEditable($targetProject)
        ]);

    }

    /**
     * 检查是否具备修改成员的权限.
     *
     * @param \Illuminate\Database\Eloquent\Model $projectModel
     * @return bool
     */
    protected function checkEditable(\Illuminate\Database\Eloquent\Model $projectModel)
    {
        if( $projectModel['creater_id'] == Auth::user()['id'] ){
            return true;
        }

        $projectMember = Project_Member::where('project_id', $projectModel['id'])
            ->where('member_id', Auth::user()['id'])
            ->firstOrFail();

        if( $projectMember['role']['name'] == 'member'){
            return false;
        } else {
            return true;
        }
    }

    /**
    * Store a newly created resource in storage.
    * 为当前项目增加项目成员，通过用户名或电子邮箱地址增加
    * 注：memberAccount为前端input属性名
    *
    * @param int $projectId 成员所在的项目的id
    * @return Response
    *
    * 返回JSON参数的格式：
    * {
    *  "error": "操作失败时此处显示错误信息"
    * }
    */
    public function store($projectId)
    {
        $targetProject = Project::findOrFail($projectId);

        if( ! $this->checkEditable($targetProject) ){
            return Response::json(['error'=>'没有添加成员的权限'], 403);
        }


        $mixed = Input::get('memberAccount');
        $addMember = User::where('username', $mixed)
            ->orWhere('email', $mixed)
            ->firstOrFail();

        $validator = $this->getStoreValidator($addMember['id'], $projectId);

        if( $validator->passes() ){
            $targetProject->members()->attach($addMember['id'], [
                'role_id'=>Input::get('role_id'),
                'created_at'=>date('Y-m-d'),
                'updated_at'=>date('Y-m-d')
            ]);

            return Response::json($addMember['id']);
        } else {
            return Response::json([
                'error'=>
                    $this->changeValidatorMessageToString(
                        $validator->messages() )
            ], 403);
        }

	}

    /**
     * 返回添加成员的数据校验器.
     *
     * @param $addMemberId
     * @return \Illuminate\Validation\Validator
     */
    protected function getStoreValidator($addMemberId, $projectId)
    {
        $data = [
            'member_id'=>$addMemberId,
            'role_id'=>Input::get('role_id')
        ];

        $rules = [
            'member_id'=>'required|check_repeat|check_myself',
            'role_id'=>'required'
        ];

        Validator::extend('check_repeat', function($attr, $value) use ($projectId){
            return is_null(
              Project_Member::where('member_id', $value)
                  ->where('project_id', $projectId)
                  ->first()
            );
        });

        $currUser = Auth::user();
        Validator::extend('check_myself', function($attr, $value) use ($currUser){
            return $value != $currUser['id'];
        });

        $message = [
            'check_repeat'=>'该成员已经存在',
            'check_myself'=>'不能添加自己'
        ];

        return Validator::make($data, $rules, $message);
    }

    /**
     * 更新成员信息，暂时只能更改其角色.
     *
     * @param $projectId
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update($projectId, $id)
    {
        $projectMemberRelation = Project_Member::where('project_id', $projectId)
            ->where('member_id', $id)->firstOrFail();

        $roleId = Input::get('role_id');
        ProjectRole::findOrFail( $roleId );

        $projectMemberRelation->update(['role_id' => $roleId]);

        return Response::make('', 200);
    }


    /**
    * 显示当前项目中，某个具体成员的个人信息.
    *
    * @param int $projectId 对应的项目id
    * @param  int  $id 对应的成员id
    * @return Response
    *
    * 返回的JSON参数格式：
    * {
    * 	"id":2,
    * 	"username":"spatra",
    * 	"email":"spatra@sp.com",
    * 	"role_id":1,
    * 	"role_label":"\u666e\u901a\u6210\u5458"
    * }
    */
    public function show($projectId, $id)
    {
        $projectMemberRelation = Project_Member::where('project_id', $projectId)
            ->where('member_id', $id)->firstOrFail();


        $memberInfo = $projectMemberRelation['member']->toArray();
        $memberInfo['role_id'] = $projectMemberRelation['role_id'];
        $memberInfo['role_label'] = $projectMemberRelation['role']['label'];

        return Response::json($memberInfo);
    }

    /**
    * 从当前项目中删除某个成员
    *
    * @param int $projectId  对应的项目id
    * @param  int  $userId 对应的成员id
    * @return Response
    * 返回的JSON参数格式：
    * {
    *	"error": "当操作失败时此处显示错误信息"
    *}
    */
    public function destroy($projectId, $userId)
    {
        $targetProject = Project::findOrFail($projectId);

        if( ! $this->checkEditable($targetProject)  ) {
            return Response::json(['error' => '没有删除成员的权限'], 403);
        }

        if( $targetProject->members()->detach($userId) ){
            return Response::make('', 200);
        }else{
            return Response::json(['error'=>'该成员不存在'], 404);
        }
    }

}
