<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\ReportServerIssueAction;
use App\Admin\Repositories\Server;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;

class ServerController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Server(), function (Grid $grid) {
            $grid->column('id');
            $grid->column('name');
            $grid->column('ip');
            $grid->column('description');
            $grid->column('location');

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(new ReportServerIssueAction());
            });

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
            });
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
        return Show::make($id, new Server(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('ip');
            $show->field('description');
            $show->field('location');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Server(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->ip('ip')->required();
            $form->text('description');
            $form->text('location');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
