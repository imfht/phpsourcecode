<?php

namespace Controller;


class Sync
{
        public function run()
        {
                echo 'sync start';
                swoole_timer_after(10000,function (){
                        echo 'sync over';
                });
                return 0;
        }
}