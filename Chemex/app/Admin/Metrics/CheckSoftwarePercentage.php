<?php


namespace App\Admin\Metrics;


use App\Models\CheckRecord;
use App\Models\CheckTrack;
use App\Models\SoftwareRecord;
use Closure;
use Dcat\Admin\Grid\LazyRenderable as LazyGrid;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Card;
use Illuminate\Contracts\Support\Renderable;

class CheckSoftwarePercentage extends Card
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

        $software_records_all = SoftwareRecord::all()->count();
        $check_record = CheckRecord::where('check_item', 'software')->where('status', 0)->first();
        if (!empty($check_record)) {
            $check_tracks_counts = CheckTrack::where('check_id', $check_record->id)
                ->where('status', '!=', 0)
                ->get()
                ->count();
            $done_counts = $check_tracks_counts . ' / ' . $software_records_all;
            $percentage = $check_tracks_counts / $software_records_all * 100;
        } else {
            $done_counts = '未找到在列的盘点任务';
            $percentage = 0;
        }

        $html = <<<HTML
<div class="info-box" style="background:transparent;margin-bottom: 0;padding: 0;">
<!--  <span class="info-box-icon" style="background: rgba(70,160,153,1);color: white;border-radius: .25rem;"><i class="feather icon-crosshair"></i></span>-->
  <div class="info-box-content">
    <span class="info-box-text">软件盘点进度</span>
    <span class="info-box-number">{$done_counts}</span>
    <div class="progress">
      <div class="progress-bar bg-info" style="background: rgba(70,160,153,1);width: {$percentage}%"></div>
    </div>
    <span class="progress-description">
      {$percentage}%
    </span>
  </div>
</div>
HTML;

        $this->content = $this->lazyRenderable($html);
        $this->noPadding();

        return $this;
    }
}
