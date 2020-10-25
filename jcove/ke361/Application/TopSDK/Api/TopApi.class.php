<?php
namespace TopSDK\Api;

class TopApi
{
    private $appkey ;
    private $secretkey;
    /**
     * $error  -1001 
     * @var unknown
     */
    private $error ='';
    public function __construct($appkey,$secret){
       
        $this->c = new TopClient();
        $this->c->appkey = $appkey;
        $this->c->secretKey = $secret;
        if(empty($this->c->appkey)){
            $this->error.='appkey不能为空';
        }
        if(empty($this->c->secretKey)){
            $this->error.='secretkey不能为空';
        }
        
    }
    
    public function getItemList($q,$cat = '', $isTmall = FALSE, $startPrice ='', $endPrice ='',$startTkRate = '', $endTkRate ='' ,$sort ='tk_rate_des', $pageNO =1, $pageSize = 30,$platForm = 1 , $loc=''){
        if(empty($q) && empty($cat)){
            $this->error .= "查询词和分类id不能同为空";
            return false;     
        }
        $req = new TbkItemGetRequest;
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,click_url,volume");
        if(!empty($q)){
            $req->setQ($q);
        }
        if(!empty($cat)){
            $req->setCat($cat);
        }
        if(!empty($loc)){
            $req->setItemloc($loc);
        }
        $req->setSort($sort);
       
        $req->setIsOverseas("false");
        if(!empty($startPrice)){
            $req->setStartPrice($startPrice);
        }
        if(!empty($endPrice)){
            $req->setEndPrice($endPrice);
        }
        if(!empty($startTkRate)){
            $req->setStartTkRate($startTkRate);
        }
        if(!empty($endTkRate)){
             $req->setEndTkRate($endTkRate);
        }
        
        
       
        
        $req->setPageNo($pageNO);
        $req->setPageSize($pageSize);
       
    
        $goodsList = '';
        $resp = $this->c->execute($req);
   
        if(!empty($resp->results->n_tbk_item)){
            $items = $resp->results->n_tbk_item;
            foreach ($items as $row){
                $goods['item_url'] = $row->item_url;
                $goods['pic_url']  = $row->pict_url;
                $goods['market_price']  = $row->reserve_price;
                $goods['price'] = $row->zk_final_price;
                if($goods['price'] ==0){
                    $goods['price'] = $goods['market_price'];
                }
                $goods['click_url'] = $row->click_url;
                $goods['name']     = $row->title;
                $goods['item_url']  = $row->item_url;
                $goods['num_iid']   = $row->num_iid;
                $goods['volume']   = $row->volume;
              
                $goodsList[] = $goods;
            }
        }
        if(isset($resp->code)){
                $this->error.=$resp->code.':'.$resp->msg;
                return false;
        }
     
        return $goodsList;
    }
    public function getItemLists($q,$cat = '', $isTmall = FALSE, $startPrice ='', $endPrice ='',$startTkRate = '', $endTkRate ='' ,$sort ='tk_rate_des', $pageNO =1, $pageSize = 30,$platForm = 1 , $loc=''){
        if(empty($q) && empty($cat)){
            $this->error .= "查询词和分类id不能同为空";
            return false;
        }
        $req = new TbkItemsGetRequest();
        $req->setFields("num_iid,seller_id,nick,title,price,volume,pic_url,item_url,shop_url");
        if(!empty($q)){
            $req->setKeyword($q);
        }
        if(!empty($cat)){
            $req->setCid($cat);
        }
        if(!empty($loc)){
            $req->setArea($loc);
        }
        $req->setSort($sort);
        $req->setMallItem($isTmall);
        $req->setOverseasItem("false");
        if(!empty($startPrice)){
            $req->setStartPrice($startPrice);
        }
        if(!empty($endPrice)){
            $req->setEndPrice($endPrice);
        }
        if(!empty($startTkRate)){
            $req->setStartCommissionRate($startTkRate);
        }
        if(!empty($endTkRate)){
            $req->setEndCommissionRate($endTkRate);
        }
    
    
         
      
        $req->setPageNo($pageNO);
        $req->setPageSize($pageSize);
         
    
        $goodsList = '';
        $resp = $this->c->execute($req);
         var_dump($resp);
        if(!empty($resp->tbk_items->tbk_item)){
            $items = $resp->tbk_items->tbk_item;
            foreach ($items as $row){
           
                $goods['item_url'] = $row->item_url;
                $goods['pic_url']  = $row->pic_url;
                $goods['market_price']  = $row->price;
                $goods['price'] = $row->discount_price;
                if($goods['price'] ==0){
                    $goods['price'] = $goods['market_price'];
                }
                $goods['click_url'] = $row->click_url;
                $goods['name']     = $row->title;
                $goods['num_iid']   = $row->num_iid;
                $goods['volume']   = $row->volume;
                if($isTmall){
                    $goods['goods_type'] = 'tmall';
                }else{
                    $goods['goods_type'] = 'taobao';
                }
                $goodsList[] = $goods;
        
            }
        }
         
        return $goodsList;
    }
    public function getItemInfo($num_iid){
        if(empty($num_iid) ){
            $this->error='非法的num_iid';
            return false;
        }
        $req = new TbkItemInfoGetRequest();
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,volume");
        $req->setNumIids($num_iid);
        $resp = $this->c->execute($req);
        if(!empty($resp->results->n_tbk_item)){
            $items = $resp->results->n_tbk_item;
            foreach ($items as $row){
                $goods['item_url'] = $row->item_url;
                $goods['pic_url']  = $row->pict_url;
                $goods['market_price']  = $row->reserve_price;
                $goods['price'] = $row->zk_final_price;
                if($goods['price'] ==0){
                    $goods['price'] = $goods['market_price'];
                }
                $goods['click_url'] = $row->click_url;
                $goods['title']     = $row->title;
                $goods['num_iid']   = $row->num_iid;
                $goods['volume']   = $row->volume;
                $goodsList[] = $goods;
            }
        }else{
            if(isset($resp->code)){
                $this->error.=$resp->code.':'.$resp->msg;
                return false;
            }
            
        }
        return $goodsList;
    }
    public function getItemsInfo($num_iid){
        if(empty($num_iid) ){
            $this->error='非法的num_iid';
            return false;
        }
        $req = new TbkItemsDetailGetRequest();
        $req->setFields("num_iid,seller_id,nick,title,price,volume,pic_url,item_url,shop_url");
        $req->setNumIids($num_iid);
        $resp = $this->c->execute($req);

        if(!empty($resp->tbk_items->tbk_item)){
            $items = $resp->tbk_items->tbk_item;
            foreach ($items as $row){
                $goods['item_url'] = $row->item_url;
                $goods['pic_url']  = $row->pic_url;
                $goods['market_price']  = $row->price;
                $goods['price'] = $row->discount_price;
                if(empty($goods['price'])||$goods['price'] ==0){
                    $goods['price'] = $goods['market_price'];
                }
                $goods['click_url'] = $row->click_url;
                $goods['name']     = $row->title;
                $goods['num_iid']   = $row->num_iid;
                $goods['volume']   = $row->volume;
                $goods['nick']   = $row->nick;

                $goodsList[] = $goods;
            }
        }else{
            if(isset($resp->code)){
                $this->error.=$resp->code.':'.$resp->msg;
                return false;
            }
    
        }
        return $goodsList;
    }
    public function error(){
        return $this->error;
    }

    public function getTpwd($url,$text,$image){
        $req                                        =   new WirelessShareTpwdCreateRequest();
        $tpwd_param = new IsvTpwdInfo;
        $tpwd_param->ext="{\"xx\":\"xx\"}";
        $tpwd_param->logo=$image;
        $tpwd_param->text=$text;
        $tpwd_param->url=$url;
        $tpwd_param->user_id=24234234234;
        $req->setTpwdParam(json_encode($tpwd_param));
        $resp = $this->c->execute($req);
        if(!empty($resp->model)){
            return $resp->model;
        }else{
            if(isset($resp->code)){
                $this->error.=$resp->code.':'.$resp->msg;
                return false;
            }
        }
    }
    
}

?>