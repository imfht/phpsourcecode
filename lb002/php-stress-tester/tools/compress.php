#! /usr/bin/env php
<?php

$phar = new Phar('pst.phar', 0, 'pst.phar');
$phar->buildFromDirectory(__DIR__ . '/../src');
$phar->setStub('#! /usr/bin/env php' . PHP_EOL . Phar::createDefaultStub('start.php', 'start.php'));
$phar->compressFiles(Phar::GZ);
if (!is_dir(__DIR__ . '/../bin')) {
    mkdir(__DIR__ . '/../bin', 0777, true);
}
shell_exec('mv ' . __DIR__ . '/pst.phar ' . __DIR__ . '/../bin/pst.phar');
