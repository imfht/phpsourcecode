<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/25 0025
 * Time: 11:47
 */

namespace app\admin\model;


use app\admin\validate\AdverValidate;
use think\exception\PDOException;
use think\Model;

class AdvertModel extends Model
{


    protected $name="good_advert";

    /**
     * 查询分页
     */
    public function getByWhere($where, $offset, $limit)
    {
        return $this->alias('u')->field( 'u.*,cate_name')
            ->join('good_cate rol', 'u.cate_id = ' . 'rol.cate_id')
            ->where($where)->limit($offset, $limit)->order('id desc')->select();
    }
    /**
     * 查询总数
     */

    public function getAll($where)
    {
        return $this->where($where)->count();
    }

    /**
     * 添加
     */
    public function add($data){
         try{
             $validate = new AdverValidate();
             if (!$validate->check($data)) {
                 return easymsg(2,'',$validate->getError());
             }
             $data['creattime']=time();
             $this->save($data);
             return easymsg(1,url('advert/index'),'添加商品广告成功');
         }catch (PDOException $e){

                 return easymsg(-1,'',$e->getMessage());

         }
    }

}