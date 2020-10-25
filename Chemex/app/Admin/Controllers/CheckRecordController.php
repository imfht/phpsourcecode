<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\CheckCancelAction;
use App\Admin\Actions\Grid\CheckFinishAction;
use App\Admin\Repositories\CheckRecord;
use App\Models\AdminUser;
use App\Models\CheckTrack;
use App\Models\DeviceRecord;
use App\Models\HardwareRecord;
use App\Models\SoftwareRecord;
use App\Support\Data;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;

class CheckRecordController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new CheckRecord(['user']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('check_item')->using(Data::items());
            $grid->column('start_time');
            $grid->column('end_time');
            $grid->column('user.name');
            $grid->column('status')->using(Data::checkRecordStatus());

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                if ($this->status == 0) {
                    $actions->append(new CheckFinishAction());
                    $actions->append(new CheckCancelAction());
                }
            });

            $grid->disableRowSelector();
            $grid->disableBatchDelete();
            $grid->disableEditButton();
            $grid->disableDeleteButton();

            $grid->toolsWithOutline(false);

            $grid->enableDialogCreate();
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
        return Show::make($id, new CheckRecord(['user']), function (Show $show) {
            $show->field('id');
            $show->field('check_item');
            $show->field('start_time');
            $show->field('end_time');
            $show->field('user.name');
            $show->field('status')->using(Data::checkRecordStatus());
            $show->field('created_at');
            $show->field('updated_at');

            $show->disableEditButton();
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
        return Form::make(new CheckRecord(), function (Form $form) {
            $form->display('id');
            $form->select('check_item')
                ->options(Data::items())
                ->required();
            $form->datetime('start_time')
                ->required();
            $form->datetime('end_time')
                ->required();
            $form->select('user_id', admin_trans_label('User'))
                ->options(AdminUser::all()
                    ->pluck('name', 'id'))
                ->required();

            $form->display('created_at');
            $form->display('updated_at');

            $form->submitted(function (Form $form) {
                $check_record = \App\Models\CheckRecord::where('check_item', $form->check_item)
                    ->where('status', 0)
                    ->first();
                if (!empty($check_record)) {
                    return $form->error('还有未完成的相同盘点内容，请先处理');
                }
            });

            $form->saved(function (Form $form) {
                $check_record = $form->repository()->eloquent();
                switch ($check_record->check_item) {
                    case 'hardware':
                        $items = HardwareRecord::all();
                        break;
                    case 'software':
                        $items = SoftwareRecord::all();
                        break;
                    default:
                        $items = DeviceRecord::all();
                }
                foreach ($items as $item) {
                    $check_track = new CheckTrack();
                    $check_track->check_id = $form->getKey();
                    $check_track->item_id = $item->id;
                    $check_track->status = 0;
                    $check_track->checker = 0;
                    $check_track->save();
                }
            });
        });
    }
}
