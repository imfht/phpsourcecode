<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\VendorRecord;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;

class VendorRecordController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new VendorRecord(), function (Grid $grid) {
            $grid->column('id');
            $grid->column('name');
            $grid->column('description');
            $grid->column('location');

            $grid->quickSearch('id', 'name')
                ->placeholder('输入ID或者名称以搜索')
                ->auto(false);

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
        return Show::make($id, new VendorRecord(), function (Show $show) {
            $show->field('id');
            $show->field('name');
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
        return Form::make(new VendorRecord(), function (Form $form) {
            $form->display('id');
            $form->text('name')
                ->required();
            $form->text('description');
            $form->text('location');
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
