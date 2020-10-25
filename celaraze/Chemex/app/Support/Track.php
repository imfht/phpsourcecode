<?php


namespace App\Support;


use App\Models\DeviceTrack;
use App\Models\HardwareTrack;
use App\Models\ServiceIssue;
use App\Models\ServiceRecord;
use App\Models\ServiceTrack;
use App\Models\SoftwareRecord;
use App\Models\SoftwareTrack;
use Illuminate\Database\Eloquent\Collection;

class Track
{
    /**
     * 获取设备当前最新的使用者
     * @param $device_id
     * @return string
     */
    public static function currentDeviceTrackStaff($device_id)
    {
        $device_track = DeviceTrack::where('device_id', $device_id)
            ->first();
        if (empty($device_track)) {
            return 0;
        } else {
            $staff = $device_track->staff;
            if (empty($staff)) {
                return -1;
            } else {
                return $staff->id;
            }
        }
    }

    //TODO
    public static function currentDeviceTrackDepartment()
    {

    }

    /**
     * 获取硬件当前归属的设备
     * @param $hardware_id
     * @return string
     */
    public static function currentHardwareTrack($hardware_id)
    {
        $hardware_track = HardwareTrack::where('hardware_id', $hardware_id)
            ->first();
        if (empty($hardware_track)) {
            return '闲置';
        } else {
            $device = $hardware_track->device;
            if (empty($device)) {
                return '设备失踪';
            } else {
                return $device->name;
            }
        }
    }

    /**
     * 获取软件当前剩余授权数量
     * @param $software_id
     * @return int|string
     */
    public static function leftSoftwareCounts($software_id)
    {
        $software = SoftwareRecord::where('id', $software_id)
            ->first();
        if (empty($software)) {
            return '软件状态异常';
        }
        $software_tracks = SoftwareTrack::where('software_id', $software_id)
            ->get();
        $used = count($software_tracks);
        if ($software->counts == -1) {
            return '不受限';
        } else {
            return $software->counts - $used;
        }
    }

    /**
     * 获取服务异常总览（看板）
     * @return ServiceRecord[]|Collection
     */
    public static function getServiceIssueStatus()
    {
        $services = ServiceRecord::all();
        foreach ($services as $service) {
            $service_status = $service->status;
            $service->start = null;
            $service->end = null;
            $service_track = ServiceTrack::where('service_id', $service->id)
                ->first();
            if (empty($service_track) || empty($service_track->device)) {
                $service->device_name = '未知';
            } else {
                $service->device_name = $service_track->device->name;
            }
            $issues = [];
            $service_issues = ServiceIssue::where('service_id', $service->id)->take(3)->get();
            foreach ($service_issues as $service_issue) {
                if (empty($service->start)) {
                    $service->start = $service_issue->start;
                }
                if (strtotime($service_issue->start) < strtotime($service->start)) {
                    $service->start = $service_issue->start;
                }
                if ($service_issue->status == 1) {
                    $service->status = 1;
                    $issue = $service_issue->issue . '<br>';
                    array_push($issues, $issue);
                }
                if ($service_issue->status == 2) {
                    $service->status = 0;
                    $issue = '<span class="status-recovery">[已修复最近一个问题]</span> ' . $service_issue->issue . '<br>';
                    array_push($issues, $issue);
                    if (empty($service->end)) {
                        $service->end = $service_issue->end;
                    }
                    if (strtotime($service_issue->end) > strtotime($service->end)) {
                        $service->end = $service_issue->end;
                    }
                }
            }
            if ($service_status == 1) {
                $service->status = 3;
                $service->start = date('Y-m-d H:i:s', strtotime($service->updated_at));
            }
            $service->issues = $issues;
        }
        $services = json_decode($services, true);
        return $services;
    }

    public function deleteSoftwareTracks($software_id)
    {

    }
}
