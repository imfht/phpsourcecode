<?php
namespace app\news\service;

use WxSDK\core\common\IApp;
use app\file\model\WxFile;
use app\file\service\UpService;
use app\news\model\WxNews;
use WxSDK\core\common\Ret;
use WxSDK\core\model\Model;
use WxSDK\Url;
use WxSDK\resource\Config;
use WxSDK\Request;

class UpNews
{
    /**
     * getNewsForUpMass
     * @method获取供群发的图文数据
     * @abstract 主要区别在于，封面图片是：永久图文使用的是永久型封面
     * @abstract 而群发图文使用的是thumb（缩略图）封面
     * @param array $id 图文id
     * @return mixed[] 操作结果
     */
    public function getNewsForUpMass(IApp $app, $id) {
        if (empty ( $id )) {
            return [
                'errCode' => 401,
                'errMsg' => '图文id错误'
            ];
        }
        $WxNews = isset ( $this->WxNews ) ? $this->WxNews : new WxNews(); // 试图在循环时提高效率
        if (is_array ( $id ) && count ( $id ) > 1) {
            $news = [ ];
            foreach ( $id as $k => $v ) {
                $res = $this->getNewsForUp ( $v );
                if ($res ['errCode']) {
                    break;
                } else {
                    $news [] = $res ['data'] ['articles'] [0];
                }
            }
            if (empty ( $news )) {
                return [
                    'errCode' => 406,
                    'errMsg' => $res ['errMsg']
                ];
            } else {
                $r ['articles'] = $news;
                return [
                    'errCode' => 0,
                    'errMsg' => '多图文获取成功',
                    'data' => $r
                ];
            }
        } else {
            if (is_array ( $id )) {
                $id = current ( $id );
            }
            $res = $WxNews->get ( $id );
            if (! $res) {
                $res = [
                    'errCode' => 402,
                    'errMsg' => '图文查询出错'
                ];
            } else {
                // $res=$res->toArray();
                $res = $this->transNewsForUp ($app, $res,1 );
            }
            return $res;
        }
    }
    /**
     * @method 上传 单/多 图文 模块内使用
     *
     * @param array $in
     * @param array $data
     * @return array 其中元素data仍是数组，而非json
     */
    public function getMediaId(IApp $app, $id) {
        $wn = new WxNews();
        $WxNews = $wn->get($id);
        if(!$WxNews){
           return new Ret('',NULL,500,'数据不存在'); 
        }
        if($WxNews['media_id'] && $WxNews['create_at'] == $WxNews['update_time']){
            return new Ret('',NULL, 0, '成功', ['media_id'=>$WxNews['media_id']]);
        }
        // 获取待上传的图文数据json
        $res = $this->getNewsForUp ($app, $id );
        if (! $res ['errCode']) {
            $model = new Model(urldecode ( json_encode ( $res ['data'] ) ));
            $url = new Url(Config::$up_news_for_mass);
            $request = new Request($app, $model, $url);
            $ret = $request->run();
            if($ret->ok()){
                $WxNews = new WxNews ();
                $r = $ret->data;
                $r ['create_at'] = time ();
                $r ['update_time'] = $r['create_at'];
                $res = $WxNews->isUpdate ( true )->allowField ( true )->save ( $r, [
                    'id' => $id
                ] );
                if ($res === 1) {
                    $res = new Ret('',NULL, 0, '成功',['media_id'=>$r['media_id']]);
                } else {
                    $res = new Ret('',NULL, 500, '数据更新失败');
                }
            }else{
                $res = $ret;
            }
        }else{
            $res = new Ret('', null, $res['errCode'], $res['errMsg']);
        }
        return $res;
    }
    /**
     * getNewsForUp
     * @method 获取图文数据，格式化为待上传的数组形式;对多图文迭代
     * @param array|number $id 图文id
     * @return mixed[]
     */
    public function getNewsForUp(IApp $app, $id) {
        if (empty ( $id )) {
            return [
                'errCode' => 401,
                'errMsg' => '图文id错误'
            ];
        }
        $WxNews = isset ( $this->WxNews ) ? $this->WxNews : new WxNews (); // 试图在循环时提高效率
        if (is_array ( $id ) && count ( $id ) > 1) {
            $news = [ ];
            foreach ( $id as $k => $v ) {
                $res = $this->getNewsForUp ( $v );
                if ($res ['errCode']) {
                    break;
                } else {
                    $news [] = $res ['data'] ['articles'] [0];
                }
            }
            if (empty ( $news )) {
                return [
                    'errCode' => 406,
                    'errMsg' => $res ['errMsg']
                ];
            } else {
                $r ['articles'] = $news;
                return [
                    'errCode' => 0,
                    'errMsg' => '多图文获取成功',
                    'data' => $r
                ];
            }
        } else {
            if (is_array ( $id )) {
                $id = current ( $id );
            }
            $res = $WxNews->get ( $id );
            if (! $res) {
                $res = [
                    'errCode' => 402,
                    'errMsg' => '图文查询出错'
                ];
            } else {
                // $res=$res->toArray();
                $res = $this->transNewsForUp ($app, $res );
            }
            return $res;
        }
    }
    /**
     * transNewsForUp
     * @method 处理数据库中的图文原始数据，以供上传至微信之用
     * @param array $news
     * @param number $mass 判断是否为群发，默认是上传永久图文
     * @return mixed[] 操作结果
     */
    private function transNewsForUp(IApp $app, $news,$mass=0) {
        /*
         * =》上传封面，并获得media_id
         * =》做一个循环，上传图文内的每个图片，并获得url
         * =》在上述循环内，用获得的url替换图文中原图的url
         * $r["thumb_media_id"]=""; =>$news['title_img']
         * $r["author"]="";
         * $r["title"]="";
         * $r["content_source_url"]="";
         * $r["content"]="";
         * $r["digest"]="";
         * $r["show_cover_pic"]="";
         * $result['articles']=$r;
         */
        $r ["author"] = urlencode ( $news ['author'] );
        $r ['title'] = urlencode ( $news ['title'] );
        $r ["digest"] = urlencode ( $news ['abstract'] );
        $r ["show_cover_pic"] = empty ( $news ['is_link_img'] ) ? 0 : 1;
        
        // 获取封面图片thumb_media_id
        $news ['title_img'] = str_replace ( '//', '/', str_replace ( '\\', '/', $news ['title_img'] ) );
        $str = explode ( '/', $news ['title_img'] );
        $img ['name'] = $str [count ( $str ) - 1];
        $img ['path'] = rtrim ( str_replace ( $img ['name'], '', $news ['title_img'] ), '/' );
        $WxFile = new WxFile();
        $res = $WxFile->where ( [
            'name' => $img ['name'],
            'path' => $img ['path']
        ] )->find ();
        if ($res) { // 查询封面图片，成功
            if($mass==1){
                $res=UpService::getThumbShortMedia($app, $res['id']);
            }else {
                $res = UpService::getImgLongMedia($app, $res['id']);
            }
            if (!$res->ok()) { // 上传失败
                return [
                    'errCode'=>$res->errCode,
                    'errMsg'=>$res->errMsg
                ];
            }
            if($mass==1){
                $r ["thumb_media_id"] = $res->data['thumb_media_id'];
            }else {
                $r ["thumb_media_id"] = $res->data['media_id'];
            }
            
            // $r['thumb_media_id']=$res['data']['media_id'];
            // 转换图文正文
            $res = $this->transNewsContent ($app, $news ['content'] );
            if ($res ['errCode']) {
                return $res;
            }
            //切记进行转义，另，后续用到该变量（$r ['content']）时需要使用urlencode()的反函数urldecode()
            $r ['content'] =  urlencode (addslashes($res ['data'] ));
            // 			dump($r["content"]);
            // 			exit;
            $result ['articles'] [] = $r;
            return [
                'errCode' => 0,
                'errMsg' => '成功',
                'data' => $result
            ];
        } else { // 查询图片失败
            return $res = [
                'errCode' => 508,
                'errMsg' => '未找到相应文件'
            ];
        }
    }
    
    /**
     * transNewsContent
     * @method 图文正文转换:替换正文中的图片等
     * @param string $content 被操作数据
     * @return mixed[] 操作结果
     */
    private function transNewsContent($app, $content) {
        preg_match_all ( "/<img[^>]*\s*src=['|\"]([^'\"]+)[^>]+>/", $content, $imgs );
        $imgs = $imgs [1];

        foreach ( $imgs as $k => $img ) {
            $res=UpService::getUp2WxNewsImg($app, $img);
            if(!$res->ok()){
                return [
                    'errCode' => $res->errCode,
                    'errMsg' =>  $res->errMsg
                ];
            }else{
                $content=str_replace($img, $res->data['news_url'], $content);
                //$imgs[$k]=$res['data'];
            }
        }
        return [
            'errCode' => 0,
            'errMsg' => '转换正文内容成功',
            'data' => $content
        ];
    }
}

