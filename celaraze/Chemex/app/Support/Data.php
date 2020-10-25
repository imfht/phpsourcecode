<?php


namespace App\Support;


use Dcat\Admin\Widgets\Alert;

class Data
{
    /**
     * 发行方式
     * @return string[]
     */
    public static function distribution()
    {
        return [
            'u' => '未知',
            'o' => '开源',
            'f' => '免费',
            'b' => '商业授权'
        ];
    }

    /**
     * 性别
     * @return string[]
     */
    public static function genders()
    {
        return [
            '无' => '无',
            '男' => '男',
            '女' => '女'
        ];
    }

    /**
     * 物件
     * @return string[]
     */
    public static function items()
    {
        return [
            'device' => '设备',
            'hardware' => '硬件',
            'software' => '软件'
        ];
    }

    /**
     * 盘点任务状态
     * @return string[]
     */
    public static function checkRecordStatus()
    {
        return [
            0 => '进行',
            1 => '完成',
            2 => '中止'
        ];
    }

    /**
     * 维修状态
     * @return string[]
     */
    public static function maintenanceStatus()
    {
        return [
            0 => '等待处理',
            1 => '处理完毕',
            2 => '取消'
        ];
    }

    /**
     * 盘点追踪状态
     * @return string[]
     */
    public static function checkTrackStatus()
    {
        return [
            0 => '未盘点',
            1 => '盘盈',
            2 => '盘亏'
        ];
    }

    /**
     * 服务异常状态
     * @return string[]
     */
    public static function serviceIssueStatus()
    {
        return [
            0 => '正常',
            1 => '故障',
            2 => '恢复',
            3 => '暂停'
        ];
    }

    /**
     * 软件标签
     * @return array
     */
    public static function softwareTags()
    {
        return [
            'windows' => [
                'windows',
                'win10',
                'win8'
            ],
            'macos' => [
                'mac',
                'cheetah',
                'puma',
                'jaguar',
                'panther',
                'tiger',
                'leopard',
                'lion',
                'mavericks',
                'yosemite',
                'capitan',
                'sierra',
                'mojave',
                'catalina',
                'bigsur'
            ],
            'linux' => [
                'linux',
                'centos',
                'ubuntu',
                'kali',
                'debian',
                'arch',
                'deepin'
            ],
            'android' => [
                'cupcake',
                'donut',
                'eclair',
                'froyo',
                'gingerbread',
                'honeycomb',
                'icecreansandwich',
                'jellybean',
                'kitkat',
                'lollipop',
                'marshmallow',
                'nougat',
                'oreo',
                'pie'
            ],
            'ios' => [
                'ios'
            ]
        ];
    }

    /**
     * 返回不支持操作的错误信息 warning
     * @return Alert
     */
    public static function unsupportedOperationWarning()
    {
        $alert = Alert::make('此功能不允许通过此操作实现。', '未提供的操作');
        $alert->warning();
        $alert->icon('feather icon-alert-triangle');
        return $alert;
    }
}
