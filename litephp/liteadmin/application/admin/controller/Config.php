<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/3/27
 * Time: 10:31
 */

namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\model\Config as ConfigModel;

/**
 * @title 网站配置
 * Class Config
 * @package app\admin\controller
 */
class Config extends BaseAdmin
{
    /**
     * @title 修改
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function edit()
    {
        if ($this->request->isGet()){
            $fields = ConfigModel::all();
            $this->assign('fields',$fields);
            return $this->fetch();
        }else{
            $fields = $this->request->post();
            foreach ($fields as $name => $value){
                try{
                    ConfigModel::where('name','=', $name)->update(['value'=>$value]);
                }catch (PDOException $e){
                    $this->error($e->getMessage(),'');
                }
            }

            $this->success('数据操作成功！','');
        }
    }
}