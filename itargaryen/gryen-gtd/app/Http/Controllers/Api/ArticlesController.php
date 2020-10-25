<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\ArticleData;
use App\Config;
use App\Http\Controllers\Controller;
use App\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    /**
     * 获取首页推荐文章.
     */
    public function topArticles()
    {
        $articles = Article::where('status', '>', 0)
            ->orderBy('views', 'desc')
            ->paginate(9);

        $articles = $articles->map(function ($article) {
            $article->href = action('ArticlesController@show', ['id' => $article->id]);
            $article->publishedAt = $article->published_at->calendar();

            return $article;
        });

        return $articles;
    }

    /**
     * 获取关联文章.
     * @param $articleId
     * @return array
     */
    public function moreArticles($articleId)
    {
        $theArticle = Article::find($articleId);
        $tags = explode(',', $theArticle->tags);
        $articles = $this->getArticlesByTags($tags)
            ->diff(collect([$theArticle]));

        if ($articles->count() < 3) {
            $articles = null;
        } else {
            $articles = $articles->random(3)
                ->map(function ($article) {
                    $article->cover = imageView2($article->cover, ['w' => '672', 'h' => '450']);
                    $article->href = action('ArticlesController@show', ['id' => $article->id]);

                    return collect($article)->only(['id', 'title', 'cover', 'href']);
                });
        }

        return $articles;
    }

    public function getArticleContent($articleId)
    {
        $content = ArticleData::where('article_id', $articleId)->get()->first();

        return $content;
    }

    /**
     * 通过 tags 获取文章.
     * @param array $tags
     * @return Collection
     */
    private function getArticlesByTags(array $tags)
    {
        $articles = new Collection();
        $tagsWithArticles = Tag::whereIn('name', $tags)->with('article')->get();

        foreach ($tagsWithArticles as $tagsObj) {
            $articles = $articles->merge($tagsObj->article);
        }

        return $articles;
    }

    /**
     * 获取文章列表.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getList(Request $request)
    {
        $sorter = '';
        $sort = [];
        $order = 'desc';
        $orderType = 'published_at';
        $status = 0;
        $onlyTrashed = 'no';
        $pageSize = empty($request->get('pageSize')) ? env('ARTICLE_PAGE_SIZE') : $request->get('pageSize');

        if (! empty($request)) {
            $onlyTrashed = $request->get('onlyTrashed');
            $sorter = $request->get('sorter');
            $status = $request->get('status');
        }

        if (strlen($sorter) > 0) {
            $sort = explode('_', $sorter);
        }

        if (count($sort) > 0) {
            $order = $sort[count($sort) - 1];
            $orderType = str_replace('_'.$order, '', $sorter);
        }

        if ($onlyTrashed === 'yes') {
            $articles = Article::onlyTrashed()
                ->orderBy($orderType, $order)
                ->paginate($pageSize);
        } else {
            $articles = Article::where('status', $status)
                ->orderBy($orderType, $order)
                ->paginate($pageSize);
        }

        foreach ($articles as &$article) {
            if (empty($article->cover)) {
                $article->cover = Config::getAllConfig('SITE_DEFAULT_IMAGE');
            }

            $article->href = action('ArticlesController@show', ['id' => $article->id]);
            $article->createdAt = $article->created_at->toDateTimeString();
            $article->updatedAt = $article->updated_at->toDateTimeString();

            if (! empty($article->published_at)) {
                $article->publishedAt = $article->published_at->toDateTimeString();
            }
        }

        return response($articles);
    }

    /**
     * TODO: 只允许软删除 status 为 0 的文章.
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|null
     * @throws \Exception
     */
    public function delete(Request $request)
    {
        $ids = $request->get('ids');
        $response = null;

        if (empty($ids)) {
            return response()->noContent(404);
        }

        $res = Article::destroy($request->get('ids'));

        if ($res > 0) {
            $response = $this->getList($request);
        }

        return empty($response) ? response($response) : $response;
    }

    /**
     * TODO: 只允许处理 status 为 0 的文章，且不能直接发布文章
     * 快速恢复文章为发布状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function restore(Request $request)
    {
        $id = $request->get('id');

        Article::onlyTrashed()
            ->find($id)->restore();

        $response = $this->getList($request);

        return empty($response) ? response($response) : $response;
    }

    /**
     * 彻底删除一篇文章.
     * TODO: 只允许处理已经软删除了的文章.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function forceDelete(Request $request)
    {
        $id = $request->get('id');
        Article::onlyTrashed()
            ->find($id)->forceDelete();

        $response = $this->getList($request);

        return empty($response) ? response($response) : $response;
    }
}
