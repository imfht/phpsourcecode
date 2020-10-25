<?php
/* 2017年2月24日 星期五 家族人物 */
namespace app\clan\controller;
use think\Db;
use app\common\controller\BasePage;
use hyang\Util;
use hyang\Validate;
class Node extends BasePage
{
    use \app\clan\controller\ClanFnTra;
    // 首页
    public function index()
    {
        $this->loadScript([
            'title'=>'Conero-祖公源居','bootstrap'=>true,'js'=>['node/index']
        ]);
        $gno = getUrlBind('index');
        $bstp = $this->bootstrap($this->view);
        $wh = $bstp->getSearchWhere('code');
        $wh['gen_no'] = $gno;
        // println($wh);
        $gnode = model('Gnode');
        $count = $gnode->where($wh)->count();
        $bstp->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['name'=>'姓名','sex'=>'性别','birth_date'=>'生日','zibei'=>'字辈','mtime'=>'维护时间']]);
        $bstp->tableGrid(['__viewTr__'=>'trs'],['table'=>'gen_node','cols'=>[
                function($record){return '<a href="'.urlBuild('!.node/edit/'.$record['gen_no'].'/pid/'.$record['pers_id']).'">'.$record['name'].'</a>';},
                'zibei','sex',
                function($record){
                    return $record['birth_date']? $record['birth_date']:$record['birthdesc'];
                }
                ,'mtime',
                function($record) use($gno){
                    return '
                        <a href="'.urlBuild('!.person/index/gno/'.$gno.'/pid/'.$record['pers_id'],'?url='.base64_encode(urlBuild('!.node/index/'.$gno))).'" class="text-info">人物</a>
                        <a href="'.urlBuild('!.node/save','?uid='.bsjson(['mode'=>'D','gen_no'=>$record['gen_no'],'pers_id'=>$record['pers_id']])).'" class="text-danger dellink">删除</a>
                    ';
                }
            ]],function($db)use($wh,$bstp){
            $page = $bstp->page_decode();
            return $db->where($wh)->page($page,30)->order('mtime desc')->select();
        });
        $this->bootstrap($this->view)->pageBar($count);
        $pages = [
            'addUrl' => urlBuild('!.node/edit/'.$gno)
        ];
        $this->assign('pages',$pages);
        return $this->fetch();
    }
    // 编辑页面
    public function edit()
    {
        $gno = getUrlBind('edit');
        
        $data = ['mode'=>'A'];
        $title = $this->getCenterVar($gno,'gen_title');
        // var_dump($title);
        $pages = [
            'backAhref' => '<a href="'.urlBuild('!.node/index/'.$gno).'">'.$title.'</a>'
        ];
        $pid = getUrlBind('pid');
        $sex = '';$name = '';
        if($pid){
            $logicNode = uLogic('Clan')->Node;
            $gnd = model('Gnode');
            $data = $gnd->get($pid)->toArray();
            $genno = $data['gen_no'];
            $data['mode'] = 'M';
            unset($data['gen_no']);
            $data['pk'] = '<input type="hidden" name="pers_id" value="'.$data['pers_id'].'">';
            $formGrid = [];
            if(isset($data['father'])){
                $qData = $gnd->where('pers_id',$data['father'])->find()->toArray();
                $data['fatherdesc'] = $qData['name'];
                $data['father_label'] = '<a href="'.urlBuild('!.node/edit/'.$genno.'/pid/'.$qData['pers_id']).'" title="点击查看其父【'.$qData['name'].'】情况">父亲</a>';
                // 兄弟姐妹    
                $field = 'concat(*<a href="*,*/conero/clan/node/edit/'.$genno.'/pid/*,`pers_id`,*.html*,*">*,`name`,*(*,if(`sex`=*M*,*男*,*女*),*)</a>*)';
                $field = Util::strtrans($field);
                $bros = $gnd->concat_ws('gen_no=\''.$genno.'\' and father=\''.$data['father'].'\' and pers_id<>\''.$pid.'\'',$field,' , ');
                if($bros) $formGrid[] = ['label'=>'兄弟姐妹','value'=>$bros];
            }
            if(isset($data['mother']) && !empty($data['mother'])){
                $qData = $gnd->where('pers_id',$data['mother'])->find()->toArray();
                $data['motherdesc'] = $qData['name'];
                $data['mother_label'] = '<a href="'.urlBuild('!.node/edit/'.$genno.'/pid/'.$qData['pers_id']).'" title="点击查看其母【'.$qData['name'].'】情况">母亲</a>';
            }
            $sex = $data['sex'];            
            // 子女获取 - 用于显示
            $map = $sex == 'M'? ['father'=>$pid]:['mother'=>$pid];
            $chs = $logicNode->getChild($data,true);
            $chs = $chs? $chs:'未发现';
            $helptext = '<a href="javascript:void(0);" id="fset_child_lnk">快速设置其子女</a>，适用批量新增；或<a href="'.url('node/children','nid='.$pid).'">分组维护</a>，用于对同级排序.';            
            $formGrid[] = ['label'=>'子女','value'=>$chs,'helptext'=>$helptext];
            // 配偶
            $spouse = $logicNode->getSpouse($data,true);
            if($spouse) $formGrid[] = ['label'=>'配偶','value'=>$spouse];

            if(!empty($formGrid)) $data['aboutFormGrid'] = $this->bootstrap()->staticFormGrids($formGrid);
            $pages['personUrl'] = urlBuild('!.person/index/gno/'.$genno.'/pid/'.$pid,'?url='.base64_encode(urlBuild('!.node/edit/'.$genno.'/pid/'.$pid)));
            $name = $data['name'].' | ';
        }
        $data['sex'] = '
            <label> <input type="radio" name="sex" value="M"'.($sex != 'F'? ' checked':'').'> 男 </label>
            <label> <input type="radio" name="sex" value="F"'.($sex == 'F'? ' checked':'').'> 女 </label>
        ';
        $data['gen_no'] = '<input type="hidden" name="gen_no" value="'.$gno.'">';
        $this->loadScript([
            'title'=> $name.'祖公源居 - Conero','bootstrap'=>true,'js'=>['node/edit']
        ]);
        $this->assign('data',$data);
        $this->assign('pages',$pages);
        return $this->fetch();
    }
    // 按照子女排序
    public function children()
    {
        $this->loadScript([
            'title'=>'祖公源居 - Conero','bootstrap'=>true,'js'=>['node/children']
        ]);
        $nid = request()->param('nid');
        $gnode = model('Gnode'); $gcenter = model('Gcenter');
        $source = $gnode->get($nid);
        $pages = [];
        $genno = $source->gen_no;
        $pages['backAhref'] = '<a href="'.urlBuild('!.center/index/'.$genno).'">'.$gcenter->getTitle($genno).'</a>';
        $map = $source->sex == 'M'? ['father'=>$nid]:['mother'=>$nid];
        $data = $gnode->where($map)->order('ser_no')->select();
        $xhtml = '';
        $ctt = 1;
        foreach($data as $v){
            $xhtml .= '<tr dataid="'.$ctt.'"><td class="rowno">'.$ctt.' 
                    <input type="checkbox" class="rowselecter">
                    <input type="hidden" name="pers_id" value="'.$v['pers_id'].'">
                </td>
                <td><input type="text" name="ser_no" class="form-control input-sm" value="'.$v['ser_no'].'"></td>
                <td><input type="text" name="name" class="form-control input-sm" value="'.$v['name'].'"></td>
                <td><input type="text" name="sex" class="form-control input-sm" value="'.$v['sex'].'"></td>
                <td><input type="text" name="birth_date" class="form-control input-sm" value="'.$v['birth_date'].'"></td>
                <td><input type="text" name="end_date" class="form-control input-sm" value="'.$v['end_date'].'"></td>
                <td></td>
                </tr>';
            $ctt++;
        }
        if($xhtml) $pages['childrenList'] = $xhtml;
        $this->assign('pages',$pages);
        return $this->fetch();
    }
    // 多记录数据维护
    public function chid_save()
    {
        list($data) = $this->_getSaveData();
        // println($data);
        $ACtt = 0;$MCtt = 0;$DCtt = 0;
        $gnode = model('Gnode');
        $uInfo = uInfo();
        $insertPlus = null;
        foreach($data as $v){
            list($svdt,$type,$map) = $this->_getSaveDlist($v,['pk'=>'pers_id','clear'=>['birth_date','end_date']]);
            // println($svdt,$type,$map);continue;
            if($type == 'D'){
                $this->pushRptBack('gen_node',$map,true);
                if($gnode->delete($map['pers_id'])) $DCtt += 1;
            }
            elseif($type == 'M'){
                if($gnode->where($map)->update($svdt)) $MCtt += 1;
            }
            elseif($type == 'A'){
                if(empty($insertPlus)){
                    $insertPlus = [
                        'user_code' => $uInfo['code'],
                        'user_name' => $uInfo['nick']
                    ];
                }
                $svdt = array_merge($insertPlus,$svdt);
                if($gnode->insert($svdt)) $ACtt += 1;
            }            
        }
        $tmpArr = [];
        if($ACtt > 0) $tmpArr[] = '新增条数据【'.$ACtt.'】';
        if($MCtt > 0) $tmpArr[] = '修改条数据【'.$MCtt.'】';
        if($DCtt > 0) $tmpArr[] = '删除条数据【'.$DCtt.'】';
        $ret = '本次数据维护情况('.count($data).')：'.(count($tmpArr)>0? implode(',',$tmpArr):'操作失败！');
        $this->success($ret);
    }

    protected function _savedata(&$data)
    {
        $ndMd = model('Gnode');
        $retData = [
            'table' => $ndMd,
            'pk'    => 'pers_id'
        ];
        $data = Util::dataClear($data,['birth_date','father','mother','zibei_no','zibei','end_date','desc']);
        if(isset($data['birth_date'])) $data['birth_date'] = Util::unspace($data['birth_date']);
        if(isset($data['end_date'])) $data['end_date'] = Util::unspace($data['end_date']);
        if(isset($data['birth_date']) && Validate::isDate($data['birth_date']) == false){
            $data['birthdesc'] = $data['birth_date'];
            unset($data['birth_date']);
        }
        if(isset($data['end_date']) && Validate::isDate($data['end_date']) == false){
            $data['diedesc'] = $data['end_date'];
            unset($data['end_date']);
        }
        $mode = isset($data['mode'])? $data['mode']:'';
        if($mode == 'A'){
            // 同级顺序自动生成，根据信后顺序
            if(isset($data['father'])){
                $serNo = $ndMd->where('father',$data['father'])->max('ser_no');
                $serNo = $serNo? ($serNo + 1):1;
            }
            else $serNo = 1;
            $data['ser_no'] = $serNo;
            $uInfo = uInfo();
            $data['user_code'] = $uInfo['code'];
            $data['user_name'] = $uInfo['nick'];
            
            $retData['url'] = urlBuild('!.node/edit/'.$data['gen_no']);
        }
        elseif($mode == 'M'){
            $retData['url'] = urlBuild('!.node/edit/'.$data['gen_no'].'/pid/'.$data['pers_id']);
        }
        else $retData['url'] = urlBuild('!.node/index/'.$data['gen_no']);
        // println($data);die;        
        return $retData;
    }
    // 快速数据保存
    public function fimport()
    {
        list($data) = $this->_getSaveData();
        if($data){
            $successCtt = 0;$existCtt = 0;
            $text = $data['context'];
            $pid = $data['pid'];
            $ndMd = model('Gnode');
            $qData = $ndMd->get($pid);
            $baseData = $qData->sex == 'M'? ['father'=>$pid]:['mother'=>$pid];
            $serNo = $ndMd->where($baseData)->max('ser_no');
            $serNo = ($serNo? $serNo : 0) + 1;            
            $baseData['gen_no'] = $qData->gen_no;
            $uInfo = uInfo();
            $baseData['user_code'] = $uInfo['code'];
            $baseData['user_name'] = $uInfo['nick'];            
            if(substr_count($text,';')) $source = explode(';',$text);
            else $source = explode("\n",$text);
            foreach($source as $v){
                $v = Util::unspace($v);
                if(empty($v)) continue;                
                $node = $baseData;
                $node['name'] = $v;
                if(substr_count($v,'+') == 0) $node['sex'] = 'M';
                else{
                    $sex = substr($v,strpos($v,'+')+1);
                    if($sex == '女') $sex = 'F';
                    else $sex = 'M';
                    $node['sex'] = $sex;
                    $node['name'] = substr($v,0,strpos($v,'+'));
                }
                
                $count = $ndMd->where(array_intersect_key($node,['father'=>'','gen_no'=>'','name'=>'']))->count();
                if($count > 0){
                    $existCtt += 1;
                    continue;
                }
                $node['ser_no'] = $serNo;
                $serNo += 1;
                // println($node);continue;
                if($ndMd->insert($node)) $successCtt += 1;                
            }
            if($successCtt > 0) $this->success('本次新增['.$successCtt.'/'.count($source).']条数据记录。'.($existCtt? '本次因为数据已经存在，('.$existCtt.')条记录拒绝新增！':''));
            $this->error('本次数据更新失败！');
        }
        $this->error('请求地址无效！');
    }
    public function ajax()
    {
        list($item,$data) = $this->_getAjaxData();
        switch($item){
            case 'get_zibei_4_nameLister':
                $zibei = model('Gzibei')->where($data)->value('zibei_no');
                return $zibei? $zibei : -1;
                break;
            case 'get_relation':
                $pid = $data['pid'];
                $gnode = model('Gnode');
                $data = $gnode->get($pid);
                $xhtml = "";
                if($data){       
                    $sex = $data['sex'];     
                    // 祖辈
                    $tmpArr = [];
                    if(!empty($data['father'])){
                        $bdata = $gnode->get($data['father']);
                        $tmpArr[] = $bdata['name'].'('.($sex == 'M'? '祖父':'外祖父').')';
                    }
                    if(!empty($data['mother'])){
                        $bdata = $gnode->get($data['mother']);
                        $tmpArr[] = $bdata['name'].'('.($sex == 'M'? '祖母':'外祖母').')';
                    }
                    if($tmpArr) $xhtml .= '<dt>祖父辈</dt><dd>'.implode(" ; ",$tmpArr).'</dd>';
                    // 父辈        
                    $xhtml .= '<dt>'.(($sex == 'M')? '父亲':'母亲').'</dt><dd>'.$data['name'].'</dd>';
                    // 同辈
                    $map = ($sex == 'M')? ['father'=>$pid]:['mother'=>$pid];
                    $data = $gnode->where($map)->order('ser_no asc,birth_date desc,mtime desc')->select();
                    $tmpArr = [];
                    foreach ($data as $k => $v) {
                        $tmpArr[] = $v['name'].'('.$v['sex'].')';
                    }
                    if(!empty($tmpArr)) $xhtml .= '<dt>兄弟姊妹</dt><dd>'.implode(" ; ",$tmpArr).'</dd>';
                    if($xhtml) $xhtml = '<dl class="dl-horizontal">'.$xhtml.'</dl>';
                }
                // return $data;
                echo $xhtml;die;
                break;
            case 'parents_insert':
                $pid = isset($data['pid'])? $data['pid']:null;
                $node = array_intersect_key($data,['sex'=>'','name'=>'','gen_no'=>'']);
                $uInfo = uInfo();
                $node['user_code'] = $uInfo['code'];
                $node['user_name'] = $uInfo['nick'];
                $gnode = model('Gnode');
                $parentId = $gnode->insertGetId($node);
                $json = [];
                if($parentId){
                    if($pid){
                        $save = $data['sex'] == 'M'? ['father'=>$parentId]:['mother'=>$parentId];
                        $cdb = $gnode->get($pid);
                        if($cdb->update($save,['pers_id'=>$pid])) $json = ['error'=>0,'msg'=>'数据保存写入成功！','data'=>$parentId];
                        else $json = ['error'=> -1,'msg'=>'人物绑定时出错！',$parentId];
                    }
                    else $json = ['error'=>0,'msg'=>'数据保存写入成功！','data'=>$parentId];
                }
                else $json = ['error'=>-1,'msg'=>'数据保存失败！','data'=>-1];
                return json($json);
                break;
            case 'relieve_request':     // 关系解除
                $successMk = false;
                // 启动事务
                Db::startTrans();
                try{
                    $gnode = model('Gnode');
                    $pid = $data['pid'];
                    $qObj = $gnode->get($pid);
                    $refid = $data['type'] == 'F'? $qObj->father : $qObj->mother;
                    if(isset($data['refid']) && $data['refid'] == $refid){
                        $svparam = $data['type'] == 'F'? ['father'=>null] : ['mother'=>null];
                        if($gnode->save($svparam,['pers_id'=>$pid])){
                            $ret = ['code'=>0,'msg'=>'关系解除成功'];
                            // 提交事务
                            Db::commit();
                            $successMk = true;
                        }
                        else $ret = ['code'=>-1,'msg'=>'关系解除失败'];
                    }
                    else $ret = ['code'=>-1,'msg'=>'请求参数无效'];
                }catch(\ Exception $e){
                    $ret = ['code'=>-1,'msg'=>'关系解除中断，原因未明'];
                    debugOut($e->getTraceAsString());
                }
                // 回滚事务
                if($successMk === false) Db::rollback();
                return json($ret);
                break;
        }
    }
}