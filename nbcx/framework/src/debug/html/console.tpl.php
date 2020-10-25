<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
if(!empty($record['log'])) {
    foreach ($record['log'] as $log ){
        \nb\Debug::optimize($log['k']);
        echo '=>';
        \nb\Debug::optimize($log['v']);
        echo "\n";
    }
}
$output->info('message');

if(!empty($record['e'])) {
    foreach ($record['e'] as $k => $v ){
        echo '['.$v['type'].']'.$v['message'].'('.$v['file'].':'.$v['line'].')'."\n";
    }
}

if(!empty($val['sql'])){

}