<?php

function assertCondition($condition, $message = "") {
    if (!$condition) {
        $prefix = '';
        foreach (debug_backtrace() as $caller) {
            if ($caller['file'] !== __FILE__) {
                $prefix = "{$caller['file']}({$caller['line']}): ";
                break;
            }
        }
        echo "{$prefix}{$message}\n";
        exit();
    }
}

function assertEquals($expect, $actual) {
    assertCondition($expect === $actual, "Expect {$expect}, but actual is {$actual}");
}

function assertNotNull($actual) {
    assertCondition(null !== $actual, "Expect not null, but actual is null");
}
