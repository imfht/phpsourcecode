<?php

namespace App\Admin\Actions\Grid;

use App\Admin\Forms\ReportServiceIssueForm;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Widgets\Modal;

class ReportServiceIssueAction extends RowAction
{
    /**
     * @return string
     */
    protected $title = '报告问题';

    public function render()
    {
        // 实例化表单类并传递自定义参数
        $form = ReportServiceIssueForm::make()->payload(['id' => $this->getKey()]);

        return Modal::make()
            ->lg()
            ->title('为 ' . $this->getRow()->name . ' 报告问题')
            ->body($form)
            ->button($this->title);
    }
}
