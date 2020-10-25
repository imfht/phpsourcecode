<?php

namespace App\Http\Controllers;

use App\Models\DeviceRecord;
use App\Models\HardwareRecord;
use App\Models\SoftwareRecord;

class InfoController extends Controller
{
    /**
     * 移动端扫码查看设备硬件软件详情
     * @param $item_id
     */
    public function info($item_id)
    {
        $item_class = explode(':', $item_id)[0];
        $item_id = explode(':', $item_id)[1];
        switch ($item_class) {
            case 'device':
                $item = DeviceRecord::where('id', $item_id)
                    ->first();
                break;
            case 'hardware':
                $item = HardwareRecord::where('id', $item_id)
                    ->first();
                break;
            case 'software':
                $item = SoftwareRecord::where('id', $item_id)
                    ->first();
                break;
            default:
                $item = [];
        }
        if (empty(!$item)) {

        }
    }
}
