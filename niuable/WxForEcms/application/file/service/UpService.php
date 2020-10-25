<?php
namespace app\file\service;

use WxSDK\core\common\IApp;
use app\file\model\WxFile;
use WxSDK\core\common\Ret;
use WxSDK\core\module\UpKit;
use app\news\model\WxNews;
use WxSDK\core\model\mass\News;
use WxSDK\core\model\mass\Article;

class UpService
{
    public static function getImgShortMedia(IApp $app, int $id){
        $wf = new WxFile();
        $r = $wf->get($id);
        if(!$r){
            return new Ret('',['errcode'=>1, 'errmsg'=>'数据不存在']);
        }
        $file = $r->toArray ();
        if (!empty($file ['short_media_id']) && strtotime($file ['up_to_wx_time']) > time ()-86400 * 3) {
            $d = [];
            $d ['media_id'] = $file ['short_media_id'];
            if (isset ( $file ['url'] )){
                $d ['url'] = $file ['url'];
            }

             return new Ret('', NULL, 0, '数据库中有现成的', $d);
        } else {
            $ret = UpKit::uploadMedia4ShortTime($app, 'image', self::getRealPath($r));
            if($ret->ok()){
                //写入数据库
                $r->update([
                    'short_media_id'=>$ret->data['media_id'],
                    'up_to_wx_time'=>time()
                ],['id'=>$id]);
                return $ret;
            }else{
                return $ret;
            }
        }
    }
    public static function getImgLongMedia(IApp $app, int $id){
        $wf = new WxFile();
        $r = $wf->get($id);
        if(!$r){
            return new Ret('',['errcode'=>1, 'errmsg'=>'数据不存在']);
        }
        $file = $r->toArray ();
        if (!empty($file ['media_id'])) {
            $d = [];
            $d ['media_id'] = $file ['media_id'];
            if (isset ( $file ['url'] )){
                $d ['url'] = $file ['url'];
            }

             return new Ret('', NULL, 0, '数据库中有现成的', $d);
        } else {
            $ret = UpKit::uploadMedia4Forever($app, 'image', self::getRealPath($r));
            if($ret->ok()){
                //写入数据库
                $r->update([
                    'media_id'=>$ret->data['media_id']
                ],['id'=>$id]);
                return $ret;
            }else{
                return $ret;
            }
        }
    }
    public static function getThumbShortMedia(IApp $app, int $id){
        $wf = new WxFile();
        $r = $wf->get($id);
        if(!$r){
            return new Ret('',['errcode'=>1, 'errmsg'=>'数据不存在']);
        }
        $file = $r->toArray ();
        if (!empty($file ['thumb_media_id']) && strtotime($file ['thumb_up_time']) > time ()-86400 * 3) {
            $d = [];
            $d ['thumb_media_id'] = $file ['thumb_media_id'];
            if (isset ( $file ['thumb_url'] )){
                $d ['url'] = $file ['thumb_url'];
            }

             return new Ret('', NULL, 0, '数据库中有现成的', $d);
        } else {
            $ret = UpKit::uploadMedia4ShortTime($app, 'thumb', self::getRealPath($r));//上传缩略图
            if($ret->ok()){
                //写入数据库
                $r->update([
                    'thumb_media_id'=>$ret->data['thumb_media_id'],
                    'thumb_up_time'=>time()
                ],['id'=>$id]);
                return $ret;
            }else{
                return $ret;
            }
        }
    }
    /**
     * upForeverFile2Wx
     * 上传永久文件到微信
     *
     * @param array $file 文件信息数组
     * @return mixed[] 正确，则元素data中包含微信端的url、media_id等数据
     */
    public static function upForeverFile2Wx(IApp $app, $file) {
        /*
         * 初始化数据
         * 分类上传
         * 写入数据库
         * 返回结果
         */
        $file ['real_path'] = realpath ( ECMS_PATH . $file ['path'] . DS . $file ['name'] );
        if ('image' == $file ['type']) {
            $res = UpKit::uploadMedia4Forever($app, 'image', $file ['real_path']);
        } elseif ('voice' == $file ['type']) {
            $res = UpKit::uploadMedia4Forever($app, 'voice', $file ['real_path']);
        } elseif ('video' == $file ['type']) {
            $res = UpKit::uploadMedia4Forever($app, 'video', $file ['real_path'],$file['title'], $file['description']);
        }else{
            return $res = new Ret('',null, 500, '不支持的文件类型');
        }
        
        if ($res->ok()) { // 正确上传，则将获取到的信息保存到数据库
            $r = $res->data;
            $r ['lifecycle'] = 'long';
            $WxFile = new WxFile ();
            $result = $WxFile->allowField ( true )->isUpdate ( true )->save ( $r, [
                'id' => $file ['id']
            ] );
            if ($result !== 1) {
                $res = new Ret('',null, 500, '文件数据更新错误');
            }
        }
        return $res;
    }
    public static function getThumbLongMedia(IApp $app, int $id){
        $wf = new WxFile();
        $r = $wf->get($id);
        if(!$r){
            return new Ret('',['errcode'=>1, 'errmsg'=>'数据不存在']);
        }
        $file = $r->toArray ();
        if (!empty($file ['thumb_long_media_id'])) {
            $d = [];
            $d ['thumb_media_id'] = $file ['thumb_long_media_id'];
            if (isset ( $file ['thumb_long_url'] )){
                $d ['url'] = $file ['thumb_long_url'];
            }

             return new Ret('', NULL, 0, '数据库中有现成的', $d);
        } else {
            $ret = UpKit::uploadMedia4Forever($app, 'thumb', self::getRealPath($r));
            if($ret->ok()){
                //写入数据库
                $ret->data['thumb_media_id']=$ret->data['media_id'];
                $r->update([
                    'thumb_long_media_id'=>$ret->data['media_id'],
                    'thumb_long_url'=>$ret->data['url']
                ],['id'=>$id]);
                return $ret;
            }else{
                return $ret;
            }
        }
    }
    public static function getVideoShortMedia(IApp $app, WxFile $file){        
        if (!empty($file ['short_media_id']) && strtotime($file ['up_to_wx_time']) > time ()-86400 * 3) {
            $d = [];
            $d ['media_id'] = $file ['short_media_id'];
            if (isset ( $file ['url'] )){
                $d ['url'] = $file ['url'];
            }
            
            return new Ret('', NULL, 0, '数据库中有现成的', $d);
        } else {
            $ret = UpKit::uploadMedia4ShortTime($app, 'video', self::getRealPath($file),[
                "title"=>$file['title'],
                "introduction"=>$file['description']
            ]);
            if($ret->ok()){
                //写入数据库
                $WxFile = new WxFile();
                $WxFile->update([
                    'short_media_id'=>$ret->data['media_id'],
                    'up_to_wx_time'=>time()
                ],['id'=>$file['id']]);
                return $ret;
            }else{
                return $ret;
            }
        }
    }
    public static function getVideoLongMedia(IApp $app, WxFile $file){
        if (!empty($file ['media_id'])) {
            $d = [];
            $d ['media_id'] = $file ['media_id'];
            if (isset ( $file ['url'] )){
                $d ['url'] = $file ['url'];
            }
            
            return new Ret('', NULL, 0, '数据库中有现成的', $d);
        } else {
            $ret = UpKit::uploadMedia4Forever($app, 'video', self::getRealPath($file),[
                "title"=>$file['title'],
                "introduction"=>$file['description']
            ]);
            if($ret->ok()){
                //写入数据库
                $WxFile = new WxFile();
                $WxFile->update([
                    'media_id'=>$ret->data['media_id'],
                    'url'=>$ret->data['url']
                ],['id'=>$file['id']]);
                return $ret;
            }else{
                return $ret;
            }
        }
    }
    
