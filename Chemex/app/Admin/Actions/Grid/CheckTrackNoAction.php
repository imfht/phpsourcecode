<?php

namespace App\Admin\Actions\Grid;

use App\Models\CheckTrack;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class CheckTrackNoAction extends RowAction
{
    /**
     * @return string
     */
    protected $title = '🔴 盘亏';

    /**
     * Handle the action request.
     *
     * @return Response
     */
    public function handle()
    {
        $check_track = CheckTrack::where('id', $this->getKey())->first();
        if (empty($check_track)) {
            return $this->response()
                ->error('没有找到此盘点追踪');
        } else {
            $check_track->status = 2;
            $check_track->checker = Admin::user()->id;
            $check_track->save();
            return $this->response()
                ->success('已盘亏')
                ->refresh();
        }
    }
}
