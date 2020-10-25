<?php

namespace fluiex;

class Logger
{

    public static function write($file, $log)
    {
        global $_G;
        
        $yearmonth = dgmdate(TIMESTAMP, 'Ym', $_G['setting']['timeoffset']);
        $logdir = DISCUZ_ROOT . './data/log/';
        $logfile = $logdir . $yearmonth . '_' . $file . '.php';
        if (@filesize($logfile) > 2048000) {
            $dir = opendir($logdir);
            $length = strlen($file);
            $maxid = $id = 0;
            while ($entry = readdir($dir)) {
                if (strpos($entry, $yearmonth . '_' . $file) !== false) {
                    $id = intval(substr($entry, $length + 8, -4));
                    $id > $maxid && $maxid = $id;
                }
            }
            closedir($dir);

            $logfilebak = $logdir . $yearmonth . '_' . $file . '_' . ($maxid + 1) . '.php';
            @rename($logfile, $logfilebak);
        }
        if ($fp = @fopen($logfile, 'a')) {
            @flock($fp, 2);
            if (!is_array($log)) {
                $log = array($log);
            }
            foreach ($log as $tmp) {
                fwrite($fp, "<?PHP exit;?>\t" . str_replace(array('<?', '?>'), '', $tmp) . "\n");
            }
            fclose($fp);
        }
    }

}
