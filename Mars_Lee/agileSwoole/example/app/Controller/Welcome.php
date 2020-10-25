<?php

namespace Controller;


class Welcome
{
        public function index()
        {
                return [
                        'code'  =>      0,
                        'view'  =>      realpath(__DIR__.'/../View/index.php')
                ];
        }
}