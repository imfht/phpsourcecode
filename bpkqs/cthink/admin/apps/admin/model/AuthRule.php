<?php
namespace app\admin\model;
use think\Model;

/**
 * 权限rule
 * @author zhanghd <zhanghd1987@foxmail.com>
 */
class AuthRule extends Model
{
	/**
	 * 库名和表名一起配置(方便之后分库处理)
	 */
	protected $table = 'cthink.__AUTH_RULE__';
	
	const RULE_URL = 1;
    const RULE_MAIN = 2;
	
	public function mainRules($map){
		return \think\Db::table($this->table)->where($map)->column('name,id');
	}
	
	public function updateRules(){
        //需要新增的节点必然位于$nodes
		$nodes = model('Menu')->returnNodes(false);

		//status全部取出,以进行更新
        $map = ['module'=>'admin','type'=>['in','1,2']];
		
        //需要更新和删除的节点必然位于$rules
        $rules = \think\Db::table($this->table)->where($map)->order('name')->select();//$AuthRule->where($map)->order('name')->select();

        //构建insert数据
        $data     = [];//保存需要插入和更新的新节点
        foreach ($nodes as $value){
            $temp['name']   = $value['url'];
            $temp['title']  = $value['title'];
            $temp['module'] = 'admin';
            if($value['pid'] >0){
                $temp['type'] = AuthRule::RULE_URL;
            }else{
                $temp['type'] = AuthRule::RULE_MAIN;
            }
            $temp['status']   = 1;
            $data[strtolower($temp['name'].$temp['module'].$temp['type'])] = $temp;//去除重复项
        }

		//保存需要更新的节点
        $update = [];
        
		//保存需要删除的节点的id
		$ids    = [];
        foreach ($rules as $index=>$rule){
            $key = strtolower($rule['name'].$rule['module'].$rule['type']);
			
            //如果数据库中的规则与配置的节点匹配,说明是需要更新的节点
			if ( isset($data[$key]) ) {
                $data[$key]['id'] = $rule['id'];//为需要更新的节点补充id值
                $update[] = $data[$key];
                unset($data[$key]);
                unset($rules[$index]);
                unset($rule['condition']);
                $diff[$rule['id']]=$rule;
            }elseif($rule['status']==1){
                $ids[] = $rule['id'];
            }
        }
        if ( count($update) ) {
            foreach ($update as $k=>$row){
                if ( $row!=$diff[$row['id']] ) {
					\think\Db::table($this->table)->where(['id'=>$row['id']])->update($row);
                }
            }
        }
        if ( count($ids) ) {
			\think\Db::table($this->table)->where(['id'=>['IN',implode(',',$ids)]])->update(['status'=>-1]);
            //删除规则是否需要从每个用户组的访问授权表中移除该规则?
        }
        if( count($data) ){
			\think\Db::table($this->table)->insertAll(array_values($data));
        }
		return true;
    }
}
