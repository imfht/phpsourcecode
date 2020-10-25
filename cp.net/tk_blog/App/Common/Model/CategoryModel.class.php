<?php
/**
 * 分类表模型
 */
namespace Common\Model;
use Think\Model;
class CategoryModel extends Model {
    //自动验证
    protected $_validate = array(
        array('name', 'require', '分类名称不能为空.^_^', 1 ),
        array('type', 'require', '分类类型必须选择.^_^', 1 ),
        array('order', 'require', '排序不能为空.^_^', 1 ),
    );

    //添加或编辑操作
    public function send_addData(){
        if (!$this->create()) return false;
        $cid = I('post.cid',0,'intval');
        $status = I('post.status') ? '1' : '0';
        $data = array(
            'name' => trim(I('post.name')),
            'type' => trim(I('post.type')),
            'description' => trim(I('post.description','')),
            'parent' => 0,
            'order' => I('post.order',0,'intval'),
            'status' => (int) $status
        );
        if ($cid) {
            //编辑操作
            $this->where(array('cid'=>$cid))->save($data);
            return true;
        } else {
            //添加操作
            if ($this->add($data)) {
                return true;
            } else {
                $this->error = '操作失败.ㄒoㄒ~';
                return false;
            }
        }
    }

    //查询一条数据
    public function getFindData($cid){
        return $this->where(array('cid'=>$cid))->find();
    }

    //删除数据
    public function execDelData($cid){
        return $this->where(array('cid'=>$cid))->delete();
    }

    /**
     * 查询所有数据并且显示分页
     * @param int $limit 每页显示多少条数据 默认显示10条
     * @return array
     */
    public function getListData($limit=10){
        $count = $this->count();   // 查询满足要求的总记录数
        $Page = new \Think\Page($count,$limit); // 实例化分页类 传入总记录数和每页显示的记录数
        //设置分页显示
        $Page->setConfig('prev','Prev');
        $Page->setConfig('next','Next');
        $show = $Page->show(); // 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $this->order('cid ASC')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($list as $k => $v) {
            $list[$k]['count'] = D('Article')->where(array('cid'=>$v['cid']))->count();
        }
        $result = array(
            'list' => $list,
            'page' => $show
        );
        return $result;
    }
}