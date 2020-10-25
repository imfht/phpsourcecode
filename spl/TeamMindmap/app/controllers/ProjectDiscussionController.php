<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-12-25
 * Time: 上午12:52
 */

class ProjectDiscussionController extends \BaseController
{
    public function __construct()
    {
        /**
         * 对讨论相关post或put请求进行数据过滤
         */
        \Libraries\MarkDownPurifier::purify(['content']);
    }


    /**
     * 返回某一项目的讨论列表
     *
     * @param int $projectId 项目id
     * @return \Illuminate\Http\JsonResponse
     *
     * 返回的JSON格式：
     * [
     *      {
     *          "id": "主键id",
     *          "creater:" {
     *              "id": "创建者用户id",
     *              "username": "创建者用户名",
     *              "head_image": "创建者用户头像",
     *         },
     *         "open": "是否开启",
     *         "title": "讨论的标题",
     *         "content": "内容",
     *         "created_at": "讨论的创建时间",
     *         "updated_at": "讨论的更新时间",
     *     },
     *      //...注意是个数组...
     * ]
     */
    public function index($projectId)
    {
        $cond = Input::all();
        $respData = ProjectDiscussion::getDiscussionByCond($projectId, $cond, [
            'user'=>0,
            'open'=>0
        ]);

        return Response::json($respData);
    }


    /**
     * 新建讨论.
     *
     * 接受的数据格式如下：
     *  "title": 讨论的标题
     *  "content": 讨论的内容
     *  "followers": 请求关注的用户的用户id组成的数组
     *
     * @param $projectId
     * @return \Illuminate\Http\JsonResponse
     *
     * 返回的JSON格式如下：
     *  成功时：
     *  {
     *      "id": "讨论的id"
     *  }
     *
     *  失败时：
     *  {
     *      "error": "相关的错误信息",
     *  }
     */
    public function store($projectId)
    {
        $postData = Input::all();

        $validator = $this->getStoreValidator($postData);

        if( $validator->passes() ){
            $discussionData = $this->buildDiscussionStoreData($postData, $projectId);
            $followers = Input::get('followers', null);
            $add = null;

            DB::transaction(function()use($discussionData, $followers, &$add){
                $add = ProjectDiscussion::create($discussionData);

                if( $followers){
                    $add->followers()->attach($followers);
                    $add['followers'] = $followers;
                    Event::fire('addFollower', $add);
                }

            });

            return Response::json(['id'=>$add['id']]);
        }else{
            return Response::json([
                "error"=>$this->changeValidatorMessageToString( $validator->getMessageBag() )
            ],403);
        }
    }

    /**
     * 成功新建讨论的数据校验器.
     *
     * @param $postData
     * @return \Illuminate\Validation\Validator
     */
    protected function getStoreValidator($postData)
    {
        $rules = [
            'title'=>'required|max:40',
            'content'=>'required',
            'followers'=>'array'
        ];

        return Validator::make($postData, $rules);
    }

    /**
     * 生成新建讨论所需要的直接数据，不包含关系.
     *
     * @param $postData
     * @param $projectId
     * @return array
     */
    protected function buildDiscussionStoreData($postData, $projectId)
    {
        $data = array_only($postData, ['title', 'content']);

        $data['creater_id'] = Auth::user()['id'];
        $data['project_id'] = $projectId;

        return $data;
    }
    /**
     * 返回具体的讨论信息
     *
     * @param $projectId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     *
     * 返回的JSON格式：
     * {
     *     "baseInfo": {
     *          "id": "主键id",
     *          "title": "讨论的标题",
     *          "content": "内容",
     *          "created_at": "讨论的创建时间",
     *          "updated_at": "讨论的更新时间",
     *      },
     *      "creater": {
     *          "id": "创建者用户id",
     *          "username": "创建者用户名",
     *          "head_image": "创建者用户头像"
     *      },
     *      "comments":[
     *           {
     *                 "id": "主键id",
     *                 "content": "评论的内容",
     *                 "creater": {
     *                      "id": "评论者的id",
     *                      "username": "评论者的用户名",
     *                      "head_image": "评论者的头像"
     *                },
     *                "created_at": "评论的创建时间",
     *                "updated_at": "评论的更新时间",
     *          },
     *          //注意是个数组..
     *     ],
     *     "followers": [
     *         {
     *             "id": "讨论指派人的id",
     *             "username": "讨论指派人的用户名",
     *             "head_image": "讨论指派人的头像"
     *         ],
     *         //注意是个数组...
     *    ]
     * }
     */
    public function show($projectId, $id)
    {
        $currDiscussion = ProjectDiscussion::where('project_id', $projectId)->findOrFail($id);
        $respData['baseInfo'] = $currDiscussion->toArray();
        $respData['creater'] = $currDiscussion->creater->toArray();
        $respData['followers'] = $currDiscussion->followers->toArray();

        return Response::json($respData);
    }

    /**
     * 更新讨论，暂时只能开启或关闭.
     *
     * @param $projectId
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update($projectId, $id)
    {
        $targetDiscussion = ProjectDiscussion::where('project_id', $projectId)
            ->where('id', $id)
            ->firstOrFail();

        $open = Input::get('open', null);

        if( $open !== null && $this->checkUpdatePrivilege($targetDiscussion)){
            $targetDiscussion->update([
               'open'=>$open
            ]);

            return Response::make('', 200);
        }else{
            return Response::make('invalid access', 403);
        }
    }

    /**
     * 检查是否有更改的权限
     *
     * @param $discussion
     * @return bool
     */
    protected function checkUpdatePrivilege($discussion)
    {
        $currentUser = Auth::user();
        if( $discussion['creater_id'] == $currentUser['id'] ){
            return true;
        }

        $belongToProject = $discussion['project'];

        if( $belongToProject['creater_id'] == $currentUser['id'] ){
            return true;
        }else{
            return ! empty( Project_Member::where('project_id', $belongToProject['id'])
                        ->where('member_id', $currentUser['id'])->get()->toArray() );
        }
    }

}