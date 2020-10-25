<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/20 9:16
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\controller;


use app\admin\model\AliyunModel;

class Setting extends Base
{

    protected $title="参数配置";
    public function sms(){
        $aliyun = new AliyunModel();
        if(request()->isPost()){
            $data = input('post.');

            $res = $aliyun->AddAndEdit($data);
            if($res['code']==1){

                $this->ky_success($res['msg'],$res['data']);
            }else{

                $this->ky_error($res['msg']);
            }
        }else{
            $rs = $aliyun->findBy();


            $name="阿里短信";
            $this->assign([
                'name'=>$name,
                'title'=>$this->title,
                'wx'=>$rs
            ]);
            return $this->fetch();
        }
    }

}