<?php

namespace App\Admin\Actions\Grid;

use App\Models\ServiceTrack;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Grid\RowAction;
use Illuminate\Http\Request;

class FixServiceIssueAction extends RowAction
{
    /**
     * @return string
     */
    protected $title = '修复';

    /**
     * Handle the action request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        // dump($this->getKey());

        $service_track = ServiceTrack::where('id', $this->getKey())->first();
        if (empty($service_track)) {
            return $this->response()
                ->error('没有找到此问题！');
        } else {
            $service_track->status = 2;
            $service_track->save();
            return $this->response()
                ->success('已报告修复！')
                ->refresh();
        }
    }

    /**
     * @return string|array|void
     */
    public function confirm()
    {
        return ['确定报告问题已经修复？'];
    }
}
