<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Common\Model;
use Common\Util\Tree;
use Common\Util\MyPage;
use Think\Model;
/**
 * 菜单模型
 * @author jry <598821125@qq.com>
 */
class CommonModel extends Model{
    protected $page;//当前页
    protected $size;//每页显示条数
    protected $start;//每页显示条数
    const MSUCCESS=0;//操作成功
    const MFAIL=1;//操作失败
    const MMSG=2;//操作返回的消息
    protected function _initialize(){
        $this->page=I('page',1);
        $this->size=15;//默认显示15条
        $this->start=($this->page-1)>0?($this->page-1)*$this->size:0;//默认显示15条
    }
    /**
     * 返回获取到的数据
     * @return mixed
     */
    public function getDatas($data,$map,$size=0){
        $tree = new Tree();
        $data = $tree->toFormatTree($data);
        if($size)$this->size=$size;
        $data['pages']=$this->pager($map,$this->size);//获取分页数据
        return $data;
    }

    /**
     * 获取分页数据
     * @param $map 根据查询条件获取数据总数
     * @param $size 每页显示的数据条数
     * @return array  获取到的数据信息
     */
    private function pager($map){
        $count=$this->where($map)->count();
        return MyPage::pager($this->page,$count,$this->size);
    }
    /**
     * 设置显示条数
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * 设置分页的开始位置
     * @param mixed $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * 添加数据
     * @return mixed
     */
    public function addData(){
        $data=$this->create();
        if($data){
            $id=$this->add();
            if($id){
                return self::MSUCCESS;//添加成功
            }else{
                return self::MFAIL;//添加失败
            }
        }else{
            return $this->getError();//添加错误的原因
        }
    }

    /**
     * 编辑数据
     * @return int|string MSUCCESS表示成功  MFAIL表示失败  getError返回错误信息
     */
    public function editData(){
        $data = $this->create();
        if($data){
            if($this->save()!== false){
                return self::MSUCCESS;//更新成功
            }else{
                return self::MFAIL;//更新失败
            }
        }else{
            return  $this->getError();
        }
    }
}
