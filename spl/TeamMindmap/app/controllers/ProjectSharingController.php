<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-1-28
 * Time: 下午4:15
 */

/**
 * 项目分享模块控制器
 * Class ProjectSharingController
 */
class ProjectSharingController extends \BaseController
{
    public function __construct()
    {
        /**
         * 分享内容过滤
         */
        \Libraries\MarkDownPurifier::purify(['content']);
    }

    /**
     * 分页获取项目中分享列表清单
     * 返回的json格式:
     * {
     *   'total' => 10,
     *   'per_page' => 10,
     *   'current_page' => 2,
     *   'last_page' => 2,
     *   'from' => 1,
     *   'to' => 2,
     *   'data' => [
     *     {
     *         'id': 1,
     *         'name': 'sharing',
     *         'content': 'dust2's sharing',
     *         'project_id': 1,
     *         'created_at': '2015-01-30 00:00:00',
     *         'updated_at': '2015-01-31 00:00:00',
     *
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($projectId)
    {
        $baseInfos = ProjectSharing::getBaseSharingsInfos($projectId);
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
     * 实现新建分享的数据储存.
     *
     * 用户提交的数据格式如下：
     *  name: 分享的名称
     *  content: 分享的内容
     *  resource: 分享关联的资源（可以是数组或单个元素), 数组元素如下：
     *      filename: 文件名（临时文件文件名)
     *      origin_name: 上传前的文件本来文件名
     *      ext_name: 文件扩展名（不带'.'前缀)
     *      mime: 文件的MIME值
     * tag: 分享所属于的标签（可以是数组或单个元素，要求对应标签的id)
     *
     * 返回的JSON格式：
     *  [成功]:
     *   {
     *      "id": "新创建分享的id"
     *   }
     *  [失败]:
     *  {
     *      "error": "相关的错误信息"
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
                DB::beginTransaction();

                $add = ProjectSharing::create( $this->buildStoreData($postData, $projectId) );
                $this->storeResources($postData, $projectId, $add);
                $this->storeTags($postData, $add);

                DB::commit();

                return Response::json([
                    'id'=>$add['id']
                ]);
            }catch (Exception $error){
                DB::rollBack();
                return Response::json([
                    'error'=>$error->getMessage()
                ], 403);
            }

        }else{
            return Response::json([
                'error' => $this->changeValidatorMessageToString($validator->getMessageBag())
            ], 403);
        }
    }

    /**
     * 将分享所属于的标签进行保存.
     *
     * @param $postData
     * @param ProjectSharing $sharing
     */
    protected function storeTags($postData, ProjectSharing $sharing)
    {
        if( isset($postData['tag']) && ! empty($postData['tag']) ){
            $sharing->tag()->attach($postData['tag']);
        }
    }

    /**
     * 将资源文件从临时文件夹移动到目标文件夹，并将相关信息存储到数据库.
     *
     * @param $postData array 用户提交的数据
     * @param $projectId int 项目id
     * @param ProjectSharing $sharing
     */
    protected function storeResources($postData, $projectId, ProjectSharing $sharing)
    {
        if( isset($postData['resource']) && !empty($postData['resource']) ){

            /*
             * 此处判断提交的附加资源是数组形式还是单个元素，但一个附加资源的描述在PHP中表示为一个关联数组。
             * 因此，此处取的一个元素来判断，如果据需是数组则表示是数组形式的提交数据（相对于单个元素）
             */
            if( ! is_array(head($postData['resource'])) ){
                $postData['resource'] = [ $postData['resource'] ];
            }

            $tempFilesDir = public_path(). '/temp/';
            $targetDir = public_path(). '/resources/';

            $currentUserId = Auth::user()['id'];
            $inTesting = App::environment('testing');

            foreach($postData['resource'] as $currentFileInfo){

                $storeData = array_only($currentFileInfo, ['filename', 'mime', 'ext_name', 'origin_name']);
                $storeData['project_id'] = $projectId;
                $storeData['creater_id'] = $currentUserId;

                $sharing->resource()->save(Resource::create($storeData));

                if( ! $inTesting ){
                    rename(
                        $tempFilesDir. $currentFileInfo['filename'],
                        $targetDir. $currentFileInfo['filename']
                    );
                }

            }

        }

    }

