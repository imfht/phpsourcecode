<?php


namespace App\Support;


use App\Models\DeviceRecord;
use App\Models\HardwareRecord;
use App\Models\SoftwareRecord;
use App\Models\SoftwareTrack;
use App\Models\StaffRecord;
use Illuminate\Support\Facades\File;

class Info
{
    /**
     * 雇员id换取name
     * @param $staff_id
     * @return string
     */
    public static function staffIdToName($staff_id)
    {
        $staff = StaffRecord::where('id', $staff_id)
            ->first();
        if (empty($staff)) {
            return '雇员失踪';
        }
        return $staff->name;
    }

    /**
     * 雇员id换取部门name
     * @param $staff_id
     * @return mixed
     */
    public static function staffIdToDepartmentName($staff_id)
    {
        $staff = StaffRecord::where('id', $staff_id)
            ->first();
        if (!empty($staff)) {
            return $staff->department->name;
        }
    }

    /**
     * 设备id获取操作系统标识
     * @param $device_id
     * @return string
     */
    public static function getSoftwareIcon($device_id)
    {
        $software_tracks = SoftwareTrack::where('device_id', $device_id)
            ->get();
        $tags = Data::softwareTags();
        $keys = array_keys($tags);
        foreach ($software_tracks as $software_track) {
            $name = trim($software_track->software()->withTrashed()->first()->name);
            for ($n = 0; $n < count($tags); $n++) {
                for ($i = 0; $i < count($tags[$keys[$n]]); $i++) {
                    if (stristr($name, $tags[$keys[$n]][$i]) != false) {
                        return $keys[$n];
                    }
                }
            }
        }
        return '';
    }

    /**
     * 更新ENV文件的键值
     * @param array $data
     */
    public static function setEnv(array $data)
    {
        $envPath = base_path() . DIRECTORY_SEPARATOR . '.env';
        $contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));
        $contentArray->transform(function ($item) use ($data) {
            foreach ($data as $key => $value) {
                if (str_contains($item, $key)) {
                    return $key . '=' . $value;
                }
            }
            return $item;
        });
        $content = implode("\n", $contentArray->toArray());
        File::put($envPath, $content);
    }

    /**
     * 构造WebSSH连接字符串
     * @param $host
     * @param $port
     * @param $username
     * @param $password
     * @return string
     */
    public static function getSSHBaseUrl($host, $port, $username, $password)
    {
        return "http://127.0.0.1:8222/?hostname=$host&port=$port&username=$username&password=$password";
    }

    /**
     * 物品id换取物品名称
     * @param $item
     * @param $item_id
     * @return string
     */
    public static function itemIdToItemName($item, $item_id)
    {
        switch ($item) {
            case 'hardware':
                $item_record = HardwareRecord::where('id', $item_id)->first();
                break;
            case 'software':
                $item_record = SoftwareRecord::where('id', $item_id)->first();
                break;
            default:
                $item_record = DeviceRecord::where('id', $item_id)->first();
        }
        if (empty($item_record)) {
            return '失踪了';
        } else {
            return $item_record->name;
        }
    }
}
