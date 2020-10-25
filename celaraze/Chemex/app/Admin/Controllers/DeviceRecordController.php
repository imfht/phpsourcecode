<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\DeviceHistoryAction;
use App\Admin\Actions\Grid\DeviceRelatedAction;
use App\Admin\Actions\Grid\DeviceSSHInfoAction;
use App\Admin\Actions\Grid\DeviceTrackAction;
use App\Admin\Actions\Grid\MaintenanceAction;
use App\Admin\Repositories\DeviceRecord;
use App\Models\DeviceCategory;
use App\Models\VendorRecord;
use App\Support\Info;
use App\Support\System;
use App\Support\Track;
use Dcat\Admin\Controllers\AdminController;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools\Selector;
use Dcat\Admin\Show;

class DeviceRecordController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new DeviceRecord(['category', 'vendor']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('qrcode')->qrcode(function () {
                return base64_encode('device:' . $this->id);
            }, 200, 200);
            $grid->column('name')->display(function ($name) {
                $tag = Info::getSoftwareIcon($this->id);
                if (empty($tag)) {
                    return $name;
                } else {
                    return "<img src='/static/images/icons/$tag.png' style='width: 25px;height: 25px;margin-right: 10px'/>$name";
                }
            });
            $grid->column('category.name');
            $grid->column('vendor.name');
            $grid->column('sn');
            $grid->column('mac');
            $grid->column('ip');
            $grid->column('owner')->display(function () {
                $res = Track::currentDeviceTrackStaff($this->id);
                switch ($res) {
                    case -1:
                        return '雇员失踪';
                    case 0:
                        return '闲置';
                    default:
                        return Info::staffIdToName($res);
                }
            });
            $grid->column('department')->display(function () {
                $res = Track::currentDeviceTrackStaff($this->id);
                if ($res < 0) {
                    return '';
                }
                return Info::staffIdToDepartmentName($res);
            });

            $grid->toolsWithOutline(false);

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(new DeviceTrackAction());
                $actions->append(new DeviceRelatedAction());
                $actions->append(new DeviceHistoryAction());
                if (!empty($this->ip) && !empty($this->ssh_username) && !empty($this->ssh_password) && !empty($this->ssh_port)) {
                    $url = Info::getSSHBaseUrl($this->ip, $this->ssh_port, $this->ssh_username, $this->ssh_password);
                    $web_ssh_status = System::checkWebSSHServiceStatus($url);
                    if ($web_ssh_status == 200) {
                        $actions->append("<a href='$url' target='_blank'>💻 通过SSH连接...</a>");
                    } else {
                        $actions->append("<a disabled>💻 通过SSH连接...（WebSSH服务未启动）</a>");
                    }
                }
                $actions->append(new DeviceSSHInfoAction());
                $actions->append(new MaintenanceAction('device'));
            });

            $grid->quickSearch('id', 'name')
                ->placeholder('输入ID或者名称以搜索')
                ->auto(false);

            $grid->selector(function (Selector $selector) {
                $selector->select('category_id', '设备分类', DeviceCategory::all()->pluck('name', 'id'));
                $selector->select('vendor_id', '制造商', VendorRecord::all()->pluck('name', 'id'));
            });

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
        return Show::make($id, new DeviceRecord(['category', 'vendor']), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('description');
            $show->field('category.name');
            $show->field('vendor.name');
            $show->field('sn');
            $show->field('mac');
            $show->field('ip');
            $show->field('photo')->image();
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
        return Form::make(new DeviceRecord(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->text('description');
            $form->select('category_id', admin_trans_label('Category'))
                ->options(DeviceCategory::all()
                    ->pluck('name', 'id'))
                ->required();
            $form->select('vendor_id', admin_trans_label('Vendor'))
                ->options(VendorRecord::all()
                    ->pluck('name', 'id'))
                ->required();
            $form->text('sn');
            $form->text('mac');
            $form->ip('ip');
            $form->image('photo')
                ->autoUpload()
                ->uniqueName();

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
