<?php


namespace App\Admin\Metrics;


use App\Models\DeviceRecord;
use Closure;
use Dcat\Admin\Grid\LazyRenderable as LazyGrid;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Card;
use Illuminate\Contracts\Support\Renderable;

class DeviceCounts extends Card
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
        $counts = DeviceRecord::all()->count();
        $route = route('device.records.index');
        $html = <<<HTML
<div class="small-box" style="margin-bottom: 0;background: rgba(103,58,183,0.7)">
  <div class="inner">
    <h3 style="color: white;">{$counts}</h3>
    <p style="color: white;">设备数量</p>
  </div>
  <div class="icon">
    <i class="feather icon-monitor"></i>
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
