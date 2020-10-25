<?php

namespace App\Admin\Actions\Grid;

use App\Admin\Forms\MaintenanceForm;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Widgets\Modal;

class MaintenanceAction extends RowAction
{
    protected $item = null;
    /**
     * @return string
     */
    protected $title = '🔧 报告维修';

    public function __construct($item)
    {
        $this->item = $item;
    }

    public function render()
    {
        // 实例化表单类并传递自定义参数
        $form = MaintenanceForm::make()->payload([
            'item' => $this->item,
            'item_id' => $this->getKey()
        ]);

        return Modal::make()
            ->lg()
            ->title('将 ' . $this->getRow()->name . ' 报告为维修')
            ->body($form)
            ->button($this->title);
    }
}
