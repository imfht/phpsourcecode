<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\ServiceTrackDisableAction;
use App\Admin\Repositories\ServiceTrack;
use App\Support\Data;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Grid;
use Dcat\Admin\Widgets\Alert;

class ServiceTrackController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ServiceTrack(['service', 'device']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('service.name');
            $grid->column('device.name');
            $grid->column('created_at');
            $grid->column('updated_at');

            $grid->toolsWithOutline(false);

            $grid->disableCreateButton();
            $grid->disableRowSelector();
            $grid->disableBatchDelete();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();

            $grid->setActionClass(Grid\Displayers\Actions::class);

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(new ServiceTrackDisableAction());
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
