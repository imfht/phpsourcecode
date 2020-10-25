<?php

namespace App\Http\Controllers\Admin;

use App\Model\Comment;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use Redirect;
use Theme;
use Logs;

class CommentsController extends Controller
{
  public function index()
  {
    $comments = Comment::sortByDesc('id')->paginate(20);
    return Theme::view('comments.index',compact('comments'));
  }

  public function show($id)
  {
    Redirect::to(route('admin.comments.index'));
  }

  public function create()
  {
    Redirect::to(route('admin.comments.index'));
  }

  public function edit($id)
  {
      if (!preg_match("/^[1-9]\d*$/", $id)) return Redirect::to('/');

      $comment = Comment::find($id);
      if (!$comment) return Redirect::to(route('admin.comments.index'));

      return Theme::view('comments.edit', compact('comment'));
  }

  public function store(ArticleRequest $request)
  {
    Redirect::to(route('admin.comments.index'));
  }

  public function update(CommentRequest $request, $id = 0)
  {
      $comment = Comment::findOrFail($id);
      $comment->update([
          'is_show' => $request->get('is_show'),
          'is_open' => $request->get('is_open'),
      ]);

      if ($comment) {
          Logs::save('comment',$comment->id,'update','审核留言');
          $message = '留言管理成功，请选择操作！';
          $url = [];
          $url['返回留言列表'] = ['url' => route('admin.comments.index')];
          $url['继续管理'] = ['url' => route('admin.comments.edit', $comment->id)];
          $url['查看文章'] = ['url' => route('article.show', $comment->article_id), 'target' => '_blank'];
          return Theme::view('message.show', compact('message', 'url'));
      }
  }

  public function destroy($id)
  {
    Comment::destroy($id);
    Logs::save('comment',$id,'destroy','删除留言');
    return ['error' => 0, 'message' => '删除成功！'];
  }

}
