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
class HelpCategoryModel extends CommonModel{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('name','require','栏目名称必须填写', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('name', '1,32', '栏目名称长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
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
        $data =$this->where($map)->limit($this->start,$this->size)->order('sort desc,id DESC')->select();//根据条件获取数据
        return $this->getDatas($data,$map);//对数据进行处理
    }

    /**
     * 对文章栏目进行删除，栏目下存在文章就无法删除
     * @param $id 栏目id
     * @return int  删除状态   MSUCCESS表示成功  MFAIL表示栏目下还有文章 MMSG表示删除失败
     */
    public function del($ids){
        if(is_array($ids)){//多个删除
              foreach($ids as $id){
                  $map['category_id']=$id;
                  $data=D('Help')->where($map)->limit(1)->select();
                  if($data){
                      return self::MFAIL;//MFAIL表示有数据
                  }
              }
            $map['id']=array('in',$ids);;
            $data=$this->where($map)->delete();
            return empty($data)?self::MMSG:self::MSUCCESS;//MMSG表示删除失败  MSUCCESS表示删除成功
        }else{//单个删除
            $map['category_id']=$ids;
            $data=D('Help')->where($map)->limit(1)->select();
            if($data){
                return self::MFAIL;//MFAIL表示有数据
            }
            $map['id']=$ids;
            $data=$this->where($map)->delete();
            return empty($data)?self::MMSG:self::MSUCCESS;//MMSG表示删除失败  MSUCCESS表示删除成功
        }

    }
}
