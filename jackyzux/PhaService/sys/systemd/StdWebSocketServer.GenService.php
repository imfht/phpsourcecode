<?php
$serviceTpl = <<<EOT
[Unit]
Description=PhaService Standard Web Socket Server
After=network.target syslog.target

[Service]
Type=forking
PIDFile={{PID_FILE}}
ExecStart={{SERVICE_CONTROL_SCRIPT}} start
ExecStop={{SERVICE_CONTROL_SCRIPT}} stop
ExecReload={{SERVICE_CONTROL_SCRIPT}} reload
Restart=always

[Install]
WantedBy=multi-user.target
EOT;

define('BASE_PATH', dirname(dirname(__DIR__)));

$serviceFileName      = 'PS_StdWebSocketServer.service';
$serviceFile          = BASE_PATH . '/var/tmp/' . $serviceFileName;
$servicePidFile       = BASE_PATH . '/var/pid/std_web_socket_server.pid';
$serviceControlScript = BASE_PATH . '/web_socket_serve';

$ret = str_replace(
    ['{{PID_FILE}}', '{{SERVICE_CONTROL_SCRIPT}}',],
    [$servicePidFile, $serviceControlScript,],
    $serviceTpl);

file_put_contents($serviceFile, $ret);

function consoleColor($msg)
{
    $RED_COLOR    = "\033[1;31m"; //红
    $GREEN_COLOR  = "\033[1;32m"; //绿
    $YELLOW_COLOR = "\033[33m"; //黄
    $BLUE_COLOR   = "\033[1;34m"; //蓝
    $PINK         = "\033[1;35m"; //粉红
    $COLOR_END    = "\033[0m"; //结束

    return str_replace(
        ['{RED}', '{GREEN}', '{YELLOW}', '{BLUE}', '{PINK}', '{END}',],
        [$RED_COLOR, $GREEN_COLOR, $YELLOW_COLOR, $BLUE_COLOR, $PINK, $COLOR_END,],
        $msg);
}


$msg = <<<EOT
┌──────────────────────────────────────────────────────────────────────────────┐
│                              {BLUE}CONGRATULATIONS!{END}                                │
│                  {BLUE}THE SYSTEMCTL SERVICE FILE GENERATED!{END}                       │ 
├──────────────────────────────────────────────────────────────────────────────┘
│ THE SERVICE DETAIL PLEASE SEE: 
│   {RED}{$serviceFile}{END}
│
├──────────────────────┐
│ {PINK}SERVICE INSTALLATION{END} │
├──────────────────────┘
│ {GREEN}sudo cp {$serviceFile} /lib/systemd/system/{END}
│ {GREEN}sudo systemctl daemon-reload{END}
│ 
├────────────────────┐
│ {PINK}SERVICE MANAGEMENT{END} │
├────────────────────┘
{YELLOW}├─1. ENABLE SERVICE ON BOOT AUTO START{END} 
│ {GREEN}sudo systemctl enable {$serviceFileName}{END}
│ 
{YELLOW}├─2. DISABLE SERVICE ON BOOT START{END}
│ {GREEN}sudo systemctl disable {$serviceFileName}{END}
│ 
{YELLOW}├─3. SERVICE START{END}
│ {GREEN}sudo systemctl start {$serviceFileName}{END}
│ 
{YELLOW}├─4. SERVICE STOP{END}
│ {GREEN}sudo systemctl stop {$serviceFileName}{END}
│ 
{YELLOW}├─5. SERVICE RESTART{END}
│ {GREEN}sudo systemctl restart {$serviceFileName}{END}
│ 
{YELLOW}├─6. SERVICE RELOAD{END}
│ {GREEN}sudo systemctl reload {$serviceFileName}{END}
│ 
{YELLOW}├─7. SERVICE CURRENT STATUS{END}
│ {GREEN}sudo systemctl status {$serviceFileName}{END}
│ 
{YELLOW}├─8. CHECK ON BOOT AUTO STARTED SERVICES{END} 
│ {GREEN}sudo systemctl is-enabled {$serviceFileName}{END}
│ 
{YELLOW}├─9. ENABLED SERVICES LIST{END}
│ {GREEN}sudo systemctl list-unit-files|grep enabled{END}
│ 
{YELLOW}├─10. CHECK START FAILED SERVICES{END}
│ {GREEN}sudo systemctl --failed{END}
└───────────────────────────────────────────────────────────────────────────────

EOT;


echo consoleColor($msg);
