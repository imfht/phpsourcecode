<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\MaintenanceFixAction;
use App\Admin\Repositories\MaintenanceRecord;
use App\Support\Data;
use App\Support\Info;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools\Selector;
use Dcat\Admin\Widgets\Alert;

class MaintenanceRecordController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new MaintenanceRecord(), function (Grid $grid) {
            $grid->column('id');
            $grid->column('item')->using(Data::items());
            $grid->column('item_id')->display(function () {
                return Info::itemIdToItemName($this->item, $this->item_id);
            });
            $grid->column('ng_description')->limit(30);
            $grid->column('ok_description')->limit(30);
            $grid->column('ng_time');
            $grid->column('ok_time');
            $grid->column('status')->using(Data::maintenanceStatus());

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();
            $grid->disableBatchActions();
            $grid->disableRowSelector();
            $grid->disableRefreshButton();

            $grid->toolsWithOutline(false);

            $grid->setActionClass(Grid\Displayers\Actions::class);

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                if ($this->status == 0) {
                    $actions->append(new MaintenanceFixAction());
                }
            });

            $grid->quickSearch();

            $grid->selector(function (Selector $selector) {
                $selector->select('status', '状态', [
                    0 => '等待处理',
                    1 => '处理完毕',
                    2 => '取消'
                ]);
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Alert
     */
    protected function detail($id)
    {
        return Data::unsupportedOperationWarning();
    }

    /**
     * Make a form builder.
     *
     * @return Alert
     */
    protected function form()
    {
        return Data::unsupportedOperationWarning();
    }
}
