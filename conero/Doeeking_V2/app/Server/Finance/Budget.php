<?php
namespace app\Server\Finance;
use DateTime;
use hyang\Logic;
use think\Db;
class Budget extends Logic
{
    // 获取-例行财务列表   
    public function getRegularList($budid,$startDt=null,$option=null){
        $num = isset($option['num'])? $option['num']:50;            // 数据最大长度
        $year = isset($option['year'])? $option['year']:null;       // 某一年数据
        // $year = 2016;
        $startDt = $startDt? $startDt:date('Y-m-d',time());
        if(is_array($budid)){
            $data = $budid;
            $budid = $data['bud_id'];
        }
        else $data = Db::table('finc_budget')->where('bud_id',$budid)->find();
        $rfn = $data['related_fn'];
        // 类型
        if(substr_count($rfn,'-') > 0) $rfn = str_replace('-','',$rfn);
        $count = substr_count($rfn,'+');        
        if($count > 0){
            $type = '收入';
            $rfn = str_replace('+','',$rfn);
        }        
        else $type = '支出';
        $fdt = $data['from_date'];
        // figure
        $count = substr_count($rfn,'/');
        if($count > 0){
            $figure = substr($rfn,$count+2);
            $rfn = str_replace('/'.$figure,'',$rfn);
        }
        else $figure = 0;
        // 时间差类型
        $dtMk = substr($rfn,strlen($rfn)-1);
        if($dtMk) $rfn = str_replace($dtMk,'',$rfn);
        $rfn = is_numeric($rfn)? intval($rfn):0;
        $mkArr = [
            'dtvalue' => $rfn,'type' => $type,'figure' => $figure,'from_dt'=>$fdt,'format'=>$dtMk
        ];
        if((is_string($option) && strtolower($option) == 'feek') || (isset($option['feek']) && $option['feek'])) return $mkArr;
        // 列表生成开始----
            // 月度   
        $trs = '';$i = 1;
        $figureSum = 0;     
        $ufigureSum = 0;   // 实际金额
        $uCount = 0;       // 执行数据条数
        if($dtMk == 'M'){
            $dtObj = new DateTime($fdt);
            $m1 = intval($dtObj->format('m'));
            $d1 = intval($dtObj->format('d'));
            $y1 = intval($dtObj->format('Y'));
            $m2 = intval(date('m',time()));
            $y2 = intval(date('Y',time()));
            $y = intval(date('Y',time()));            
            // 筛选某一年的数据
            if($year){
                $y = $year; $y1 = $year;
            }            
            for($y; $y >= $y1; $y--){                               
                $m2 = ($y == $y2)? $m2:12;
                $minMonth = ($y == $y1)? $m1:1;
                for($m=$m2; $m >= $minMonth; $m--){
                    $figureSum = $figureSum + $figure;
                    $tmpDt = $y.'-'.($m<10? '0':'').$m.'-'.$d1;
                    $actor = '<a href="javascript:void(0);" class="w2finance">记账</a>';
                    // println(['bud_id'=>$budid,'pdate'=>$tmpDt]);
                    $planQuery = Db::table('finc_plan')->where(['bud_id'=>$budid,'pdate'=>$tmpDt])->find();
                    $actor = empty($planQuery)? $actor:'<a href="javascript:void(0);" class="r2finance" dataid="'.$planQuery['no'].'">详情</a>';
                    if(!empty($planQuery)){
                        $ufigureSum = $ufigureSum + $planQuery['usumfg'];
                        $uCount = $uCount + 1;
                        $trs .= '<tr><td>'.$i.'</td><td dataid="date">'.$tmpDt.' / '.$planQuery['udate'].' ('.$planQuery['dtday'].')</td><td dataid="type">'.$type.'</td><td dataid="figure">'.$figure.' / '.$planQuery['usumfg'].' ('.$planQuery['dtfigure'].')</td><td>'.$actor.'</td></tr>';
                    }
                    else $trs .= '<tr><td>'.$i.'</td><td dataid="date">'.$tmpDt.'</td><td dataid="type">'.$type.'</td><td dataid="figure">'.$figure.'</td><td>'.$actor.'</td></tr>';
                    // 数据过长时中断
                    if($i >= $num) break;
                    $i++;
                }
            }
        }
        $days =  getDays($fdt,sysdate());   
        // println($rfn,$type,$figure,$fdt,$dtMk);
        // println($trs);
        return ['trs'=>$trs,'fsum'=>$figureSum,'count'=>($i-1),'day'=>$days,'usumfg'=>$ufigureSum,'uCount'=>$uCount];
        // return $trs;
    }
    public function rglEchartOption($budid,$startDt=null)
    {
        $option = [];
        // $option = $this->getRegularList($budid,$startDt,'feek');
        $data = Db::table('finc_plan')->where('bud_id',$budid)->field('dtday,dtfigure,sumsingle,usumfg,pdate')->order('pdate')->select();
        $xAxis = [];$usumfg = [];$dtday = [];$dtfigure = [];
        foreach($data as $v){
            $xAxis[] = $v['pdate'];
            $usumfg[] = $v['usumfg'];
            $dtday[] = $v['dtday'];
            $dtfigure[] = $v['dtfigure'];
        }
        $series = [
            ['name'=>'日期差','type'=>'line','data'=>$dtday,'yAxisIndex'=>1],
            ['name'=>'实际执行金额','type'=>'line','data'=>$usumfg],            
            ['name'=>'资金差','type'=>'line','data'=>$dtfigure]
            // ['name'=>'实际执行金额','type'=>'line','data'=>$usumfg,"label" =>["normal"=> ["show"=>true,"position"=> 'insideRight']]]
        ];
        $option['title'] = ['text'=>'例行财务','subtext'=>'制表时间：'.sysdate()];
        // $option['tooltip'] = ['trigger'=>'axis','axisPointer'=>['type'=>'shadow']];  
        $option['legend'] = ['data'=>['日期差','实际执行金额','资金差']];
        $option['grid'] = json_decode('{left: "3%",right: "4%",bottom: "3%",containLabel: true}',true);
        $option['xAxis'] = ['data'=>$xAxis];
        $option['series'] = $series;
        return $option;
    }
}
