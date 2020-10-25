<?php

namespace App\Admin\Forms;

use App\Models\DeviceRecord;
use App\Models\SoftwareRecord;
use App\Models\SoftwareTrack;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

/**
 * 设备记录分配使用者
 * Class DeviceTrackForm
 * @package App\Admin\Forms
 */
class SoftwareTrackForm extends Form implements LazyRenderable
{
    use LazyWidget;

    // 处理请求
    public function handle(array $input)
    {
        // 获取软件id
        $software_id = $this->payload['id'] ?? null;

        // 获取设备id，来自表单传参
        $device_id = $input['device_id'] ?? null;

        // 如果没有软件id或者设备id则返回错误
        if (!$software_id || !$device_id) {
            return $this->error('参数错误');
        }

        // 软件记录
        $software = SoftwareRecord::where('id', $software_id)->first();
        // 如果没有找到这个软件记录则返回错误
        if (!$software) {
            return $this->error('软件不存在');
        }

        // 设备记录
        $device = DeviceRecord::where('id', $device_id)->first();
        // 如果没有找到这个设备记录则返回错误
        if (!$device) {
            return $this->error('设备不存在');
        }

        // 软件追踪
        $software_track = SoftwareTrack::where('software_id', $software_id)
            ->where('device_id', $device_id)
            ->first();

        // 如果软件授权数量为非无限制
        if ($software->counts != -1) {
            $software_tracks = SoftwareTrack::where('software_id', $software_id)
                ->get();
            $used = count($software_tracks);
            $diff = $software->counts - $used;
            if ($diff <= 0) {
                return $this->error('软件可用授权数量不足，无法归属');
            }
        }

        // 如果软件追踪非空，则删除旧追踪，为了留下流水记录
        if (!empty($software_track)) {
            // 如果新设备和旧设备相同，返回错误
            if ($software_track->device_id == $device_id) {
                return $this->error('设备没有改变，无需重新归属');
            }
        }

        // 创建新的硬件追踪
        $software_track = new SoftwareTrack();
        $software_track->software_id = $software_id;
        $software_track->device_id = $device_id;
        $software_track->save();

        return $this->success('软件归属成功');
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
