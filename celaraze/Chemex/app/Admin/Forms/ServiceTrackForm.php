<?php

namespace App\Admin\Forms;

use App\Models\DeviceRecord;
use App\Models\ServiceRecord;
use App\Models\ServiceTrack;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Symfony\Component\HttpFoundation\Response;

class ServiceTrackForm extends Form implements LazyRenderable
{
    use LazyWidget;

    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return Response
     */
    public function handle(array $input)
    {
        // 获取服务id
        $service_id = $this->payload['id'] ?? null;

        // 获取设备id，来自表单传参
        $device_id = $input['device_id'] ?? null;

        // 如果没有服务id或者设备id则返回错误
        if (!$service_id || !$device_id) {
            return $this->error('参数错误');
        }

        // 服务记录
        $service = ServiceRecord::where('id', $service_id)->first();
        // 如果没有找到这个服务记录则返回错误
        if (!$service) {
            return $this->error('服务不存在');
        }

        // 设备记录
        $device = DeviceRecord::where('id', $device_id)->first();
        // 如果没有找到这个设备记录则返回错误
        if (!$device) {
            return $this->error('设备不存在');
        }

        // 服务追踪
        $service_track = ServiceTrack::where('service_id', $service_id)
            ->where('device_id', $device_id)
            ->first();

        // 如果硬件追踪非空，则删除旧追踪，为了留下流水记录
        if (!empty($service_track)) {
            // 如果新设备和旧设备相同，返回错误
            if ($service_track->device_id == $device_id) {
                return $this->error('设备没有改变，无需重新归属');
            }
            $service_track->delete();
        }

        // 创建新的硬件追踪
        $service_track = new ServiceTrack();
        $service_track->service_id = $service_id;
        $service_track->device_id = $device_id;
        $service_track->save();

        return $this->success('服务归属成功');
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->select('device_id', '新设备')
            ->options(DeviceRecord::all()->pluck('name', 'id'))
            ->required();
    }
}
