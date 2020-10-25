<?php
/* 2017年2月25日 星期六 家庭字辈
 *
 */
namespace app\common\model;
use app\common\model\BaseModel;
class Gzibei extends BaseModel{
    protected $table = 'gen_zibei';
    protected $pk = 'zibei_no';    
    // 字辈自动生成字符串
    // $genno 家谱编号
    public function zibei2Str($genno,$limiter=null)
    {
        $sql = 'SELECT group_concat(`zibei`) as `zibeistr` FROM `gen_zibei` where gen_no=? order by ser_no desc';   // 排序无效
        $sql = 'SELECT group_concat(`zibei`) as `zibeistr` FROM (select `zibei` from `gen_zibei` where `gen_no`=? order by `ser_no` asc) a';
        $data = $this->db()->query($sql,[$genno]);
        if($data){
            $data = $data['0']['zibeistr'];
            if($limiter)  $data = str_replace(',',$limiter,$data);
        }
        return $data;
    }
    // 根据字辈的先后关系自动排序
    public function zibeiOrder($genno,$serno=null,$check=true)
    {
        $db = $this->db();
        $isEnd = false;
        if($check){
            $check = $db->where('gen_no',$genno)->count();
            if($check > 0){
                $map = 'parent_no is null and gen_no="'.$genno.'"';
                $data = $db->where($map)->find()->toArray();
                $serno = $serno? $serno: 0;
                if($data){
                    $map = ['zibei_no'=>$data['zibei_no']];
                    $serno = $serno + 1;
                    $svData = ['ser_no'=> $serno];
                    $db->where($map)->update($svData);
                    $map = ['parent_no'=>$data['zibei_no'],'gen_no'=>$genno];
                    return $this->zibeiOrder($map,$serno,false);
                }
            }
            else return ['error'=>-1,'msg'=>'由于字辈中没有数据记录，导致操作失败!'];
        }
        elseif(is_array($genno) && $check == false){
            $data = $db->where($genno)->find();
            if($data) $data = $data->toArray();
            if($data){
                $map = ['zibei_no'=>$data['zibei_no']];
                $serno = $serno + 1;
                $svData = ['ser_no'=> $serno];
                $db->where($map)->update($svData);
                $map = ['parent_no'=>$data['zibei_no'],'gen_no'=>$data['gen_no']];
                $this->zibeiOrder($map,$serno,false);
            }
            $isEnd = true;
        }
        if($isEnd) return ['error' => 0,'msg'=>'字辈排序成功!'];
        return ['error' => -1,'msg'=>'字辈排序失败，原因未知!'];
    }
}