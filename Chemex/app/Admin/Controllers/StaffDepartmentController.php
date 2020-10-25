<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\StaffDepartment;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;

class StaffDepartmentController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new StaffDepartment(['parent']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('name');
            $grid->column('description');
            $grid->column('parent.name');

            $grid->enableDialogCreate();

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
        return Show::make($id, new StaffDepartment(['parent']), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('description');
            $show->field('parent.name');
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
        return Form::make(new StaffDepartment(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->text('description');
            $form->select('parent_id', admin_trans_label('Parent'))
                ->options(\App\Models\StaffDepartment::all()->pluck('name', 'id'))
                ->default(0);

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
