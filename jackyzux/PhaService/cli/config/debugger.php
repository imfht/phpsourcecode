<?php

/**
 * Kint
 *
 * @param array ...$vars
 */
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
