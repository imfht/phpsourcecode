<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\StaffDeleteAction;
use App\Admin\Repositories\StaffRecord;
use App\Support\Data;
use App\Models\StaffDepartment;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;

class StaffRecordController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new StaffRecord(['department']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('name');
            $grid->column('department.name');
            $grid->column('gender');
            $grid->column('title');
            $grid->column('mobile');
            $grid->column('email');

            $grid->quickSearch('id', 'name')
                ->placeholder('输入ID或者名称以搜索')
                ->auto(false);

            $grid->enableDialogCreate();
            $grid->disableDeleteButton();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(new StaffDeleteAction());
            });

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
        return Show::make($id, new StaffRecord(['department']), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('department.name');
            $show->field('gender');
            $show->field('title');
            $show->field('mobile');
            $show->field('email');
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
        return Form::make(new StaffRecord(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->select('department_id', admin_trans_label('Department'))
                ->options(StaffDepartment::all()
                    ->pluck('name', 'id'))
                ->required();
            $form->select('gender')
                ->options(Data::genders())
                ->required();
            $form->text('title');
            $form->mobile('mobile');
            $form->email('email');

            $form->display('created_at');
            $form->display('updated_at');

            $form->disableDeleteButton();
        });
    }
}
