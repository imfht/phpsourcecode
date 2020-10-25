<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/11
 * Time: 13:51
 */

namespace app\admin\controller;


use app\common\model\content\Attachment;
use think\Controller;

/**
 * @title 文件上传
 * Class Upload
 * @package app\admin\controller
 */
class Upload extends Controller
{
    /**
     * @title 文件上传
     * @auth 1
     *          通用文件上传，校验MD5快速重传
     * @param Request $request
     * @param Response $response
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function file()
    {
        $file = current($this->request->file());
        $res = Attachment::where('hash','=',$file->hash('md5'))->find();
        if (!empty($res)){
            return json(['code'=>0,'msg'=>'上传成功','data'=>['src'=>$res['path']]]);
        }
        // 移动到框架应用根目录/uploads/ 目录下
        $dir = env('root_path').DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'uploads';
        $info = $file->validate([
            'size'=>config('upload.max_size_allow'),
            'ext'=>config('upload.file_ext_allow'),
            'type'=>config('upload.file_type_allow')
        ])->move( $dir);
        if($info){
            $hash = $info->hash('md5');
            $savename = '/uploads/'.str_replace('\\','/',$info->getSaveName());
            $domain = config('liteadmin.upload_full_address')?request()->domain():'';

            $data = [
                'hash'=>$hash,
                'path'=>$domain.$savename,
                'create_time'=>$this->request->time(),
                'size'=>$info->getInfo('size')
            ];

            try{
                Attachment::insert($data);
            }catch (PDOException $e){
                return json(['code'=>1,'msg'=>$e->getMessage()]);
            }
            return json(['code'=>0,'msg'=>'上传成功','data'=>['src'=>$data['path']]]);

        }else{
            return json(['code'=>1,'msg'=>$file->getError()]);
        }
    }

    /**
     * @title 检查图片是否存在
     * @auth 1
     * @param Request $request
     * @return Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkFile()
    {
        $hash = $this->request->get('hash');
        $file = Attachment::where('hash','=',$hash)->find();

        if (!empty($file)){
            return json(['code'=>0,'msg'=>'上传成功','data'=>['src'=>$file['path']]]);
        }else{
            return json(['code'=>1,'msg'=>'文件不存在']);
        }
    }

    /**
     * @title 百度umeditor富文本编辑器图片插入
     * @auth 1
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function ueditor()
    {
        $file = current($this->request->file());
        $res = Attachment::where('hash','=',$file->hash('md5'))->find();
        if (!empty($res)){
            $json = [
                "originalName" => pathinfo($res['path'],PATHINFO_BASENAME) ,
                "name" => pathinfo($res['path'],PATHINFO_BASENAME) ,
                "url" => $res['path'],
                "size" => $res['size'] ,
                "type" => pathinfo($res['path'],PATHINFO_EXTENSION) ,
                "state" => "SUCCESS"
            ];
            return json($json)->header('Content-Type','text/html');
        }
        // 移动到框架应用根目录/uploads/ 目录下
        $dir = env('root_path').DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'uploads';
        $info = $file->validate([
            'size'=>config('upload.max_size_allow'),
            'ext'=>config('upload.editor_ext_allow'),
            'type'=>config('upload.editor_type_allow')
        ])->move( $dir);
        if($info){
            $hash = $info->hash('md5');
            $savename = '/uploads/'.str_replace('\\','/',$info->getSaveName());
            $domain = config('liteadmin.upload_full_address')?request()->domain():'';

            $data = [
                'hash'=>$hash,
                'path'=>$domain.$savename,
                'create_time'=>$this->request->time(),
                'size'=>$info->getInfo('size')
            ];

            try{
                Attachment::insert($data);
            }catch (PDOException $e){
                $json = [
                    "state" => $e->getMessage()
                ];
                return json($json);
            }
            $json = [
                "originalName" => $info->getFilename(),
                "name" => $info->getFilename(),
                "url" => $data['path'],
                "size" => $info->getInfo('size') ,
                "type" => '.'.$info->getExtension() ,
                "state" => "SUCCESS"
            ];
            return json($json)->header('Content-Type','text/html');
        }else{
            $json = [
                "state" => $file->getError()
            ];
            return json($json)->header('Content-Type','text/html');
        }
    }

    /**
     * @title wangEditor富文本编辑器图片插入
     * @auth 1
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function wangeditor()
    {
        $file = current($this->request->file());
        $res = Attachment::where('hash','=',$file->hash('md5'))->find();
        if (!empty($res)){
            $json = [
                "errno" => 0,
                "data" => [
                    $res['path']
                ]
            ];
            return json($json);
        }
        // 移动到框架应用根目录/uploads/ 目录下
        $dir = env('root_path').DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'uploads';
        $info = $file->validate([
            'size'=>config('upload.max_size_allow'),
            'ext'=>config('upload.editor_ext_allow'),
            'type'=>config('upload.editor_type_allow')
        ])->move( $dir);
        if($info){
            $hash = $info->hash('md5');
            $savename = '/uploads/'.str_replace('\\','/',$info->getSaveName());
            $domain = config('liteadmin.upload_full_address')?request()->domain():'';

            $data = [
                'hash'=>$hash,
                'path'=>$domain.$savename,
                'create_time'=>$this->request->time(),
                'size'=>$info->getInfo('size')
            ];

            try{
                Attachment::insert($data);
            }catch (PDOException $e){
                $json = [
                    "errno" => 1,
                    "msg" => $e->getMessage()
                ];
                return json($json);
            }
            $json = [
                "errno" => 0,
                "data" => [
                    $data['path']
                ]
            ];
            return json($json);
        }else{
            $json = [
                "errno" => 1,
                "msg" => $file->getError()
            ];
            return json($json);
        }
    }

    /**
     * @title markdown编辑器图片插入
     * @auth 1
     */
    public function markdown()
    {
        $file = $this->request->file('editormd-image-file');
        $res = Attachment::where('hash','=',$file->hash('md5'))->find();
        if (!empty($res)){
            $json = [
                "success" => 1,
                "url" => $res['path']
            ];
            return json($json);
        }
        // 移动到框架应用根目录/uploads/ 目录下
        $dir = env('root_path').DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'uploads';
        $info = $file->validate([
            'size'=>config('upload.max_size_allow'),
            'ext'=>config('upload.editor_ext_allow'),
            'type'=>config('upload.editor_type_allow')
        ])->move( $dir);
        if($info){
            $hash = $info->hash('md5');
            $savename = '/uploads/'.str_replace('\\','/',$info->getSaveName());
            $domain = config('liteadmin.upload_full_address')?request()->domain():'';

            $data = [
                'hash'=>$hash,
                'path'=>$domain.$savename,
                'create_time'=>$this->request->time(),
                'size'=>$info->getInfo('size')
            ];

            try{
                Attachment::insert($data);
            }catch (PDOException $e){
                $json = [
                    "success" => 0,
                    "message" => $e->getMessage()
                ];
                return json($json);
            }
            $json = [
                "success" => 1,
                "url" => $data['path']
            ];
            return json($json);
        }else{
            $json = [
                "success" => 0,
                "message" => $file->getError()
            ];
            return json($json);
        }
    }

