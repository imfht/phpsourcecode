<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/5/29
 * Time: 上午11:05
 */


use Inhere\Queue\QueueInterface;

require dirname(__DIR__) . '/../../autoload.php';

$q = \Inhere\Queue\Queue::make([
    'driver' => 'sysv', // shm sysv php
    'id' => 12,
    'serializer' => 'serialize',
    'deserializer' => 'unserialize',
]);
//var_dump($q);

echo "driver is: {$q->getDriver()}\n\n";

$r[] = $q->push('n1');
$r[] = $q->push('n2');
$r[] = $q->push(['n3-array-value']);
$r[] = $q->push('h1', QueueInterface::PRIORITY_HIGH);
$r[] = $q->push('l1', QueueInterface::PRIORITY_LOW);
$r[] = $q->push('n4');
//var_dump($r);

$i = 6;

while ($i--) {
    var_dump($q->pop());
    usleep(50000);
}
