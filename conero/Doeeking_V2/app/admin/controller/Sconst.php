<?php
namespace app\admin\controller;
use think\Controller;
class Sconst extends controller{
    public function _initialize(){
        if($this->_initTplCheck(['save'])) return;
        $action = request()->action();
        $this->loadScript([
            'auth'=>'DEV','title'=>'Conero-系统常量','js'=>['Sconst/'.$action],'css'=>['Sconst/'.$action],'bootstrap'=>true
        ]);
    }    
    public function index()
    {        
        $bstp = $this->bootstrap($this->view);
        $wh = $bstp->getSearchWhere();
        $wh['user_name'] ='CONST';
        $bstp->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['gover_name'=>'代码','gover_value'=>'名称','plus_name'=>'值','plus_desc'=>'描述','gover_desc'=>'使用范围','remark'=>'备注','sys_date'=>'编辑日期']]);
        $count = $wh? $this->croDb('sys_site')->where($wh)->count() : $this->croDb('sys_site')->count();        
        $bstp->tableGrid(['__viewTr__'=>'trs'],
            ['table'=>'sys_site',
                'cols'=>['gover_name',function($record){return '<a href="/conero/admin/sconst/edit.html?uid='.bsjson(['mode'=>'M','sysid'=>$record['sys_id']]).'" title="点击查看详情">'.$record['gover_value'].'</a>';}
                    ,'plus_name','plus_desc','gover_desc','remark','sys_date']],
                function($db){
                    $bstp = $this->bootstrap();
                    $page = $bstp->page_decode();
                    $wh = $bstp->getSearchWhere();
                    $wh['user_name'] ='CONST';
                    return $db->page($page,30)->where($wh)->order('gover_name')->select();
        });
        $bstp->pageBar($count);
        $this->assign('page',[
            'addUrl' => '/conero/admin/sconst/edit.html?uid='.bsjson(['mode'=>'A','mtime'=>sysdate('date')])    // 新增跳转按钮
        ]);
        return $this->fetch();
    }
    // 编辑页面 - 预留 删除编辑状态
    public function edit()
    {
        $args = isset($_GET['uid'])? bsjson($_GET['uid']):array();
        $page = [];
        if(isset($args['mode'])) $page['mode'] = $args['mode'];
        $page['descrip'] = isset($args['mode'])? ($args['mode'] == 'A'? '新增':($args['mode'] == 'M'? '编辑':'删除')):'新增';
        $page['other'] = '
            <li><a href="#fastsearch" tabindex="-1" id="fastsearch-tab" role="tab" data-toggle="tab" aria-controls="fastsearch" aria-expanded="true">快速查找</a></li>        
        ';
        if(isset($args['mode']) && $args['mode'] == 'M' && isset($args['sysid'])){
            $surce = $this->croDb('sys_site')->where('sys_id',$args['sysid'])->field('gover_name,gover_value')->find();
            $goverName = $surce['gover_name'];
            $page['gover_name'] = $goverName;
            $page['gover_value'] = $surce['gover_value'];
            $recordList = '';
            $map = ['gover_name'=>$goverName,'user_name'=>'CONST'];
            $record = $this->croDb('sys_site')->where($map)->select();
            $i = 1;
            foreach($record as $v){
                $recordList .= '
                    <tr dataid="'.$i.'"><td class="rowno">'.$i.'</td>
                        <td><input type="hidden" value="'.$v['sys_id'].'" name="sys_id"><input type="text" name="plus_name" class="form-control" value="'.$v['plus_name'].'" readonly="true" required></td>    <td><input type="text" name="plus_desc" class="form-control" value="'.$v['plus_desc'].'" required></td>
                        <td><input type="text" name="plus_value" class="form-control" value="'.$v['plus_value'].'"></td>
                        <td><input type="text" name="remark" class="form-control" value="'.$v['remark'].'" ></td>
                    </tr>
                ';
                $i++;
            }
            if($recordList) $page['recordList'] = $recordList;
            // 其他操作 
            // data-toggle="tab" 作为调整页面处理  <a href="#dropdown1" tabindex="-1" role="tab" id="dropdown1-tab" data-toggle="tab" aria-controls="dropdown1"
            $page['other'] = '
                <li><a href="#delconst" tabindex="-1" id="delconst-tab" role="tab" data-toggle="tab" aria-controls="delconst" aria-expanded="true">删除该常量</a></li>
                <li><a href="/conero/admin/sconst/edit.html?uid='.bsjson(['mode'=>'A','mtime'=>sysdate('date')]).'" tabindex="-1" role="tab" id="dropdown2-tab" aria-controls="dropdown2">新增</a></li>
            ';
            //println($recordList);
        }
        $this->assign('page',$page);
        if(isset($args['mode'])) $this->_JsVar('mode',$args['mode']);
        return $this->fetch();
    }
    public function save()
    {
        $data = $_POST;
        $tb = 'sys_site';
        $data = count($data) == 0? $_GET:$data;
        if(isset($data['uid'])) $data = bsjson($data['uid']);
        $mode = isset($data['mode'])? $data['mode'] : null;
        $ret = '';
        // 新增操作
        if($mode == 'A'){
            $dtl = json_decode($data['dtl'],true);
            $sumy = json_decode($data['sumy'],true);
            $count = count($sumy);$i = 0;
            foreach($sumy as $v){
                $value = array_merge($v,$dtl);
                $value['user_code'] = uInfo('code');
                $value['user_name'] = 'CONST';
                $value['user_ip'] = request()->ip();                
                //println($value);
                if($this->croDb($tb)->insert($value)){$i = $i + 1;}
            }
            
            $ret = '本次数据新增过程中，数据维护情况成功/总数如: 【'.$i.'/'.$count.'】';
            $this->success($ret);
        }
        // 删
        elseif($mode == 'D'){
            $map = ['gover_name'=>$data['gover_name'],'user_name'=>'CONST'];
            $this->pushRptBack($tb,$map,true);
            if($this->croDb($tb)->where($map)->delete()){
                return $this->success('本次数据删除成功！','sconst/index');
            }
        }
        // 改
        elseif($mode == 'M'){
            $dtl = json_decode($data['dtl'],true);
            $sumy = json_decode($data['sumy'],true);
            $cArr = [];$count = count($sumy);
            foreach($sumy as $v){
                $type = isset($v['type'])? $v['type']:(isset($v['sys_id']) && !empty($v['sys_id'])? 'M':null);            
                if($type == 'D'){       // 删除
                    $this->pushRptBack($tb,['sys_id'=>$v['sys_id']],true);
                    if($this->croDb($tb)->where('sys_id',$v['sys_id'])->delete()) $cArr['d'] = isset($cArr['d'])? ($cArr['d'] + 1) : 1;
                }
                elseif($type == 'M'){   // 修改
                    if($this->croDb($tb)->where('sys_id',$v['sys_id'])->update($v)) $cArr['m'] = isset($cArr['m'])? ($cArr['m'] + 1) : 1;
                }
                else{   // 新增
                    $value = array_merge($v,$dtl);
                    $value['user_code'] = uInfo('code');
                    $value['user_name'] = 'CONST';
                    $value['user_ip'] = request()->ip();                
                    //println($value);
                    if($this->croDb($tb)->insert($value)) $cArr['a'] = isset($cArr['a'])? ($cArr['a'] + 1) : 1;
                }
            }
            if($cArr){
                $tmpArr = [];
                foreach($cArr as $k=>$v){
                    if($k == 'm') $tmpArr[] = '修改数据【'.$v.'/'.$count.'】条';
                    elseif($k == 'd') $tmpArr[] = '删除数据【'.$v.'/'.$count.'】条';
                    elseif($k == 'a') $tmpArr[] = '新增数据【'.$v.'/'.$count.'】条';
                }
                $ret = '本次编辑数据情况: '.implode(',',$tmpArr);
                $this->success($ret,'sconst/index');
            }
        }
        println($data);
    }
}