    /**
     * @title ckeditor编辑器图片插入
     * @auth 1
     */
    public function ckeditor()
    {
        $file = current($this->request->file());
        $res = Attachment::where('hash','=',$file->hash('md5'))->find();
        if (!empty($res)){
            $json = [
                "uploaded" => 1,
                "fileName" => pathinfo($res['path'],PATHINFO_FILENAME),
                "url" => $res['path']
            ];
            return json($json);
        }
        // 移动到框架应用根目录/uploads/ 目录下
        $dir = env('root_path').DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'uploads';
        $info = $file->validate([
            'size'=>config('upload.max_size_allow'),
            'ext'=>config('upload.editor_ext_allow'),
            'type'=>config('upload.editor_type_allow')
        ])->move( $dir);
        if($info){
            $hash = $info->hash('md5');
            $savename = '/uploads/'.str_replace('\\','/',$info->getSaveName());
            $domain = config('liteadmin.upload_full_address')?request()->domain():'';
            $data = [
                'hash'=>$hash,
                'path'=>$domain.$savename,
                'create_time'=>$this->request->time(),
                'size'=>$info->getInfo('size')
            ];

            try{
                Attachment::insert($data);
            }catch (PDOException $e){
                $json = [
                    "uploaded" => 0,
                    "error" => [
                        "message"=>$e->getMessage()
                    ]
                ];
                return json($json);
            }
            $json = [
                "uploaded" => 1,
                "url" => $data['path'],
                "fileName"=> $info->getFilename()
            ];
            return json($json);
        }else{
            $json = [
                "uploaded" => 0,
                "error" => [
                    "message"=>$file->getError()
                ]
            ];
            return json($json);
        }
    }
}