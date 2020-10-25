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
 * 售后物流
 * Class Goods
 * @package App\Models
 */
class RefundDelivery extends BaseModels
{

    protected $table = 'refund_delivery';
    protected $guarded = ['id'];

    /**
     * 保存数据
     * @param int $id
     * @param array $save_data 需要保存的数据
     * @return bool|mixed
     */
    public static function saveData($save_data)
    {
        if (!$save_data) return false;
        try {
            $res = DB::transaction(function () use ($save_data) {
                $log = $save_data['log'];
                unset($save_data['log']);
                $res = self::create($save_data);
                Refund::where('id', $save_data['refund_id'])->update(['status' => Refund::STATUS_RECEIVED, 'delivery_at' => get_date()]);
                //日志信息
                if ($log) {
                    //日志信息
                    $image = array();
                    if (isset($log['image'])) {
                        $image = $log['image'];
                        unset($log['image']);
                    }
                    $log_res = RefundLog::create($log);
                    $log_id = $log_res->id;
                    //日志图片
                    $image_data = array();
                    foreach ($image as $key => $value) {
                        $image_data[] = array(
                            'log_id' => $log_id,
                            'image' => $value
                        );
                    }
                    if ($image_data) RefundImage::insert($image_data);
                }
                return $res;
            });
        } catch (\Exception $e) {
            dd($e);
            $res = false;
        }
        return $res;
    }

}
