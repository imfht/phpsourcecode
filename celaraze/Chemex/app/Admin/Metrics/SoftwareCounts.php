<?php


namespace App\Admin\Metrics;


use App\Models\SoftwareRecord;
use Closure;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Card;
use Illuminate\Contracts\Support\Renderable;

class SoftwareCounts extends Card
{
    /**
     * @param string|Closure|Renderable|LazyWidget $content
     *
     * @return $this
     */
    public function content($content)
    {
        $counts = SoftwareRecord::all()->count();
        $route = route('software.records.index');
        $html = <<<HTML
<div class="small-box" style="margin-bottom: 0;background: rgba(0,150,136,0.7)">
  <div class="inner">
    <h3 style="color: #ffffff;">{$counts}</h3>
    <p style="color: white;">软件数量</p>
  </div>
  <div class="icon">
    <i class="feather icon-disc"></i>
  </div>
  <a href="{$route}" class="small-box-footer">
    前往查看 <i class="feather icon-arrow-right"></i>
  </a>
</div>
HTML;

        $this->content = $this->lazyRenderable($html);
        $this->noPadding();

        return $this;
    }
}
