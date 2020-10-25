<?php

namespace App\Admin\Actions\Grid;

use App\Support\Data;
use App\Models\DeviceRecord;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Widgets\Modal;

class DeviceRelatedAction extends RowAction
{
    /**
     * @return string
     */
    protected $title = '📝 软硬件清单';

    public function render()
    {
        // 实例化表单类并传递自定义参数
        $id = $this->getKey();

        $device = DeviceRecord::where('id', $id)
            ->firstOrFail();

        // 获取所有硬件
        $hardware = $device->hardware;
        // 获取所有软件
        $software = $device->software;

        // 转换软件授权方式的显示内容
        foreach ($software as $item) {
            $item->distribution = Data::distribution()[$item->distribution];
        }

        $data['hardware'] = $hardware;
        $data['software'] = $software;

        return Modal::make()
            ->lg()
            ->title($this->getRow()->name . ' 的软硬件清单')
            ->body(view('device_related')->with('data', $data))
            ->button($this->title);
    }
}
