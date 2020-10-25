<?php
namespace app\ebcms\behavior;

class Oplog
{

    public function run(&$params)
    {
        $rule = strtolower(request()->module() . '_' . request()->controller() . '_' . request()->action());
        if (strtolower(request()->action()) != 'index') {
            if (!\think\Cache::has('eb_rules')) {
                $rules = \think\Db::name('auth_rule')->column('title,opstr', 'name');
                \think\Cache::set('eb_rules', $rules);
            } else {
                $rules = \think\Cache::get('eb_rules');
            }
            $title = isset($rules[$rule]) ? ($rules[$rule]['opstr']?:$rules[$rule]['title']) : '未知操作！';
            $data = [
                'url' => request()->domain() . request()->url(),
                'request' => serialize(request()->request()),
                'manager_id' => \think\Session::get('manager_id') ?: 0,
                'title' => $title,
                'ids' => input('ids') ?: input('id'),
                'create_time' => time(),
                'ip' => request()->ip(0, true),
            ];
            \think\Db::name('oplog')->insert($data);
        }

    }
}