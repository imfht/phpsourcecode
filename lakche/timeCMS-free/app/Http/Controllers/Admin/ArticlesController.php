<?php

namespace App\Http\Controllers\Admin;

use App\Model\Article;
use App\Model\Category;
use App\Model\Attachment;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use Redirect;
use Hash;
use Cache;
use Theme;
use Logs;

class ArticlesController extends Controller
{
  public function index()
  {
    $articles = Article::sortByDesc('id')->paginate(20);
    return Theme::view('articles.index',compact('articles'));
  }

  public function show($id)
  {
    if(!preg_match("/^[1-9]\d*$/",$id)) return Redirect::to('/');

    $type = Category::find($id);
    if(!$type) return Redirect::to(route('admin.articles.index'));

    $articles = Article::where('category_id',$id)->sortByDesc('id')->paginate(20);
    return Theme::view('articles.show',compact('articles','type'));
  }

  public function create()
  {
    $article = new Article;
    $article->id = 0;
    $article->is_show = 1;
    $article->is_top = 0;
    $article->is_recommend = 0;
    $article->sort = 0;
    $article->views = 0;
    $article->category_id = 1;
    $article->tag = '';
    $article->hash = Hash::make(time());
    return Theme::view('articles.create',compact('article'));
  }

  public function edit($id)
  {
      if (!preg_match("/^[1-9]\d*$/", $id)) return Redirect::to('/');

      $article = Article::find($id);
      if (!$article) return Redirect::to(route('admin.articles.index'));

      if ($article->hash == '') {
          $article->hash = Hash::make(time() . rand(1000, 9999));
      }

      return Theme::view('articles.edit', compact('article'));
  }

  public function store(ArticleRequest $request)
  {
      $article = Article::create([
          'title' => $request->get('title'),
          'category_id' => $request->get('category_id'),
          'sort' => $request->get('sort'),
          'views' => $request->get('views'),
          'tag' => $request->get('tag'),
          'is_recommend' => $request->get('is_recommend'),
          'is_top' => $request->get('is_top'),
          'is_show' => $request->get('is_show'),
          'info' => $request->get('info'),
          'url' => $request->get('url'),
          'cover' => $request->get('cover'),
          'thumb' => $request->get('thumb'),
          'text' => $request->get('text'),
          'subtitle' => $request->get('subtitle'),
          'author' => $request->get('author'),
          'source' => $request->get('source'),
          'keywords' => $request->get('keywords'),
          'description' => $request->get('description'),
          'hash' => $request->get('hash'),
      ]);

      if ($article) {
          Logs::save('article',$article->id,'store','添加文章');
          Cache::store('article')->flush();
          Attachment::where(['hash' => $article->hash, 'project_id' => 0])->update(['project_id' => $article->id]);
          $message = '文章添加成功，请选择操作！';
          $url = [];
          $url['返回文章列表'] = ['url' => route('admin.articles.index')];
          if ($article->category_id > 0) $url['返回栏目文章列表'] = ['url' => route('admin.articles.show', $article->category_id)];
          $url['继续添加'] = ['url' => route('admin.articles.create')];
          $url['继续编辑'] = ['url' => route('admin.articles.edit', $article->id)];
          $url['查看文章'] = ['url' => route('article.show', $article->id), 'target' => '_blank'];
          return Theme::view('message.show', compact('message', 'url'));
      }
  }

  public function update(ArticleRequest $request, $id = 0)
  {
      $article = Article::findOrFail($id);
      $article->update([
          'title' => $request->get('title'),
          'category_id' => $request->get('category_id'),
          'sort' => $request->get('sort'),
          'views' => $request->get('views'),
          'tag' => $request->get('tag'),
          'is_recommend' => $request->get('is_recommend'),
          'is_top' => $request->get('is_top'),
          'is_show' => $request->get('is_show'),
          'info' => $request->get('info'),
          'url' => $request->get('url'),
          'cover' => $request->get('cover'),
          'thumb' => $request->get('thumb'),
          'text' => $request->get('text'),
          'subtitle' => $request->get('subtitle'),
          'author' => $request->get('author'),
          'source' => $request->get('source'),
          'keywords' => $request->get('keywords'),
          'description' => $request->get('description'),
          'hash' => $request->get('hash'),
      ]);

      if ($article) {
          Logs::save('article',$article->id,'update','修改文章');
          Cache::store('article')->flush();
          Attachment::where(['hash' => $article->hash, 'project_id' => 0])->update(['project_id' => $article->id]);
          $message = '文章修改成功，请选择操作！';
          $url = [];
          $url['返回文章列表'] = ['url' => route('admin.articles.index')];
          if ($article->category_id > 0) $url['返回栏目文章列表'] = ['url' => route('admin.articles.show', $article->category_id)];
          $url['继续添加'] = ['url' => route('admin.articles.create')];
          $url['继续编辑'] = ['url' => route('admin.articles.edit', $article->id)];
          $url['查看文章'] = ['url' => route('article.show', $article->id), 'target' => '_blank'];
          return Theme::view('message.show', compact('message', 'url'));
      }
  }

  public function destroy($id)
  {
    Article::destroy($id);
    Cache::store('article')->flush();
    Logs::save('article',$id,'destroy','删除文章');
    return ['error' => 0, 'message' => '删除成功！'];
  }

}
