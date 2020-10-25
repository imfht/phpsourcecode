<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\FixServiceIssueAction;
use App\Admin\Repositories\ServiceTrack;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;

class ServiceTrackController extends AdminController
{
    protected $status = [
        0 => '正常',
        1 => '异常',
        2 => '修复'
    ];

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ServiceTrack(['service']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('service.name');
            $grid->column('status')->using($this->status);
            $grid->column('description');
            $grid->column('recovery');

            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();
            $grid->disableBatchActions();
            $grid->disableRowSelector();
            $grid->disableCreateButton();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(new FixServiceIssueAction());
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
        return Show::make($id, new ServiceTrack(['service']), function (Show $show) {
            $show->field('id');
            $show->field('service.name');
            $show->field('status')->using($this->status);
            $show->field('description');
            $show->field('recovery');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return void
     */
    protected function form()
    {
        return;
//        return Form::make(new ServiceTrack(), function (Form $form) {
//            $form->display('id');
//            $form->select('service_id')
//                ->options(Service::all()
//                    ->pluck('name', 'id'));
//            $form->text('status');
//            $form->text('description');
//            $form->text('recovery');
//
//            $form->display('created_at');
//            $form->display('updated_at');
//        });
    }
}
