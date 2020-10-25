<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\console\output\table;

class Cell {

    private $value;

    private $options = [
        'rowspan' => 1,
        'colspan' => 1,
    ];

    /**
     * @param string $value
     * @param array $options
     */
    public function __construct($value = '', array $options = []) {
        if (is_numeric($value) && !is_string($value)) {
            $value = (string)$value;
        }

        $this->value = $value;

        // check option names
        if ($diff = array_diff(array_keys($options), array_keys($this->options))) {
            throw new \InvalidArgumentException(sprintf('The Cell does not support the following options: \'%s\'.', implode('\', \'', $diff)));
        }

        $this->options = array_merge($this->options, $options);
    }

    /**
     * Returns the cell value.
     *
     * @return string
     */
    public function __toString() {
        return $this->value;
    }

    /**
     * Gets number of colspan.
     *
     * @return int
     */
    public function getColspan() {
        return (int)$this->options['colspan'];
    }

    /**
     * Gets number of rowspan.
     *
     * @return int
     */
    public function getRowspan() {
        return (int)$this->options['rowspan'];
    }
}
