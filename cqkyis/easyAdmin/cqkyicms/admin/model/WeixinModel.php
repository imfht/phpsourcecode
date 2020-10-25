<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/19 14:20
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\model;


use think\Model;

class WeixinModel extends Model
{

    protected $name="system_weixin";
    /**
     * 微信配置
     */
    public function addAndEdit($data){
        try {

            $setOne = $this->where('type',1)->find();
            if($setOne){
              $this->save($data,['type'=>1]);
              return easymsg(1,url('weixin/index'),'修改成功！');
            }else{
                $data['type']=1;
              $this->save($data);
                return easymsg(1,url('weixin/index'),'添加成功！');
            }


        }catch(PDOException $e){
            return easymsg(-1,'',$e->getMessage());
        }
    }

    /**
     * 根据类型查询
     */

    public function bytype($id){
        return $this->where('type',$id)->find();
    }


    /**小程序
    */
    public function aeapp($data){
            try {

                $setOne = $this->where('type',2)->find();
                if($setOne){
                  $this->save($data,['type'=>2]);
                  return easymsg(1,url('weixin/app'),'修改成功！');
                }else{
                    $data['type']=2;
                  $this->save($data);
                    return easymsg(1,url('weixin/app'),'添加成功！');
                }


            }catch(PDOException $e){
                return easymsg(-1,'',$e->getMessage());
            }
        }

}