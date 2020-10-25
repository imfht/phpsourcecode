<?php

namespace App\Admin\Actions\Grid;

use App\Models\CheckRecord;
use App\Models\CheckTrack;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;

class CheckCancelAction extends RowAction
{
    /**
     * @return string
     */
    protected $title = '❌ 取消盘点任务';

    /**
     * Handle the action request.
     *
     * @return Response
     */
    public function handle()
    {
        $check_tracks = CheckTrack::where('check_id', $this->getKey())->get();
        foreach ($check_tracks as $check_track) {
            $check_track->delete();
        }
        $check_record = CheckRecord::where('id', $this->getKey())->firstOrFail();
        if ($check_record->status == 1) {
            return $this->response()
                ->warning('失败，此项盘点任务已经完成了。');
        }
        if ($check_record->status == 2) {
            return $this->response()
                ->warning('失败，此项盘点任务已经取消过了。');
        }
        $check_record->status = 2;
        $check_record->save();
        return $this->response()
            ->success('盘点任务已经取消！')
            ->refresh();
    }

    /**
     * @return string|array|void
     */
    public function confirm()
    {
        return ['取消此盘点任务？', '取消后，相应的盘点追踪将全部被移除。'];
    }
}
