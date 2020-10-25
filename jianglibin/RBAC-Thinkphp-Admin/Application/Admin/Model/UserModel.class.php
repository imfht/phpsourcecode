<?php
namespace Admin\Model;
use Think\Model;
/**
 *
 * @category   UserController
 * @package    User
 * @author  stone <shijiangbo929@163.com>
 * @license
 * @version    PHP version 5.4
 * @link
 * @since   2016年12月7日
 *
 **/
class UserModel extends Model{    
    /*
     * 查询会员
     * @param $where 查询条件
     * @return 成功返回$data，失败返回false;
     * @author stone
     * @since 2016-12-7
     * */
    public function getUserData($where){
        if ($where){
            $count = $this->field('uid')->where($where)->count();
        }else{
            $count = $this->field('uid')->count();
        }       
	$Page = new\Think\Page($count,10);
	$Page->setConfig('header','共%TOTAL_ROW%条');
	$Page->setConfig('prev','上一页');
	$Page->setConfig('next','下一页');
	$Page->setConfig('first','首页');
	$Page->setConfig('last','末页');
	$Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
	$show = $Page->show();
        if ($where){
            $data = $this
                ->order('add_dateline desc')
                ->where($where)
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        } else {
            $data = $this
                ->order('add_dateline desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        }        
        if (!$data){return false; } 
        return array($data,$show,$count);
    }
    /*
     * 查询单个会员
     * @param $id 查询条件
     * @return 成功返回$data，失败返回false;
     * @author stone
     * @since 2016-12-8
     * */
    public function getUserDetail($id){
        if (!$id){return false;}
        $data = $this->find($id);
        return $data;
    }
    
}