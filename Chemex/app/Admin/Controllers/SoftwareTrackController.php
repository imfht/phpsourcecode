<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\SoftwareTrackDisableAction;
use App\Admin\Repositories\SoftwareTrack;
use App\Support\Data;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Grid;
use Dcat\Admin\Widgets\Alert;

class SoftwareTrackController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new SoftwareTrack(['software', 'device']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('software.name');
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
                $actions->append(new SoftwareTrackDisableAction());
            });

            $grid->quickSearch('software_id')
                ->placeholder('输入软件ID以筛选')
                ->auto(false);

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
