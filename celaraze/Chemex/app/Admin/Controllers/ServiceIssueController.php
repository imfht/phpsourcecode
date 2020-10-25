<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\ServiceFixAction;
use App\Admin\Repositories\ServiceIssue;
use App\Support\Data;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Widgets\Alert;

class ServiceIssueController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ServiceIssue(['service']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('service.name');
            $grid->column('issue');
            $grid->column('status')->using(Data::serviceIssueStatus());
            $grid->column('start');
            $grid->column('end');

            $grid->setActionClass(Grid\Displayers\Actions::class);

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                if ($this->status == 1) {
                    $actions->append(new ServiceFixAction());
                }
            });

            $grid->toolsWithOutline(false);

            $grid->disableCreateButton();
            $grid->disableRowSelector();
            $grid->disableBatchDelete();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();

        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new ServiceIssue(['service']), function (Show $show) {
            $show->field('id');
            $show->field('service.name');
            $show->field('issue');
            $show->field('status')->using(Data::serviceIssueStatus());
            $show->field('start');
            $show->field('end');
            $show->field('created_at');
            $show->field('updated_at');

            $show->disableDeleteButton();
            $show->disableEditButton();
        });
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
