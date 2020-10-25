<?php
namespace app\ebcms\controller;
class Config extends \app\ebcms\controller\Common
{
    
    // 所有模板显示
    public function index()
    {
        if (request()->isGet()) {
            return $this->fetch();
        }
    }

    public function setting()
    {
        if (request()->isGet()) {

            if (!$x = \think\Db::name('configcate')->where(['name' => input('name'),'status'=>1])->find()) {
                $this->error('暂无配置项');
            }

            $_where = array(
                'status' => 1,
                'category_id' => array('eq', $x['id']),
            );
            if ($configs = \think\Db::name('config') -> where($_where)->order('sort desc,id asc')->select()) {
                $ids = [];
                foreach ($configs as $key => $item) {
                    $ids[] = $item['id'];
                    $tmp = [];
                    $tmp['id'] = $item['id'];
                    $tmp['title'] = $item['title'];
                    $tmp['remark'] = $item['remark'];
                    $tmp['type'] = substr($item['form'], 5);
                    $tmp['field'] = 'config[' . $item['id'] . ']';
                    $tmp['value'] = $item['value'];
                    $tmp['config'] = array_merge(['disabled'=>0,'readonly'=>0],(Array)json_decode($item['config'],true));
                    $tmp['unique'] = md5(uniqid().$tmp['id']);
                    if (strpos($item['group'], '@')) {
                        list($group,$k) = explode('@', $item['group']);
                    }else{
                        $group = $item['group'];
                    }
                    $groups[$group][$item['id']] = $tmp;
                }
                $groups[$group][] = [
                    'field' => 'config_verify',
                    'value' => \ebcms\Func::eb_encrypt($ids),
                    'type' => 'hidden',
                    'title' => '',
                    'config' => [],
                    'id' => '0',
                ];

                $form = array(
                    'action' => \think\Url::build('ebcms/config/setting'),
                    'group' => '系统配置',
                    'title' => '修改配置',
                    'unique' => md5(uniqid()),
                    'group' => $x['title'],
                );

                $this->assign('form', $form);
                $this->assign('groups', $groups);
                $this->assign('data', $configs);
                return $this->fetch('common/form');
            } else {
                $this->error('暂无配置项');
            }
        } elseif (request()->isPost()) {
            $data = input('config/a');
            $config_verify = input('config_verify');
            if (!$ids = \ebcms\Func::eb_decrypt($config_verify)) {
                $this->error('非法提交！');
            }
            // 验证数据真实性
            if (array_keys($data) != array_intersect(array_keys($data), $ids)) {
                $this->error('非法操作！');
            }
            // 更新数据
            \think\Db::transaction(function() use($data,$ids){
                foreach ($ids as $key) {
                    $value = isset($data[$key])?$data[$key]:'';
                    $value = is_array($value)?json_encode($value):$value;
                    \think\Db::name('config')->where(array('id' => array('eq', $key)))->setField('value', $value);
                }
                // 更新配置缓存
                \ebcms\Config::config(true);
            });
            $this->success('修改成功');
        }
    }
}