<?php
/**
 * 标签表模型
 */
namespace Common\Model;
use Think\Model;
class TagsModel extends Model {
    //自动验证
    protected $_validate = array(
        array('tname', 'require', '标签名称不能为空.^_^', 1 ),
    );

    //添加或编辑操作
    public function send_addData(){
        if (!$this->create()) return false;
        $tid = I('post.tid',0,'intval');
        $tname = trim(I('post.tname'));
        if ($tid) {
            //编辑
            $this->where(array('tid'=>$tid))->save(array('tname'=>$tname));
            return true;
        } else {
            //添加
            if (strrpos($tname,",")) {
                //将字符串转换成数组
                $tData = explode(",",$tname);
                foreach($tData as $v){
                    $this->add(array('tname'=>$v));
                }
                return true;
            } else {
                if ($this->add(array('tname'=>$tname))) {
                    return true;
                } else {
                    $this->error = '操作失败.ㄒoㄒ~';
                    return false;
                }
            }
        }
    }

    //查询一条数据
    public function getFindData($tid){
        return $this->where(array('tid'=>$tid))->find();
    }

    //删除数据
    public function execDelData($tid){
        return $this->where(array('tid'=>$tid))->delete();
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
        $list = $this->order('tid ASC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $result = array(
            'list' => $list,
            'page' => $show
        );
        return $result;
    }
}