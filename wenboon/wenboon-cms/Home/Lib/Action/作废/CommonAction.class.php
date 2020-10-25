<?php
class CommonAction extends Action {
    Public function _initialize()
    {
        $cid=I('cid');
        $id=I('id');
        $type['id']=0;
        //站点信息
        $site=M('site')->find(1);
        //所有分类
        if(!empty($cid))
        {
            $type=M('category')->find($cid);
            $type['url']=U($type['module'].'/'.$type['list'],array('cid'=>$cid));
            if($type['thumb']==null){
                $pn=M('category')->find($type['pid']);
                $type['thumb']=$pn['thumb'];
            }
        }
        if(!empty($id))
        {
            switch (strtolower($Think.MODULE_NAME))
            {
            case 'article':
              $body=D('Article')->relation(true)->find($id);
              break;  
            case 'commod':
              $body=D('Commodity')->relation(true)->find($id);
              break;
            default:
            }
            $type=M('category')->find($body['tid']);
            $type['url']=U($type['module'].'/'.$type['list'],array('cid'=>$cid));
        }
        
        $postion[]=array();
        $tem=M('category')->find($type['pid']);
        if(!empty($tem)){
            $postion[0]=$tem;
            $postion[1]=$type;
        }
        else{
            $postion[0]=$type;
        }
            
        $this->body=$body;
        $this->type=$type;
        $this->site=$site;
        $this->postion=$postion; 
    }
}
?>