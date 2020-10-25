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
                $i = 0;
                while (true) {
                        echo $i++;
                        sleep(1);
                }
        }

        public function after()
        {
                echo 'this process after';
        }
}