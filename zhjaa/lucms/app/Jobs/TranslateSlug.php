<?php

namespace App\Jobs;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Topic;
use App\Handlers\SlugTranslateHandler;

class TranslateSlug implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
//        $this->handle(); 「开启队列分发后，无需使用该操作」
    }

    public function handle()
    {
        // 请求百度 API 接口进行翻译
        $slug = app(SlugTranslateHandler::class)->translate($this->article->title);

        // 为了避免模型监控器死循环调用，我们使用 DB 类直接对数据库进行操作
        \DB::table('articles')->where('id', $this->article->id)->update(['slug' => $slug]);
    }
}
