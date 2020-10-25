<?php


namespace App\Admin\Metrics;


use App\Models\CheckRecord;
use Closure;
use Dcat\Admin\Grid\LazyRenderable as LazyGrid;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Card;
use Illuminate\Contracts\Support\Renderable;

class CheckRecordsReview extends Card
{
    /**
     * @param string|Closure|Renderable|LazyWidget $content
     *
     * @return $this
     */
    public function content($content)
    {
        if ($content instanceof LazyGrid) {
            $content->simple();
        }
        $counts = CheckRecord::where('status', 1)->get()->count();
        $route = route('device.records.index');
        $html = <<<HTML
<div class="info-box" style="background:transparent;margin-bottom: 0;">
  <span class="info-box-icon" style="background: rgba(99,181,247,1);color: white"><i class="feather icon-message-square"></i></span>
  <div class="info-box-content">
    <span class="info-box-text">在列的盘点任务</span>
    <span class="info-box-number">{$counts}</span>
  </div>
</div>
HTML;

        $this->content = $this->lazyRenderable($html);
        $this->noPadding();

        return $this;
    }
}
