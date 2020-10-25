<?php

namespace App\Admin\Forms;

use App\Models\MaintenanceRecord;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

class MaintenanceFixForm extends Form implements LazyRenderable
{
    use LazyWidget;

    // 处理请求
    public function handle(array $input)
    {
        // 获取物品id
        $id = $this->payload['id'] ?? null;

        // 获取故障说明，来自表单传参
        $ok_description = $input['ok_description'] ?? null;

        // 获取故障时间，来自表单传参
        $ok_time = $input['ok_time'] ?? null;

        // 获取故障时间，来自表单传参
        $status = $input['status'] ?? null;

        // 如果没有物品、物品id、故障说明、故障时间则返回错误
        if (!$id || !$ok_description || !$ok_time || !$status) {
            return $this->error('参数错误');
        }

        $maintenance_record = MaintenanceRecord::where('id', $id)->first();

        // 如果没有找到这个物品记录则返回错误
        if (!$maintenance_record) {
            return $this->error('物品不存在');
        }

        // 创建新的硬件追踪
        $maintenance_record->ok_description = $ok_description;
        $maintenance_record->ok_time = $ok_time;
        $maintenance_record->status = $status;
        $maintenance_record->save();

        return $this->success('维修记录更新成功');
    }

    /**
     * 表单
     */
    public function form()
    {
        $this->text('ok_description', '故障说明')->required();
        $this->datetime('ok_time', '故障时间')->required();
        $this->select('status', '处理结果')->options([
            1 => '已维修',
            2 => '取消维修'
        ])->required();
    }
}
