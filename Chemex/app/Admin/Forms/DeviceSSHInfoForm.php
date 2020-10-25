<?php

namespace App\Admin\Forms;

use App\Models\DeviceRecord;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

class DeviceSSHInfoForm extends Form implements LazyRenderable
{
    use LazyWidget;

    // 处理请求
    public function handle(array $input)
    {
        // 获取设备id
        $device_id = $this->payload['id'] ?? null;

        // 获取SSH账号，来自表单传参
        $ssh_username = $input['ssh_username'] ?? null;

        // 获取SSH密码，来自表单传参
        $ssh_password = $input['ssh_password'] ?? null;

        // 获取SSH端口，来自表单传参
        $ssh_port = $input['ssh_port'] ?? null;

        // 如果没有设备id或者SSH相关信息则返回错误
        if (!$device_id || !$ssh_username || !$ssh_password || !$ssh_port) {
            return $this->error('参数错误！');
        }

        // 设备记录
        $device = DeviceRecord::where('id', $device_id)->first();
        // 如果没有找到这个设备记录则返回错误
        if (!$device) {
            return $this->error('设备不存在！');
        }

        $device->ssh_username = $ssh_username;
        $device->ssh_password = base64_encode($ssh_password);
        $device->ssh_port = $ssh_port;
        $device->save();

        return $this->success('SSH信息配置成功！');
    }

    /**
     * 表单
     */
    public function form()
    {
        $this->text('ssh_username', 'SSH账户')
            ->required();
        $this->text('ssh_password', 'SSH密码')
            ->required();
        $this->number('ssh_port', 'SSH端口')
            ->min(0)
            ->max(65535)
            ->default(22)
            ->required();
    }
}
