<?php

namespace App\Admin\Forms;

use App\Models\Service;
use App\Models\ServiceTrack;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Symfony\Component\HttpFoundation\Response;

class ReportServerIssueForm extends Form implements LazyRenderable
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
        $server_id = $this->payload['id'] ?? null;
        $description = $input['description'] ?? null;
        $recovery = $input['recovery'] ?? null;
        if (!$description || !$recovery) {
            return $this->error('参数错误！');
        }
        $services = Service::where('server_id', $server_id)->get();
        foreach ($services as $service) {
            $service_track = ServiceTrack::where('service_id', $service->id)->first();
            if (empty($service_track)) {
                $service_track = new ServiceTrack();
                $service_track->service_id = $service->id;
                $service_track->status = 1;
            } else {
                if ($service_track->status == 2) {
                    $service_track->delete();
                }
            }
            $service_track->description = $description;
            $service_track->recovery = $recovery;
            $service_track->save();
        }
        return $this->success('问题已记录！');
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('description', '问题描述')->required();
        $this->datetime('recovery', '预计恢复时间')->required();
    }
}
