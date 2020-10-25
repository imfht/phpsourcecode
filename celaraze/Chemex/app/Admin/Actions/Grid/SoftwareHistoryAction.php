<?php

namespace App\Admin\Actions\Grid;

use App\Models\SoftwareTrack;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Widgets\Modal;

class SoftwareHistoryAction extends RowAction
{
    /**
     * @return string
     */
    protected $title = '📃 软件变动履历';

    public function render()
    {
        // 实例化表单类并传递自定义参数
        $id = $this->getKey();

        $data = [];

        $single = [
            'type' => '',
            'name' => '',
            'status' => '',
            'style' => '',
            'datetime' => ''
        ];

        $software_tracks = SoftwareTrack::withTrashed()
            ->where('software_id', $id)
            ->get();

        foreach ($software_tracks as $software_track) {
            $single['type'] = '设备';
            if (!empty($software_track && !empty($software_track->device))) {
                $single['name'] = $software_track->device->name;
            } else {
                $single['name'] = '未知';
            }
            $single['status'] = '+';
            $single['datetime'] = json_decode($software_track, true)['created_at'];
            array_push($data, $single);
            if (!empty($software_track->deleted_at)) {
                $single['status'] = '-';
                $single['datetime'] = json_decode($software_track, true)['deleted_at'];
                array_push($data, $single);
            }
        }

        $datetime = array_column($data, 'datetime');
        array_multisort($datetime, SORT_DESC, $data);

        return Modal::make()
            ->lg()
            ->title($this->getRow()->name . ' 的变动履历')
            ->body(view('history')->with('data', $data))
            ->button($this->title);
    }
}
