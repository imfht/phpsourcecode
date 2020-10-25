<?php
/*
 * 2017年1月16日 星期一
 * 财务报表： Report
*/
namespace app\finance\controller;
use think\Controller;
class Report extends Controller
{
    public function index(){
        // 自动新增数据并重定位
        $this->autoCreateData();
        $this->loadScript([
            'auth'=>'','title'=>'Conero-财务报表','bootstrap'=>true,'js'=>['Report/index']
        ]);
        $cid = uInfo('cid');
        $pages = [];
        // 年份检测提示    
        $qdata = $this->_query('select date_format(MIN(use_date),\'%Y\') as `min`,date_format(max(use_date),\'%Y\') as `max` from finc_set where center_id = ? and use_date <> ?',[$cid,'0000-00-00']); 
        $close = true;
        $tip = '';
        if(isset($qdata['min']) && isset($qdata['max']) && $qdata['min'] && $qdata['max']){
            if($qdata['min'] == $qdata['max'])
            $tip = '只发现您在'.$qdata['min'].'年内的数据';
            else
            $tip = '年份在'.$qdata['min'].'与'.$qdata['max'].'之间';
            $close = false;
        }
        if($close == false)     
            $pages['yearCheckIpt'] = 
                '<input type="text" class="form-control" '.(isset($qdata['max'])? 'max="'.$qdata['max'].'" ':'').'id="ycheckipt" '.(isset($qdata['min'])? 'min="'.$qdata['min'].'" ':'').'placeholder="'.$tip.'">
                '
            ;
        // 当前财务自动化处理
        $monthNo = date('Y-m-00');
        $pages['mno'] = $monthNo;
        $pages['mdesc'] = fNoParseText($monthNo);
        $this->_indexAutoCurrData($monthNo);
        // 获取指定账单的详情
        if(isset($_GET['acc'])){
            $facc = model('Faccount');
            // $source = $facc->where('acc_id',$_GET['acc'])->select();
            // $source = $facc->where($_GET['acc'])->field()->find();
            // $source = $facc->where($_GET['acc'])->find();
            // $source = $facc->where($_GET['acc'])->field('acc_id,acc_no')->find();
            $source = $facc->where('acc_id',$_GET['acc'])->find();
            // println($facc);
            // println($source['acc_id']);
            // println($source->toArray());
            // var_dump($source);
            $pages['acc'] = $pages['mdesc'];
            $pages['acc_head'] = fNoParseText($source['acc_no']);
            $record = $source->toArray();
            // 财务报表为空时
            if(empty($record['content'])){
                $facc->where('acc_id',$_GET['acc'])->update([
                        'content'=>$this->accContent($record)
                    ]);
                $record = $facc->where('acc_id',$_GET['acc'])->find()->toArray();
            }
            $pages['accsrc'] = $record;
            // 数据推荐 季度/年度报表    
            $recom = ulogic('Finance')->accRecommend($record['acc_no'],'account');
            $recomAccNoHref = '';
            if(isset($recom['year'])) $recomAccNoHref .= '<a href="'.urlBuild('!.report',['__get'=>['accno'=>$recom['year']]]).'" class="btn btn-link" title="生成财务报表">'.fNoParseText($recom['year']).'报表</span></a>';
            if(isset($recom['month'])) $recomAccNoHref .= '<a href="'.urlBuild('!.report',['__get'=>['accno'=>$recom['month']]]).'" class="btn btn-link" title="生成财务报表">'.fNoParseText($recom['month']).'报表</span></a>';
            if($recomAccNoHref) $pages['recomAccNoHref'] = $recomAccNoHref;
            // println($recom);
        }

        // 历史账单数据加载
        $btsp = $this->bootstrap($this->view);
        $tmpWh = ['center_id'=>$cid];
        $wh = $btsp->getSearchWhere();        
        $wh = $wh? array_merge($tmpWh,$wh): $tmpWh;
        $count = $this->croDb('finc_account')->where($wh)->count();
        $btsp->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['acc_no'=>'编号','incount'=>'收入合计','editor'=>'支出合计','edittm'=>'创建时间','nearest_dt'=>'维护时间']]);
        $btsp->tableGrid(['__viewTr__'=>'trs'],['table'=>'finc_account','cols'=>[function($record){return '<a href="/conero/finance/report.html?acc='.$record['acc_id'].'">'.fNoParseText($record['acc_no']).'</a>';},'acc_no','incount','outcount','nearest_dt','edittm']],function($db) use($wh,$btsp){
                $page = $btsp->page_decode();
                return $db->page($page,30)->where($wh)->order('acc_no desc')->select();
        });
        $btsp->pageBar($count);
        $this->assign('pages',$pages);
        return $this->fetch();
    }
    // 自动写入当前月份的财务账单分析
    private function _indexAutoCurrData($no){
        $facc = model('Faccount');
        $cid = uInfo('cid');
        $map = ['acc_no' => $no,'center_id' => $cid];
        $isEmpty = $facc->where($map)->count();
        if(empty($isEmpty)){
            $sql = 'select count(*) as dck from finc_set where center_id = ? and use_date like concat(date_format(curdate(),\'%Y-%m\'),\'%\')';
            $data = $this->_query($sql,[$cid]);
            // println($isEmpty,$map,$data);
            // $data = null;
            if(empty($data) || empty($data['dck'])) $isEmpty = true;
        }
        if(empty($isEmpty)){
            $data = $this->_query('call fincacc_rpt_sp(?,?,?)',[$cid,null,'M']);
            // println($isEmpty,$map,$data);
            // 数据保存
            $content = '';
            $data['content'] = $this->accContent($data);
            $facc->data($data)->save();
        }
        // println($map);
    }
    // 自动新增数据并重定位
    private function autoCreateData(){
        $cid = uInfo('cid');        
        if(isset($_GET['accno'])){
            $accno = $_GET['accno'];
            $has = $this->croDb('finc_account')->where(['center_id'=>$cid,'acc_no'=>$accno])->count();            
            if($has<1){
                $value = '';
                $type = '';
                if(substr_count($accno,'-00-00') && $this->croDb('finc_set')->where('center_id=\''.$cid.'\' and use_date like \''.substr($accno,0,4).'-%\'')->count()>0){
                    $value = substr($accno,0,4);
                    $type = 'Y';
                }
                elseif(substr_count($accno,'-00-')){
                    $value = substr($accno,0,5);
                    $value .= 'S'.(intval(substr($accno,-1)));
                    $type = 'S';
                }
                elseif(substr($accno,-2) == '00' && $this->croDb('finc_set')->where('center_id=\''.$cid.'\' and use_date like \''.substr($accno,0,7).'-%\'')->count()>0){
                    $value = substr($accno,0,7);
                    $type = 'M';
                }
                $data = ($value && $type)? $this->_query('call fincacc_rpt_sp(?,?,?)',[$cid,$value,$type]):null;
                if($data) $data['content'] = $this->accContent($data);
                // println("call fincacc_rpt_sp($cid,$value,$type)",$data);                
                $facc = model('Faccount');
                $facc->save($data);
                $id = $facc->acc_id;
                urlBuild('.report',['__get'=>['acc'=>$id]]);
                // println("call fincacc_rpt_sp($cid,$value,$type)",$data);
            }
        }
        // 全年数据月度数据更新
        elseif(isset($_GET['ayms'])){
            $year = $_GET['ayms'];
            $data = $this->getYearMonthData($year); 
            foreach($data as $v){
                if(!empty($v['hasdata'])) continue;
                $month = $v['month'];
                $data = $this->_query('call fincacc_rpt_sp(?,?,?)',[$cid,$year.'-'.$month,'M']);
                if($data){
                    $data['content'] = $this->accContent($data);   
                    $facc = model('Faccount');                                                       
                    $ret = $facc->save($data);
                    // println($ret);
                    // println($data);
                }
            }
            urlBuild('.report');
        }
    }    
    // 数据更新
    public function refresh()
    {
        $acc = request()->param('acc');
        if($acc){
            // 重新计算数据
            $this->_query('call fincacc_rpt_sp(?,?,?)',[$acc,null,'uid']);
            $facc = model('Faccount');
            $data = $facc->get($acc)->toArray();          
            $data['content'] = $this->accContent($data);
            unset($data['acc_id']);
            $facc->where('acc_id',$acc)->update($data);       
        }
        urlBuild('.report',['__get'=>['acc'=>$acc]]);
    }
    // 数据删除
    public function delete()
    {
        $acc = request()->param('acc');
        if($acc){
            $this->pushRptBack('finc_account',['acc_id'=>$acc],true);
            $dt = model('Faccount')->get($acc);
            $dt->delete();
        }
        urlBuild('.report');
    }
    // 报文备份
    public function backup(){
        $acc = request()->param('acc');
        echo $acc;
        echo '<br>财务报表备份！';
    }
    // 报文下载  2017年2月7日 星期二
    // 参考 http://phpword.codeplex.com/documentation
    // 下载后的word文档乱码
    public function download()
    {

        $acc = request()->param('acc');  
        $data = model('Faccount')->get($acc)->toArray();
        // 生成pdf
        return $this->createPDF($data);
        require_once EXTEND_PATH.'PHPWord.php';
        // 
        $PHPWord = new \PHPWord();
        $section = $PHPWord->createSection();
        // $section->addText($data['content']);
        $section->addText('杨-yt like never show shen');

        // At least write the document to webspace:
        $objWriter = \PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
        $objWriter->save($data['acc_no'].'.docx');
    }
    // 生成pdf报文类
    // https://packagist.org/packages/dompdf/dompdf
    // 导出中文乱码
    private function createPDF($data)
    {
        require_once EXTEND_PATH.'Dompdf/autoload.inc.php';
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($data['content']);
        
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream();
    }
    // 报文生成
    private function accContent($data)
    {
        $accContentData = $data;
        $accContentData['type'] = fNoParseText($data['acc_no']);
        // 制表时间
        $accContentData['time'] = sysdate();        
        // 数据表清单
        $table = '';
        if(isset($data['from_date']) && $data['to_date']){
            $datalist = $this->croDb('finc_setview')->where(
                'center_id = \''.$data['center_id'].'\' and user_code = \''.uInfo('code').'\' and use_date >= \''.$data['from_date'].'\' and use_date <= \''.$data['to_date'].'\''
            )->order('use_date,type,master,plus_desc')->select();
            $list = '';
            $i = 1;
            foreach($datalist as $v){
                $list .= '<tr><td>'.$i.'</td><td>'.$v['use_date'].'</td><td>'.$v['master'].'</td><td>'.$v['figure'].'</td><td>'.$v['name'].'</td><td>'.$v['type'].'</td><td>'.$v['plus_desc'].'</td><td>'.$v['sider'].'</td><td>'.$v['explanin'].'</td><td>'.$v['set_date'].'</td></tr>';
                $i += 1;
            }
            $table = $list? 
                '<table>
                <tr><th>#</th><th>日期</th><th>事务甲方</th><th>金额</th><th>名称</th><th>收支</th><th>用途</th><th>事务甲方</th><th>详情</th><th>编辑日期</th></tr>
                '.$list.'</table>'
                : '未查询到数据表'
            ;
            //println($datalist);
            // die;
        }
        $accContentData['table'] = $table;
        return model('Textpl')->renderContent('accContent',$accContentData);
    }
    // ajax 求情
    public function ajax()
    {
        $data = count($_POST)>0? $_POST:$_GET;
        $item = isset($data['item'])? $data['item']:'';
        $ret;
        switch($item){
            // 报表补漏查询，可生成的
            case 'check_by_year':
                $year = $data['year'];
                /*
                $cid = uInfo('cid');
                $sql = 'select date_format(use_date,\'%Y-%m-00\') as accno,(select acc_no from finc_account where center_id = \''.$cid.'\' and acc_no = date_format(use_date,\'%Y-%m-00\')) as hasdata,date_format(use_date,\'%m\') as month from finc_set where center_id=\''.$cid.'\' and use_date like \''.$year.'%\' group by date_format(use_date,\'%m\') order by use_date asc';
                $data = $this->_query($sql);
                */
                $data = $this->getYearMonthData($year);
                $ret = json($data);
                break;
        }
        return $ret;
    }
    // 根据财务账单获取全年数据
    private function getYearMonthData($year)
    {
        $cid = uInfo('cid');
        $sql = 'select date_format(use_date,\'%Y-%m-00\') as accno,(select acc_no from finc_account where center_id = \''.$cid.'\' and acc_no = date_format(use_date,\'%Y-%m-00\')) as hasdata,date_format(use_date,\'%m\') as month from finc_set where center_id=\''.$cid.'\' and use_date like \''.$year.'%\' group by date_format(use_date,\'%m\') order by use_date asc';
        $data = $this->_query($sql);
        return $data;
    }
}