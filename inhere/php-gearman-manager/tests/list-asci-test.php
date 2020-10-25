<?php
function listChr()
{
    for ($i = 0; $i < 256; ++$i) {
        echo "chr($i) will output: " . chr($i) . PHP_EOL;
    }
}

listChr();
