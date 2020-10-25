<?php

namespace App\Http\Controllers;

use App\Article;
use App\Config;
use App\Events\PublishArticle;
use App\File;
use App\Http\Requests\CreateArticleRequest;
use App\Http\Requests\UpdateArticleStatus;
use App\Tag;
use Auth;
use Carbon\CarbonImmutable;
use DB;
use Illuminate\Support\Facades\Request;

class ArticlesController extends Controller
{
    /**
     * 文章列表页.
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @internal param Article $article
     */
    public function index()
    {
        $articles = Article::where('status', '>', 0)
            ->orderBy('published_at', 'desc')
            ->paginate(env('ARTICLE_PAGE_SIZE'));

        foreach ($articles as &$article) {
            if (empty($article->cover)) {
                $article->cover = Config::getAllConfig('SITE_DEFAULT_IMAGE');
            }

            if (! empty($article->published_at)) {
                $article->publishedAt = $article->published_at->calendar();
            }
        }

        return view('articles.index', compact('articles'));
    }

    /**
     * @param $tag
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tag($tag)
    {
        $tag = Tag::where('name', $tag)
            ->first();

        $articles = empty($tag) ? (object) [] : $tag->article()
            ->where('status', '>', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(env('ARTICLE_PAGE_SIZE'));

        foreach ($articles as &$article) {
            if (empty($article->cover)) {
                try {
                    $article->cover = Config::getAllConfig('SITE_DEFAULT_IMAGE');
                } catch (\Exception $e) {
                    \Log::error('Get SITE_DEFAULT_IMAGE error.', $e);
                }
            }
        }

        return view('articles.index', compact('articles'));
    }

    /**
     * 新建文章页面.
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function create()
    {
        $tags = Tag::orderBy('num', 'desc')->take(7)->get();
        $article['cover'] = Config::getAllConfig('SITE_DEFAULT_IMAGE');
        $article = (object) $article;
        $bodyClassString = 'no-padding';

        return view('articles.create', compact('tags', 'article', 'bodyClassString'));
    }

    /**
     * 保存新的文章.
     *
     * @param CreateArticleRequest|\Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @internal param Article $article
     */
    public function store(CreateArticleRequest $request)
    {
        $resParams = $request->all();

        if (isset($resParams['status'])) {
            return response()->json([
                'code' => 400,
                'message' => 'Status Not Allowed',
                'type' => 'danger',
            ]);
        }

        $resParams['cover'] = empty($resParams['cover']) ? env('SITE_DEFAULT_IMAGE') : $resParams['cover'];
        $resParams['status'] = 0;

        /* 创建文章 */
        $article = Article::create($resParams);

        /* 标签处理 */
        Tag::createArticleTagProcess($request->get('tags'), $article->id);

        /* 更新文章内容 */
        $article->withContent()->create([
            'content' => $request->get('content'),
        ]);

        $href = action('ArticlesController@edit', ['id' => $article->id]);

        return response()->json([
            'code' => 200,
            'message' => '文章提交成功',
            'type' => 'success',
            'href' => $href,
        ]);
    }

    /**
     * 上传文章封面图.
     * @return array
     */
    public function cover()
    {
        $File = Request::file('cover');

        return File::upload($File);
    }

    /**
     * 文章详情页.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $article = Article::find($id);

        /* 没有数据跳转首页 */
        if (empty($article)) {
            return redirect('/');
        }

        /* 没有权限跳转首页 */
        if (($article->trashed() || $article->status < 1) && ! Auth::check()) {
            return redirect('/');
        }

        $article = Article::getTagArray($article);

        $content = $article->withContent()->first()->content;
        $article->content = handleContentImage($content);

        $siteTitle = $article->title;
        $siteKeywords = $article->tags;
        $siteDescription = $article->description;

        $article->timestamps = false;

        if (! Auth::check()) {
            $article->increment('views');
        }

        return view('articles.show', compact('siteTitle', 'siteKeywords', 'siteDescription', 'article'));
    }

    /**
     * 文章编辑页.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function edit($id)
    {
        $article = Article::withTrashed()->find($id);
        $article->cover = empty($article->cover) ? Config::getAllConfig('SITE_DEFAULT_IMAGE') : $article->cover;

        $article = Article::getTagArray($article);
        $tags = Tag::orderBy('num', 'desc')->take(7)->get();
        $bodyClassString = 'no-padding';

        return view('articles.edit', compact('article', 'tags', 'bodyClassString'));
    }

    /**
     * 更新文章.
     *
     * @param  int $id
     * @param CreateArticleRequest|\Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, CreateArticleRequest $request)
    {
        $updateData = $request->all();

        if (isset($updateData['status'])) {
            return response()->json([
                'code' => 400,
                'message' => 'Status Not Allowed',
                'type' => 'danger',
            ]);
        }

        $updateData['updated_at'] = CarbonImmutable::now();
        $updateData['status'] = 0;

        $article = Article::withTrashed()
            ->find($id);

        $article->update($updateData);

        /* 更新文章内容 */
        $article->withContent()->update([
            'content' => $request->get('content'),
        ]);

        return response()->json([
            'code' => 200,
            'message' => '更新成功',
            'type' => 'success',
        ]);
    }

    /**
     * 更新文章状态：0，草稿；1，已发布.
     */
    public function updateStatus(UpdateArticleStatus $request)
    {
        $article = Article::find($request->get('id'));

        if ($request->get('status') === '1') {
            if ($article->modified_times === 0) {
                $res = $article->update([
                    'published_at' => CarbonImmutable::now(),
                    'status' => $request->get('status'),
                    'modified_times' => DB::raw('modified_times + 1'),
                ]);
            } else {
                $res = $article->update([
                    'status' => $request->get('status'),
                    'modified_times' => DB::raw('modified_times + 1'),
                ]);
            }
        } else {
            $res = $article->update([
                'status' => $request->get('status'),
            ]);
        }

        event(new PublishArticle());

        return response()->json([
            'code' => 200,
            'message' => $res ? '更新成功' : '更新失败',
            'type' => $res ? 'success' : 'danger',
        ]);
    }
}
