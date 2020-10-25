<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\ReportServiceIssueAction;
use App\Admin\Repositories\Service;
use App\Models\Server;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;

class ServiceController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Service(['server']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('server.name');
            $grid->column('name');
            $grid->column('description');

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(new ReportServiceIssueAction());
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
        return Show::make($id, new Service(['server']), function (Show $show) {
            $show->field('id');
            $show->field('server.name');
            $show->field('name');
            $show->field('description');
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
        return Form::make(new Service(), function (Form $form) {
            $form->display('id');
            $form->select('server_id', admin_trans_label('Server'))
                ->options(Server::all()
                    ->pluck('name', 'id'))
                ->required();
            $form->text('name')->required();
            $form->text('description');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
