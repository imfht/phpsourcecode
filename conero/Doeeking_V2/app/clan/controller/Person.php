<?php
/* 2017年2月24日 星期五 家族中的人物 */
namespace app\clan\controller;
use app\common\controller\BasePage;
class Person extends BasePage
{
    public function index()
    {        
        $param = request()->param();
        $name = '人物';
        $pages = [];
        if(isset($param['pid'])){
            $logicNode = uLogic('Clan')->Node;
            $pid = $param['pid'];
            $ndMd = model('Gnode');
            $ndQuery = $ndMd->get($pid);
            $name = $ndQuery->name;
            // 获取 gojs 所需要的参数
            $goJsParam = $logicNode->GoJsOption($ndQuery);
            // debugOut($goJsParam);
            $this->_JsVar('_node',$goJsParam);
            $pages = $ndQuery->toArray();            
            $pages['clan_rlt_line'] = $logicNode->relationship($pages);
            $pages['clan_rlt_line'] = ($pages['clan_rlt_line']? '<h4>关系图</h4>':'').$pages['clan_rlt_line'];
            $gno = $pages['gen_no'];
            $pages['birth_date'] = empty($pages['birth_date'])? $pages['birthdesc']:$pages['birth_date'];
            $pages['end_date'] = empty($pages['end_date'])? $pages['diedesc']:$pages['end_date'];
            $pages['sex'] = $pages['sex'] == 'M'? '男':'女';
            unset($pages['birthdesc']);
            unset($pages['pers_id']);
            $pages['pid'] = $pid;
            $zibeiNo = $pages['zibei_no'];
            $qData = $ndMd->where(['gen_no'=>$gno,'zibei_no'=>$zibeiNo])->limit(100)->select(); 
            $xhtml = '';
            foreach($qData as $v){
                $url = isset($_GET['url'])? urlBuild('!.person/index/gno/'.$gno.'/pid/'.($v->pers_id),'?url='.$_GET['url']):urlBuild('!.person/index/gno/'.$gno.'/pid/'.($v->pers_id));
                $xhtml .= '<a href="'.$url.'" class="col-md-3'.($v->pers_id == $pid? ' text-danger':'').'">'.($v->name).'</a>';
            }      
            if($xhtml) $pages['same_zb_list'] = '<div class="row"><h4 class=" col-md-12">同字辈族人</h4>'.$xhtml.'</div>';
            if(isset($_GET['url'])) $pages['url'] = base64_decode($_GET['url']);
            
        }
        $this->loadScript([
            'title' => $name.' - 祖公源居 - Conero','bootstrap'=>true,'js'=>['person/index_godemo','person/index'],'require'=>['gojs']
        ]);
        $this->assign('pages',$pages);
        return $this->fetch();
    }
}