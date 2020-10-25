<?php
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 */
namespace wstmart\common\taglib;
use think\template\TagLib;
class Wst extends TagLib{
    /**
     * 定义标签列表
     */
    protected $tags   =  [
        'friendlink' => ['attr' => 'num,key,id,cache'],
        'ads' => ['attr' => 'code,num,key,id,cache'],
        'article' => ['attr' => 'cat,num,key,id,cache'],
        'goods' => ['attr' => 'type,cat,num,key,id,cache'],
        'brand' => ['attr' => 'cat,num,key,id,cache'],
        'table'=>['table','where','num','order','field','id','key'],
        'order' =>['attr'=>'type,num,key,id,cache,field'],
        'favorite' =>['attr'=>'type,num,key,id,cache,field'],
    	'searchkey' => ['attr' => 'type,key,id,cache'],
    	'score' => ['attr'=>'cat,num,key,id,cache'],
        'shop' => ['attr' => 'cat,num,key,id,cache'],
        'shopgoods' => ['attr' => 'type,shop,cat,num,key,id,cache'],
    	'shopcats' => ['attr'=>'cat,num,key,id,shop,cache']
    ];
    /**
     *  单表查询操作标签   
     *  table:表名
     *   where:查询条件
     *   num:limit
     *   order:排序条件
     *   field:需要取哪些字段
     *   key:序号
     *   id:循环中定义的元素变量
     * {wst:table table="goods" field="goodsId,goodsName" num='6'}{/wst:table}
     */
    public function tagTable($tag, $content){
        $table   = $tag['table'];
        $where   = isset($tag['where'])?$tag['where']:'0';
        $order   = isset($tag['order'])?$tag['order']:'0';
        $field   = isset($tag['field'])?$tag['field']:'*';
        /*$catId  = isset($tag['cat'])?$tag['cat']:0;
        $flag     = substr($catId, 0, 1);
        if (':' == $flag) {
            $catId = $this->autoBuildVar($catId);
            $parseStr .= '$_result=' . $catId . ';';
            $catId = '$_result';
        } else {
            $catId = $this->autoBuildVar($catId);
        }*/
        $id     = isset($tag['id'])?$tag['id']:'vo';
        $num    = isset($tag['num'])?(int)$tag['num']:0;
        $cache  = isset($tag['cache'])?$tag['cache']:0;
        $key    = isset($tag['key'])?$tag['key']:'key';
        $parse  = '<?php ';
        $parse .= '$wstTagGoods =  model("common/Tags")->wstDb("'.$table.'","'.$where.'","'.$order.'","'.$field.'",'.$num.','.$cache.'); ';
        $parse .= 'foreach($wstTagGoods as $'.$key.'=>$'.$id.'){';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php } ?>';
        return $parse;
    }

    /**
     * 商品数据调用    
     *  type:推荐/新品/热销/精品/浏览历史/看了又看/猜你喜欢  - recom/new/hot/best/history/visit/guess
     *   cat:商品分类
     *   num:获取记录数量
     * cache:缓存时间
     *   key:序号
     *    id:循环中定义的元素变量
     * {wst:goods type='hot' cat='1' num='6'}{/wst:goods}
     */
    public function tagGoods($tag, $content){
    	$type   = $tag['type'];
    	$catId  = isset($tag['cat'])?$tag['cat']:0;
        $flag     = substr($catId, 0, 1);
        if (':' == $flag) {
            $catId = $this->autoBuildVar($catId);
            $parseStr .= '$_result=' . $catId . ';';
            $catId = '$_result';
        } else {
            $catId = $this->autoBuildVar($catId);
        }
    	
    	$id     = isset($tag['id'])?$tag['id']:'vo';
        $num    = isset($tag['num'])?(int)$tag['num']:0;
        $cache  = isset($tag['cache'])?$tag['cache']:0;
        $key    = isset($tag['key'])?$tag['key']:'key';
        $parse  = '<?php ';
        $parse .= '$wstTagGoods =  model("common/Tags")->listGoods("'.$type.'",'.$catId.','.$num.','.$cache.'); ';
        $parse .= 'foreach($wstTagGoods as $'.$key.'=>$'.$id.'){';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php } ?>';
        return $parse;
    }
    /**
     * 广告数据调用
     *   num:获取记录数量
     * cache:缓存时间
     *   key:序号
     *    id:循环中定义的元素变量
     * {wst:friendlink num='6'}{/wst:ads} 
     */
    public function tagFriendlink($tag, $content){
    	$id     = isset($tag['id'])?$tag['id']:'vo';
        $num    = isset($tag['num'])?(int)$tag['num']:99;
        $cache  = isset($tag['cache'])?$tag['cache']:0;
        $key    = isset($tag['key'])?$tag['key']:'key';
        $parse  = '<?php ';
        $parse .= '$wstTagFriendlink =  model("common/Tags")->listFriendlink('.$num.','.$cache.'); ';
        $parse .= 'foreach($wstTagFriendlink as $'.$key.'=>$'.$id.'){';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php } ?>';
        return $parse;
    }
    
    /**
     * 广告数据调用
     *  code:广告代码
     *   num:获取记录数量
     * cache:缓存时间
     *   key:序号
     *    id:循环中定义的元素变量
     * {wst:ads code='1' cat='1' num='6'}{/wst:ads} 
     */
    public function tagAds($tag, $content){
    	$code   = $tag['code'];
    	$id     = isset($tag['id'])?$tag['id']:'vo';
        $num    = isset($tag['num'])?(int)$tag['num']:99;
        $cache  = isset($tag['cache'])?$tag['cache']:0;
        $key    = isset($tag['key'])?$tag['key']:'key';
        $parse  = '<?php ';
        $parse .= '$wstTagAds =  model("common/Tags")->listAds("'.$code.'",'.$num.','.$cache.'); ';
        $parse .= 'foreach($wstTagAds as $'.$key.'=>$'.$id.'){';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php } ?>';
        return $parse;
    }
    
    /**
     * 文章数据调用
     *   cat:文章分类ID 或者 'new'
     *   num:获取记录数量
     * cache:缓存时间
     *   key:序号
     *    id:循环中定义的元素变量
     * {wst:article cat='1' num='6'}{/wst:article} 
     */
    public function tagArticle($tag, $content){
        $cat   = $tag['cat'];
    	$id     = isset($tag['id'])?$tag['id']:'vo';
        $num    = isset($tag['num'])?(int)$tag['num']:99;
        $cache  = isset($tag['cache'])?$tag['cache']:0;
        $key    = isset($tag['key'])?$tag['key']:'key';
        $parse  = '<?php ';
        $parse .= '$wstTagArticle =  model("common/Tags")->listArticle("'.$cat.'",'.$num.','.$cache.'); ';
        $parse .= 'foreach($wstTagArticle as $'.$key.'=>$'.$id.'){';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php } ?>';
        return $parse;
    }

    /**
     * 品牌数据调用
     *   cat:分类ID
     *   num:获取记录数量
     * cache:缓存时间
     *   key:序号
     *    id:循环中定义的元素变量
     * {wst:brand cat='1' num='6'}{/wst:brand} 
     */
    public function tagBrand($tag, $content){
        $cat   = $tag['cat'];
        $id     = isset($tag['id'])?$tag['id']:'vo';
        $num    = isset($tag['num'])?(int)$tag['num']:99;
        $cache  = isset($tag['cache'])?$tag['cache']:0;
        $key    = isset($tag['key'])?$tag['key']:'key';
        $parse  = '<?php ';
        $parse .= '$wstTagBrand =  model("common/Tags")->listBrand('.$cat.','.$num.','.$cache.'); ';
        $parse .= 'foreach($wstTagBrand as $'.$key.'=>$'.$id.'){';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php } ?>';
        return $parse;
    }

    /**
     * 店铺数据调用
     *   cat:分类ID
     *   num:获取记录数量
     * cache:缓存时间
     *   key:序号
     *    id:循环中定义的元素变量
     * {wst:shop cat='1' num='6'}{/wst:shop} 
     */
    public function tagShop($tag, $content){
        $cat    = $tag['cat'];
        $id     = isset($tag['id'])?$tag['id']:'vo';
        $num    = isset($tag['num'])?(int)$tag['num']:99;
        $cache  = isset($tag['cache'])?$tag['cache']:0;
        $key    = isset($tag['key'])?$tag['key']:'key';
        $parse  = '<?php ';
        $parse .= '$wstTagShop =  model("common/Tags")->listShop('.$cat.','.$num.','.$cache.'); ';
        $parse .= 'foreach($wstTagShop as $'.$key.'=>$'.$id.'){';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php } ?>';
        return $parse;
    }

    /**
     * 订单数据调用
     *  type:订单访问者类型，可选值为user或者shop
     *   num:获取记录数量
     * cache:缓存时间
     *   key:序号
     *    id:循环中定义的元素变量
     *fields:需要读取的订单字段
     * {wst:order type='user' ownId='1' num='6'}{/wst:order} 
     */
    public function tagOrder($tag, $content){
        $type   = isset($tag['type'])?$tag['type']:'';
        $id     = isset($tag['id'])?$tag['id']:'vo';
        $num    = isset($tag['num'])?(int)$tag['num']:99;
        $cache  = isset($tag['cache'])?$tag['cache']:0;
        $key    = isset($tag['key'])?$tag['key']:'key';
        $fields  = isset($tag['field'])?$tag['field']:'';
        $parse  = '<?php ';
        $parse .= '$wstTagOrder =  model("common/Tags")->listOrder("'.$type.'",'.$num.','.$cache.',"'.$fields.'"); ';
        $parse .= 'foreach($wstTagOrder as $'.$key.'=>$'.$id.'){';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php } ?>';
        return $parse;
    }
    
    /**
     * 搜索关键词数据调用
     *   type:0只获取关键词，1获取关键词和搜索关键词的搜索数
     *   cache:缓存时间
     *   key:序号
     *   id:循环中定义的元素变量
     * {wst:searchkey type='0' num='6'}{/wst:searchkey}
     */
    public function tagSearchkey($tag, $content){
    	$type   = $tag['type'];
    	$id     = isset($tag['id'])?$tag['id']:'vo';
    	$cache  = isset($tag['cache'])?$tag['cache']:0;
    	$key    = isset($tag['key'])?$tag['key']:'key';
    	$parse  = '<?php ';
    	$parse .= '$wstTagSearchkey =  model("common/Tags")->listSearchkey('.$type.','.$cache.'); ';
    	$parse .= 'foreach($wstTagSearchkey as $'.$key.'=>$'.$id.'){';
    	$parse .= '?>';
    	$parse .= $content;
    	$parse .= '<?php } ?>';
    	return $parse;
    }

    /**
     * 收藏商品/商家数据调用
     *  type:收藏类型，可选值为goods或者shop
     *   num:获取记录数量
     * cache:缓存时间
     *   key:序号
     *    id:循环中定义的元素变量
     *fields:需要读取的记录字段
     * {wst:favorite type='user' ownId='1' num='6'}{/wst:order} 
     */
    public function tagFavorite($tag, $content){
        $type   = isset($tag['type'])?$tag['type']:'';
        $id     = isset($tag['id'])?$tag['id']:'vo';
        $num    = isset($tag['num'])?(int)$tag['num']:99;
        $key    = isset($tag['key'])?$tag['key']:'key';
        $fields  = isset($tag['field'])?$tag['field']:'';
        $parse  = '<?php ';
        $parse .= '$wstTagFavorite =  model("common/Tags")->listFavorite("'.$type.'",'.$num.',"'.$fields.'"); ';
        $parse .= 'foreach($wstTagFavorite as $'.$key.'=>$'.$id.'){';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php } ?>';
        return $parse;
    }
    
    /**
     * 高评分商品数据调用
     *   cat:分类ID
     *   num:获取记录数量
     *   cache:缓存时间
     *   key:序号
     *   id:循环中定义的元素变量
     * {wst:score type='0' num='6'}{/wst:score}
     */
    public function tagScore($tag, $content){
    	$cat   = $tag['cat'];
    	$id     = isset($tag['id'])?$tag['id']:'vo';
    	$num    = isset($tag['num'])?(int)$tag['num']:99;
    	$cache  = isset($tag['cache'])?$tag['cache']:0;
    	$key    = isset($tag['key'])?$tag['key']:'key';
    	$parse  = '<?php ';
    	$parse .= '$wstTagScore =  model("common/Tags")->listScore('.$cat.','.$num.','.$cache.'); ';
    	$parse .= 'foreach($wstTagScore as $'.$key.'=>$'.$id.'){';
    	$parse .= '?>';
    	$parse .= $content;
    	$parse .= '<?php } ?>';
    	return $parse;
    }
    
    /**
     * 店铺分类数据调用
     *   cat:分类ID
     *   num:获取记录数量
     *   shopid:店铺id
     *   key:序号
     *   id:循环中定义的元素变量
     * {wst:shopscats cat='0' num='6'shopid='1'}{/wst:shopscats}
     */
    public function tagShopCats($tag, $content){
    	$cat    = isset($tag['cat'])?$tag['cat']:0;
    	$id     = isset($tag['id'])?$tag['id']:'vo';
    	$num    = isset($tag['num'])?(int)$tag['num']:99;
    	$key    = isset($tag['key'])?$tag['key']:'key';
    	$shopid  = isset($tag['shop'])?$tag['shop']:0;
        $cache  = isset($tag['cache'])?$tag['cache']:0;
    	$parse  = '<?php ';
    	$parse .= '$wstTagShopscats =  model("common/Tags")->listShopCats('.$cat.','.$num.','.$shopid.','.$cache.'); ';
    	$parse .= 'foreach($wstTagShopscats as $'.$key.'=>$'.$id.'){';
    	$parse .= '?>';
    	$parse .= $content;
    	$parse .= '<?php } ?>';
    	return $parse;
    }

     /**
     * 店铺商品数据调用    
     *  type:推荐/新品/热销/精品  - recom/new/hot/best
     *   cat:分类ID
     *   shop:店铺ID
     *   num:获取记录数量
     * cache:缓存时间
     *   key:序号
     *    id:循环中定义的元素变量
     * {wst:shopgoods name='hot' cat='1' num='6'}{/wst:goods}
     */
    public function tagShopGoods($tag, $content){
        $type   = $tag['type'];
        $shopId  = isset($tag['shop'])?$tag['shop']:0;
        $flag     = substr($shopId, 0, 1);
        if (':' == $flag) {
            $shopId = $this->autoBuildVar($shopId);
            $parseStr .= '$_result=' . $shopId . ';';
            $shopId = '$_result';
        } else {
            $shopId = $this->autoBuildVar($shopId);
        }
        $cat    = isset($tag['cat'])?$tag['cat']:0;
        $id     = isset($tag['id'])?$tag['id']:'vo';
        $num    = isset($tag['num'])?(int)$tag['num']:0;
        $cache  = isset($tag['cache'])?$tag['cache']:0;
        $key    = isset($tag['key'])?$tag['key']:'key';
        $parse  = '<?php ';
        $parse .= '$wstTagShopGoods =  model("common/Tags")->listShopGoods("'.$type.'",'.$shopId.','.$cat.','.$num.','.$cache.'); ';
        $parse .= 'foreach($wstTagShopGoods as $'.$key.'=>$'.$id.'){';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php } ?>';
        return $parse;

    }
}