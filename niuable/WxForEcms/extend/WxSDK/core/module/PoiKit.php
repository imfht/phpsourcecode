<?php
namespace WxSDK\core\module;

use WxSDK\Request;
use WxSDK\Url;
use WxSDK\core\common\IApp;
use WxSDK\core\model\Model;
use WxSDK\core\model\poi\PoiModel;
use WxSDK\core\model\poi\map\InfoInMap;
use WxSDK\core\model\poi\map\StoreInfo;
use WxSDK\core\model\poi\wxa\Info;
use WxSDK\core\utils\Tool;
use WxSDK\resource\Config;
/**
 * 门店管理
 * @author 97893
 *
 */
class PoiKit
{
    public function uploadTitleImage(IApp $app,string $filename){
        $url = new Url(Config::$up_img_for_news_content);
        $media = Tool::createMediaData($filename, 'buffer');
        $model = new Model($media);
        $request = new Request($app, $model, $url);
        
        return $request->run();
    }
    
    public function add(IApp $app, PoiModel $model){
        $req = new Request($app, $model, new Url(Config::$poi_add));
        return $req->run();
    }
    
    public function update(IApp $app, PoiModel $model){
        $req = new Request($app, $model, new Url(Config::$poi_update));
        return $req->run();
    }
    
    public function search(IApp $app, string $poiId){
        $req = new Request($app, new Model(['poi_id'=>$poiId]), new Url(Config::$poi_search));
        return $req->run();
    }
    
    public function delete(IApp $app, string $poiId){
        $req = new Request($app, new Model(['poi_id'=>$poiId]), new Url(Config::$poi_delete));
        return $req->run();
    }
    
    public function getList(IApp $app, int $begin, int $limit = 10){
        $req = new Request($app, new Model(['begin'=>$begin,'limit'=>$limit])
            , new Url(Config::$poi_get_list));
        return $req->run();
    }
    
    /**
     * 门店类目列表
     */
    public function getwxcategory(IApp $app){
        $req = new Request($app, new Model(), new Url(Config::$poi_getwxcategory));
        return $req->run();
    }
    
    /**
     * 拉取门店小程序类目
     * @param IApp $app
     * @return \WxSDK\core\common\Ret
     */
    public function wxaGetCategory(IApp $app){
        $req = new Request($app, new Model(), new Url(Config::$poi_wxa_getcategory));
        return $req->run();
    }
    
    /**
     * 新增门店小程序
     * @param IApp $app
     * @param Info $model
     * @return \WxSDK\core\common\Ret
     */
    public function addPoiWxa(IApp $app, Info $model){
        $req = new Request($app, new Model(), new Url(Config::$poi_wxa_add));
        return $req->run();
    }
    
    /**
     * 查询审核结果
     * @param IApp $app
     * @return \WxSDK\core\common\Ret
     */
    public function getPoiWxaAuditInfo(IApp $app){
        $req = new Request($app, new Model(), new Url(Config::$poi_wxa_get_merchant_audit_info));
        return $req->run();
    }
    
    /**
     * 更新信息
     * @param IApp $app
     * @return \WxSDK\core\common\Ret
     */
    public function updatePoiWxaInfo(IApp $app, string $headimg_mediaid, $introduce = ''){
        $req = new Request($app, new Model([
            "headimg_mediaid"=> $headimg_mediaid,
            'intro'=>$introduce
        ]), new Url(Config::$poi_wxa_update_info));
        return $req->run();
    }
    /**
     * 在腾讯地图中搜索门店
     * @param IApp $app
     * @param string $districtid 对应 拉取省市区信息接口 中的id字段
     * @param string $keyword 搜索的关键词
     * @return \WxSDK\core\common\Ret
     */
    public function searchInMap(IApp $app, string $districtid, string $keyword) {
        $req = new Request($app, new Model([
            "districtid"=> $districtid,
            'keyword'=>$keyword
        ]), new Url(Config::$poi_wxa_update_info));
        return $req->run();
    }
    
    /**
     * 在地图中创建门店
     * @param IApp $app
     * @param Info $model
     * @return \WxSDK\core\common\Ret
     */
    public function addPoiInMap(IApp $app, InfoInMap $model){
        $req = new Request($app, $model, new Url(Config::$poi_add_in_map));
        return $req->run();
    }
    
    /**
     * 添加门店
     * @param IApp $app
     * @param Info $model
     * @return \WxSDK\core\common\Ret
     */
    public function addStore(IApp $app, StoreInfo $model){
        $req = new Request($app, $model, new Url(Config::$poi_add_store));
        return $req->run();
    }
    
    /**
     * 更新门店
     * @param IApp $app
     * @param Info $model
     * @return \WxSDK\core\common\Ret
     */
    public function updateStore(IApp $app, StoreInfo $model){
        $req = new Request($app, $model, new Url(Config::$poi_update_store));
        return $req->run();
    }
    
    /**
     * 获取单个门店信息
     * @param IApp $app
     * @param string $poi_id
     * @return \WxSDK\core\common\Ret
     */
    public function getStore(IApp $app, string $poi_id){
        $req = new Request($app, new Model(['poi_id'=>$poi_id]), new Url(Config::$poi_get_store_info));
        return $req->run();
    }
    /**
     * 获取门店列表
     * @param IApp $app
     * @param int $offset
     * @param int $limit
     * @return \WxSDK\core\common\Ret
     */
    public function getStoreList(IApp $app, int $offset, int $limit = 10){
        $req = new Request($app, new Model([
            'offset'=>$offset,
            'limit'=>$limit
        ]), new Url(Config::$poi_get_store_list));
        return $req->run();
    }
    /**
     * 删除门店
     * @param IApp $app
     * @return \WxSDK\core\common\Ret
     */
    public function deleteStore(IApp $app, string $poi_id){
        $req = new Request($app, new Model(['poi_id'=>$poi_id ]), new Url(Config::$poi_delete_store));
        return $req->run();
    }
    /**
     * 获取门店小程序配置的卡券
     * @param IApp $app
     * @param string $poi_id
     * @return \WxSDK\core\common\Ret
     */
    public function getStoreCard(IApp $app, string $poi_id){
        $req = new Request($app, new Model(['poi_id'=>$poi_id ]), new Url(Config::$card_get_4_store));
        return $req->run();
    }
    /**
     * 设置门店小程序的卡券
     * @param IApp $app
     * @param string $poi_id
     * @return \WxSDK\core\common\Ret
     */
    public function setStoreCard(IApp $app, string $poi_id, string $card_id){
        $req = new Request($app, new Model([
            'poi_id'=>$poi_id,
            'card_id'=>$card_id
        ]), new Url(Config::$card_set_4_store));
        return $req->run();
    }
}

