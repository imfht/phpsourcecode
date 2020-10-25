<?php 
/**
 * fusionCharts 报表适配器
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date  2013-7-20
 */
namespace framework\libraries;

class ChartsXml
{
    private $config = array(
            'charts_title'=>'',
            'start_time'=>null,
            'end_time'=>null,
            'units'=>'hour',
            'fields'=>array('pv'=>'访问量(pv)','uv'=>'访客量(uv)','ip'=>'IP'),
            'color'=>array(
                'pv'=>'','uv'=>'','ip'=>''
            ),
        );
        
    private $xml ='';
    private $horizontal=array();
    
    function __construct($config){
        if(is_array($config))
            $this->config =array_merge($this->config, $config);
        $this->xml ='';
    }
    public function __get($name){
        if(isset($this->config[$name]))
            return $this->config[$name]; 
        return NULL;
    }
    public function __set($name,$value){
        if(isset($this->config[$name]))
            $this->config[$name] = $value;
    }
    public function __isset($name){
        return isset($this->config[$name]);
    }
    /**
     * 横轴
     */
    public function horizontal(){
        $time['start'] = strtotime($this->start_time);
        $time['end']  = strtotime($this->end_time);
        if($this->units=='hour'){
            
            $datenum =($time['end']-$time['start'])/(3600);
            //时间轴
            $this->xml['horizontal'] ='<categories>';
            for($i=0;$i<$datenum;$i++){
                $d =$time['start']+$i*3600;
                $this->xml['horizontal'] .="<category label='".date('H',$d)."'/>";
                $this->horizontal[] =(int)date('H',$d);
            }
            $this->xml['horizontal'] .='</categories>';
            
        }elseif($this->units=='date'){
            
            $datenum =($time['end']-$time['start'])/(3600*24)+1;
            //时间轴
            $this->xml['horizontal'] ='<categories>';
            for($i=0;$i<$datenum;$i++){
                $d =$time['start']+$i*3600*24;
                $this->xml['horizontal'] .="<category label='".date('m/d',$d)."'/>";
                $this->horizontal[] =date('Y-m-d',$d);
            }
            //print_r($datelist);
            $this->xml['horizontal'] .='</categories>';
        }elseif($this->units=='week'){
            $datenum =($time['end']-$time['start'])/(3600*24)+1;
            //时间轴
            $week_map =array();
            for($i=0;$i<$datenum;$i++){
                $d =  $time['start']+$i*3600*24;
                $week = date('W',$d);
                $week_map[$week]['start'] = $week_map[$week]['start']?min($week_map[$week]['start'],$d):$d;
                $week_map[$week]['end'] = max($week_map[$week]['end'],$d);
                
            }
            
            $this->xml['horizontal'] ='<categories>';
            foreach($week_map as $key=>$val){
                $this->xml['horizontal'] .="<category label='".date('m/d',$val['start']).'-'.date('m/d',$val['end'])."'/>";
                $this->horizontal[] =$key;
            }
            
            //print_r($this->horizontal);
            $this->xml['horizontal'] .='</categories>';
            //echo $this->xml['horizontal'];
        }elseif($this->units=='month'){
            $datenum =($time['end']-$time['start'])/(3600*24)+1;
            //时间轴
            $week_map =array();
            for($i=0;$i<$datenum;$i++){
                $d = $time['start']+$i*3600*24;
                $month = date('n',$d);
                $week_map[$month]['start'] = $week_map[$month]['start']?min($week_map[$month]['start'],$d):$d;
                $week_map[$month]['end'] = max($week_map[$month]['end'],$d);  
            }
            
            $this->xml['horizontal'] ='<categories>';
            foreach($week_map as $key=>$val){
                $this->xml['horizontal'] .="<category label='".date('m/d',$val['start']).'-'.date('m/d',$val['end'])."'/>";
                $this->horizontal[] =$key;
            }
            $this->xml['horizontal'] .='</categories>';
        }
    }
    /**
     * 数据轴
     */
    public function vertical(&$data){
        //数据轴
        $this->xml['vertical'] = '';
        foreach($this->fields as $field=>$name){
            $this->xml['vertical'] .="<dataset seriesName='{$name}' color='' anchorBorderColor=''>";
            foreach($this->horizontal as $date){
                if(!isset($data[$date][$field])){
                    $data[$date][$field] = 0;
                }
                $this->xml['vertical'] .="<set value='{$data[$date][$field]}'/>";
            }
           $this->xml['vertical'] .= "</dataset>";
        }
    }
    /**
     * 生成xml
     */
    public function xml(&$data){
        //if(!$data)
            //return false;
        $this->horizontal();
        $this->vertical($data);
        $this->xml['header'] = "<?xml version='1.0' encoding='utf-8'?>";
        $this->xml['description'] = "<chart caption='{$this->charts_title}' numdivlines='9' lineThickness='3' showValues='0' numVDivLines='22' formatNumberScale='1' labelDisplay='ROTATE' slantLabels='1' anchorRadius='2' anchorBgAlpha='50' showAlternateVGridColor='1' anchorAlpha='100' animation='1' limitsDecimalPrecision='0' divLineDecimalPrecision='1'>";
        $this->xml['footer']="</chart>";
        $xml =$this->xml['header'].$this->xml['description'].$this->xml['horizontal'].$this->xml['vertical'].$this->xml['footer'];
        return $xml;
    }
    
}