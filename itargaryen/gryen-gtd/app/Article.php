<?php

namespace App;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Article extends Eloquent implements Feedable
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'tags',
        'cover',
        'status',
        'created_at',
        'updated_at',
        'published_at',
        'modified_times',
    ];

    protected $dates = ['deleted_at', 'published_at'];

    /**
     * 文章内容.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function withContent()
    {
        return $this->hasOne('App\ArticleData');
    }

    /**
     * 从数据库获取文章，标签处理.
     * @param $article
     * @return bool
     */
    public static function getTagArray(&$article)
    {
        if (empty($article)) {
            return false;
        }

        if (isset($article->id)) {
            $article->tagArray = explode(',', $article->tags);
        } else {
            foreach ($article as &$value) {
                $value->tags = explode(',', $value->tags);
            }
        }

        return $article;
    }

    public function toFeedItem()
    {
        return FeedItem::create([
            'id' => $this->id,
            'title' => $this->title,
            'summary' => $this->description,
            'updated' => $this->updated_at,
            'link' => action('ArticlesController@show', ['id' => $this->id]),
            'author' => env('APP_NAME'),
        ]);
    }

    public static function getFeedItems()
    {
        return self::where('status', '>', '0')->get();
    }
}
