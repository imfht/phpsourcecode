<?php

namespace App\Providers;

use App\Models\Advertisement;
use App\Models\AdvertisementPosition;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\IpFilter;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SystemConfig;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
        Route::bind('permission', function ($value) {
            return Permission::where('id', $value)->first();
        });
        Route::bind('role', function ($value) {
            return Role::where('id', $value)->first();
        });
        Route::bind('user', function ($value) {
            return User::where('id', $value)->first();
        });
        Route::bind('advertisement_position', function ($value) {
            return AdvertisementPosition::where('id', $value)->first();
        });
        Route::bind('advertisement', function ($value) {
            return Advertisement::where('id', $value)->first();
        });
        Route::bind('category', function ($value) {
            return Category::where('id', $value)->first();
        });
        Route::bind('tag', function ($value) {
            return Tag::where('id', $value)->first();
        });
        Route::bind('article', function ($value) {
            return Article::where('id', $value)->first();
        });

        Route::bind('attachment', function ($value) {
            return Attachment::where('id', $value)->first();
        });

        Route::bind('system_config', function ($value) {
            return SystemConfig::where('id', $value)->first();
        });

        Route::bind('ip_filter', function ($value) {
            return IpFilter::where('id', $value)->first();
        });
        Route::bind('ip_filter', function ($value) {
            return IpFilter::where('id', $value)->first();
        });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
