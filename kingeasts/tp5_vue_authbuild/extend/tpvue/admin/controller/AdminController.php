<?php
// 后台公共控制器
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
// | 原作者：心云间、凝听
// +----------------------------------------------------------------------

namespace tpvue\admin\controller;


class AdminController extends BaseController
{
  /**
     * [setStatus 设置状态属性]
     * 设置一条或者多条数据的状态
     * @param $script 严格模式要求处理的纪录的uid等于当前登陆用户UID
     */
    public function setStatus($model = CONTROLLER_NAME, $script = false) {

        $ids    =$this->param['ids'];
        $status =$this->param['status'];
        if (empty($ids)) {
            $this->error('请选择要操作的数据');
        }
        $model_primary_key = model($model)->getPk();
        $map[$model_primary_key] = [$model_primary_key,'in',$ids];
        if ($script) {
            $map['uid'] = array('eq', is_login());
        }

        switch ($status) {
            case 'forbid' :  // 禁用条目
                $data = array('status' => 0);
                $this->editRow(
                    $model,
                    $data,
                    $map,
                    array('success'=>'操作成功','error'=>'操作失败')
                );
                break;
            case 'resume' :  // 启用条目
                $data = array('status' => 1);
                $map  = array_merge(array('status' => 0), $map);
                $this->editRow(
                    $model,
                    $data,
                    $map,
                    array('success'=>'操作成功','error'=>'操作失败')
                );
                break;
            case 'hide' :  // 隐藏条目
                $data = array('status' => 1);
                $map  = array_merge(array('status' => 0), $map);
                $this->editRow(
                    $model,
                    $data,
                    $map,
                    array('success'=>'隐藏成功','error'=>'隐藏失败')
                );
                break;
            case 'show' :  // 显示条目
                $data = array('status' => 0);
                $map  = array_merge(array('status' => 1), $map);
                $this->editRow(
                   $model,
                   $data,
                   $map,
                   array('success'=>'显示成功','error'=>'显示失败')
                );
                break;
            case 'recycle' :  // 移动至回收站
                $data['status'] = -1;
                $this->editRow(
                    $model,
                    $data,
                    $map,
                    array('success'=>'成功移至回收站','error'=>'删除失败')
                );
                break;
            case 'restore' :  // 从回收站还原
                $data = array('status' => 1);
                $map  = array_merge(array('status' => -1), $map);
                $this->editRow(
                    $model,
                    $data,
                    $map,
                    array('success'=>'恢复成功','error'=>'恢复失败')
                );
                break;
            case 'delete'  :  // 删除条目
                $result = model($model)->where($map)->delete();
                if ($result) {
                    $this->success('删除成功，不可恢复！');
                } else {
                    $this->error('删除失败');
                }
                break;
            default :
                $this->error('参数错误');
                break;
        }
    }

    /**
     * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
     * @param string $model 模型名称,供M函数使用的参数
     * @param array  $data  修改的数据
     * @param array  $map   查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息
     *                       array(
     *                           'success' => '',
     *                           'error'   => '',
     *                           'url'     => '',   // url为跳转页面
     *                           'ajax'    => false //是否ajax(数字则为倒数计时)
     *                       )
     */
    final protected function editRow($model, $data, $map, $msg) {
        $id = array_unique((array)input('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        //如存在id字段，则加入该条件
        // $fields = model($model)->getDbFields();
        // if (in_array('id', $fields) && !empty($id)) {
        //     $where = array_merge(
        //         array('id' => array('in', $id )),
        //         (array)$where
        //     );
        // }
        $msg = array_merge(
            array(
                'success' => '操作成功！',
                'error'   => '操作失败！',
                'url'     => ' ',
                'ajax'    => IS_AJAX
            ),
            (array)$msg
        );
        $result = model($model)->where($map)->update($data);
        if ($result != false) {
            $this->success($msg['success']);
        } else {
            $this->error($msg['error']);
        }
    }

    /**
     * [fuck 非法操作转404]
     */
    protected function fuck()
    {
        return $this->error('404!');
    }

    /**
     * 清理缓存
     * @return [type] [description]
     */
    public function delCache() {
           header("Content-type: text/html; charset=utf-8");
            //清文件缓存
            $dirs = array('../runtime/');
            @mkdir('runtime',0777,true);
            //清理缓存
            foreach($dirs as $dir) {
                $this->rmdirr($dir);
            }
            $this->success('清除缓存成功！');
     }

    /**
     * 删除
     * @param  [type] $dirname [description]
     * @return [type]          [description]
     */
    public function rmdirr($dirname) {

          if (!file_exists($dirname)) {
                return false;
          }
          if (is_file($dirname) || is_link($dirname)) {
                return unlink($dirname);
          }

          $dir = dir($dirname);
          if($dir){

               while (false !== $entry = $dir->read()) {
                if ($entry == '.' || $entry == '..' || $entry == '.gitignore') {
                 continue;
                }

                //递归
                $this->rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
               }

          }
          $dir->close();
          return true;
    }
}