    /**
     * 构造用于实现新建分享的数据存储关联数组.
     *
     * @param $postData array 用户提交的数据
     * @param $projectId int 项目id
     * @return array
     */
    protected function buildStoreData($postData, $projectId)
    {
        $data = array_only($postData, ['name', 'content']);
        $data['creater_id'] = Auth::user()['id'];
        $data['project_id'] = $projectId;

        return $data;
    }

    /**
     * 生成新建信息所使用的校验器
     * @param $postData
     * @return \Illuminate\Validation\Validator
     */
    protected function getStoreValidator($postData)
    {
        Validator::extend('check_resource', function($attr, $value, $params){
            //Just a hacking...
            if( App::environment('testing') ){
                return true;
            }

            if( ! is_array($value) ){
                $value = [$value];
            }

            $tempFilesDir = public_path(). '/temp/';

            foreach ($value as $fileInfo) {
                if( !isset($fileInfo['filename']) || !file_exists( $tempFilesDir. $fileInfo['filename']) ){
                    return false;
                }

            }

            return true;
        });

        $rules = [
            'name' => 'required|max:50',
            'content' => 'required|max:1000',
            'creater_id' => 'exist:users,id',
            'resource'=> 'check_resource'
        ];

        return Validator::make($postData, $rules);
    }

    /**
     * 返回具体的分享信息.
     *
     * 返回的JSON格式：
     * {
     *   "id": "对应的分享的id",
     *   "name": "分享的名称",
     *   "content" : "分享的内容，文本描述",
     *   "creater_id": "创建者id",
     *   "creater": {
     *      "id": "创建者id",
     *      "username": "创建者用户名"
     *      "email": "创建者电子邮件",
     *   },
     *   "created_at": "分享的创建时间",
     *   "tag": [
     *      {
     *          "id": "标签的id",
     *          "name": "标签的名",
     *     },
     *    //....注意是个数组
     *  ],
     *  "resource": [
     *     {
     *          "id": "资源id",
     *          "origin_name": "用户上传的文件的原来文件名",
     *          "created_at" : "资源的创建时间"
     *    },
     *    //....注意是个数组
     *  ]
     * }
     * @param $projectId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($projectId, $id)
    {
        $target = $this->getSharingInProject($projectId, $id);

        $fields = ['id', 'name', 'content', 'creater_id', 'creater', 'tag', 'resource', 'created_at'];

        return Response::json(
            $this->getSectionalValuesFromModel($target, $fields)
        );
    }

    /**
     * 删除某个资源.
     *
     * 注意，只有项目创建者、项目管理员以及资源上传者才具备执行权限.
     *
     * @param $projectId
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws Exception
     */
    public function destroy($projectId, $id)
    {
        $target = $this->getSharingInProject($projectId, $id);

        if( $this->checkDeleteRight($target) &&  $target->delete() ){
            return Response::make('', 200);
        }else{
            return Response::json(['error'=>'删除失败'], 403);
        }
    }

    /**
     * 内部使用，从指定的项目中查找分享.
     *
     * @param $projectId int 项目id
     * @param $sharingId int 分享id
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    protected function getSharingInProject($projectId, $sharingId)
    {
        return ProjectSharing::where('project_id', $projectId)
            ->where('id', $sharingId)
            ->firstOrFail();
    }


    /**
     * 检查用户是否具备删除的权限.
     *
     * @param ProjectSharing $sharing
     * @return bool
     */
    protected function checkDeleteRight(ProjectSharing $sharing)
    {
        $currentUserId = Auth::user()['id'];

        if( $currentUserId == $sharing['creater_id'] ){
            return true;
        }

        return Project::checkManagerOrCreater($currentUserId, $sharing['project']);
    }

}