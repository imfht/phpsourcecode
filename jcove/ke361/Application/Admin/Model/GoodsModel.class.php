<?php
namespace Admin\Model;
use Think\Model;
class GoodsModel extends Model{
	protected $_auto = array(
	    array('create_time',NOW_TIME,self::MODEL_INSERT),
	    array('add_id', UID,self::MODEL_INSERT),
	    array('add_uname',USER_NAME,self::MODEL_INSERT),
	    array('status',1,self::MODEL_INSERT)
	);
	/* 自动验证规则 */
	protected $_validate = array(
	    array('name', 'require', '标题不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
	    array('name', '1,80', '标题长度不能超过80个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
	    array('description', '1,200', '简介长度不能超过200个字符', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
	    array('cate_id', 'require', '分类不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_INSERT),
	    array('cate_id', 'require', '分类不能为空', self::EXISTS_VALIDATE , 'regex', self::MODEL_UPDATE),
	   	);

    public function delGoods($id){
        $this->where("id='{$id}'")->delete();
    }

    public function info($id){
        $where = array(
            'id' => $id
        );
        $res = $this->where($where)->find();
        $res['pic_url'] = get_image_url($res['pic_url']);
        return $res;
    }
	/*
	* 添加商品，num_iid不允许重复
	*/
	public function addGoods($data){
		if(!empty($data) ){
			$where['num_iid'] = $data['num_iid'];
			$this->create($data);
		}else{
			$where['num_iid'] = $this->data['num_iid'];
		}
		$info = $this->where($where)->find();
		if(empty($info)){
			return $this->add() ? 1: 0;
		}
		return -2;
	}

    public function goodsCount($where=1){
        return $this->where($where)->count();            
    }
    public function getGoodsList($Page,$where){
        $goods = $this->where($where)->order('`sort` DESC,id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($goods as $k => $v){
            $goods[$k]['pic_url'] =get_image_url($v['pic_url']);
        }
        return $goods;
    }
   
}