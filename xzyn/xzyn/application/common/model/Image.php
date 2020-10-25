<?php
namespace app\common\model;

use think\Model;

class Image extends Model
{
	//新增和更新自动完成列表
//  protected $auto = ['content'];

//	设置json类型字段
	protected $json = ['imgurl'];

    public function getImgurlAttr($value, $data)
	{
		$imgurl_arr = [];
        if( !empty($data['imgurl']) ){
        	foreach ($data['imgurl'] as $k => $v) {
        		if( !empty($v) ){
        			if( stristr($v, 'http://' ) ){
						$imgurl_arr[] = $v;
					}else{
						$imgurl_arr[] = request()->domain().$v;
					}
        		}
        	}
        }
		return $imgurl_arr;
    }

    public function getAuditTurnAttr($value, $data)	// audit 审核字段【获取器】
    {
        $audit = [ 0 =>' 未审', 1 => '已审' ];
		return $audit[$data['audit']];
    }


}