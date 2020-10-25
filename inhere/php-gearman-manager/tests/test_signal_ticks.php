<?php
declare(ticks=1);

echo "USE: declare(ticks=1) \n";
echo "安装信号处理器...\n";

pcntl_signal(SIGHUP,  function($signo) {
     echo "信号处理器被调用\n";
});

echo "为自己生成SIGHUP信号...\n";
posix_kill(posix_getpid(), SIGHUP);

// pcntl_signal_dispatch();
register_tick_function(function(){
    echo "分发...\n";
}, true);

echo "完成\n";
