<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\ServiceDeleteAction;
use App\Admin\Actions\Grid\ServiceIssueAction;
use App\Admin\Actions\Grid\ServiceTrackAction;
use App\Admin\Repositories\ServiceRecord;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;

class ServiceRecordController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ServiceRecord(), function (Grid $grid) {
            $grid->column('id');
            $grid->column('name');
            $grid->column('description');
            $grid->column('status')->switch('green');

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(new ServiceDeleteAction());
                $actions->append(new ServiceTrackAction());
                $actions->append(new ServiceIssueAction());
            });

            $grid->enableDialogCreate();
            $grid->disableDeleteButton();

            $grid->toolsWithOutline(false);
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
        return Show::make($id, new ServiceRecord(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('description');
            $show->field('created_at');
            $show->field('updated_at');

            $show->disableDeleteButton();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new ServiceRecord(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->text('description');
            $form->switch('status')->default(0);

            $form->display('created_at');
            $form->display('updated_at');

            $form->disableDeleteButton();
        });
    }
}
