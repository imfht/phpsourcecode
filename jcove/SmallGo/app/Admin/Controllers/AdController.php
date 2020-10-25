<?php
/**
 * User: XiaoFei Zhai
 * Date: 17/10/9
 * Time: 下午4:54
 */

namespace App\Admin\Controllers;


use App\Models\Ad;
use App\Models\AdPosition;
use App\Models\Category;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class AdController
{
    use ModelForm;
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $position                       =   AdPosition::all()->pluck('name','position');
        $array                          =   $position->toArray();
        return Admin::grid(Ad::class, function (Grid $grid) use ($array){

            $grid->id('ID')->sortable();
            $grid->name('名称');
            $grid->url('链接');
            $grid->cover('图片')->image();
            $grid->position('位置')->display(function ($position) use ($array){
                return $array[$position];
            });
            $grid->expire_date('有效期');
            $grid->created_at();
            $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Ad::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name','名称');
            $form->image('cover','图片');
            $form->date('expire_date','有效期');
            $form->url('url','链接');
            $form->select('position', '位置')->options(AdPosition::all()->pluck('name','position'));
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

}