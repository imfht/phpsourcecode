<?php

namespace App\Models;

use DB;


class AdvertisementPosition extends Model
{
    protected $fillable = [
        'name', 'type', 'description','created_at'
    ];

    public function scopeTypeSearch($query, $value)
    {
        return $query->where('type', $value);
    }


    public function destroyAction()
    {

        DB::beginTransaction();
        try {
            $this->delete();
            DB::commit();
            return $this->baseSucceed([], '广告位删除成功');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->baseFailed('内部错误');
        }
    }

}
