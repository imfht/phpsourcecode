<?php
namespace app\finance\controller;
use think\Loader;
use think\Controller;
use think\Db;
use app\Finance\model\Forgan;
use app\Server\Finance;
use hyang\Bootstrap;
class Fincset extends Controller
{
    // 首页
    public function index()
    {
        $this->loadScript([
            'auth'=>'','title'=>'Conero-财物系统','js'=>['Fincset/index'],'css'=>['Fincset/index']
        ]);
        if(isset($_GET['action'])) $this->_indexAction(base64_decode($_GET['action']));
        $orgn = new Forgan;        
        $f = new Finance();
        $form = [
            'master' => '<select name="master_id" required="required" onChange="App.masterChange(this)">'.$f->master().'</select>',
            'sider'  => '<select name="sider_id" class="sider_sel" onChange="App.siderChange(this)">'.$f->master('all').'</select>',
            'purpose' => $f->purpose()
        ];
        $this->assign([
            'form' => $form,
            // 'fincset_log' => ''
            'fincset_log' => $this->_fincLog()
        ]);             
        return $this->fetch('index');
    }    
    private function _fincLog()
    {               
        $declareTb = function($tb,$class){
            $table = '
                <table class="'.$class.' hidden">
                    <tr><th>#</th><th>日期</th><th>事务甲方</th><th>类型</th><th>金额</th><th>用途</th><th>名称</th><th>事务乙方</th><th>备注</th><th>操作</th></tr>
                    '.$tb.'
                </table>    
            ';
            return $table;
        };
        // 函数式编程
        $makeTb = function($data=null){
            if(!is_array($data) || empty($data)) return ['trs'=>'','count'=>''];
            $tb = '';
            $i = 1;
            foreach($data as $v){
                $tb .= '<tr dataid="'.$v['finc_no'].'"><td>'.$i.'</td><td>'.$v['use_date'].'</td><td>'.$v['master'].'</td><td>'.$v['type'].'</td><td>'.$v['figure'].'</td><td>'.$v['plus_desc'].'</td><td>'.$v['name'].'</td><td>'.$v['sider'].'</td><td>'.$v['explanin'].'</td><td><a href="javascript:void(0);" dataurl="/Conero/finance/fincset?action='.base64_encode('del$$'.$v['finc_no']).'" class="del_btn">删除</a><a href="javascript:void(0);" class="edit_btn">修改</a></td></tr>';
                $i++;
            }
            return ['trs'=>$tb,'count'=>$i-1];
        };
        $title = ['<a href="javascript:void(0);" class="tab_nav active" dataid="console">信息控制台</a>']; 
        $html = '<div class="console dance" id="page_console"><p>'.sysdate().' => 当前没有任务信息！</p></div>'; 
        $data = $this->_query('call fincset_log_sp("'.uInfo('code').'",null)',true);
        $dataGrid = $makeTb($data);
        $tb = $dataGrid['trs']? $dataGrid['trs']:'';
        $tb = $tb? $declareTb($tb,'today'):'';
        if($tb){
            $title[] = '<a href="javascript:void(0);" class="tab_nav" dataid="today">今日登账记录('.$dataGrid['count'].')</a>';
            $html .= $tb;
        }
        $data = $this->_query('call fincset_log_sp("'.uInfo('code').'","2week")',true);
        $dataGrid = $makeTb($data);
        $tb = $dataGrid['trs']? $dataGrid['trs']:'';
        $tb = $tb? $declareTb($tb,'2_week'):'';
        if($tb){
            $title[] = '<a href="javascript:void(0);" class="tab_nav" dataid="2_week">近两周登账记录('.$dataGrid['count'].')</a>';
            $html .= $tb;
        }
        $title[] = '<a href="javascript:void(0);" class="tab_nav" dataid="about">财务概述</a>';
        $html .= $this->_indexReport();
        /*
        $data = $this->_query('call fincset_log_sp("'.uInfo('cid').'","about")',true);
        if($data){
            $data = $data[0];
            $title[] = '<a href="javascript:void(0);" class="tab_nav" dataid="about">财务概述</a>';
            $html .= '
                <div class="about hidden">
                    <b>财务周报告</b>
                    <div>
                    </div>
                    <b>财务月报告</b>
                    <div>
                    </div>
                    <b>财务年报告</b>
                    <div>
                    </div>
                    <b>财务总报告</b>
                    <div>
                        <p>分析时间：'.sysdate().'</p>
                        <p>当前包含的总财务账单条：'.$data['sum'].',其中包含：收入共'.$data['in_sum'].',支出'.$data['out_sum'].'</p>
                        <p>资金总流量：'.$data['figure_all'].',其中包含：收入共'.$data['figure_in'].',支出'.$data['figure_out'].'</p>
                        <p>登账时间统计：账单设置天数'.$data['setdt_ctt'].',账单使用天数'.$data['usedt_ctt'].'</p>
                        <p><a href="/conero/finance/fincset/report.html" target="_blank">更多...</a></p>
                    </div>
                </div>
            ';
        }
        */
        $title[] = '<a href="javascript:void(0);" class="tab_nav" dataid="search">财务检索</a>';
        if(count($title)>0){
            $tmp = implode(' ',$title);
            if($tmp) $html = '<p class="title">'.$tmp.'</p>'.$html;
        }
        // 财务检索
        $html .= '<div class="search hidden">
            <p>
                <select id="search_key">
                    <option value="name">名称</option>
                    <option value="master">事务甲方</option>
                    <option value="use_date">日期</option>
                    <option value="figure">金额</option>
                    <option value="plus_value">用途</option>
                    <option value="sider">事务乙方</option>
                    <option value="explanin">详情</option>
                </select>
                <input type="text" id="svalue">                
            </p>
            <div class="container"></div>
        </div>
        ';
        return $html;
    }
    private function _indexReport()
    {
        // 匿名函数生成报文
        $createReport = function($type){
            $data = $this->_query('call fincset_log_sp("'.uInfo('code').'","'.$type.'")',true);
            if(!isset($data[0])) return '<span style="color:red;font-size:italic;">无法获取到分析数据，也许您还没有任何而造成的！</span>';
            $data = $data[0];
            $html = '
                <div>
                    <p>当前包含的总财务账单条：'.$data['sum'].',其中包含：收入共'.$data['in_sum'].',支出'.$data['out_sum'].'</p>
                    <p>资金总流量：'.$data['figure_all'].',其中包含：收入共'.$data['figure_in'].',支出'.$data['figure_out'].'</p>
                    <p>登账时间统计：账单设置天数'.$data['setdt_ctt'].',账单使用天数'.$data['usedt_ctt'].'</p>
                </div>
            ';
            return $html;
        };
        //$data = $this->_query('call fincset_log_sp("'.uInfo('cid').'","about")',true);          
        return '
            <div class="about hidden">
                <b>财务周报告</b>                    
                <div>
                '.$createReport('aboutweek').'
                </div>
                <b>财务月报告</b>
                <div>
                '.$createReport('aboutmouth').'
                </div>
                <b>财务年报告</b>
                <div>
                '.$createReport('aboutyear').'
                </div>
                <b>财务总报告</b>
                <div>
                    <p>分析时间：'.sysdate().'</p>
                    '.$createReport('about').'
                    <p><a href="/conero/finance/fincset/report.html" target="_blank">更多...</a></p>
                </div>
            </div>
        ';
    }
    // ?? 登入到期时-拒绝无主数据写入-如何保存数据等下次登入时显示上一次失败的操作数据
    public function doIndex()
    {
        if(isset($_GET['action'])){}
        else{
            $data = $_POST;
            if(isset($data['multi']) && $data['multi'] == 'Y'){$this->_multiDoIndex($data);die;}
            //debugOut($data,true);die;            
            if(isset($data['map'])){
                $map = ['finc_no'=>$data['map']];unset($data['map']);
                $data['set_date'] = sysdate();
                $src = Db::table('finc_set')->where($map)->field('actbak')->find();
                $actbak = $src['actbak'];
                $actbak .= '<br>账户名称（'.uInfo('name').'）于 '.sysdate().'修改此条财物账单！';
                $data['actbak'] = $actbak;
                $ctt = Db::table('finc_set')->where($map)->update($data);
                if($ctt) $this->success('数据修改成功！！');
                else $this->success('无法正常修改数据！');
            }else{
                $data['finc_no'] = date('ymd');
                $data['center_id'] = uInfo('cid');
                $data['actbak'] = '账户名称（'.uInfo('name').'）于'.sysdate().'新增此条财物账单！';
                $ctt = Db::table('finc_set')->insert($data);
                if($ctt) $this->success('成功插入一条数据');
                else $this->success('数据提交失败');
            }
        }
    }
    // 多条数据支出
    private function _multiDoIndex($saveData)
    {
        $data = json_decode($saveData['data'],true);
        $mode = $saveData['mode'];
        $all = 0;
        $success = 0;
        //$ctt = "测试";
        foreach($data as $v){
            $fset = $v;
            if('A' == $mode){                
                $fset['center_id'] = uInfo('cid');
                $fset['actbak'] = '<br>账户名称（'.uInfo('name').'）于 '.sysdate().'新增此条财物账单！';
                //debugOut($fset,true);
                $ctt = Db::table('finc_set')->insert($fset);
                if($ctt) $success++;
            }
            elseif('M' == $mode){
                $map = ['finc_no'=>$fset['map']];unset($fset['map']);
                $src = Db::table('finc_set')->where($map)->field('actbak')->find();
                $actbak = $src['actbak'];
                $actbak .= '<br>账户名称（'.uInfo('name').'）于 '.sysdate().'修改此条财物账单！';
                $fset['actbak'] = $actbak;
                $fset['set_date'] = sysdate();
                $ctt = Db::table('finc_set')->where($map)->update($fset);
                //debugOut([$map,$fset],true);$ctt=null;
                if($ctt) $success++;
            }
            $all++;
        }
        //die;
        $this->success('['.sysdate().']本次提交'.($mode == 'A'? '新增':'修改').'数据，运行情况：'.$success.'/'.$all);
    }
    private function _indexAction($action)
    {
        // 删除
        if(substr_count($action,'del$$')>0){
            $action = str_replace('del$$','',$action);
            $this->pushRptBack('finc_set',['finc_no'=>$action],true);
            $res = Db::table('finc_set')->where('finc_no',$action)->delete();
            // 删除 shopList 表
            $having = Db::table('fshop_list')->where(['module'=>'finc_set','related_id'=>$action])->count();
            $shop = '';
            if($having>0){
                $map = ['module'=>'finc_set','related_id'=>$action];
                $this->pushRptBack('fshop_list',$map,true);
                Db::table('fshop_list')->where($map)->delete();
            }
            if($res){
                // go('/Conero/finance/fincset');
                $this->success("数据已经成功被删除！！");
            }
            $this->_JsVar('csl_delete','本次数据删除失败！');
        }
    }
    // 数据表格
    public function table()
    {
        $this->loadScript([
            'auth'=>'','title'=>'Conero-财物系统','js'=>['Fincset/table'],'css'=>['Fincset/table'],'bootstrap'=>true
        ]);
        // 数据渲染    
        $count = $this->croDb('finc_setview')->count();
        $btsp = (new Bootstrap())->linkApp($this->view);
        $wh = $btsp->getSearchWhere('code');
        $count = $this->croDb('finc_setview')->where($wh)->count();
        $btsp->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['name'=>'名称','use_date'=>'日期','master'=>'事务甲方','type'=>'类型','figure'=>'金额','plus_desc'=>'用途','sider'=>'事务乙方','explanin'=>'详情','set_date'=>'编辑日期']]);
        $btsp->tableGrid(['__viewTr__'=>'fincset'],['table'=>'finc_setview','cols'=>['use_date','name','master','type','figure','plus_desc','sider','explanin','set_date']],function($db) use ($wh,$btsp){
                $page = $btsp->page_decode();
                return $db->page($page,30)->where($wh)->order('use_date desc')->select();
        });
        $this->bootstrap($this->view)->pageBar($count);
        return $this->fetch();
    }
    // 财务报告- 星期/月/年 ??
    public function report()
    {
        $this->loadScript([
            'auth'=>'','title'=>'财务报告-Conero-财物系统','js'=>['Fincset/report'],'css'=>['Fincset/report'],'bootstrap'=>true
        ]);
        return $this->fetch();
    }
    // 快速登账
    public function fast()
    {
        $this->loadScript([
            'auth'=>'','title'=>'财务列表-Conero-财物系统','js'=>['Fincset/fast'],'css'=>['Fincset/fast'],'bootstrap'=>true
        ]);
        // 获取数据列表        
        $this->bootstrap($this->view)->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['name'=>'名称','use_date'=>'日期','master'=>'事务甲方','type'=>'类型','figure'=>'金额','plus_desc'=>'用途','sider'=>'事务乙方','explanin'=>'详情','set_date'=>'编辑日期']]);
        $btsp = new Bootstrap();
        $wh = $btsp->getSearchWhere('code');
        $count = $this->croDb('finc_setview')->where($wh)->count();
        $no = $btsp->page_decode();
        $page = $btsp->pageBar($count);
        $list = $this->_fastList($no);
        if($page) $list .= $page;
        $this->assign('fset_list',$list);

        return $this->fetch();
    }   
    private function _fastList($no=1,$num=30)
    {
        $start = ($no-1)*$num;
        $html = '';
        $wh = $this->bootstrap()->getSearchWhere();
        $wh = is_array($wh)? $wh : array();
        $tmp = [];//debugOut($wh,true);
        foreach($wh as $k=>$v){
            if(is_array($v)){
                $tmp[] = $k. ' like \''.$v[1].'\'';
            }
            else $tmp[] = $k.'=\''.$v.'\'';
        }
        $wh = count($tmp)>0? 'and '.implode(' and ',$tmp).' ':'';
        //$data = $this->croDb('finc_setview')->where('center_id',uInfo('cid'))->page($no,$num)->order('update desc')->select();
        // $sql = 'select concat(\'(\',use_date,\' : \',name,\') [\',master,\' : \',figure,if(type = \'支出\',\' -> \',\' <- \'),sider,\'] {\',plus_desc,\':\',explanin,\'}\') as tpl from finc_setview where center_id=:center_id '.$wh.'order by use_date desc limit '.$start.','.$num;//debugOut($sql);
        $sql = 'select concat(\'(\',use_date,\' : \',name,\') [\',master,\' : \',figure,if(type = \'支出\',\' -> \',\' <- \'),sider,\'] {\',plus_desc,\':\',explanin,\'}\') as tpl from finc_setview where user_code=:user_code '.$wh.'order by use_date desc limit '.$start.','.$num;//debugOut($sql);
        $data = Db::query($sql,['user_code'=>uInfo('code')]);
        $i = 1;
        foreach($data as $v){
            if(empty($v['tpl'])) continue;
            $html .= '<tr><td>'.$i.'</td><td><a href="javascript:void(0);" class="selected">'.$v['tpl'].'</a></td></tr>';
            $i++;
        }
        if($html) $html = '<table class="table"><tr><th>序号</th><th>内容<th></tr>'.$html.'</table>';
        else $html = null;
        return $html;
    }
    // 财务记账
    public function edit()
    {
        $this->loadScript([
            'auth'=>'','title'=>'财务列表-Conero-财物系统','bootstrap'=>true,'require'=>['echart'],'js'=>['Fincset/edit']
        ]);        
        $uInfo = uInfo();
        $this->_edit4impdata($uInfo);
        $fnce = new Finance();
        $pages = [
            'masterSelector' => '<select name="master_id" class="form-control input-sm" onChange="app.masterListener(this)" required>'.$fnce->master().'</select>',
            'purposeSelector' => $fnce->purpose('<select name="purpose" class="form-control input-sm" required>')
        ];
        $siderSeletor = '<select name="sider_id" class="form-control" onChange="app.siderListener(this)" required>'.$fnce->master('all').'</select>';
        $this->_JsVar('siderSelector',$siderSeletor);

        // 今日新增/维护
        $count = Db::table('finc_set')->where('center_id=\''.$uInfo['cid'].'\' and set_date like \''.sysdate('date').'%\'')->count();
        if($count > 0){ // 触发器运行速度缓慢-遂先检测
            $todayRpt = $this->_query('call fincset_log_sp("'.uInfo('code').'",null)',true);
            $pages['todayRpt'] = $this->mkFincsetTable($todayRpt,['id'=>'fset2today_panel','title'=>'今日登账记录']);
            // println($todayRpt);
        }

        $this->assign('pages',$pages);        
        
        return $this->fetch();
    }
    // 文件导入处理
    private function _edit4impdata($uInfo){
        $post = $_POST;
        if(isset($post['formid']) && $post['formid'] == '_impdata_'){
            $fmt = $post['format'];
            // $filename = ROOT_PATH.'Files/UpFiles/finset('.sysdate('date').')_'.time().'.'.array_pop(explode('.',$_FILES['fimp']['name']));
            $filename = ROOT_PATH.'Files/UpFiles/finset('.sysdate('date').')_'.$uInfo['nick'].'_'.time().'.'.$fmt;
            move_uploaded_file($_FILES['fimp']['tmp_name'],$filename);                        
            $fdata = [];
            if($fmt == 'cro'){
                $content = file_get_contents($filename);
                foreach(explode(',',$content) as $v){
                    $tmp = fcset_parse($v);
                    if(isset($tmp[0])) $fdata[] = $tmp[0];
                }
            }
            else if($fmt == 'csv'){
                if (($handle = fopen($filename, "r")) !== FALSE){
                    while (($v = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $fdata[] = $v;
                    }
                }
            }
            if($fdata && count($fdata) > 0){
                $this->_JsVar('record',$fdata);
                // debugOut($fdata);
            }
            //println($fdata);
        }
    }
    // 文件导出处理 - 数据量过大是考虑 压缩包
    public function _edit4expdata($data){
        $format = $data['format'];
        $order = $data['okey'].' '.$data['ovalue'];
        $content = '';
        // thinkPHP 框架提供 filed 无法达到要求
        $field = ($format == 'cro')?
            // 'concat(\'(\',use_date,\' : \',name,\') [\',master,\':\',figure,if(type = \'支出\',\' -> \',\' <- \'),sider,\'] {\',plus_desc,\':\',explanin,\'}\') as tpl':
            'select concat(\'(\',use_date,\' : \',name,\') [\',master,\' : \',figure,if(type = \'支出\',\' -> \',\' <- \'),sider,\'] {\',plus_desc,\':\',explanin,\'}\') as tpl from finc_setview where user_code=:user_code' :
            'use_date,name,master,figure,type,sider,plus_desc,explanin'
            ;
        // 全部
        if($data['exptype'] == 'all'){
            if($format == 'cro'){
                $sql = $field.' order by '.$order;//debugOut($sql);
                $result = Db::query($sql,['user_code'=>uInfo('code')]);
            }
            else $result = Db::table('finc_setview')->where('user_code',uInfo('code'))->field($field)->order($order)->select();        
            
        }
        // 部分 - 条件筛选
        else{
            if($format == 'cro'){
                $sql = $field
                    .(empty($data['skey'])? '':' and '.$data['skey'].' like \'%'.$data['svalue'].'%\'')
                    .((!empty($data['sudate']) && !empty($data['eudate']))? 'and use_date between \''.$data['sudate'].'\' and \''.$data['eudate'].'\'':'')
                    .' order by '.$order;//debugOut($sql);
                $result = Db::query($sql,['user_code'=>uInfo('code')]);
            }
            else{
                $map = ['user_code'=>uInfo('code')];
                if(!empty($data['skey'])) $map[$data['skey']] = ['like','%'.$data['svalue']];
                if(!empty($data['sudate']) && !empty($data['eudate'])) $map['use_date'] = ['between time',[$data['sudate'],$data['eudate']]];                  // where('create_time','between time',['2015-1-1','2016-1-1']);
                $result = Db::table('finc_setview')->where($map)->field($field)->order($order)->select();
            }
        }
        // 文本组合
        $count = count($result);$i=1;
        foreach($result as $v){
            if($format == 'cro' && (empty($v) || empty($v['tpl']))) continue;   // 格式检测            
            $content .= (($format == 'cro')? $v['tpl'] : implode(',',$v))
                        . ($i < $count? ",\r\n":'') 
                        ;
            
            $i += 1;
        }
        $config = [
            'name' => 'fsetreport('.sysdate('date').')_'.uInfo('nick').'_'.time().'.'.$format
        ];
        \hyang\Download::setConfig($config);
        \hyang\Download::loadContent($content);
        //println($data);
    }
    // 数据修改 - 新增
    public function save()
    {
        $data = $_POST;
        
        $formid = isset($data['formid'])? $data['formid']:'';
        if(isset($data['formid'])) unset($data['formid']);
        switch($formid){
            case 'expdata4edit':
                return $this->_edit4expdata($data);
                break;
        }

        $i = 0;$ect = 0;$act = 0;
        foreach($data as $v){
            $record = json_decode($v,true);
            // 如果事务甲乙方由于-js 描述写入失败时自动处理 - app.siderListener(this) 事件处理失败 - 2017年1月15日 星期日
            if(!empty($record['master_id']) && empty($record['master'])){
                $record['master'] = $this->croDb('finc_organ')->where('id',$record['sider_id'])->value('name');
            }
            if(!empty($record['sider_id']) && empty($record['sider'])){
                $record['sider'] = $this->croDb('finc_organ')->where('id',$record['sider_id'])->value('name');
            }
            // println($record,'');die;
            if(isset($record['finc_no'])){
                $map = ['finc_no'=>$record['finc_no']];unset($record['finc_no']);
                if(Db::table('finc_set')->where($map)->update($record)) $ect += 1;
            }
            else{
                $record['center_id'] = uInfo('cid');
                $record['actbak'] = '<br>账户名称（'.uInfo('name').'）于 '.sysdate().'新增此条财物账单！'; 
                if(Db::table('finc_set')->insert($record)) $act += 1;
            }
            $i += 1;
        }
        if($i > 0){
            $ct = count($data);
            $rpt = '本次数据操作工程中'.(($ct-$i) >0? '，修改【'.$ect.'/'.($ct-$i).'】(成功/总数)，':'').($i >0? '，新增【'.$act.'/'.$i.'】(成功/总数)':'');
            $this->success($rpt);
        }
        println($record);
    }
     // 快速登账后台数据保存
    public function fasetsave()
    {
        $data = $_POST;
        debugOut($data,true);
    }

    // ajax 操作
    public function ajax()
    {
        isset($_POST['item']) or die('404');
        $item = $_POST['item'];
        $ret = '';
        switch($item){
            case 'getDataByNo':
                $ret = Db::table('finc_set')->where('finc_no',$_POST['no'])->field('finc_no,use_date,name,type,figure,master,master_id,purpose,sider,sider_id,explanin')->find();
                if(empty($ret['sider_id'])) unset($ret['sider_id']);
                $ret = is_array($ret)? json_encode($ret,true):'N';
                break;
            case 'getSearchData':
                $data = Db::table('finc_setview')->where('`'.$_POST['skey'].'` like \'%'.$_POST['svalue'].'%\' and `user_code`=\''.uInfo('code').'\'')->select();
                //debugOut('`'.$_POST['skey'].'` like \'%'.$_POST['svalue'].'%\'');
                $tableTh = '<table><tr><th>#</th><th>日期</th><th>事务甲方</th><th>类型</th><th>金额</th><th>用途</th><th>名称</th><th>事务乙方</th><th>备注</th><th>操作</th></tr>';
                $i = 1;
                foreach($data as $v){
                    //$ret .= '<tr><td>'.$i.'</td></tr>';
                    $ret .= '<tr dataid="'.$v['finc_no'].'"><td>'.$i.'</td><td>'.$v['use_date'].'</td><td>'.$v['master'].'</td><td>'.$v['type'].'</td><td>'.$v['figure'].'</td><td>'.$v['plus_desc'].'</td><td>'.$v['name'].'</td><td>'.$v['sider'].'</td><td>'.$v['explanin'].'</td><td><a href="javascript:void(0);" dataurl="/Conero/finance/fincset?action='.base64_encode('del$$'.$v['finc_no']).'" class="del_btn">删除</a><a href="javascript:void(0);" onClick="App.edit_btn(this)">修改</a><a href="javascript:void(0);" onClick="App.fabout_btn(this)">详情</a></td></tr>';
                    $i++;
                }
                if($ret) $ret = $tableTh.$ret.'</table>';
                else $ret = '未检索到数据';
                break;
            case 'get2week4edit':
                $data = $this->_query('call fincset_log_sp("'.uInfo('code').'","2week")',true);
                $ret = $this->mkFincsetTable($data,['id'=>'fset2week_panel']);
                /*
                // return $data;
                $tr = '';
                $i = 1;
                foreach($data as $v){
                    $tr .= '<tr dataid="'.$v['finc_no'].'"><td>'.$i.'</td><td>'.$v['use_date'].'</td><td>'.$v['master'].'</td><td>'.$v['type'].'</td><td>'.$v['figure'].'</td><td>'.$v['plus_desc'].'</td><td>'.$v['name'].'</td><td>'.$v['sider'].'</td><td>'.$v['explanin'].'</td><td><a href="javascript:void(0);" dataurl="/Conero/finance/fincset?action='.base64_encode('del$$'.$v['finc_no']).'" class="del_btn" onClick="app.del_btn(this)">删除</a><a href="javascript:void(0);" onClick="app.edit_btn(this)">修改</a><a href="javascript:void(0);" onClick="app.fabout_btn(this)">详情</a></td></tr>';
                    $i++;
                }
                if($tr){
                    $ret = '
                        <div class="panel panel-default" id="fset2week_panel">
                        <div class="panel-heading"><a href="javascript:void(0);" onClick="app.panelToggle(this)"><h4>最近两周登账记录('.($i-1).')</h4></a></div>
                        <div class="panel-body">
                        <table class="table">
                            <tr><th>#</th><th>日期</th><th>事务甲方</th><th>类型</th><th>金额</th><th>用途</th><th>名称</th><th>事务乙方</th><th>备注</th><th>操作</th></tr>
                            '.$tr.'
                        </table>
                        </div>
                        </div>
                    ';
                }
                */
                break;
            case 'listener4edit':
                $map = bsjson($_POST['map']);
                $wh = [];
                foreach($map as $k=>$v){
                    $wh[] = $k.' like \'%'.$v.'%\'';
                }
                if(!empty($wh)){
                    $wh = count($wh) == 1? ' and '.$wh[0]:implode(' and ',$wh);
                    $map = [];
                }
                else $wh = '';
                $map['user_code'] = uInfo('code');        
                $sql = 'select concat(\'(\',use_date,\' : \',name,\') [\',master,\' : \',figure,if(type = \'支出\',\' -> \',\' <- \'),sider,\'] {\',plus_desc,\':\',explanin,\'}\') as tpl from finc_setview where user_code=:user_code'.$wh.' order by set_date desc limit 1,5';
                $data = Db::query($sql,$map);
                return $data;                
                break;
            case 'searchmkchart4edit':
                $map = bsjson($_POST['map']);
                $map['user_code'] = uInfo('code');
                // 最大限度 100 条
                $option = [];$xAxis = [];$series = [];
                // $qData = Db::table('finc_setview')->where($map)->field('use_date,sum(figure) as figure')->page(1,100)->group('use_date')->order('use_date desc')->select();           
                $qData = Db::table('finc_setview')->where($map)->field('use_date,sum(figure) as figure')->group('use_date')->order('use_date desc')->select();           
                foreach($qData as $v){
                    $xAxis[] = $v['use_date'];
                    $series[] = $v['figure'];
                }
                $option = [
                    'xAxis' => ['data'=>$xAxis],
                    'series' => ['data'=>$series]
                ];
                return $option;
                break;
        }
        echo $ret;
        die;
    }
    // 通用表格生成器 - 2017年1月8日 星期日
    private function mkFincsetTable($data,$option)
    {
        $tr = '';$ret = '';
        $i = 1;
        $id = $option['id'];
        $title = isset($option['title'])? $option['title']:'最近两周登账记录';
        foreach($data as $v){
            $tr .= '<tr dataid="'.$v['finc_no'].'"><td>'.$i.'</td><td>'.$v['use_date'].'</td><td>'.$v['master'].'</td><td>'.$v['type'].'</td><td>'.$v['figure'].'</td><td>'.$v['plus_desc'].'</td><td>'.$v['name'].'</td><td>'.$v['sider'].'</td><td>'.$v['explanin'].'</td><td><a href="javascript:void(0);" dataurl="/Conero/finance/fincset?action='.base64_encode('del$$'.$v['finc_no']).'" class="del_btn" onClick="app.del_btn(this)">删除</a><a href="javascript:void(0);" onClick="app.edit_btn(this)">修改</a><a href="javascript:void(0);" onClick="app.fabout_btn(this)">详情</a></td></tr>';
            $i++;
        }
        if($tr){
            $ret = '
                <div class="panel panel-default" id="'.$id.'">
                <div class="panel-heading"><a href="javascript:void(0);" onClick="app.panelToggle(this)"><h4>'.$title.'('.($i-1).')</h4></a></div>
                <div class="panel-body">
                <table class="table">
                    <tr><th>#</th><th>日期</th><th>事务甲方</th><th>类型</th><th>金额</th><th>用途</th><th>名称</th><th>事务乙方</th><th>备注</th><th>操作</th></tr>
                    '.$tr.'
                </table>
                </div>
                </div>
            ';
        }
        return $ret;
    }
}
