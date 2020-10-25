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
class HelpModel extends CommonModel{
    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('title','require','文章标题必须填写', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,32', '文章标题长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('category_id', 'require', '请选择栏目', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('content', 'require', '文章内容必须填写', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
      );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('publish_time', NOW_TIME, self::MODEL_BOTH)
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
        $map['h.`id`|title'] = array($condition, $condition, '_multi'=>true); //搜索条件
        $data=$this->table($this->trueTableName.' h')->join(D('HelpCategory')->trueTableName.' hc on h.category_id=hc.id')
            ->field('h.*,hc.name')->where($map)->limit($this->start,$this->size)
            ->order('h.id DESC')->select();
        foreach($data as $k=>$v){
            $data[$k]['publish_time']=date('Y-m-d H:i:s',$v['publish_time']);//对时间进行格式化
        }
        $cmap['id|title'] = array($condition, $condition, '_multi'=>true); //分页条件，和上面的搜索条件不同
        return $this->getDatas($data,$cmap);//对数据进行处理
    }
}
