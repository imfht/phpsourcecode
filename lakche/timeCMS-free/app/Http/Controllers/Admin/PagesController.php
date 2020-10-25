<?php

namespace App\Http\Controllers\Admin;

use App\Model\Page;
use App\Model\Attachment;
use App\Http\Controllers\Controller;
use App\Http\Requests\PageRequest;
use Redirect;
use Cache;
use Hash;
use Theme;
use Logs;

class PagesController extends Controller
{
  public function index()
  {
    $pages = Page::sortByDesc('id')->paginate(20);
    return Theme::view('pages.index',compact('pages'));
  }

  public function create()
  {
    $page = new Page;
    $page->id = 0;
    $page->views = 0;
    $page->is_open = 1;
    $page->hash = Hash::make(time() . rand(1000, 9999));
    return Theme::view('pages.create',compact('page'));
  }

  public function edit($id)
  {
    if(!preg_match("/^[1-9]\d*$/",$id)) return Redirect::to('/');

    $page = Page::find($id);
    if(!$page) return Redirect::to(route('admin.pages'));

    if ($page->hash == '') {
      $page->hash = Hash::make(time() . rand(1000, 9999));
    }

    return Theme::view('pages.edit',compact('page'));
  }

  public function store(PageRequest $request)
  {
    $page = Page::create([
        'url' => $request->get('url'),
        'view' => $request->get('view'),
        'views' => $request->get('views'),
        'is_open' => $request->get('is_open'),
        'openurl' => $request->get('openurl'),
        'cover' => $request->get('cover'),
        'thumb' => $request->get('thumb'),
        'hash' => $request->get('hash'),
    ]);

    if ($page) {
      Logs::save('page',$page->id,'store','添加单页');
      Cache::store('page')->flush();
      Attachment::where(['hash' => $page->hash, 'project_id' => 0])->update(['project_id' => $page->id]);
      $message = '单页添加成功，请选择操作！';
      $url = [];
      $url['返回单页列表'] = ['url' => route('admin.pages.index')];
      $url['继续添加'] = ['url' => route('admin.pages.create')];
      $url['继续编辑'] = ['url' => route('admin.pages.edit', $page->id)];
      $url['查看单页'] = ['url' => route('page.show', $page->url), 'target' => '_blank'];
      return Theme::view('message.show', compact('message', 'url'));
    }
  }

  public function update(PageRequest $request, $id = 0)
  {
    $page = Page::findOrFail($id);
    $page->update([
        'url' => $request->get('url'),
        'view' => $request->get('view'),
        'views' => $request->get('views'),
        'is_open' => $request->get('is_open'),
        'openurl' => $request->get('openurl'),
        'cover' => $request->get('cover'),
        'thumb' => $request->get('thumb'),
        'hash' => $request->get('hash'),
    ]);

    if ($page) {
      Logs::save('page',$page->id,'update','修改单页');
      Cache::store('page')->flush();
      Attachment::where(['hash' => $page->hash, 'project_id' => 0])->update(['project_id' => $page->id]);
      $message = '单页修改成功，请选择操作！';
      $url = [];
      $url['返回单页列表'] = ['url' => route('admin.pages.index')];
      $url['继续添加'] = ['url' => route('admin.pages.create')];
      $url['继续编辑'] = ['url' => route('admin.pages.edit', $page->id)];
      $url['查看单页'] = ['url' => route('page.show', $page->url), 'target' => '_blank'];
      return Theme::view('message.show', compact('message', 'url'));
    }
  }

  public function destroy($id)
  {
    Page::destroy($id);
    Cache::store('page')->flush();
    Logs::save('page',$id,'destroy','删除单页');
    return ['error' => 0, 'message' => '删除成功！'];
  }

}
