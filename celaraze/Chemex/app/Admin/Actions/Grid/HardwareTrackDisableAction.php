<?php

namespace App\Admin\Actions\Grid;

use App\Models\HardwareTrack;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;

class HardwareTrackDisableAction extends RowAction
{
    /**
     * @return string
     */
    protected $title = '🔗 解除归属';

    /**
     * Handle the action request.
     *
     * @return Response
     */
    public function handle()
    {
        $hardware_track = HardwareTrack::where('id', $this->getKey())->first();

        if (empty($hardware_track)) {
            return $this->response()->error('找不到此硬件归属记录！');
        }

        $hardware_track->delete();

        return $this->response()
            ->success('硬件归属解除成功！')
            ->refresh();
    }

    /**
     * @return string|array|void
     */
    public function confirm()
    {
        return ['确认解除与此设备的关联？'];
    }
}
