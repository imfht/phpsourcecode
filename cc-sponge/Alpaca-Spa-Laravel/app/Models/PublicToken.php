<?php
namespace App\Models;

use App\Models\Base\BaseModel;

/**
 *
 * @author ChengCheng
 * @date 2018-10-13 15:14:54
 * @property int(11)      id 'id''
 * @property char(50)     member_id '用户id''
 * @property tinyint(2)   member_type '用户类型，枚举|1-用户-USER|2-管理员-ADMIN''
 * @property varchar(15)  mobile '手机号''
 * @property varchar(100) email '邮箱''
 * @property varchar(65)  code 'code''
 * @property varchar(65)  token 'token''
 * @property datetime     available_time '有效截至时间''
 */
class PublicToken extends BaseModel
{
    // 数据表名字
    protected $table = "tb_public_token";

    // 枚举字段
    const MEMBER_TYPE_USER  = 1;    //用户类型:用户
    const MEMBER_TYPE_ADMIN = 2;    //用户类型:管理员

    /**
     * 分页查询
     * @author ChengCheng
     * @date 2018-10-13 15:14:54
     * @param array $data
     * @return array
     */
    public function lists($data = [])
    {
        //查询条件
        $query = $this;
        //根据id查询
        if (isset($data['id'])) {
            $query = $query->where('id', $data['id']);
        }

        //总数
        $total = $query->count();

        //分页参数
        $query = $this->initPaged($query, $data);

        //排序参数
        $query = $this->initOrdered($query, $data);

        //分页查找
        $info = $query->get();

        //返回结果，查找数据列表，总数
        $result          = array();
        $result['list']  = $info->toArray();
        $result['total'] = $total;
        return $result;
    }

    /**
     * 编辑
     * @author ChengCheng
     * @date 2018-10-13 15:14:54
     * @param array $data
     * @return array
     */
    public function edit($data)
    {
        // 判断是否是修改
        if (empty($data['id'])) {
            $model = new self;
        } else {
            $model = self::model()->find($data['id']);
            if (empty($model)) {
                return null;
            }
        }

        // 填充字段
        if (isset($data['member_id'])) {
            $model->member_id = $data['member_id'];
        }
        if (isset($data['member_type'])) {
            $model->member_type = $data['member_type'];
        }
        if (isset($data['mobile'])) {
            $model->mobile = $data['mobile'];
        }
        if (isset($data['email'])) {
            $model->email = $data['email'];
        }
        if (isset($data['code'])) {
            $model->code = $data['code'];
        }
        if (isset($data['token'])) {
            $model->token = $data['token'];
        }
        if (isset($data['available_time'])) {
            $model->available_time = $data['available_time'];
        }

        // 保存信息
        $model->save();

        // 返回结果
        return $model;
    }
}
