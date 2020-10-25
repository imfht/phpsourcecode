<?php


namespace App\Admin\Metrics;


use App\Models\StaffRecord;
use Closure;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Card;
use Illuminate\Contracts\Support\Renderable;

class StaffCounts extends Card
{
    /**
     * @param string|Closure|Renderable|LazyWidget $content
     *
     * @return $this
     */
    public function content($content)
    {
        $counts = StaffRecord::all()->count();
        $route = route('staff.records.index');
        $html = <<<HTML
<div class="small-box" style="margin-bottom: 0;background: rgba(139,195,74,0.7)">
  <div class="inner">
    <h3 style="color: #ffffff;">{$counts}</h3>
    <p style="color: white;">雇员数量</p>
  </div>
  <div class="icon">
    <i class="feather icon-user-check"></i>
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
