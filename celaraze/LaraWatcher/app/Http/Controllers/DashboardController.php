<?php

namespace App\Http\Controllers;

use App\Models\Service;

class DashboardController extends Controller
{
    public function index()
    {
        $services = Service::all();
        dd($services);
        foreach ($services as $service) {

        }
    }
}
