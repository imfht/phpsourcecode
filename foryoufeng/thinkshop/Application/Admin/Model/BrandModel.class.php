<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Model;
use Common\Model\CommonModel;
use Common\Util\Page;
use Common\Util\Tree;
use Think\Model;
/**
 * 菜单模型
 * @author jry <598821125@qq.com>
 */
class BrandModel extends CommonModel{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('name','require','品牌名称必须填写', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '1,32', '品牌名称长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
      );
    /**
     * 返回获取到的数据
     * @return mixed
     */
    public function getData()
    {
        //搜索
        $keyword = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['id|name'] = array($condition, $condition, '_multi'=>true); //搜索条件
        $data =$this->where($map)->limit($this->start,$this->size)->order('sort DESC, id DESC')->select();//根据条件获取数据
        return $this->getDatas($data,$map);//对数据进行处理
    }
}
