<?php
/* 2017年2月27日 星期一
 * 个人计划
*/
namespace app\center\Logic;
use app\center\Logic\Controller;
use hyang\Util;
class Lfplan extends Controller{
    public function init(&$opts,$action){
        if($action == 'index'){
            $js = $opts['js'];
            $js[] = 'index/lfplan';
            $opts['js'] = $js;
        }elseif($action == 'edit'){
            $js = $opts['js'];
            $js[] = 'index/lfplan_edit';
            $opts['js'] = $js;
            $opts['require'] = ['tinymce','datetimepicker'];
        }
    }
    // 主页
    public function main()
    {
        $app = $this->app;
        $bstp = $app->bootstrap();
        $wh = $bstp->getSearchWhere('code');
        $lpmd = model('LifePlan');
        $count = $lpmd->where($wh)->count();
        $html = $bstp->GridSearchForm(['__cols__'=>['name'=>'计划','start_dt'=>'起始日期','end_dt'=>'结束日期','edittm'=>'创建时间','period'=>'计划周期','end_mk'=>'结束标识'],'ipts'=>'<input type="hidden" name="lfplan">']);
        $this->assign('searchfrom',$html);
        if($count > 0){
             $trs = $bstp->tableGrid('feek',[
                    'table'  => 'life_plan',
                    'dataid' => 'listno',
                    'cols'   => [
                        function($record){return '<a href="'.urlBuild('!.index/edit/lfplan/'.$record['listno']).'">'.$record['name'].'</a>';},
                        'period','start_dt','end_dt','edittm','end_mk',
                        function($record){
                            return '<a href="'.urlBuild('!.index/save/lfplan','?uid='.bsjson(['mode'=>'D','map'=>['listno'=>$record['listno']]])).'" class="delinker">删除</a>
                                    <a href="javascript:void(0);" class="plan_el_lnk">计划项</a>
                            ';
                        }
                    ]
                ],
                function($db) use($bstp,$wh){
                    $page = $bstp->page_decode();
                    return $db->where($wh)->order('edittm desc')->page($page,30)->select();
            });
            $pageBar = $bstp->pageBar($count);
            if($trs){
                $this->assign([
                    'trs' => $trs,
                    'trspageBar' => $pageBar,
                ]);
            }
        }

        return $this->fetch('lfplan');
    }
    // 编辑
    public function edit($view)
    {
        $this->viewInit($view);
        $editParam = [
            'navbar'    => '<li><a href="/conero/center.html?lfplan">个人计划</a></li>',
            'navActive' => '编辑'
        ];
        $data = [];
        $query = request()->param();
        $lfmd = model('LifePlan');
        // println($query);
        // 计划项
        if(isset($query['el'])){
            $editParam['navActive'] .= '计划项编辑';
            $plistno = $query['lfplan'];
            // $this->assign('data',$data); 
            $lpemd = model('LifePlanEl');
            $source = $lpemd->where('p_listno',$plistno)->select();
            $trs = '';
            $ctt = 1;
            foreach($source as $v){
                $trs .= '
                    <tr dataid="'.$ctt.'"><td class="rowno">'.$ctt.'<input type="hidden" name="listno" value="'.$v['listno'].'"></td>
                        <td><input type="text" name="name" class="form-control" value="'.$v['name'].'" required></td>
                        <td><input type="text" name="start_dt" value="'.$v['start_dt'].'" class="form-control"></td>
                        <td><input type="text" name="pend_dt" value="'.$v['pend_dt'].'" class="form-control"></td>
                        <td><input type="text" name="period" value="'.$v['period'].'" class="form-control"></td>
                    </tr>
                ';
                $ctt++;
            }
            $this->assign('trs',$trs);
            $pages = [];
            if($ctt == 1){
                $pData = $lfmd->get($plistno);
                if(isset($pData['start_dt']))
                    $this->assign('pages',['start_dt_md'=>$pData['start_dt']]);
            }            
            $this->editPageParam($editParam);
            $this->form($view,'lfplan_el');
            $this->app->_JsVar('actionUrl',urlBuild('!.index/save/lfplan'));
            return;
        }
        // 计划维护
        elseif(isset($query['lfplan'])){
            $listno = $query['lfplan'];
            $data = $lfmd->get($listno);
            $data['pk'] = '<input type="hidden" name="listno" value="'.$listno.'">';
            $data['addEllnk'] = urlBuild('!.index/edit/lfplan/'.$listno.'/el/new');
        }
        $this->assign('data',$data); 
        $this->editPageParam($editParam);
        $this->form($view);
    }
    // 数据保存
    public function save(){
        $app = $this->app;
        list($data,$mode,$map) = $app->_getSaveData('listno');
        // println($data,$mode,$map);die;
        if(isset($data['dataid']) && 'plan_el' == $data['dataid']){
            $this->elSaveHandler($data);return;
        }
        $lfmd = model('LifePlan');
        if('A' == $mode){
            $data = Util::dataClear($data,['start_dt','end_dt','period']);
            if(isset($data['start_dt']) && isset($data['end_dt']) && !isset($data['period'])) $data['period'] = getDays($data['end_dt'],$data['start_dt']);
            $data['user_code'] = uInfo('code');
            if($lfmd->save($data)) $this->success('数据新增成功！',urlBuild('!center:','?lfplan'));
            else $this->error('十分遗憾，数据新增失败');
        }
        elseif('M' == $mode){
            if($lfmd->where($map)->update($data)) $this->success('数据更新成功！',urlBuild('!center:','?lfplan'));
            else $this->error('十分遗憾，数据更新失败');
        }
        elseif('D' == $mode){
            $app->pushRptBack('life_plan',$map,true);
            if($lfmd->where($map)->delete()) $this->success('数据删除成功！',urlBuild('!center:','?lfplan'));
            else $this->error('十分遗憾，数据删除失败');
        }
        println($data,$mode,$map);
    }
    // 数据项数据维护
    private function elSaveHandler($data)
    {
        $app = $this->app;
        unset($data['dataid']);
        $plistno = $data['p_listno'];
        unset($data['p_listno']);
        $lfemd = model('LifePlanEl');
        $addCtt = 0;$edtCtt = 0; $delCtt = 0;
        foreach($data as $v){
            list($elData,$mode,$map) = $app->_getSaveData('listno',json_decode($v,true));
            $elData['p_listno'] = $plistno;
            // println($elData,$mode,$map);continue;
            if(isset($elData['type'])) $mode = 'D';
            if('A' == $mode){
                $elData = Util::dataClear($elData,['start_dt','pend_dt','period']);
                if($lfemd->insert($elData)) $addCtt += 1;
            }
            elseif('M' == $mode){
                $elData = Util::dataClear($elData,['start_dt','pend_dt','period']);
                if($lfemd->where($map)->update($elData)) $edtCtt += 1;
            }
            elseif('D' == $mode){
                $app->pushRptBack('life_plan_el',$map,true);
                if($lfemd->where($map)->delete()) $delCtt += 1;
            }
        }
        $tmp = [];
        if($addCtt > 0) $tmp[] = '新增数据'.$addCtt.'条';
        if($edtCtt > 0) $tmp[] = '修改数据'.$edtCtt.'条';
        if($delCtt > 0) $tmp[] = '删除数据'.$delCtt.'条';
        $ret = implode(',',$tmp).',数据总数为：'.count($data);
        $this->success($ret);
    }
    // ajax 请求
    public function ajax()
    {
        $app = $this->app;
        list($item,$data) = $app->_getAjaxData();
        switch($item){
            case 'get_plan_els';
                $lpmd = model('LifePlan');
                $cplan = $lpmd->get($data['dataid']);
                $elData = $cplan->el;
                $xhtml = '';
                foreach($elData as $v){
                    $xhtml .= '<li><span dataid="name">'.$v['name'].'</span> (@'.$v['start_dt'].' - '.(empty($v['end_dt'])? '未定':$v['end_dt']).')'.(empty($v['period'])? '':'['.$v['period'].']').' 
                        <span style="float:right;">
                        <a href="'.urlBuild('!index:textedit','?uid='.bsjson(['model'=>'LifePlanEl','dataid'=>'listno','title'=>'name','content'=>'el_log','get'=>$v['listno']])).'" target="_blank">编辑</a>
                        <a href="javascript:void(0);" onClick="LP.readElContent(\''.base64_encode($v['listno']).'\',this)">阅读</a>
                        </span>
                        </li>
                    ';
                }
                $xhtml = '<p><a href="'.urlBuild('!.index/edit/lfplan/'.$data['dataid'].'/el/new').'">进入编辑页面</a>，数据获取时间：'.sysdate().'</p><ol>'.$xhtml.'</ol>';      
                echo $xhtml;
                return;break;
            case 'get_plan_eltext':
                $lfemd = model('LifePlanEl');
                echo $lfemd->where('listno',base64_decode($data['dataid']))->value('el_log');
                return;break;
        }
        println($item,$data);
    }
}