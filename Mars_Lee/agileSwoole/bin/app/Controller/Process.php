<?php

namespace Controller;


class Process
{
        public function before()
        {
                echo 'this process before';
        }

        public function run()
        {
//                swoole_timer_after(10000,function (){
//                        $file = rand(0,99999);
//                        file_put_contents($file.'.txt','sync !!!'.PHP_EOL, FILE_APPEND);
//                });
//
//                swoole_timer_tick(10000,function (){
//                        $file = rand(0,99999);
//                        file_put_contents($file.'.txt','sync !!!'.PHP_EOL, FILE_APPEND);
//                });
	
                $file = rand(0,99999);
                while (true) {
                        file_put_contents($file.'.txt','sync !!!'.PHP_EOL, FILE_APPEND);
                }
        }

        public function after()
        {
                echo 'this process after';
        }
}
