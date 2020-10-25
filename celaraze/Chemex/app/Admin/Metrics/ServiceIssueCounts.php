<?php


namespace App\Admin\Metrics;


use App\Models\ServiceIssue;
use App\Models\ServiceRecord;
use Closure;
use Dcat\Admin\Grid\LazyRenderable as LazyGrid;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Card;
use Illuminate\Contracts\Support\Renderable;

class ServiceIssueCounts extends Card
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
        $counts = 0;
        $services = ServiceRecord::all();
        foreach ($services as $service) {
            $service_issue = ServiceIssue::where('service_id', $service->id)
                ->where('status', 1)
                ->first();
            if (!empty($service_issue)) {
                $counts++;
            }
        }
        $route = route('service.issues.index');
        $html = <<<HTML
<div class="small-box" style="margin-bottom: 0;background: rgba(239,83,80,0.7)">
  <div class="inner">
    <h3 style="color: #ffffff;">{$counts}</h3>
    <p style="color: white;">服务异常</p>
  </div>
  <div class="icon">
    <i class="feather icon-power"></i>
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
