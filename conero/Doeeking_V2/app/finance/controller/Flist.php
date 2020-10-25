<?php
namespace app\finance\controller;
use think\Controller;
use app\Server\Finance;
use Exception;
class Flist extends Controller
{
    // 初始化
    public function _initialize(){
        if($this->_initTplCheck(['save'])) return;
        $action = request()->action();
        $this->loadScript([
            'auth'=>'','title'=>'Conero-财务清单','js'=>['Flist/'.$action],'css'=>['Flist/'.$action]
        ]);
        $this->useFrontFk('bootstrap');
    }
    public function index()
    {
        $type = getUrlBind('index');
        $type = $type? $type:'list';     //
        switch($type){
            case 'plist':
                $this->_fshop();
                break;
            default:
                $this->_fshoplist();
        }
        $this->assign("type",$type);
        return $this->fetch();
    }
    // 清单父项
    private function _fshop()
    {
        $html = '';
        $cid = uInfo('cid');
        $btsp = $this->bootstrap($this->view);
        $page = $btsp->page_decode();
        $sql = 'select count(*)  as `ctt` from (select count(*) from fshop_list where center_id=\''.$cid.'\' group by related_id) a';
        $qData = $this->_query($sql);$count = isset($qData['ctt'])? $qData['ctt']:0;
        // println($qData,$count);
        // $count = $this->croDb('fshop_list')->where('center_id=\''.$cid.'\' and related_id is not null')->count();
        $data = $this->_query('call flists_act_sp(?,?,\'A\',null)',[$cid,$page]);
        $data = isset($data[0])? $data:[0=>$data];
        $i = 1;
        foreach($data as $v){
            $html .= '<tr><td>'.$i.'</td><td><a href="/conero/finance/flist/edit/p/'.$v['related_id'].'">'.($v['name']? $v['name']:"无记录").'</a></td><td>'.$v['figure'].'</td><td>'.$v['sum'].'</td><td>'.$v['amount'].'</td><td>'.$v['date'].'</td></tr>';
            $i++;
        }        
        $btsp->pageBar(['count'=>$count],'pflistPageBar');
        $this->assign('fshop',$html);
    }
    // 清单全列
    private function _fshoplist()
    {
        $cid = uInfo('cid');
        $btsp = $this->bootstrap($this->view);
        $page = $btsp->page_decode();
        // println($page);
        $count = $this->croDb('fshop_list')->where('center_id',$cid)->count();
        $tr = '';
        $data = $this->_query('call flists_act_sp(?,?,\'S\',null)',[uInfo('cid'),$page]);
        // <tr><th>序号</th><th>日期</th><th>名称</th><th>数量</th><th>单价</th><th>总价</th><th>备注</th>编辑日期</tr>
        $i = 1;
        foreach($data as $v){
            $tr .= '<tr><td>'.$i.'</td><td>'.$v['shopdate'].'</td><td>'.$v['event'].'</dt><td>'.$v['goods'].'</td><td>'.$v['amount'].'</td><td>'.$v['single'].'</td><td>'.$v['sumsingle'].'</td><td>'.$v['remark'].'</td><td>'.$v['edittm'].'</td></tr>';
            $i++;
        }
        $btsp->pageBar(['count'=>$count],'flistPageBar');
        $this->assign('fshoplist',$tr);
    }
    public function edit()
    {        
        $this->_eUpdateRecord();
        return $this->fetch();
    }
    private function _eUpdateRecord()
    {
        $rId = getUrlBind('p');
        $fin = new Finance();
        $sumy = ['use_date'=>date('Y-m-d'),'name'=>'','master_id'=>'','master'=>'','type'=>'','sider_id'=>'','sider'=>'','purpose'=>'','figure'=>''];
        if($rId){
            $sumy = $this->croDb('finc_set')->where('finc_no',$rId)->find();
            if(empty($sumy)){
                utf8();
                if(isset($_GET['del'])){
                    $map = ['module'=>'finc_set','related_id'=>$rId];
                    $this->pushRptBack('fshop_list',$map,true);
                    $ret = $this->croDb('fshop_list')->where($map)->delete();
                    echo '<script>alert("您已经成功删除该清单明细，共【'.$ret.'】条");location.href="/conero/finance/flist.html";</script>';die;
                }                
                echo '该数据清单的的父项已经被删除，您可<a href="?del='.md5(sysdate()).'">删除该数据清单</a>/<a href="/conero/finance/flist.html">忽略</a>这些无效的数据清单！';die;
            };
            $sumy['finc_no'] = '<input type="hidden" name="finc_no" value="'.$sumy['finc_no'].'">';
        }
        else{
            $this->_JsVar('cid',uInfo('cid'));
        }
        $sumy['master_id'] = '<select name="master_id" class="form-control input-sm" id="_key_master_id" required>'.$fin->master(null,isset($sumy['master_id'])? $sumy['master_id']:null).'</select>';
        if(isset($sumy['sider']) && empty($sumy['sider_id'])){
            $this->assign('selfSider','<input id="_key_sider_id" class="form-control input-sm" value="'.$sumy['sider'].'" name="sider" required="" type="text">');
        }
        else $sumy['sider_id'] = '<select name="sider_id" class="form-control input-sm" id="_key_sider_id" onClick="app.siderChange(this)" required>'.$fin->master('all',isset($sumy['sider_id'])? $sumy['sider_id']:null).'</select>';
        $sumy['purpose'] = $fin->purpose('<select name="purpose" class="form-control input-sm" id="_key_purpose" required>',isset($sumy['purpose'])? $sumy['purpose']:null);
        $dtl = [];
        $dtlHtml = '';
        if($rId){
            $dtl = $this->croDb('fshop_list')->where(['module'=>'finc_set','related_id'=>$rId])->select();
            $i = 1;
            foreach($dtl as $v){
                $dtlHtml .= '
                    <tr dataid="'.$i.'">
                        <td class="rIndex">'.$i.(isset($v['shop_no'])? '<input type="hidden" name="shop_no" value="'.$v['shop_no'].'">':'').'</td>
                        <td><input type="text" name="goods" class="form-control input-sm" value="'.$v['goods'].'" required></td>
                        <td><input type="text" name="goodsModel" value="'.$v['goodsModel'].'" class="form-control input-sm"></td>
                        <td><input type="text" name="single" class="form-control input-sm" value="'.$v['single'].'" onBlur="app.singleCheck(this)" required></td>                        
                        <td><input type="text" name="amount" class="form-control input-sm" value="'.$v['amount'].'" onBlur="app.amountCheck(this)" value="1" required></td>
                        <td><input type="text" name="store" class="form-control input-sm"  value="'.$v['store'].'" required></td>
                        <td><input type="text" name="remark" class="form-control input-sm"  value="'.$v['remark'].'"></td>                        
                    </tr>
                ';
                $i++;
            }
        }
        $this->assign([
            'sumy'  => $sumy,
            'dtlList'   => $dtlHtml,
            'mode'      => $rId? 'M':'A'
        ]);
    }
    public function save()
    {
        $data = $_POST;debugOut($data);
        $ret = '';
        if(isset($data['delist']) && !empty($data['delist'])){
            $ctt = 0;
            foreach(explode(',',$data['delist']) as $v){
                if($v){
                    $this->pushRptBack('fshop_list',['shop_no'=>$v],true);
                    $ret = $this->croDb('fshop_list')->where('shop_no',$v)->delete();
                    if($ret) $ctt += 1;
                }
            }
            if($ctt>0) $ret = '本次共删除明细【'.$ctt.'】条';
            else $ret = '';
        }
        if(isset($data['summy'])){
            $finSet = json_decode($data['summy'],true);
            $cid = uInfo('cid');
            if(isset($finSet['finc_no']) && !empty($finSet['finc_no'])){//修改数据
                $setNo = $finSet['finc_no'];unset($finSet['finc_no']);
                $tmp = $this->croDb('finc_set')->where('finc_no',$setNo)->update($finSet);
                if($tmp) $ret = '概略数据已修改！';
            }
            else{// fincset>+
                $finSet['center_id'] = $cid;
                $finSet['actbak'] = '<br>账户名称（'.uInfo('name').'）于 '.sysdate().'新增此条财物账单！';
                $set = $this->croDb('finc_set');
                //$setNo = $set->insertGetId($finSet);debugOut($setNo);
                $setNo = $set->insert($finSet);// 无法返回当前新增的ID ??
                if($setNo){
                    $setNo = $this->croMd()->fetchOne('select finc_no from finc_set order by set_date desc,finc_no desc limit 1');
                }
                else{$this->success("【财务登账概略】新增错误！");return;}
            }
            if(empty($setNo)){$this->success("【财务登账概略】新增错误！");return;}
            //$shop = $this->croDb('fshop_list');
            $list = json_decode($data['dtl'],true);
            $count = 0;$editCount = 0;
            foreach($list as $v){
                $shopData = $v;
                if(isset($shopData['shop_no']) && !empty($shopData['shop_no'])){// 修改数据
                    $map = ['shop_no'=>$shopData['shop_no']];unset($shopData['shop_no']);
                    $tmp = $this->croDb('fshop_list')->where($map)->update($shopData);
                    if($tmp) $editCount += 1;
                }
                else{// 新增数据
                    $shopData['module'] = 'finc_set';
                    $shopData['related_id'] = $setNo;
                    $shopData['center_id'] = $cid;
                    $shopData['sumsingle'] = intval($shopData['amount'])*intval($shopData['single']);
                    try{
                        if($this->croDb('fshop_list')->insert($shopData)) $count++;
                    }catch(Exception $e){
                        $report = "\r\n--------（财务清单/写入明细是出错[Flist/save]-".sysdate()."）---------\r\n".'错误信息：'.$e->getMessage();
                        $report .= "\r\n".'错误信息：'.$e->getTraceAsString();
                        debugOut($report);
                    }
                }
            }
            $report = $editCount > 0? '本次修改数据明细【'.$editCount.'】条':'';
            $report .= $count >0? (empty($report)? '':'；').'数据明细新增【'.$count.'】条':'';
            if(!empty($ret)) $report .= ($report? '；':'').$ret;
            $this->success($report);return;
        }
        if($ret){$this->success($ret);return;}
        $this->error('数据新增失败！');
    }
}