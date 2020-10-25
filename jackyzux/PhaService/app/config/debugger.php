<?php

/**
 * Kint
 *
 * @param array ...$vars
 */
require BASE_PATH .'/vendor/kint-php/kint/build/kint.phar';

function dd(...$vars)
{
    Kint::dump(...$vars);
    exit;
}

Kint::$aliases[] = 'dd';
function sd(...$vars)
{
    s(...$vars);
    exit;
}

Kint::$aliases[] = 'sd';
