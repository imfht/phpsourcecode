<?php

namespace App\Models;

use DB;

class Tag extends Model
{

    protected $fillable = [
        'name',
    ];

    public function destroyAction()
    {

        DB::beginTransaction();
        try {
            $this->delete();
            DB::commit();
            return $this->baseSucceed([], '删除成功');
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return $this->baseFailed('内部错误');
        }
    }

}
