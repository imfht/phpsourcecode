<?php
namespace app\common\model;

use think\Model;

class Config extends Model
{
    public function getStatusTurnAttr($value, $data)
    {
        $turnArr = [0=>'停用', 1=>'在用'];
        return $turnArr[$data['status']];
    }

    /**
     * 读取配置类型
     * @return array
     */
    public function getTypeList() {
        $typeList = [
            'text'   	=> '文本(单行)',
            'textarea' 	=> '文本(多行)',
//          'number'   	=> '数字',
            'image'    	=> '图片',
//          'datetime' 	=> '日期时间',
            'select'   	=> '下拉列表',
//          'selects'  	=> '列表(多选)',
//          'checkbox' 	=> '复选框',
//          'radio'    	=> '单选框',
//          'array'    	=> '数组',
        ];
        return $typeList;
    }

    /**
     * 读取分类分组列表
     * @return array
     */
    public static function getGroupList() {
    	$groupList = cache('PEIZHIZU_TYPE');
		if(!$groupList){
			$groupLists = Config::column('type','id');
			$groupList = array_unique($groupLists);
			cache('PEIZHIZU_TYPE',$groupList);
		}
        return $groupList;
    }

    /**
     * @Title: confv
     * @Description: todo(获取配置值)
     * @param string $k
     * @param string $type
     * @return string
     * @author 戏中有你
     * @date 2018年1月17日
     * @throws
     */
    public function confv($k, $type){
        $where = [
            'k' => $k,
            'type' => $type
        ];
        $result = $this->where($where)->value('v');
        return htmlspecialchars_decode($result);
    }
}