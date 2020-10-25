<?php

/**
 * Helper
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Html;

class Helper
{
    /**
     * Generate a link to a JavaScript file.
     *
     * @param  string $url
     * @param  array $attributes
     * @return string
     */
    public function script($url, $attributes = array())
    {
        $attributes['src'] = $url;
        return '<script' . $this->attributes($attributes) . '></script>' . PHP_EOL;
    }

    /**
     * Generate a link to a CSS file.
     *
     * @param  string $url
     * @param  array $attributes
     * @return string
     */
    public function style($url, $attributes = array())
    {
        $defaults = array('media' => 'all', 'type' => 'text/css', 'rel' => 'stylesheet');

        $attributes = $attributes + $defaults;

        $attributes['href'] = $url;

        return '<link' . $this->attributes($attributes) . '>' . PHP_EOL;
    }

    /**
     * Generate an HTML image element.
     *
     * @param  string $url
     * @param  string $alt
     * @param  array $attributes
     * @param  bool $secure
     * @return string
     */
    public function image($url, $alt = null, $attributes = array())
    {
        $attributes['alt'] = $alt;
        return '<img src="' . $url . '"' . $this->attributes($attributes) . '>';
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param  array $attributes
     * @return string
     */
    public function attributes($attributes)
    {
        $html = array();

        // For numeric keys we will assume that the key and the value are the same
        // as this will convert HTML attributes such as "required" to a correct
        // form like required="required" instead of using incorrect numerics.
        foreach ((array)$attributes as $key => $value) {
            $element = $this->attributeElement($key, $value);
            if (!is_null($element)) $html[] = $element;
        }

        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }

    /**
     * Build a single attribute element.
     *
     * @param  string $key
     * @param  string $value
     * @return string
     */
    protected function attributeElement($key, $value)
    {
        if (is_numeric($key)) $key = $value;

        if (!is_null($value)) return $key . '="' . e($value) . '"';
    }
}
