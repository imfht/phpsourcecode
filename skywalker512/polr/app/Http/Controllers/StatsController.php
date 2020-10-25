<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Redirect;
use Carbon\Carbon;

use App\Models\Link;
use App\Models\Clicks;
use App\Helpers\StatsHelper;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller {
    const DAYS_TO_FETCH = 30;

    public function displayStats(Request $request, $short_url) {
        $validator = \Validator::make($request->all(), [
            'left_bound' => 'date',
            'right_bound' => 'date'
        ]);

        if ($validator->fails() && !session('error')) {
            // Do not flash error if there is already an error flashed
            return redirect()->back()->with('error', 'Invalid date bounds.');
        }

        $user_left_bound = $request->input('left_bound');
        $user_right_bound = $request->input('right_bound');

        // Carbon bounds for StatHelper
        $left_bound = $user_left_bound ?: Carbon::now()->subDays(self::DAYS_TO_FETCH);
        $right_bound = $user_right_bound ?: Carbon::now();

        if (Carbon::parse($right_bound)->gt(Carbon::now()) && !session('error')) {
            // Right bound must not be greater than current time
            // i.e cannot be in the future
            return redirect()->back()->with('error', 'Right date bound cannot be in the future.');
        }

        if (!$this->isLoggedIn()) {
            return redirect(route('login'))->with('error', '请登录查看统计信息。');
        }

        $link = Link::where('short_url', $short_url)
            ->first();

        // Return 404 if link not found
        if ($link == null) {
            return redirect(route('admin'))->with('error', '链接不存在。');
        }
        if (!env('SETTING_ADV_ANALYTICS')) {
            return redirect(route('login'))->with('error', '功能未开启。');
        }

        $link_id = $link->id;

        if ( (session('username') != $link->creator) && !self::currIsAdmin() ) {
            return redirect(route('admin'))->with('error', '无权查看。');
        }

        try {
            // Initialize StatHelper
            $stats = new StatsHelper($link_id, $left_bound, $right_bound);
        }
        catch (\Exception $e) {
            if (!session('error')) {
                // Do not flash error if there is already an error flashed
                return redirect()->back()->with('error', 'Invalid date bounds.
                    The right date bound must be more recent than the left bound.');
            }
        }

        $day_stats = $stats->getDayStats();
        $country_stats = $stats->getCountryStats();
        $referer_stats = $stats->getRefererStats();

        return view('link_stats', [
            'link' => $link,
            'day_stats' => $day_stats,
            'country_stats' => $country_stats,
            'referer_stats' => $referer_stats,

            'left_bound' => ($user_left_bound ?: $left_bound->toDateTimeString()),
            'right_bound' => ($user_right_bound ?: $right_bound->toDateTimeString()),

            'no_div_padding' => true
        ]);
    }
}
