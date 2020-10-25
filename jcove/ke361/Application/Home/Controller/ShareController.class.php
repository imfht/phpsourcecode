<?php
namespace Home\Controller;

use Home\Controller\HomeController;

class ShareController extends HomeController
{
    public function share(){       
        $shareId    = I('get.share_id');
        $type       = I('get.type');     
        $id         = I('get.id');
        if($type=='article'){
            $info = D('Document')->field('id,title,cover_id')->where(array('id'=>$id))->find();
            $siteUrl    = U('/article/'.$info['id'],'',true,true);
            $pic        = C('WEB_SITE_URL').get_cover($info['cover_id'],'path');
            $siteTitle  = $info['title'];
            D('Document')->where(array('id'=>$id))->setInc('share_count',1);
        }
        if($type=='goods'){
            $info = D('Goods')->field('id,name,pic_url')->where(array('id'=>$id))->find();
            $siteUrl    = U('/goods/'.$info['id'],'',true,true);
            $pic        =C('WEB_SITE_URL'). get_image_url($info['cover_id']);
            $siteTitle  = $info['name'];
            D('Goods')->where(array('id'=>$id))->setInc('share_count',1);
        }
       
        $appkey     ='';
        if($shareId=='tsina'){
            $appkey = $config['sinakey'];
        }    
        $requestUrl = "http://www.jiathis.com/send/?webid=".$shareId.
        "&url=".urlencode($siteUrl).
        "&title=".$siteTitle.
        "&uid=".$uid;
        if(!empty($appkey)){
            $requestUrl.="&appkey=".$appkey;
        }
        if(!empty($pic)){
            $requestUrl.="&pic=".$pic;
        }
        dump($pic);
        dump($siteUrl);
        redirect($requestUrl);
    }
}

?>