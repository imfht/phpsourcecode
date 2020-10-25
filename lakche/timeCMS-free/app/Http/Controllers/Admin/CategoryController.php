<?php

namespace App\Http\Controllers\Admin;

use App\Model\Category;
use App\Model\Attachment;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use Request;
use Redirect;
use Cache;
use Hash;
use Theme;
use Logs;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('parent_id', 0)->get();
        return Theme::view('category.index', compact('categories'));
    }

    public function show($id = 0)
    {
        if (!preg_match("/^[1-9]\d*$/", $id)) return Redirect::to('/');

        $parent = Category::find($id);
        if (!$parent) return Redirect::to(route('admin.category.index'));

        $categories = Category::where('parent_id', $id)->get();
        return Theme::view('category.show', compact('categories', 'parent'));
    }

    public function create()
    {
        $parent_id = intval(Request::get('parent_id'));
        $category = new Category;
        $category->is_nav_show = 1;
        $category->id = 0;
        $category->parent_id = $parent_id;
        $category->sort = 0;
        $category->hash = Hash::make(time() . rand(1000, 9999));
        return Theme::view('category.create', compact('category'));
    }

    public function edit($id)
    {
        if (!preg_match("/^[1-9]\d*$/", $id)) return Redirect::to('/');

        $category = Category::find($id);
        if (!$category) return Redirect::to(route('admin.category.index'));

        if ($category->hash == '') {
            $category->hash = Hash::make(time() . rand(1000, 9999));
        }

        return Theme::view('category.edit', compact('category'));
    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create([
            'title' => $request->get('title'),
            'info' => $request->get('info'),
            'sort' => $request->get('sort'),
            'parent_id' => $request->get('parent_id'),
            'cover' => $request->get('cover'),
            'thumb' => $request->get('thumb'),
            'is_nav_show' => $request->get('is_nav_show'),
            'keywords' => $request->get('keywords'),
            'description' => $request->get('description'),
            'templet_all' => $request->get('templet_all'),
            'templet_nosub' => $request->get('templet_nosub'),
            'templet_article' => $request->get('templet_article'),
            'hash' => $request->get('hash'),
        ]);

        if ($category) {
            Logs::save('category',$category->id,'store','创建文章分类');
            Cache::store('category')->flush();
            Attachment::where(['hash' => $category->hash, 'project_id' => 0])->update(['project_id' => $category->id]);
            $message = '栏目添加成功，请选择操作！';
            $url = [];
            $url['返回根栏目'] = ['url' => route('admin.category.index')];
            if ($category->parent_id > 0) $url['返回子栏目'] = ['url' => route('admin.category.show', $category->parent_id)];
            $url['继续添加'] = ['url' => route('admin.category.create')];
            $url['继续编辑'] = ['url' => route('admin.category.edit', $category->id)];
            $url['查看栏目'] = ['url' => route('category.show', $category->id), 'target' => '_blank'];
            return Theme::view('message.show', compact('message', 'url'));
        } else {
            return back()->withErrors(['title' => '添加失败']);
        }
    }

    public function update(CategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update([
            'title' => $request->get('title'),
            'info' => $request->get('info'),
            'sort' => $request->get('sort'),
            'parent_id' => $request->get('parent_id'),
            'cover' => $request->get('cover'),
            'thumb' => $request->get('thumb'),
            'is_nav_show' => $request->get('is_nav_show'),
            'keywords' => $request->get('keywords'),
            'description' => $request->get('description'),
            'templet_all' => $request->get('templet_all'),
            'templet_nosub' => $request->get('templet_nosub'),
            'templet_article' => $request->get('templet_article'),
            'hash' => $request->get('hash'),
        ]);

        if ($category) {
            Logs::save('category',$category->id,'update','修改文章分类');
            Cache::store('category')->flush();
            Attachment::where(['hash' => $category->hash, 'project_id' => 0])->update(['project_id' => $category->id]);
            $message = '栏目修改成功，请选择操作！';
            $url = [];
            $url['返回根栏目'] = ['url' => route('admin.category.index')];
            if ($category->parent_id > 0) $url['返回子栏目'] = ['url' => route('admin.category.show', $category->parent_id)];
            $url['继续添加'] = ['url' => route('admin.category.create')];
            $url['继续编辑'] = ['url' => route('admin.category.edit', $category->id)];
            $url['查看栏目'] = ['url' => route('category.show', $category->id), 'target' => '_blank'];
            return Theme::view('message.show', compact('message', 'url'));
        } else {
            return back()->withErrors(['title' => '添加失败']);
        }
    }

    public function destroy($id)
    {
        Category::destroy($id);
        Cache::store('category')->flush();
        Logs::save('category',$id,'destroy','删除文章分类');
        return ['error' => 0, 'message' => '删除成功！'];
    }

}
