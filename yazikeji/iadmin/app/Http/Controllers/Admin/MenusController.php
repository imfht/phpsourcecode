<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MenusStoreRequest;
use Illuminate\Http\Request;
use Services\MenuService;

class MenusController extends Controller
{
    protected $menu;

    public function __construct(MenuService $menu)
    {
        $this->menu = $menu;
    }

    /**
     * 菜单列表
     * @return $this
     */
    public function index()
    {
        $list = $this->menu->getMenusTwo();
        return view('admin.menus.index')->with('menus', $list);
    }

    public function show()
    {
        return view('admin.menus.show');
    }

    /**
     * 添加菜单页面
     * @return $this
     */
    public function create()
    {
        $list = $this->menu->getMenusTwo();
        return view('admin.menus.create')->with('menus', $list);
    }

    /**
     * 提交新增菜单数据
     * @param MenusStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(MenusStoreRequest $request)
    {
        $data = $request->only('name', 'display_name', 'uri', 'sort', 'pid');
        $result = $this->menu->store($data);
        if ($result === true) {
            showMessage('添加成功', route('menus.index'));
        } else {
            showMessage($result);
        }
        return redirect()->back();
    }

    public function edit($id)
    {
        //获取数据
        return view('admin.menus.edit')
            ->with('info', $this->menu->findById($id))
            ->with('menus', $this->menu->getMenusTwo());
    }

    public function update(Request $request, $id)
    {
        $data = $request->only('name', 'display_name', 'uri', 'sort', 'pid');
        if ($this->menu->update($data, $id)) {
            showMessage('更新成功', route('menus.index'));
        } else {
            showMessage('更新失败');
        }
        return redirect()->back();

    }

    public function destroy($id)
    {
        $message = '删除失败';
        if ($this->menu->delete($id)) {
            $message = '删除成功';
        }
        showMessage($message);
        return back();

    }
}
