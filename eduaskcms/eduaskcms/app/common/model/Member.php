<?php
namespace app\common\model;

class Member extends App
{

    //关联模型 
    public $parentModel = 'User';

    public $assoc = [
        'User' => array(
            'type' => 'belongsTo'
        )
    ];

    public function initialize()
    {
        $this->form = [
            'id' => [
                'type' => 'integer',
                'name' => 'ID',
                'elem' => 'hidden',
            ],
            'user_id' => array(
                'type' => 'integer',
                'name' => '所属用户',
                'foreign' => 'User.username',
                'prepare' => array(
                    'property' => 'options',
                    'type' => 'select',
                    'params' => array(
                        'where' => array()
                    )
                ),
                'elem' => 'format',
                'list' => 'assoc'
            ),
            'nickname' => array(
                'type' => 'string',
                'name' => '昵称',
                'elem' => 'text',
                'list' => 'show',
            ),
            'truename' => array(
                'type' => 'string',
                'name' => '真实姓名',
                'elem' => 'text',
                'list' => 'show',
            ),
            'headimg' => array(
                'type' => 'string',
                'name' => '头像',
                'elem' => 'image.upload',
                'list' => 'image',
                'upload' => array(
                    'maxSize' => 512,
                    'validExt' => array('jpg', 'png', 'gif')
                )
            ),
            'sex' => array(
                'type' => 'string',
                'name' => '性别',
                'elem' => 'radio',
                'options' => ['m' => '男', 'f' => '女', 'x' => '保密']
            ),
            'mobile' => array(
                'type' => 'string',
                'name' => '手机号码',
                'elem' => 'text',
                'list' => 'show',
            ),
            'region' => array(
                'type' => 'none',
                'name' => '所在地区',
                'elem' => 'multi_select.ajax',
                'multi_field' => [
                    'province' => '省',
                    'city' => '市',
                    'area' => '区',
                ],
                'multi_options' => [
                    'order' => ['list_order' => 'DESC','id' => 'ASC'],
                    'where' => []
                ],
                'foreign' => 'Region.title',
                'list' => 'assoc'
            ),
            'province' => array(
                'type' => 'integer',
                'name' => '省',
                'elem' => 0
            ),
            'city' => array(
                'type' => 'integer',
                'name' => '市',
                'elem' => 0
            ),
            'area' => array(
                'type' => 'integer',
                'name' => '区',
                'elem' => 0
            ),
            'address' => array(
                'type' => 'string',
                'name' => '联系地址',
                'elem' => 'text',
                'list' => 'show',
            ),

        ];
        call_user_func_array(['parent', __FUNCTION__], func_get_args());
    }


    //数据验证    
    protected $validate = [];

    public function getByUser($user_id)
    {
        $member = $this->where(['user_id' => intval($user_id)])->find();
        if (empty($member)) {
            $userModel = model('User');
            $user = $userModel->get(intval($user_id));
            if (empty($user)) {
                return false;
            }

            $data['user_id'] = intval($user_id);
            $data['nickname'] = '';
            $this->isValidate(false)->isUpdate(false)->save($data);
            $member = $this;
        }
        return $member;
    }
}
