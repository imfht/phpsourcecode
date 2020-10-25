<?php

/**
 * 项目分享中关联的标签控制器，作为辅助控制器
 * 处理业务为：获取标签列表、根据标签筛选分享
 * Class TagController
 */
class TagController extends \BaseController
{

    /**
     * @param $projectId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($projectId)
    {
        $baseInfos = Tag::getTagsInfos($projectId);
        return Response::json($baseInfos);
    }


    /**
     * 根据标签获取项目中分享列表清单
     * 返回的json格式(分页):
     * {
     *   'total' => 10,
     *   'per_page' => 10,
     *   'current_page' => 2,
     *   'last_page' => 2,
     *   'from' => 1,
     *   'to' => 2,
     *   'data' => [
     *     {
     *         'creater':                         //创建者的信息
     *           {
     *             'id': 1                        //创建者的id
     *             'username': 'admin'            //创建者的用户名
     *             'email': 'admin@example.com'   //建者的邮箱
     *             'head_image': '/public/?'      //创建者的头像
     *           },
     *
     *          'tags': [                         //标签列表
     *            {
     *              'id': 1                       //标签的id
     *              'name': 'tag 1'               //标签的名称
     *              'label': '标签1'               //标签的标识名
     *            },
     *            ......
     *           ],
     *
     *          'resources': [                    //资源列表
     *            {
     *              'id': 1                       //资源的id
     *              'ext_name': 'png'             //资源文件的扩展名
     *              'mime': 'image/png'           //资源文件的mime
     *              'filename': '/public/?'       //资源文件的名称
     *              'origin_name': '静静的头像.png' //资源文件原来的名字
     *            },
     *            ......
     *           ] //end of resources
     *     },
     *     ......
     *    ] //end of data
     * }
     * @param $projectId
     * @param $tagId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($projectId, $tagId)
    {

        $baseInfos = ProjectSharing::getBaseSharingsInfosByTag($projectId, $tagId);
        $sharingsInfos = isset($baseInfos['data']) ? $baseInfos['data'] : $baseInfos;

        foreach ($sharingsInfos as $sharingKey => $perSharingBaseInfo) {

            $model = ProjectSharing::find($perSharingBaseInfo['id']);

            $perSharingBaseInfo['creater'] = $model['creater']->toArray();
            $perSharingBaseInfo['tags'] = $model['tag']->toArray();
            $perSharingBaseInfo['resources']  =  $model['resource']->toArray();

            $sharingsInfos[$sharingKey] = $perSharingBaseInfo;
        }

        if(isset($baseInfos['data'])) {
            $baseInfos['data'] = $sharingsInfos;
        } else {
            $baseInfos = $sharingsInfos;
        }

        return Response::json($baseInfos);
    }

    /**
     * 新建标签.
     *
     * 提交的数据格式如下：
     *  name: 表签名（最多30个字符)
     *
     * 【注意】 如果所指定标签已经存在，则直接返回已存在标签的信息
     *
     * 返回的JSON格式：
     *  [成功]
     *  {
     *    "id": "标签的id",
     *    'name": "表签名"
     *  }
     *  [失败]
     *  {
     *   "error": "相关的错误信息"
     *  }
     *
     * @param $projectId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($projectId)
    {
        $postData = Input::all();

        $validator = $this->getStoreValidator($postData);

        if( $validator->passes() ){
            try{

                $target = Tag::where('name', $postData['name'] )->first();

                if( ! $target ){
                    $target = Tag::create([
                        'project_id'=>$projectId,
                        'name'=>$postData['name']
                    ]);
                }


                return Response::json([
                   'id' => $target['id'],
                    'name' => $target['name']
                ]);
            }catch (Exception $err){
                return Response::json([
                    'error'=>$err->getMessage()
                ], 500);
            }

        }else{
            return Response::json([
                'error'=>$this->changeValidatorMessageToString($validator->getMessageBag())
            ], 403);
        }
    }

    /**
     * 获取新建标签时使用的数据校验器.
     *
     * @param $postData
     * @return \Illuminate\Validation\Validator
     */
    protected function getStoreValidator($postData)
    {
        return Validator::make($postData, [
           'name'=>'required|max:30'
        ]);
    }

    /**
     * 删除标签
     *
     * @param $projectId
     * @param $id
     * @return \Illuminate\Http\Response
     * @throws Exception
     */
    public function destroy($projectId, $id)
    {
        $target = Tag::where('project_id', $projectId)
            ->where('id', $id)
            ->firstOrFail();

        if( Project::checkManagerOrCreater($projectId, Auth::user() ) ){
            return Response::make('invalidated access', 403);
        }

        if( $target->delete() ){
            return Response::make('', 200);
        }else{
            return Response::json(['error'=>'内部错误'], 500);
        }
    }
}