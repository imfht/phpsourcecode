<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\HardwareCategory;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;

class HardwareCategoryController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new HardwareCategory(), function (Grid $grid) {
            $grid->column('id');
            $grid->column('name');
            $grid->column('description');

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
        return Show::make($id, new HardwareCategory(), function (Show $show) {
            $show->field('id');
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
        return Form::make(new HardwareCategory(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->text('description');
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
