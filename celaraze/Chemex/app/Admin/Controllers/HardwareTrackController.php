<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\HardwareTrackDisableAction;
use App\Admin\Repositories\HardwareTrack;
use App\Support\Data;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Grid;
use Dcat\Admin\Widgets\Alert;

class HardwareTrackController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new HardwareTrack(['hardware', 'device']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('hardware.name');
            $grid->column('device.name');
            $grid->column('created_at');
            $grid->column('updated_at');

            $grid->disableCreateButton();
            $grid->disableRowSelector();
            $grid->disableBatchDelete();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();
            $grid->setActionClass(Grid\Displayers\Actions::class);

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(new HardwareTrackDisableAction());
            });

            $grid->toolsWithOutline(false);
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
