<?php
namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    //添加 软删除功能
    use SoftDeletes;

    //定义内置字段 - const
    const UPDATED_AT = 'update_at';
    const CREATED_AT = 'create_at';
    const DELETED_AT = 'delete_at';

    //定义内置字段 - protected
    protected $primaryKey   = 'id';              //主键
    protected $_key_creator = 'creator';         //创建人
    protected $_key_updater = 'updater';         //修改人
    protected $_key_ip      = 'ip';              //操作人IP

    /**
     * 单例模式
     * @author Chengcheng
     * @date 2016-10-21 09:00:00
     * @return static
     */
    public static function model()
    {
        $model = new static();
        return $model;
    }

    /**
     * findById方法 根据主键查找数据
     * @author Chengcheng
     * @date 2017-6-23
     * @param array|int $options
     * @return static
     */
    public static function findById($options = [])
    {
        return self::find($options);
    }

    /**
     * 覆盖save方法，添加自动完成字段
     * @author Chengcheng
     * @date 2017-6-23
     * @param array|int $options
     * @return static
     */
    public function save(array $options = [])
    {
        return parent::save($options);
    }

    /**
     * 处理分页参数
     * @author Chengcheng
     * @date 2017-6-23
     * @param array
     * @param array
     * @return static
     */
    public function initPaged($query,$data)
    {
        $pageSize = isset($data['pageSize']) ? $data['pageSize'] : 0;
        $pageNum  = isset($data['pageNum']) ? $data['pageNum'] : 1;
        if (!empty($pageSize)) {
            $query= $query->limit($pageSize);
        }
        if (!empty($pageSize) && !empty($pageNum)) {
            $query = $query->offset(($pageNum - 1) * $pageSize);
        }
        return $query;
    }

    /**
     * 处理排序参数
     * @author Chengcheng
     * @date 2017-6-23
     * @param array
     * @param array
     * @return static
     */
    public function initOrdered($query,$data)
    {
        if (!empty($data['orders'])) {
            foreach ($data['orders'] as $key=>$value) {
                $query = $query->orderBy($key, $value);
            }
        } else {
            $query = $query->orderBy('id', 'desc');
        }
        return $query;
    }
    /**
     * 覆盖getDates方法，去掉laravel自动格式化时间功能
     * @author Chengcheng
     * @date 2017-6-23
     * @param array
     * @return static
     */
    public function getDates()
    {
        return [];
    }
}