    public static function getVoiceShortMedia(IApp $app, int $id){
        $wf = new WxFile();
        $r = $wf->get($id);
        if(!$r){
            return new Ret('',['errcode'=>1, 'errmsg'=>'数据不存在']);
        }
        $file = $r->toArray ();
        if (!empty($file ['short_media_id']) && strtotime($file ['up_to_wx_time']) > time ()-86400 * 3) {
            $d = [];
            $d ['media_id'] = $file ['short_media_id'];
            if (isset ( $file ['url'] )){
                $d ['url'] = $file ['url'];
            }

             return new Ret('', NULL, 0, '数据库中有现成的', $d);
        } else {
            $ret = UpKit::uploadMedia4ShortTime($app, 'voice', self::getRealPath($r));
            if($ret->ok()){
                //写入数据库
                $r->update([
                    'short_media_id'=>$ret->data['media_id'],
                    'up_to_wx_time'=>time()
                ],['id'=>$id]);
                return $ret;
            }else{
                return $ret;
            }
        }
    }
    /**
     * getUp2WxNewsImg
     * @todo 获取上传到微信的、图文正文内的图片信息
     * @param string $path 相对路径，以“/”开头
     */
    public static function getUp2WxNewsImg(IApp $app, string $path){
        $path = str_replace ( '//', '/', str_replace ( '\\', '/', $path ) );
        $str = explode ( '/', $path );
        $name = $str [count ( $str ) - 1];
        $dir = str_replace ( '/' . $name, '', $path );
        $WxFile=new WxFile();
        $res = $WxFile->where ( [
            'name' => $name,
            'path' => $dir
        ] )->find();
        if($res){
            if(empty($res['news_url'])){
                $realPath = realpath(ECMS_PATH . $path);
                $ret=UpKit::uploadImage4MpnewsContent($app, $realPath);
                if($ret->ok()){
                    //写入数据库
                    $WxFile->update(['news_url'=>$ret->data['url']],['id'=>$res['id']]);
                    $ret->data['news_url'] = $ret->data['url'];
                }
                return $ret;
            }else{
                return new Ret('',NULL, 0, '数据库查询成功', $res);
            }
        }else{
            return new Ret('',NULL, 508, '未找到图片');
        }
    }
    
    public static function getVoiceLongMedia(IApp $app, int $id){
        $wf = new WxFile();
        $r = $wf->get($id);
        if(!$r){
            return new Ret('',['errcode'=>1, 'errmsg'=>'数据不存在']);
        }
        $file = $r->toArray ();
        if (!empty($file ['media_id'])) {
            $d = [];
            $d ['media_id'] = $file ['media_id'];
            if (isset ( $file ['url'] )){
                $d ['url'] = $file ['url'];
            }

             return new Ret('', NULL, 0, '数据库中有现成的', $d);
        } else {
            $ret = UpKit::uploadMedia4Forever($app, 'voice', self::getRealPath($r));
            if($ret->ok()){
                //写入数据库
                $r->update([
                    'media_id'=>$ret->data['media_id']
                ],['id'=>$id]);
                return $ret;
            }else{
                return $ret;
            }
        }
    }
    
    private static function getRealPath(WxFile $file){
        return realpath ( ECMS_PATH . $file ['path'] . '/' . $file ['name'] );
    }
}

