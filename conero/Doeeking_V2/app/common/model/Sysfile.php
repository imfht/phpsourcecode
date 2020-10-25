<?php
/* 2017年2月25日 星期六 家庭字辈
 *
 */
namespace app\common\model;
use app\common\model\BaseModel;
class Sysfile extends BaseModel{
    protected $table = 'sys_file';
    protected $pk = 'file_id';
    // 获取文件列表
    // $map = ['grp_more'=>'grp_moreMK']
    public function getFileList($map,$fileGroup=null,$personId=true)
    {
        list($key) = array_keys($map);
        $wh = [
            'grp_more'      => $key,
            'grp_moreMK'    => $map[$key],
        ];
        if($fileGroup) $wh['file_group'] = $fileGroup;
        if($personId){
            $uInfo = uInfo();
            $wh = array_merge($wh,['user_code'=>$uInfo['code'],'file_own'=>$uInfo['nick']]);
        }
        return $this->where($wh)->field('file_type as `type`,file_size as `size`,file_name as `name`,url_name as `url`,edittm,remark')->select();
    }
}