<?php

namespace App\Admin\Actions\Grid;

use App\Admin\Forms\MaintenanceFixForm;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Widgets\Modal;

class MaintenanceFixAction extends RowAction
{
    protected $title = '🧱 处理维修';

    public function render()
    {
        // 实例化表单类并传递自定义参数
        $form = MaintenanceFixForm::make()->payload(['id' => $this->getKey()]);

        return Modal::make()
            ->lg()
            ->title('处理 ' . $this->getRow()->name . ' 的维修结果')
            ->body($form)
            ->button($this->title);
    }
}
