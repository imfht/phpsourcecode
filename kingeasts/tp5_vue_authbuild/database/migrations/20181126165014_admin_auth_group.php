<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AdminAuthGroup extends Migrator
{
    public function change()
    {
        $rules = [];
        for ($i = 1; $i <= 40; $i++) {
            $rules[] = $i;
        }
        $rules = implode(',',$rules);
        $table = $this->table('admin_auth_group');
        $table->addColumn('title', 'string', ['limit'=>100, 'default'=>'']);
        $table->addColumn('description', 'string', ['limit'=>255, 'default'=>'']);
        $table->addColumn('rules', 'text');
        $table->addColumn('status', 'boolean', ['default'=>1]);
        $table->addColumn('create_time', 'integer', ['default'=>0]);
        $table->addColumn('update_time', 'integer', ['default'=>0]);
        $table->insert([
            'title'=>'超级管理员',
            'description'=>'所有权限都有',
            'rules'=>$rules,
            'create_time'=>$_SERVER['REQUEST_TIME']
        ]);
        $table->save();

    }
}
