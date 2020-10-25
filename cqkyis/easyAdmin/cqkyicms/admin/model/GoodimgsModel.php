<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/23 0023
 * Time: 18:03
 */

namespace app\admin\model;


use think\exception\PDOException;
use think\Model;
use think\facade\Env;
class GoodimgsModel extends Model
{

    protected $name="good_imgs";


    /**
     * @param $data
     * @return \think\Collection
     * @throws \Exception
     * 添加产品图片数组
     */
    public function addimgs($data){

       return     $this->saveAll($data);
      }

      /**
       * 批量删除图片
       *
       *
       */
      public function removImg($data){
          if($data){
              foreach ($data as $v){
                $list=  $this->where('good_id',$v)->select();
                 if($list) {
                     foreach ($list as $vo) {
                         $filepath=Env::get('root_path')."/Uploads/".$vo['imgs'];
                         if(is_file($filepath)){
                             unlink($filepath);
                         }
                         $this->where('id',$vo['id'])->delete();
                         //$this->delete();
                     }
                 }
              }

          }
      }



}