<?php
namespace app\Server\Clan;
use hyang\Logic;
use hyang\Util;
use think\Db;
class Node extends Logic
{
    // 获取相关的请亲戚关系
    public function relationship($qData)
    {
        $xhtml = '';
        $gnode = model('Gnode');
        $data = is_array($qData)? $qData : $gnode->get($qData)->toArray();
        $sex = $data['sex'];     
        $pid = $data['pers_id'];
        $tmpArr = []; // 父辈
        $sameArr = []; // 同辈
        if(!empty($data['father'])){
            $subData = $this->childern($data['father'],$gnode);     
            foreach($subData as $v){
                $sameArr[] = $v['name'].($pid == $v['pers_id']? '(自己)':'');
            }       
            $bdata = $gnode->get($data['father']);
            if($bdata['father']){
                $subData = $this->childern($bdata['father'],$gnode);
                foreach($subData as $v){
                    $tmpArr[] = $v['name'].($v['pers_id'] == $data['father']? '(父)':'');
                }
            }
            else $tmpArr[] = $bdata['name'].'(父)';
        }
        if(!empty($data['mother'])){
            $bdata = $gnode->get($data['mother']);
            $tmpArr[] = $bdata['name'].'(母)';
        }
        if($tmpArr) $xhtml .= '<dt>父辈</dt><dd>'.implode(" ; ",$tmpArr).'</dd>';
        // 同辈        
        $sameStr = implode(" ; ",$sameArr);
        $sameStr = $sameStr? $sameStr : $data['name'].'(自己)';
        $xhtml .= '<dt>同辈</dt><dd>'.$sameStr.'</dd>';
        // 子辈
        $map = ($sex == 'M')? ['father'=>$pid]:['mother'=>$pid];
        $data = $gnode->where($map)->order('ser_no asc,birth_date desc,mtime desc')->select();
        $tmpArr = [];
        foreach ($data as $k => $v) {
            $tmpArr[] = $v['name'].'('.$v['sex'].')';
        }
        if(!empty($tmpArr)) $xhtml .= '<dt>子辈</dt><dd>'.implode(" ; ",$tmpArr).'</dd>';
        if($xhtml) $xhtml = '<dl class="dl-horizontal">'.$xhtml.'</dl>';
        return $xhtml;
    }
    // 获取父节点子节点
    public function childern($paretNo,$md=null,$sex='M',$callback=null)
    {
        if(empty($paretNo)) return;
        $md = $md? $md:model('Gnode');
        $map = $sex == 'F'? ['mother'=>$paretNo]:['father'=>$paretNo];
        $data = $md->where($map)->select();
        $xhtml = '';$tmpArr = [];
        if($callback instanceof \Closure){
            foreach($data as $v){
                $tmp = call_user_func($callback,$v);
                if(is_array($tmp)) $tmpArr[] = $tmp;
                elseif(!empty($tmp) && is_string($tmp)) $xhtml .= $tmp;
            }
        }
        if($xhtml || $tmpArr){
            return $xhtml? $xhtml:$tmpArr;
        }
        return $data;
    }
    // 获取 GoJs 所需要的数据
    // $pid 个人ID
    // { key: "1",              name: "Don Meow",   source: "cat1.png" }
    public function GoJsOption_exp($pid,&$option=null,$limit=100)
    {
        $option = $option? $option:[];
        $ndDb = model('Gnode');
        if(is_string($pid)){
            $qdt = $ndDb->get($pid);
        }
        else{
            $qdt = $pid;
            $pid = $qdt->pers_id;
        }
        if(empty($option)){
            $option[] = [
                'key'   => $pid,
                'name'  => $qdt->name
            ];
        }
        $map = $qdt->sex == 'M'? ['father'=>$pid]:['mother'=>$pid];
        $qdts = $ndDb->where($map)->select();
        foreach($qdts as $v){
            $curPid = $v->pers_id;
            /*
            if($qdt->sex == 'M' && !empty($qdt->mother)){
            }
            elseif($qdt->sex == 'F' && !empty($qdt->father)){
                $option[] = [];
            }
            */
            $option[] = [
                'key'   => $curPid,
                'parent'=> $pid,
                'name'  => $v->name
            ];
            // 限制数量
            if(count($option) <= $limit){
                $this->GoJsOption($v,$option);
            }
        }
        return $option;
    }
    // 获取 GoJs 所需要的数据
    // $pid 个人ID
    // { key: "1",              name: "Don Meow",   source: "cat1.png" }
    public function GoJsOption($pid,&$option=null,$limit=1000)
    {
        static $optionExistTack = [];
        $option = $option? $option:[];
        $ndDb = model('Gnode');
        if(is_string($pid)){
            $qdt = $ndDb->get($pid);
        }
        else{
            $qdt = $pid;
            $pid = $qdt->pers_id;
        }
        if(empty($option)){
            $tmp = [
                'key'   => $pid,
                'n'  => $qdt->name,
                's'  => $qdt->sex
            ];
            if(!empty($qdt->father)){
                $prtId = $qdt->father;
                $tmp['f'] = $prtId;
                $name = $ndDb->where('pers_id',$prtId)->value('name');
                $option[] = ['key'=>$prtId,'n'=>$name,'s'=>'M'];     // 其父节点
                // 兄弟节点
                $qdts = $ndDb->where('father = "'.$prtId.'" and pers_id <> "'.$pid.'"')->select();
                foreach($qdts as $v){
                    $option[] = ['key'=>$v->pers_id,'n'=>$v->name,'s'=>$v->sex,'f'=>$prtId];
                }
            }
            if(!empty($qdt->mother)){
                $tmp['m'] = $qdt->mother;
                $name = $ndDb->where('pers_id',$tmp['m'])->value('name');
                $option[] = ['key'=>$tmp['m'],'n'=>$name,'s'=>'F'];     // 其母节点
            }
            $option[] = $tmp;
        }
        $map = $qdt->sex == 'M'? ['father'=>$pid]:['mother'=>$pid];
        $qdts = $ndDb->where($map)->select();
        foreach($qdts as $v){
            $curPid = $v->pers_id;
            $tmp = [
                'key'   => $curPid,
                'n'  => $v->name,
                's'  => $v->sex
            ];
            if(!empty($v->father)){
                $prtId = $v->father;
                $tmp['f'] = $prtId;
                if($qdt->sex == 'F' && !isset($optionExistTack[$prtId])){
                    $name = $ndDb->where('pers_id',$prtId)->value('name');
                    $option[] = ['key'=>$prtId,'n'=>$name,'s'=>'M'];     // 其父节点
                    $optionExistTack[$prtId] = $name;
                }
            }
            if(!empty($v->mother)){
                $prtId = $v->mother;
                $tmp['m'] = $prtId;                
                if($qdt->sex == 'M' && !isset($optionExistTack[$prtId])){
                    $name = $ndDb->where('pers_id',$prtId)->value('name');
                    $option[] = ['key'=>$prtId,'n'=>$name,'s'=>'F'];     // 其母节点
                    $optionExistTack[$prtId] = $name;
                }
            }
            $option[] = $tmp;
            // 限制数量
            if(count($option) <= $limit){
                $this->GoJsOption($v,$option);
            }
        }
        return $option;
    }
    // 获取子女 $pid id/object 查询结果
    public function getChild($pid,$concat_sw=false)
    {
        $gndMd = model('Gnode');
        if(is_string($pid)){
            $data = $gndMd->get($pid)->toArray();
        }else{
            $data = is_array($pid)? $pid : (array)$pid;
            $pid = $data['pers_id'];
        }        
        if(empty($data)) return;
        $map = $data['sex'] == 'M'? ['father'=>$pid]:['mother'=>$pid];
        $field = 'concat(*<a href="*,*/conero/clan/node/edit/*,`gen_no`,*/pid/*,`pers_id`,*.html*,*">*,`name`,*(*,if(`sex`=*M*,*男*,*女*),*)</a>*)';
        $field = Util::strtrans($field);
        if($concat_sw) return $gndMd->concat_ws($map,$field,' , ');
        return $gndMd->where($map)->select();
    }
    // 获取到配偶
    public function getSpouse($pid,$concat_sw=false)
    {
        $gndMd = model('Gnode');
        if(is_string($pid)){
            $data = $gndMd->get($pid)->toArray();
        }else{
            $data = is_array($pid)? $pid : (array)$pid;
            $pid = $data['pers_id'];
        }
        $group = $data['sex'] == 'M'? 'mother':'father';
        $sql = 'select '.$group.' as `spouse` from gen_node where '.($data['sex'] == 'M'? 'father':'mother').'=? and '.$group.' is not null group by '.$group.' order by ser_no';
        $data = $gndMd->query($sql,[$pid]);
        $ids = [];
        if($data){            
            foreach($data as $v) $ids[] = $v['spouse'];
        }
        if($ids) $list = $gndMd->all($ids);
        else $list = null;
        if($concat_sw){
            if(empty($list)) return "";
            $tmpArr = [];
            foreach($list as $v){
                $tmpArr[] = '<a href="'.urlBuild('!.node/edit/'.$v['gen_no'].'/pid/'.$v['pers_id']).'">'.$v['name'].'</a>';
            }
            return implode(' , ',$tmpArr);
        }
        return $list;
    }
}