<?php
/**
 * Created by PhpStorm.
 * User: zhoujun
 * Date: 2018/3/30
 * Time: 23:04
 */

namespace app\timer\lib;


use think\Log;

class TimingWheel
{

    public $wheel = [];

    const LENGTH = 3600;

    public function __construct()
    {
        $this->wheel = array_fill(0,self::LENGTH,[]);
    }

    /**
     * 找出当前需要执行的任务，并将其从时间轮片中删除
     * @return array
     */
    public function popSlots(){
        $cur_index = $this->getCurIndex();
        $list = $this->wheel[$cur_index];
        $slots = [];
        if(!empty($list)){
            foreach ($list as $key => $item){
                if($item['round'] == 0){
                    $slots[] = $item['data'];
                    unset($this->wheel[$cur_index][$key]);
                }else{
                    $this->wheel[$cur_index][$key]['round'] -= 1;
                }
            }
        }
        return $slots;
    }

    /**
     * @param $interavl
     * @param $data
     * 新增一个数据到时间仑片中
     */
    public function add($interavl,$data){
        $cur_index = $this->getCurIndex();
        $total_index = $cur_index + $interavl;
        $round = intval($interavl/self::LENGTH);
        $index = $total_index%self::LENGTH;
        if($interavl % self::LENGTH == 0){
            $round -= 1;
        }
        $slot = [
            'round' => $round,
            'data' => $data
        ];
        Log::info("slot:".print_r($slot,true));
        $this->wheel[$index][] = $slot;
    }

    /**
     * @return float|int
     * 获取当前时间轮片已走到哪个节点
     */
    public function getCurIndex(){
        $now_timestamp = Timer::$now_time;
        $minute = date('i',$now_timestamp);
        $second = date('s',$now_timestamp);
        return (int)$minute*60 + (int)$second;
    }

    /**
     * 重置
     */
    public function clear(){
        $this->wheel = array_fill(0,self::LENGTH,[]);
    }

    /**
     * 调试用
     */
    public function desc(){
        $list = [];
        foreach ($this->wheel as $key => $slots){
            if(!empty($slots)){
                $list[$key] = $slots;
            }
        }
        Log::info(print_r($list,true));
    }

}