<?php 
namespace App\Models; 
use App\Models\Base\BaseModel;
 
/** 
 * 
 * @author ChengCheng 
 * @date 2018-10-14 16:13:34 
 * @property int(11) id 'id'' 
 * @property char(50) open_id 'openid'' 
 * @property char(50) name '昵称''
 * @property char(50) gender '性别''
 * @property char(255) avatar '头像URL'' 
 * @property varchar(30) country '用户所在国家'' 
 * @property varchar(30) province '用户所在省份'' 
 * @property varchar(30) city '用户所在城市'' 
 * @property varchar(30) language '语言en,zh_CN,zh_TW'' 
 * @property int(11) member_id '用户ID'' 
 */ 
class WechatUser extends BaseModel 
{ 
    // 数据表名字
    protected $table = "tb_wechat_user"; 

    /**
     * 分页查询
     * @author ChengCheng
     * @date 2018-10-14 16:13:34 
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
        $query = $this->initPaged($query,$data);
        
        //排序参数
        $query = $this->initOrdered($query,$data);
        
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
     * @date 2018-10-14 16:13:34 
     * @param array $data
     * @return static
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
        if(isset($data['open_id'])){
            $model->open_id               = $data['open_id'];
        }
        if(isset($data['name'])){
            $model->name                  = $data['name'];
        }
        if(isset($data['gender'])){
            $model->gender                  = $data['gender'];
        }
        if(isset($data['avatar'])){
            $model->avatar                = $data['avatar'];
        }
        if(isset($data['country'])){
            $model->country               = $data['country'];
        }
        if(isset($data['province'])){
            $model->province              = $data['province'];
        }
        if(isset($data['city'])){
            $model->city                  = $data['city'];
        }
        if(isset($data['language'])){
            $model->language              = $data['language'];
        }
        if(isset($data['member_id'])){
            $model->member_id             = $data['member_id'];
        }
        
        // 保存信息
        $model->save();
        
        // 返回结果
        return $model;
    }
}
