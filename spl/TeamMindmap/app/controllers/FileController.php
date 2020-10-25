<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-1-28
 * Time: 下午4:44
 */




/**
 * Class FileController
 *
 * 用于实现临时文件的上传以及进行
 */
class FileController extends \BaseController
{
    public function __construct()
    {
        $this->beforeFilter('csrf_header', ['on'=>'post']);
    }

    /**
     * 此方法用于临时上传文件（后续操作再将其移动到最终的位置）
     *
     * 提交的数据格式：
     *  'file': 待上传的文件
     *
     * 返回的JSON格式：
     *
     * [成功时]
     *  {
     *   "filename": "临时文件名",
     *   "mime": "临时文件的MIME值",
     *   "size": :临时文件的大小， 单位为KB"
     * }
     *  [失败时]
     *  {
     *   "error": "相关的错误信息"
     *  }
     * @param $resourceName string 临时上传的资源名称
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function postTemp($resourceName)
    {
        $validator = $this->getValidator($resourceName, Input::all());

        if( ! $validator ){
            return Response::make('invalidated access', 403);
        }

        if( $validator->passes() ){
            $this->removeOldFiles(public_path(). '/temp/', 60 * 10 );

            $tmpFile = Input::file('file');
            $newFilename = $this->getUserTempFileNewName($tmpFile->getFilename());

            $respData = [
                'filename'=>$newFilename,
                'mime'=>$tmpFile->getClientMimeType(),
                'size'=> $tmpFile->getSize() / 1024,
                'ext_name'=>$tmpFile->getClientOriginalExtension(),
                'origin_name'=>$tmpFile->getClientOriginalName()
            ];

            $tmpFile->move(public_path(). '/temp/', $newFilename);

            return Response::json($respData);

        }else{
            return Response::json([
                'error'=>$this->changeValidatorMessageToString($validator->getMessageBag())
            ], 403);
        }
    }

    /**
     *
     * 获取文件校验的校验器
     *
     * @param $resourceName string 临时上传的资源名称
     * @param $postData array 提交的数据的集合
     * @return bool|\Illuminate\Validation\Validator
     */
    protected function getValidator($resourceName, $postData)
    {
        if(isset( $this->rules[$resourceName]) ){

            return Validator::make($postData, [
                'file' => $this->rules[$resourceName]
            ]);
        }else{
            return false;
        }
    }

    /**
     * 获取当前用户的临时文件的新文件名（统一格式化）
     *
     * @param $filename
     * @return string
     */
    protected function getUserTempFileNewName($filename)
    {
        return Auth::user()['id']. '-'. $filename;
    }

    /**
     * 生成资源下载相应.
     *
     * @param $resourceId
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getResourceDownload($resourceId)
    {
        if( Request::ajax() || Request::wantsJson() ){
            return Response::make('invalid access', 403);
        } else {
            $resource = Resource::findOrFail($resourceId);

            return Resource::makeDownloadResponse($resource);
        }
    }

    public function getOutlink($link)
    {

    }

    /**
     * 移除过期的旧文件
     * @param $dir
     * @param $timeSeconds
     */
    protected function removeOldFiles($dir, $timeSeconds)
    {
        $handle = opendir($dir);

        if( $handle ){
            while( false !== ($file = readdir($handle)) ){
                $filePath = $dir. $file;
                if( time() - $timeSeconds > filemtime($filePath) && $file != '.gitkeep'){
                    @unlink($filePath);
                }
            }
        }

        closedir($handle);
    }

    protected  $rules = [
        'resource'=>'max:2048|required|mimes:pdf,jpg,jpeg,doc,docx,ppt,pptx,xls,png'
    ];
}