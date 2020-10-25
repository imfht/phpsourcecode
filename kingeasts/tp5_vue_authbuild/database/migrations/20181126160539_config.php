<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Config extends Migrator
{
    public function change()
    {
        $table = $this->table('config');
        $table->addColumn('name', 'string', ['limit'=>30, 'default'=>'']);
        $table->addColumn('type', 'integer', ['limit'=>3, 'default'=>0]);
        $table->addColumn('title', 'string', ['limit'=>50, 'default'=>'']);
        $table->addColumn('group', 'integer', ['limit'=>3, 'default'=>0]);
        $table->addColumn('extra', 'string', ['limit'=>255, 'default'=>'']);
        $table->addColumn('remark', 'string', ['limit'=>100, 'default'=>'']);
        $table->addColumn('default', 'string', ['default'=>'', 'limit'=>255]);
        $table->addColumn('placeholder', 'string', ['default'=>'', 'limit'=>255]);
        $table->addColumn('value', 'text');
        $table->addColumn('sort', 'integer', ['default'=>0, 'limit'=>6]);
        $table->addColumn('status', 'integer', ['limit'=>4, 'default'=>0]);
        $table->addColumn('create_time', 'integer', ['default'=>0]);
        $table->addColumn('update_time', 'integer', ['default'=>0]);

        $table->insert([
            [
                'name'=>'web_title',
                'type'=>1,
                'title'=>'网站标题',
                'group'=>1,
                'extra'=>'',
                'remark'=>'请填写网站名称，例如：教师中国',
                'status'=>1,
                'value'=>'管理系统',
                'default'=>'',
                'placeholder'=>'',
                'sort'=>0,
                'create_time'=>$_SERVER['REQUEST_TIME']
            ],
            [
                'name'=>'web_keywords',
                'type'=>1,
                'title'=>'网站关键词',
                'group'=>1,
                'extra'=>'',
                'remark'=>'请填写网站关键词，多个词汇使用 , 分开。例如：PHP,MYSQL',
                'status'=>1,
                'value'=>'PHP,MYSQL',
                'default'=>'',
                'placeholder'=>'',
                'sort'=>1,
                'create_time'=>$_SERVER['REQUEST_TIME']
            ],
            [
                'name'=>'web_description',
                'type'=>2,
                'title'=>'网站描述',
                'group'=>1,
                'extra'=>'',
                'remark'=>'请填写网站描述，例如：TpAndVue',
                'status'=>1,
                'value'=>'TpAndVue',
                'default'=>'',
                'placeholder'=>'',
                'sort'=>2,
                'create_time'=>$_SERVER['REQUEST_TIME']
            ],
            [
                'name'=>'config_type_list',
                'type'=>3,
                'title'=>'配置类型列表',
                'group'=>3,
                'extra'=>'',
                'remark'=>'主要用于数据解析和页面表单的生成',
                'status'=>1,
                'value'=>"0:数字\r\n1:字符\r\n2:文本\r\n3:数组\r\n4:枚举",
                'default'=>'',
                'placeholder'=>'',
                'sort'=>3,
                'create_time'=>$_SERVER['REQUEST_TIME']
            ],
            [
                'name'=>'config_group_list',
                'type'=>3,
                'title'=>'配置分组',
                'group'=>3,
                'extra'=>'',
                'remark'=>'主要用于数据解析和页面表单的生成',
                'status'=>1,
                'value'=>"1:基本\r\n2:注册\r\n3:系统",
                'default'=>'',
                'placeholder'=>'',
                'sort'=>4,
                'create_time'=>$_SERVER['REQUEST_TIME']
            ],
            [
                'name'=>'web_close',
                'type'=>4,
                'title'=>'网站状态',
                'group'=>1,
                'extra'=>'0:关闭,1:开启',
                'remark'=>'设置“网站”状态',
                'status'=>1,
                'value'=>'1',
                'default'=>'',
                'placeholder'=>'',
                'sort'=>5,
                'create_time'=>$_SERVER['REQUEST_TIME']
            ],
            [
                'name'=>'web_icp',
                'type'=>1,
                'title'=>'网站备案',
                'group'=>1,
                'extra'=>'',
                'remark'=>'请填写网站版权，例如：京ICP备XXXX号',
                'status'=>1,
                'value'=>'',
                'default'=>'',
                'placeholder'=>'',
                'sort'=>8,
                'create_time'=>$_SERVER['REQUEST_TIME']
            ],
            [
                'name'=>'system_trace',
                'type'=>4,
                'title'=>'调试模式',
                'group'=>3,
                'extra'=>'0:关闭,1:开启',
                'remark'=>'是否打开网站页面Trace调试模式',
                'status'=>1,
                'value'=>'0',
                'default'=>'',
                'placeholder'=>'',
                'sort'=>1,
                'create_time'=>$_SERVER['REQUEST_TIME']
            ],
            [
                'name'=>'user_allow_register',
                'type'=>4,
                'title'=>'会员注册',
                'group'=>2,
                'extra'=>'0:关闭,1:开启',
                'remark'=>'会员注册是否开启',
                'status'=>1,
                'value'=>'0',
                'default'=>'',
                'placeholder'=>'',
                'sort'=>10,
                'create_time'=>$_SERVER['REQUEST_TIME']
            ],
            [
                'name'=>'sms_expiring_time',
                'type'=>0,
                'title'=>'短信验证有效期',
                'group'=>2,
                'extra'=>'',
                'remark'=>'请设置短信验证有效期时间（单位秒）',
                'status'=>1,
                'value'=>'60',
                'default'=>'',
                'placeholder'=>'',
                'sort'=>75,
                'create_time'=>$_SERVER['REQUEST_TIME']
            ],
            [
                'name'=>'enterprise_telephone',
                'type'=>1,
                'title'=>'企业客服电话',
                'group'=>1,
                'extra'=>'',
                'remark'=>'请设置企业客服电话',
                'status'=>1,
                'value'=>'10086',
                'default'=>'',
                'placeholder'=>'',
                'sort'=>100,
                'create_time'=>$_SERVER['REQUEST_TIME']
            ]
        ]);
        $table->save();

    }
}
