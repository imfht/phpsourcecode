<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\HardwareDeleteAction;
use App\Admin\Actions\Grid\HardwareHistoryAction;
use App\Admin\Actions\Grid\HardwareTrackAction;
use App\Admin\Actions\Grid\MaintenanceAction;
use App\Admin\Repositories\HardwareRecord;
use App\Support\Track;
use App\Models\HardwareCategory;
use App\Models\VendorRecord;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;

class HardwareRecordController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new HardwareRecord(['category', 'vendor']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('qrcode')->qrcode(function () {
                return base64_encode('hardware:' . $this->id);
            }, 200, 200);
            $grid->column('name');
            $grid->column('description');
            $grid->column('category.name');
            $grid->column('vendor.name');
            $grid->column('specification');
            $grid->column('sn');
            $grid->column('', admin_trans_label('Owner'))->display(function () {
                return Track::currentHardwareTrack($this->id);
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(new HardwareDeleteAction());
                $actions->append(new HardwareTrackAction());
                $actions->append(new HardwareHistoryAction());
                $actions->append(new MaintenanceAction('hardware'));
            });

            $grid->quickSearch('id', 'name')
                ->placeholder('输入ID或者名称以搜索')
                ->auto(false);

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
        return Show::make($id, new HardwareRecord(['category', 'vendor']), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('description');
            $show->field('category.name');
            $show->field('vendor.name');
            $show->field('specification');
            $show->field('sn');
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
        return Form::make(new HardwareRecord(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->text('description');
            $form->select('category_id', admin_trans_label('Category'))
                ->options(HardwareCategory::all()
                    ->pluck('name', 'id'))
                ->required();
            $form->select('vendor_id', admin_trans_label('Vendor'))
                ->options(VendorRecord::all()
                    ->pluck('name', 'id'))
                ->required();
            $form->text('specification')->required();
            $form->text('sn');

            $form->display('created_at');
            $form->display('updated_at');

            $form->disableDeleteButton();
        });
    }
}
