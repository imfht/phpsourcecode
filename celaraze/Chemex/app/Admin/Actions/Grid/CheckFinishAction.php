<?php

namespace App\Admin\Actions\Grid;

use App\Models\CheckRecord;
use App\Models\CheckTrack;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;

class CheckFinishAction extends RowAction
{
    /**
     * @return string
     */
    protected $title = '⚡ 完成任务';

    /**
     * Handle the action request.
     *
     * @return Response
     */
    public function handle()
    {
        $check_track = CheckTrack::where('status', 0)->where('check_id', $this->getKey())->first();
        if (empty($check_track)) {
            $check_record = CheckRecord::where('id', $this->getKey())->firstOrFail();
            if ($check_record->status == 1) {
                return $this->response()
                    ->warning('失败，此项盘点任务已经被完成过了。');
            }
            if ($check_record->status == 2) {
                return $this->response()
                    ->warning('失败，此项盘点任务已经被提前中止了。');
            }
            $check_record->status = 1;
            $check_record->save();
            return $this->response()
                ->success('太棒了，已经完成了此项盘点全部内容！')
                ->refresh();
        } else {
            return $this->response()
                ->error('失败，至少还有一项未完成的盘点追踪：' . $check_track->id);
        }
    }

    /**
     * @return string|array|void
     */
    public function confirm()
    {
        return ['完成盘点任务？', '请确认已经完成了所有相关的盘点追踪工作。'];
    }
}
