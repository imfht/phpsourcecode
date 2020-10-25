<?php

namespace App\Admin\Forms;

use App\Models\DeviceRecord;
use App\Models\HardwareRecord;
use App\Models\HardwareTrack;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

/**
 * 设备记录分配使用者
 * Class DeviceTrackForm
 * @package App\Admin\Forms
 */
class HardwareTrackForm extends Form implements LazyRenderable
{
    use LazyWidget;

    // 处理请求
    public function handle(array $input)
    {
        // 获取硬件id
        $hardware_id = $this->payload['id'] ?? null;

        // 获取设备id，来自表单传参
        $device_id = $input['device_id'] ?? null;

        // 如果没有硬件id或者设备id则返回错误
        if (!$hardware_id || !$device_id) {
            return $this->error('参数错误');
        }

        // 硬件记录
        $hardware = HardwareRecord::where('id', $hardware_id)->first();
        // 如果没有找到这个硬件记录则返回错误
        if (!$hardware) {
            return $this->error('硬件不存在');
        }

        // 设备记录
        $device = DeviceRecord::where('id', $device_id)->first();
        // 如果没有找到这个设备记录则返回错误
        if (!$device) {
            return $this->error('设备不存在');
        }

        // 硬件追踪
        $hardware_track = HardwareTrack::where('hardware_id', $hardware_id)
            ->where('device_id', $device_id)
            ->first();

        // 如果硬件追踪非空，则删除旧追踪，为了留下流水记录
        if (!empty($hardware_track)) {
            // 如果新设备和旧设备相同，返回错误
            if ($hardware_track->device_id == $device_id) {
                return $this->error('设备没有改变，无需重新归属');
            }
            $hardware_track->delete();
        }

        // 创建新的硬件追踪
        $hardware_track = new HardwareTrack();
        $hardware_track->hardware_id = $hardware_id;
        $hardware_track->device_id = $device_id;
        $hardware_track->save();

        return $this->success('硬件归属成功');
    }

    /**
     * 表单
     */
    public function form()
    {
        $this->select('device_id', '新设备')
            ->options(DeviceRecord::all()->pluck('name', 'id'))
            ->required();
    }
}
