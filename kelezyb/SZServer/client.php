<?php
define('SEND_TIMEOUT', 1);
define('REQUEST_SIZE', 10000);

$start = microtime(true);
for ($i = 0; $i < REQUEST_SIZE; $i++) {
    $host = '127.0.0.1';
    $port = 9502;
    $errno = 0;
    $errmsg = '';
    $handle = pfsockopen($host, $port, $errno, $errmsg);

    if ($handle) {
        stream_set_blocking($handle, 0); //0非阻塞模式
        stream_set_timeout($handle, SEND_TIMEOUT);
    }

    $data = gzcompress(json_encode(array(
        'C' => 'Index.Index',
        'ID' => time(),
        'D' => array(
            'a' => 10,
            'b' => 20
        )
    )));

    $len = strlen($data);
//    print $len;
    $buffer = '';
    $buffer .= pack('N', $len);
    $buffer .= $data;
    fwrite($handle, $buffer, strlen($buffer));
    fclose($handle);
//    echo "Send is success.\n";
}

$end = microtime(true);
echo sprintf("%0.4f ms\n", ($end - $start) * 1000 / REQUEST_SIZE);