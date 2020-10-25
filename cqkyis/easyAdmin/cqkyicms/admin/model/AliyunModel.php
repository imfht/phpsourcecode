<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/20 9:17
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\model;


use think\Model;

class AliyunModel extends Model
{

    protected $name="system_aliyun_sms";

  public function AddAndEdit($data){
      try {

          $setOne = $this->find();
          if($setOne){
              $this->save($data,['id'=>$data['id']]);
              return easymsg(1,url('setting/sms'),'修改成功！');
          }else{

              $this->save($data);
              return easymsg(1,url('setting/sms'),'添加成功！');
          }


      }catch(PDOException $e){
          return easymsg(-1,'',$e->getMessage());
      }
  }

    public function findBy(){
        return $this->find();
    }

}