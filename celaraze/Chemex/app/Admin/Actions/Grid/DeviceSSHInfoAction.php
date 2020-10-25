<?php

namespace App\Admin\Actions\Grid;

use App\Admin\Forms\DeviceSSHInfoForm;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Widgets\Modal;

class DeviceSSHInfoAction extends RowAction
{
    /**
     * @return string
     */
    protected $title = '✍ 编辑SSH连接信息';

    public function render()
    {
        // 实例化表单类并传递自定义参数
        $form = DeviceSSHInfoForm::make()->payload(['id' => $this->getKey()]);

        return Modal::make()
            ->lg()
            ->title('为 ' . $this->getRow()->name . ' 补充SSH连接信息')
            ->body($form)
            ->button($this->title);
    }
}
