<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AdvertisementCollection extends ResourceCollection
{
    public function toArray($request)
    {

        $collection = $this->collection;
        $collection->each(function ($info) {
            $overdu_time = 25*3600; // 「过期时间/秒」
            if ($info->end_at) {
                $overdu_time = (strtotime($info->end_at) - time());
            }
            $info->overdue_time = $overdu_time;
        });
        return [
            'data' => $collection
        ];

    }
}
