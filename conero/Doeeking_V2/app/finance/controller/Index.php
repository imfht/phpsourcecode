<?php
namespace app\finance\controller;
use think\Loader;
use think\Controller;
class Index extends Controller
{
    // 初始化
    public function _initialize(){
        if($this->_initTplCheck(['save','fincset'])) return;
        /*
        $action = request()->action();
        $this->loadScript([
            'auth'=>'','title'=>'Conero-财务系统','js'=>['Index/'.$action],'css'=>['Index/'.$action],'bootstrap'=>true
        ]);
        */
    }
    // 保留模块的- 用于为iframe开发 - 默认风格
    public function index()
    {
        $theme = $this->reportThemeChooseRpt();
        if($theme == 'bootstrap'){return $this->ibootstrap();}
        //println($theme);die;
        $action = request()->action();
        $this->loadScript([
            'title'=>'Conero-财务系统','js'=>['Index/'.$action],'css'=>['Index/'.$action],'bootstrap'=>true
        ]);
        return $this->fetch();
    }
    // bootstrap 风格
    protected function ibootstrap()
    {
        $this->loadScript([
            'title'=>'财务系统 - Conero','js'=>'Index/ibootstrap','bootstrap'=>true
        ]);
        $data = model('Menu')->getMenuList('finance');
        $mainlinks = '';
        foreach($data as $v){
            $v = $v->toArray();
            $mainlinks .= '<li><a href="javascript:void(0);" dataurl="'.$v['url'].'" dataid="'.$v['code_mk'].'">'.$v['descrip'].'</a></li>';
        }       
        $pages = [
            'fyday'  => getDays('2017'),
            'dfyday' => getDays('2017',sysdate()),
            'fmday'  => getDays(date('m',time())),
            'dfmday'  => getDays(date('m',time()),sysdate())
        ];
        $pages['ryday'] = ceil($pages['dfyday']/$pages['fyday']*100);
        $pages['rmday'] = ceil($pages['dfmday']/$pages['fmday']*100);
        $this->assign([
            'mainlinks' => $mainlinks,
            'pages' => $pages
        ]);        
        return $this->fetch('ibootstrap');
    }
    public function fincset()
    {
        $this->loadScript([
            'auth'=>'','title'=>'Conero-财务系统','js'=>['Index/fincset'],'css'=>['Index/fincset'],'bootstrap'=>true
        ]);
        $uInfo = uInfo();
        $js = '';
        $uid = isset($_GET['uid'])? bsjson($_GET['uid']):'';//{type:"",value:""}
        $type = isset($uid['type'])? $uid['type']:null;
        $page = isset($uid['page'])? $uid['page']:null;     
        if($type){
            $value = isset($uid['value'])? $uid['value']:null;
            $data = $this->_query('call fincset_chart_sp(?,?,?,?)',[$uInfo['cid'],$type,$value,$page]);
            //debugOut(['call fincset_chart_sp(?,?,?,null)',[$uInfo['cid'],$type,$value]]);
        }
        else $data = $this->_query('call fincset_chart_sp(?,null,?,?)',[$uInfo['cid'],null,$page]);
        //debugOut($uid);
        $label = [];$num = [];$figure = [];$incount = [];        
        foreach($data as $v){
            $label[] = $v['label'];
            $num[] = $v['num'];
            $figure[] = $v['figure'];
            $incount[] = $v['incount'];
        }        
        $series = [
            ['name'=>'数据条数','type'=>'line','data'=>$num,'yAxisIndex'=>1],
            ['name'=>'总金额','type'=>'line','data'=>$figure],
            ['name'=>'总收入','type'=>'line','data'=>$incount]
        ];
        $tpl = ['year'=>'按全年度','month'=>'按月份','outliney'=>'按年份','organ'=>'财务机构','master'=>'事务甲方','purpose'=>'用途'];
        //$tpl = ['year'=>'按全年度','year_setdate'=>'按全年度','month'=>'按月份','month_setdate'=>'按月份','outliney'=>'按年份','outliney_setdate'=>'按年份','organ'=>'财务机构','organ_setdate'=>'财务机构','master'=>'事务甲方','master_setdate'=>'事务甲方','purpose'=>'用途','purpose_setdate'=>'用途'];
        
        $head = '';
        foreach($tpl as $k=>$v){
            $head .= '<a href="/conero/finance/index/fincset.html?uid='.bsjson(['type'=>$k]).'" class="btn '.(substr_count($type,$k)>0? 'btn-primary':'btn-default').' btn-sm">'.$v.'</a>';
        }
        // 子标题-- 生成器/函数
        $subhead = "";
        $decYearSelected = function($uid=null){
            // select date_format(use_date,'%Y') as `year`,date_format(curdate(),'%Y')+10 as `b`,date_format(curdate(),'%Y')-10 as `e` from finc_set where date_format(use_date,'%Y') between (date_format(curdate(),'%Y')-10) and date_format(curdate(),'%Y')+10 group by date_format(use_date,'%Y') order by date_format(use_date,'%Y') desc
            $html = "";
            $year = isset($uid['value'])? $uid['value']:date('Y');
            $year = substr_count($year,'-')>0? substr($year,0,4):$year;            
            $urlArr = isset($uid['type'])? ['type'=>$uid['type']]:array();
            $col = isset($urlArr['type']) && substr_count($urlArr['type'],'setdate')>0? 'set_date':'use_date';
            $sql = '
                select date_format('.$col.',\'%Y\') as `year` from finc_set where center_id =? and date_format('.$col.',\'%Y\') between ('.$year.'-15) and ('.$year.'+10) group by date_format('.$col.',\'%Y\') order by date_format('.$col.',\'%Y\') desc
            ';
            $data = $this->dbQuery($sql,[uInfo('cid')]);
            foreach($data as $v){
                $y = $v['year'];
                $urlArr['value'] = $y;
                $html .= '<option value="'.$y.'"'.($y == $year? ' selected':'').' dataid="/conero/finance/index/fincset.html?uid='.bsjson($urlArr).'">'.$y.'</option>';
            }
            if($html){
                $plus = '';
                //if(substr_count($uid['type'],'month')) $month = $this->ajax(['item'=>'fincset/getmonth','year'=>$year,'uid'=>(isset($_GET['uid'])? $_GET['uid']:'')]);
                //debugOut([substr_count($uid['type'],'month'),$uid['uid']]);
                if(substr_count($uid['type'],'month')>0) $plus = $this->_ajax(['item'=>'fincset/getmonth','year'=>(isset($uid['value'])? $uid['value']:$year),'uid'=>(isset($_GET['uid'])? $_GET['uid']:'')]);       
                elseif(substr_count($uid['type'],'organ')>0 || substr_count($uid['type'],'master')>0 || substr_count($uid['type'],'purpose')>0){
                    if(isset($uid['value']) && preg_match('/^[0-9\-]+$/',$uid['value'])){
                        //debugOut([$uid,'配备成功',preg_match('/^[0-9\-]+$/',$uid['value'])]);
                        //$year = substr($uid['value'],0,4);
                        $plus = $this->_ajax(['item'=>'fincset/getmonth','year'=>(isset($uid['value'])? $uid['value']:$year),'uid'=>(isset($_GET['uid'])? $_GET['uid']:'')]);
                    }
                    else $plus = '<button type="button" class="btn btn-success btn-xs" onClick="app.addMonthBtn(this)">月份</button>';             
                }   
                $html = '<select onChange="app.yearChange(this)" typeid="'.$uid['type'].'"'.(isset($_GET['uid'])? ' uid="'.(isset($_GET['uid'])? $_GET['uid']:'').'"':'').'>'.$html.'</select>'.$plus;
            }
            //foreach($data as $v){}
            //debugOut($urlArr);
            return $html;
        };
        // 分页获取器
        $setPageBar = function($uid=null){
            $html = '';
            $type = isset($uid['type'])? $uid['type']:'';
            $bind = [uInfo('cid')];$count = 0; 
            
            // select count(*) from (select substr(set_date,1,4) from finc_set group by substr(set_date,1,4) ) a
            if($type == 'outliney_setdate' || $type == 'outliney') $sql = 'select count(*) as ctt from (select substr('.($type == 'outliney'? 'use_date':'set_date').',1,4) from finc_set where center_id = ? group by substr('.($type == 'outliney'? 'use_date':'set_date').',1,4)) a';
            elseif($type == 'organ_setdate' || $type == 'organ') $sql = 'select count(*) as ctt from finc_organ where center_id = ?';
            elseif($type == 'master_setdate' || $type == 'master'){
                $wh = '';
                if(isset($uid['value'])) $wh = ' and '.($type == 'master'? 'use_date':'set_date').' like \''.$uid['value'].'%\'';
                $sql = 'select count(*) as ctt from (select master_id from finc_set where center_id = ?'.$wh.' and master_id is not null group by master_id) a';
            }
            elseif($type == 'purpose_setdate' || $type == 'purpose'){
                $wh = '';
                if(isset($uid['value'])) $wh = ' and '.($type == 'purpose'? 'use_date':'set_date').' like \''.$uid['value'].'%\'';
                $sql = 'select count(*) as ctt from (select plus_desc from finc_setview where center_id = ?'.$wh.' group by plus_desc) a';
            }
            else{             
                $sql = 'select count(*) as ctt from (select use_date from finc_set where center_id = ? group by use_date) a';
            }
            if($sql) $count = $this->dbQuery($sql,$bind);
            if(isset($count[0])) $count = $count[0];
            if(isset($count['ctt'])) $count = $count['ctt'];
            if($count>30){
                $all = ceil(intval($count)/30);$page = isset($uid['page'])? intval($uid['page']):1;
                $html = '<span>';$urlBind = $uid? $uid:array();
                if($page > 1){ $urlBind['page'] = $page-1;$html .= '<a href="/conero/finance/index/fincset.html?uid='.bsjson($urlBind).'"> << </a>';}
                $html .= $page.'/'.$all;
                if($page < $all){$urlBind['page'] = $page+1;$html .= '<a href="/conero/finance/index/fincset.html?uid='.bsjson($urlBind).'"> >> </a>';}
                $html .= '</span>';
            }
            else $html = '数据条数<b>'.$count.'</b>';
            //debugOut([$html,$count]);
            return $html;
        };


        // switch($type){ 语句无效
        if(substr_count($type,"year")>0){
            $subhead = '<a href="/conero/finance/index/fincset.html?uid='.bsjson(['type'=>"year"]).'"'.($type == "year"? ' class="selected"':"").'>使用日期</a>
                <a href="/conero/finance/index/fincset.html?uid='.bsjson(['type'=>"year_setdate"]).'"'.($type != "year"? ' class="selected"':"").'>编辑日期</a>
                '.$decYearSelected($uid).'
            ';
        }
        elseif(substr_count($type,"month")>0){
            $subhead = '<a href="/conero/finance/index/fincset.html?uid='.bsjson(['type'=>"month"]).'"'.($type == "month"? ' class="selected"':"").'>使用日期</a>
                <a href="/conero/finance/index/fincset.html?uid='.bsjson(['type'=>"month_setdate"]).'"'.($type != "month"? ' class="selected"':"").'>编辑日期</a>
                '.$decYearSelected($uid).'
            ';
        }
        elseif(substr_count($type,"outliney")>0){
            $pageBar = $setPageBar($uid);
            $subhead = '<a href="/conero/finance/index/fincset.html?uid='.bsjson(['type'=>"outliney"]).'"'.($type == "outliney"? ' class="selected"':"").'>使用日期</a>
                <a href="/conero/finance/index/fincset.html?uid='.bsjson(['type'=>"outliney_setdate"]).'"'.($type != "outliney"? ' class="selected"':"").'>编辑日期</a>
            ';
            if($pageBar) $subhead .= '<span>'.$pageBar.'</span>';
        }
        elseif(substr_count($type,"organ")>0){
            $pageBar = $setPageBar($uid);
            if(isset($uid['value']))
                $subhead = '<a href="/conero/finance/index/fincset.html?uid='.bsjson(['type'=>"organ"]).'"'.($type == "organ"? ' class="selected"':"").'>使用日期</a>
                    <a href="/conero/finance/index/fincset.html?uid='.bsjson(['type'=>"organ_setdate"]).'"'.($type != "organ"? ' class="selected"':"").'>编辑日期</a>
                    '.$decYearSelected($uid).'
                ';
            else $subhead = $decYearSelected($uid);
            if($pageBar) $subhead .= '<span>'.$pageBar.'</span>';
        }
        elseif(substr_count($type,"master")>0){   
            $pageBar = $setPageBar($uid);         
            if(isset($uid['value']))
                $subhead = '<a href="/conero/finance/index/fincset.html?uid='.bsjson(['type'=>"master"]).'"'.($type == "master"? ' class="selected"':"").'>使用日期</a>
                <a href="/conero/finance/index/fincset.html?uid='.bsjson(['type'=>"master_setdate"]).'"'.($type != "master"? ' class="selected"':"").'>编辑日期</a>
                '.$decYearSelected($uid).'
                ';
            else $subhead = $decYearSelected($uid);
            if($pageBar) $subhead .= '<span>'.$pageBar.'</span>';
        }
        elseif(substr_count($type,"purpose")>0){
            $pageBar = $setPageBar($uid);
            if(isset($uid['value']))
                $subhead = '<a href="/conero/finance/index/fincset.html?uid='.bsjson(['type'=>"purpose"]).'"'.($type == "purpose"? ' class="selected"':"").'>使用日期</a>
                <a href="/conero/finance/index/fincset.html?uid='.bsjson(['type'=>"purpose_setdate"]).'"'.($type != "purpose"? ' class="selected"':"").'>编辑日期</a>
                '.$decYearSelected($uid).'
                ';
            else $subhead = $decYearSelected($uid);
            if($pageBar) $subhead .= '<span>'.$pageBar.'</span>';
        }
        elseif(!isset($uid['type'])){// 按天
            $subhead = '<span>'.$setPageBar($uid).'</span>';
        }
        $subhead = $subhead ? '<div class="subhead">'.$subhead.'<div>':'';
        $head = $head? '<div class="figure-nav">'.$head.'<a href="/conero/finance/index/fincset.html" class="btn '.((substr_count($type,"day")>0 || empty($type))? 'btn-primary':'btn-default').' btn-sm">按天</a>'.$subhead .'</div>':'';
        $this->_echartDiv([
            ['id'=>'figure','test'=>'<img src="/conero/public/img/loading-football.jpg">','head'=>$head]
            //,"bootstrap" => true
        ]);
        $type = str_replace('_setdate','',$type);
        $js .= $this->_echartOption('figure','{
            title: {text: "'.(isset($tpl[$type])? $tpl[$type]:'按年份').'",subtext: (new Date()).sysdate()},
            tooltip:{trigger: "axis",axisPointer:{type:"shadow"}},
            toolbox:{show:true,orient:"horizontal",
                feature:{
                    saveAsImage:{type:"png",name:"财务登账分析",show:true,title:"保存为图像"},
                    dataView : {show: true, readOnly: false},
                    magicType:{show:true,type:["line","bar"],title:"切换"}
                }},
            legend:{data:[\'数据条数\',\'总金额\',\'总收入\']},
            grid:{left: "3%",right: "4%",bottom: "3%",containLabel: true},
            xAxis:{
                type : \'category\',
                data : '.json_encode($label).'
            },
            yAxis:[
                {name:"金额(元)",type:"value",nameGap:20},
                {name:"数据条数",type:"value",position:"right"},
            ],
            series:'.json_encode($series).'
        }');
        return $this->ext_echart($js);
    }
    public function ajax($data=null)
    {
        $data = $data? $data:$_POST;
        return $this->_ajax($data);
        die;
    }
    private function _ajax($data)
    {
        $item = isset($data['item'])? $data['item']:null;
        if($item) unset($data['item']);
        $ret = '';
        switch($item){
            case "fincset/getmonth":
                $year = $data['year'];                
                $year = substr_count($year,'-')>0? substr($year,0,4):$year;
                $uid = is_array($data['uid'])? $data['uid']:bsjson($data['uid']);
                $col = isset($uid['type']) && substr_count($uid['type'],'setdate')>0? 'set_date':'use_date';
                $sql = 'select date_format('.$col.',\'%m\') as `month` from finc_set where center_id = ? and '.$col.' like \''.$year.'-%\' group by date_format('.$col.',\'%m\') order by date_format('.$col.',\'%m\')';
                $data = $this->dbQuery($sql,[uInfo('cid')]);
                $html = '';
                $curmonth = isset($uid['value'])? $uid['value']:date('Y-m');
                foreach($data as $v){
                    $month = $v['month'];
                    $sArr = $uid;
                    $sArr['value'] = $year.'-'.$month;
                    $html .= '<option value="'.$month.'" dataid="/conero/finance/index/fincset.html?uid='.bsjson($sArr).'"'.($curmonth == $sArr['value']? ' selected':'').'>'.$month.'</option>';
                }
                if($html) $ret = '<select class="month" onChange="app.monthChange(this)">'.$html.'</select>';
                //debugOut([$sql,$data]);
                break;
        }
        return $ret;
    }
}