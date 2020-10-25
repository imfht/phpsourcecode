<?php

namespace App\Http\Controllers\Admin;

use App\Model\Menu;
use App\Http\Controllers\Controller;
use App\Http\Requests\MenuRequest;
use Redirect;
use Hash;
use Cache;
use Theme;
use Logs;

class MenusController extends Controller
{
  public function index()
  {
    $menus = Menu::sortByDesc('id')->paginate(20);
    return Theme::view('menus.index',compact('menus'));
  }

  public function create()
  {
    $menu = new Menu;
    $menu->id = 0;
    $menu->position = 1;
    $menu->sort = 0;
    $menu->is_open = 1;
    $menu->hash = Hash::make(time() . rand(1000, 9999));
    return Theme::view('menus.create',compact('menu'));
  }

  public function edit($id)
  {
    if(!preg_match("/^[1-9]\d*$/",$id)) return Redirect::to('/');

    $menu = Menu::find($id);
    if(!$menu) return Redirect::to(route('admin.menus'));

    if ($menu->hash == '') {
      $menu->hash = Hash::make(time() . rand(1000, 9999));
    }

    return Theme::view('menus.edit',compact('menu'));
  }

  public function store(MenuRequest $request)
  {
    $menu = Menu::create([
        'name' => $request->get('name'),
        'url' => $request->get('url'),
        'position' => $request->get('position'),
        'sort' => $request->get('sort'),
        'is_open' => $request->get('is_open'),
        'hash' => $request->get('hash'),
    ]);

    if ($menu) {
      Logs::save('menu',$menu->id,'store','添加菜单');
      Cache::store('menu')->flush();
      $message = '菜单添加成功，请选择操作！';
      $url = [];
      $url['返回菜单列表'] = ['url' => route('admin.menus.index')];
      $url['继续添加'] = ['url' => route('admin.menus.create')];
      $url['继续编辑'] = ['url' => route('admin.menus.edit', $menu->id)];
      return Theme::view('message.show', compact('message', 'url'));
    }
  }

  public function update(MenuRequest $request, $id = 0)
  {
    $menu = Menu::findOrFail($id);
    $menu->update([
      'name' => $request->get('name'),
      'url' => $request->get('url'),
      'position' => $request->get('position'),
      'sort' => $request->get('sort'),
      'is_open' => $request->get('is_open'),
      'hash' => $request->get('hash'),
    ]);

    if ($menu) {
      Logs::save('menu',$menu->id,'update','修改菜单');
      Cache::store('menu')->flush();
      $message = '菜单修改成功，请选择操作！';
      $url = [];
      $url['返回菜单列表'] = ['url' => route('admin.menus.index')];
      $url['继续添加'] = ['url' => route('admin.menus.create')];
      $url['继续编辑'] = ['url' => route('admin.menus.edit', $menu->id)];
      return Theme::view('message.show', compact('message', 'url'));
    }
  }

  public function destroy($id)
  {
    Menu::destroy($id);
    Cache::store('menu')->flush();
    Logs::save('menu',$id,'destroy','删除菜单');
    return ['error' => 0, 'message' => '删除成功！'];
  }

}
