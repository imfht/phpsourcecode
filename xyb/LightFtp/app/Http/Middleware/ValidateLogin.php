<?php namespace App\Http\Middleware;

use Closure;
use League\Flysystem\Adapter\Ftp;

class ValidateLogin {

    protected $ftp;

    public function handle($request, Closure $next)
    {
        if(!\Session::get('serverIP') || !\Session::get('user') || !\Session::get('password'))
            return redirect('public/login');
        else
            return $next($request);
    }
}