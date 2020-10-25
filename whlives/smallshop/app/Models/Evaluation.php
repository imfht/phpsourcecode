<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;
use Illuminate\Support\Facades\DB;

/**
 * 商品评价
 * Class Evaluation
 * @package App\Models
 */
class Evaluation extends BaseModels
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '锁定'
    ];

    //是否有图
    const IS_IMAGE_TRUE = 1;
    const IS_IMAGE_FALSE = 0;

    protected $table = 'evaluation';
    protected $guarded = ['id'];

    /**
     * 获取图片
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function image() {
        return $this->hasMany('App\Models\EvaluationImage');
    }

    /**
     * 保存数据
     * @param  array $save_data 需要保存的数据
     * @return bool|mixed
     */
    public static function saveData($save_data) {
        if (!$save_data) return false;
        try {
            $res = DB::transaction(function () use ($save_data) {
                foreach ($save_data as $data) {
                    $image = $data['image'];
                    unset($data['image']);
                    $result = self::create($data);
                    $id = $result->id;
                    if ($image) {
                        $image_data = array();
                        foreach ($image as $key => $value) {
                            $image_data[] = array(
                                'e_id' => $id,
                                'image' => $value
                            );
                        }
                        if ($image_data)EvaluationImage::insert($image_data);
                    }
                }
                return true;
            });
        } catch (\Exception $e) {
            $res = false;
        }
        return $res;
    }
}
