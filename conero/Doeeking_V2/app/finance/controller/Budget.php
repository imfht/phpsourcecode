<?php
namespace app\finance\controller;
use think\Controller;
use app\Server\Finance;
class Budget extends Controller
{
    public function index()
    {
        $this->loadScript([
            'auth'=>'','title'=>'Conero-财物计划','js'=>['Budget/index'],'css'=>['Budget/index']
        ]);
        $this->useFrontFk('bootstrap');
        $this->assign([
            'budno'       => $this->_indexNav(),
            'content'     => ''
        ]);
        // 例行财务 --------------------------------------------------------------
        $pages = [];
        $cid = uInfo('cid');
            // * 例行财务列表
        $regular = $this->bootstrap()->tableGrid('feek',['table'=>'finc_budget','cols'=>[function($record){return '<a href="/conero/finance/budget/regulars/'.$record['bud_id'].'.html">'.$record['name'].'</a>';},'from_date','related_fn','descrip','createtm']],function($db) use($cid){
                $wh = ['bud_no'=>'regulars','center_id'=>$cid];
                return $db->page(1,30)->where($wh)->order('createtm desc')->select();
        });
        if($regular) $pages['regularTrs'] = $regular;
            // * 例行财务出单           
        $trs = fcMkregularRecord();
        if($trs) $pages['rlneedstr'] = $trs ;        
        if($pages) $this->assign('pages',$pages);
        // println($pages);
        return $this->fetch('index');
    }
    private function _indexNav()
    {
        $html = '';
        $navList = function($arr,$type=null){
            $ctt = "";            
            $cur = $type? budNoRec($type):null;
            $rec = $cur? true:false;
            foreach($arr as $v){
                $ctt .= '<li><a href="">'.$v['finctype'].'</a></li>';
                if($cur == $v['finctype']){
                    $rec = false;
                }
            }            
            if($rec) $ctt = '<li><a href="" class="recommand" title="点击生成">'.$cur.'</a></li>'.$ctt;
            //if($type == 's') debugOut([$rec,$ctt],true);
            if($ctt){                
                $ctt = '<ol class="list-unstyled">'.$ctt.'</ol>';
            }
            return $ctt;
        };
        //年度
        $year = $this->_query('call fbudget_type_sp(?,?,?,?)',[uInfo('cid'),'y','finc_budget','null']);
        $html .= '<div class="sidebar-module sidebar-module-inset"><h4>年度计划</h4>';
        if($year){}
        else $html .= '<p>'.date('Y').'<a href="">点击生成</a></p>';
        $html .= '</div>';
        // 季度
        $quarter = $this->_query('call fbudget_type_sp(?,?,?,?)',[uInfo('cid'),'s','finc_budget','sys']);
        $html .= '<div class="sidebar-module"><h4>季度计划</h4>';
        $html .= $navList($quarter,'s');
        $html .= '</div>';
        ///debugOut($quarter);
        // 月度
        $month = $this->_query('call fbudget_type_sp(?,?,?,?)',[uInfo('cid'),'m','finc_budget','sys']);
        $html .= '<div class="sidebar-module"><h4>月计划</h4>';
        if($month){$html .= $navList($month,'m');}
        $html .= '</div>';
        return $html;
    }
    // 编辑
    public function edit()
    {
        $type = getUrlBind('edit');
        // println($type);
        $this->loadScript([
            'auth'=>'','title'=>'Conero-财物计划','bootstrap'=>true,'require'=>['datetimepicker'],'js'=>['Budget/edit']
        ]);
        $pages = [];
        if($type){
            $pages['formid'] = $type;
            if($type == 'newrl'){
                $pages['navDesc'] = '例行财务编辑';
                $this->_JsVar('cid',uInfo('cid'));                
            }
            $budid = getUrlBind($type);
            if($budid){
                $data = $this->croDb('finc_budget')->where('bud_id',$budid)->find();
                if($data){
                    $data['pkHelper'] = '<input type="hidden" name="bud_id" value="'.$data['bud_id'].'"><input type="hidden" name="mode" value="M">';
                    $this->assign('data',$data);
                }
            }
            $this->_JsVar('formid',$type);            
        }
        if($pages) $this->assign('pages',$pages);
        return $this->fetch();
    }
    // 财务例行
    public function regulars(){        
        $this->loadScript([
            'auth'=>'','title'=>'Conero-财物计划','bootstrap'=>true,'js'=>['Budget/regulars'],'require'=>['echart']
        ]);
        $budid = getUrlBind('regulars');
        if($budid){
            $datas = $this->croDb('finc_budget')->where('bud_id',$budid)->find();
            $dbt = ulogic('Finance/Budget');
            $mkList = $dbt->getRegularList($datas);
            $datas['rglist'] = $mkList['trs'];
            $datas['rgdescrip'] = '计划/实际累计金额：'.$mkList['fsum'].'/'.$mkList['usumfg'].'；计划/实际共加载数据条：'.$mkList['count'].'/'.$mkList['uCount'].'；时间差'.$mkList['day'].'天数.';
            $this->assign('data',$datas);
            $this->_JsVar('cid',uInfo('cid'));
        }
        return $this->fetch();
    }
    // regulars - 例行财务保存
    public function regularsv()
    {
        $data = $_POST;
        $formid = isset($data['formid'])? $data['formid']:'';
        // 财务登账-> 计划到账单 或者账单写入计划表(计划反馈)
        if($formid == 'plan2sets'){
            $orig = bsjson($data['orig']);  // 计划数据
            $form = bsjson($data['form']);  // 表单数据
            $cid = uInfo('cid');
            if(isset($form['fset_no'])){    // 财务账单反馈-计划
                $fsetNo = $form['fset_no'];
                $form = $this->croDb('finc_set')->where('finc_no',$fsetNo)->find(); // 从实际账单中获取
                $planData = $orig;
                $planData['type'] = $planData['type'] == '收入'? 'IN':'OU';
                // 收入是- 差值计算：  实际收入 - 支出// 否则反之
                $dtfg = $planData['type'] == 'IN'? ($form['figure'] - intval($planData['sumsingle'])) : (intval($planData['sumsingle'])-$form['figure']);
                $planData = array_merge($planData,[
                    'center_id' => $cid,'fset_no' =>$fsetNo,'udate'=>$form['use_date'],'usumfg'=>$form['figure'],'dtfigure'=>$dtfg,
                    'name'=>$form['name'],'sider'=>$form['sider'],'expn'=>$form['purpose'],'remark'=>'从财务执行账单反馈计划-通过例行财务',
                    'status'=>'00','dtday'=>getDays($planData['pdate'],$form['use_date'])
                ]);
                // println($planData);die;
                if($this->croDb('finc_plan')->insert($planData)) $this->success('财务计划已登记成功，根据实际执行的财务账单情况');
                else $this->success('本次财务计划明细新增失败！');
            }
            println($planData);
            // println($planData,$form);
            return;
        }
        elseif($formid == 'plan2setsflush'){
            $map = ['no'=>$data['no']];
            $qData = $this->croDb('finc_plan')->where($map)->find();
            $fset = $this->croDb('finc_set')->where('finc_no',$qData['fset_no'])->find();
            $svData = [];
            // 金额比较
            if($qData['usumfg'] != $fset['figure']){
                $svData['usumfg'] = $fset['figure'];
                $svData['dtfigure'] = $qData['type'] == 'IN'? ($fset['figure'] - $qData['sumsingle']) : ($qData['sumsingle']-$fset['figure']);
            }
            // 日期比较
            if($qData['udate'] != $fset['use_date']){
                $svData['udate'] = $fset['use_date'];
                $svData['dtday'] = getDays($qData['pdate'],$fset['use_date']);
            }
            if(!empty($svData)){
                $svData['bak_log'] = ($qData['bak_log']? $qData['bak_log']:$qData['remark']).'<br>用于-（'.uInfo('nick').'）-于 '.sysdate().'更新数数据！';
                if($this->croDb('finc_plan')->where($map)->update($svData)) $this->success('数据更新成功！');
                else $this->success('本次您尝试更新，但是已经失败了');
            }
            else $this->success('您的数据是最新的，现在无须更新');
        }
        println($data);
    }
    public function save()
    {
        $data = $_POST;
        if(empty($data) && isset($_GET['uid'])) $data = bsjson($_GET['uid']);
        $umark = isset($data['umark'])? $data['umark']:'';
        $mode = isset($data['mode'])? $data['mode']:'';
        if($mode) unset($data['mode']);
        // 通用-数据删除
        if($mode == 'D' && isset($data['bud_id'])){
            $this->pushRptBack('finc_budget',['bud_id'=>$data['bud_id']],true);
            if($this->croDb('finc_budget')->where('bud_id',$data['bud_id'])->delete()) $this->success('数据已经被成功删除！',urlBuild('!finance:budget'));
            else $this->success('本次数据清除失败！',urlBuild('!finance:budget'));
        }
        // 财务例行 - 数据维护
        if($umark == 'regular'){
            if(empty($mode)) $mode = isset($data['bud_id'])? 'M':'A';
            if($mode == 'A'){
                $data['center_id'] = uInfo('cid');
                $data['bud_no'] = 'regulars';
                if(is_numeric($data['figure'])) $data['figure'] = 0;
                if($this->croDb('finc_budget')->insert($data)) $this->success('您已成功新增一条财务例行计划');
                else $this->success('数据保存失败');
            }
            elseif($mode == 'M'){
                $map  = ['bud_id'=>$data['bud_id']];
                unset($data['bud_id']);
                if($this->croDb('finc_budget')->where($map)->update($data)) $this->success('数据已经成功更新！');
                else $this->success('数据修改失败，可能你没有修改数据！');
            }
        }
        println($data);
    }
    public function ajax(){
        $data = $_POST;
        $item = isset($data['item'])? $data['item']:'';
        switch($item){
            case 'regulars/chart':      // 获取图表数据
                $budid = $data['list'];
                // return $budid;
                return ulogic('Finance/Budget')->rglEchartOption($budid);
                break;
            case 'regulars/aboutplan':  // 计划详情
                $no = $data['list'];
                $qData = $this->croDb('finc_plan')->where('no',$no)->find();
                $sql = 'select concat(\'(\',use_date,\' : \',name,\') [\',master,\' : \',figure,if(type = \'支出\',\' -> \',\' <- \'),sider,\'] {\',plus_desc,\':\',explanin,\'}\') as tpl from finc_setview where user_code=:user_code and finc_no=:finc_no';
                $fset = $this->_query($sql,['user_code'=>uInfo('code'),'finc_no'=>$qData['fset_no']]);
                $html = '<div class="well">
                    <div class="page-header"><h4>'.$qData['name'].'</h4></div>
                    <p>财物计划：'.$qData['pdate'].' '.$qData['sumsingle'].'</p>
                    <p>财物账单：'.$qData['udate'].' '.$qData['usumfg'].'</p>
                    <p>财物账单示例：'.$fset['tpl'].'</p>
                    <p>操作： <a href="javascript:app.flushPlanData(\''.$no.'\');" class="btn btn-link">重新生成数据</a></p>
                    </div>';
                return $html;
                break;
        }
        println($data);
    }   
}