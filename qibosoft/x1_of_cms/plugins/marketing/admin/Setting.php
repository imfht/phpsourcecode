<?php
namespace plugins\marketing\admin;

use app\common\controller\admin\Setting AS _Setting;


class Setting extends _Setting
{    
    protected $config = [
//             [
//                     'c_key'=>'money_types',
//                     'title'=>'自定义虚拟币名称',
//                     'c_value'=>"回香豆\r\n威望\r\n铜板",
//                     'options'=>'',
//                     'c_descrip'=>'每种积分名称换一行',
//                     'form_type'=>'textarea',
//                     'ifsys'=>0,
//                     'list'=>1,
//             ],
            [
                    'c_key'=>'min_getout_money',
                    'title'=>'最低提现金额',
                    'c_value'=>'50',
                    'options'=>"",
                    'c_descrip'=>'',
                    'form_type'=>'money',
                    'ifsys'=>0,
                    'list'=>0,
            ],
            [
                    'c_key'=>'getout_percent_money',
                    'title'=>'提现手续费',
                    'c_value'=>'',
                    'options'=>"",
                    'c_descrip'=>'0即不收手续费,0.01即收取1个点的手续费',
                    'form_type'=>'usergroup',
                    'ifsys'=>0,
                    'list'=>0,
            ],
            [
                    'c_key'=>'getout_need_join_mp',
                    'title'=>'是否要求先关注公众号才能提现',
                    'c_value'=>'',
                    'form_type'=>'radio',
                    'options'=>"0|不要求\r\n1|要求先关注公众号",
                    'ifsys'=>0,
                    'list'=>-1,
            ],
            [
                    'c_key'=>'getout_need_yzphone',
                    'title'=>'是否要求先绑定手机号才能提现',
                    'c_value'=>'',
                    'form_type'=>'radio',
                    'options'=>"0|不要求\r\n1|要求绑定手机号",
                    'ifsys'=>0,
                    'list'=>-1,
            ],
    ];
    
    /**
     * 参数设置
     * {@inheritDoc}
     * @see \app\common\controller\admin\Setting::index()
     */
    public function index($group=null){
        return parent::index($group);
    }
}

