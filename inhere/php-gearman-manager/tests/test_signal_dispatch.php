<?php

echo "USE: pcntl_signal_dispatch()\n";
echo "安装信号处理器...\n";

pcntl_signal(SIGHUP,  function($signo) {
     echo "信号处理器被调用\n";
});

echo "为自己生成SIGHUP信号...\n";
posix_kill(posix_getpid(), SIGHUP);

echo "分发...\n";
pcntl_signal_dispatch();

echo "完成\n";
