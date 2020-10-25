<?php

namespace App\Admin\Controllers;

use App\Admin\Metrics\Examples;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceTrack;
use Dcat\Admin\Controllers\Dashboard;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('Dashboard')
            ->description('Description...')
            ->body(function (Row $row) {
                $row->column(6, function (Column $column) {
                    $column->row(Dashboard::title());
                    $column->row(new Examples\Tickets());
                });

                $row->column(6, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(6, new Examples\NewUsers());
                        $row->column(6, new Examples\NewDevices());
                    });

                    $column->row(new Examples\Sessions());
                    $column->row(new Examples\ProductOrders());
                });
            });
    }

    public function dashboard()
    {
        $services = Service::all();
        foreach ($services as $service) {
            $service->server;
            $service->status = 0;
            $service->issue = '';
            $service->recovery = '';
            $service_track = ServiceTrack::where('service_id', $service->id)
                ->first();
            if (!empty($service_track)) {
                $service->status = 1;
                $service->issue = $service_track->description;
                $service->recovery = $service_track->recovery;
                if ($service_track->status == 2) {
                    $service->status = 2;
                    $service->recovery = date('Y-m-d H:i:s', strtotime($service->updated_at));
                }
            }
        }
        $services = json_decode($services, true);
        return view('dashboard')->with('services', $services);
    }
}
