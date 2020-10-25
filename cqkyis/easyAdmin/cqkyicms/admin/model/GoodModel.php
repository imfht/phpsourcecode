<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/13 10:07
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\model;


use app\admin\validate\GoodValidate;

use think\Model;
use app\admin\model\GoodimgsModel;
use think\facade\Env;
class GoodModel extends Model
{

    protected $name="good";
    protected $pk = 'good_id';

    public function getByWhere($where, $offset, $limit)
    {
        return $this->alias('u')->field( 'u.*,cate_name')
            ->join('good_cate rol', 'u.cate_id = ' . 'rol.cate_id')
            ->where($where)->limit($offset, $limit)->order('good_id desc')->select();
    }

    public function getAll($where)
    {
        return $this->where($where)->count();
    }




    /**
     * @param $data
     * @return array
     */

    public function add($data,$images){
        try {

            $validate  = new GoodValidate();
            if (!$validate->check($data)) {
                return easymsg(2,'',$validate->getError());
            }
            $data['creattime']=time();
            $this->save($data);
            $Goodimg = new GoodimgsModel();
            if($images){
                $datas = array();
                foreach ($images as $k=>$v){
                    $datas[$k]['good_id']=$this->good_id;
                    $datas[$k]['imgs']=$v;
                }
                $Goodimg->addimgs($datas);
            }

            return easymsg(1,url('good/index'),'添加商品成功');
        }catch(PDOException $e){
            return easymsg(-1,'',$e->getMessage());
        }
    }


    /**
     * 批量删除
     */

    public function batchRemove($data){
        try {
            if($data){
            foreach ($data as $k=>$v){
               $result= $this->where('good_id',$v)->find();
               $filepath=Env::get('root_path')."/Uploads/".$result['good_img'];
                if(is_file($filepath)){
                    unlink($filepath);
                }
            }
            $GoodImg = new GoodimgsModel();
            $GoodImg->removImg($data);
            }
            $this->destroy($data);
            return easymsg(1,url('good/index'),'删除成功！');
        }catch (PDOException $e){
            return easymsg(-1,'',$e->getMessage());
        }
    }


}