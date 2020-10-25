<?php
namespace Core\Assets;

class Manager
{
    protected $_source = array();

    public function add($type, $name, $content, $collection = null, $inline = false)
    {
        if ($type !== 'js' && $type !== 'css') {
            return $this;
        }
        if (empty($content)) {
            return $this;
        }
        $source = array(
            'type' => $type,
            'isInline' => $inline,
            'isOutput' => false,
            'content' => $content,
            'collection' => $collection,
        );
        $this->_source[$name . '_' . $type] = $source;
        return $this;
    }

    public function addJs($name, $content, $collection = null)
    {
        $this->add('js', $name, $content, $collection);
        return $this;
    }

    public function addCss($name, $content, $collection = null)
    {
        $this->add('css', $name, $content, $collection);
        return $this;
    }

    public function addInlineCss($name, $content, $collection = null)
    {
        $this->add('css', $name, $content, $collection, true);
        return $this;
    }

    public function addInlineJs($name, $content, $collection = null)
    {
        $this->add('js', $name, $content, $collection, true);
        return $this;
    }

    public function remove($name, $type)
    {
        unset($this->_source[$name . '_' . $type]);
        return $this;
    }

    public function removeJs($name)
    {
        unset($this->_source[$name . '_js']);
        return $this;
    }

    public function removeCss($name)
    {
        unset($this->_source[$name . '_css']);
        return $this;
    }

    public function output($type = null, $isInline = null, $collection = null)
    {
        $output = '';
        $outputJs = '';
        $outputCss = '';
        foreach ($this->_source as $name => $source) {
            if (!is_null($collection)) {
                if ($source['collection'] != $collection) {
                    break;
                }
            }
            if (!is_null($isInline)) {
                if ($source['isInline'] != $isInline) {
                    break;
                }
            }
            if (!is_null($type)) {
                if ($source['type'] != $type) {
                    break;
                }
            }
            if ($this->_source[$name]['isOutput'] === false) {
                if ($source['isInline'] === true) {
                    if ($source['type'] == 'js') {
                        $outputJs .= $source['content'];
                    } else {
                        $outputCss .= $source['content'];
                    }
                } else {
                    $attributes = '';
                    if (isset($source['attributes'])) {
                        $attributes = renderAttributes($source['attributes']);
                    }
                    if ($source['type'] == 'js') {
                        $output .= '<script ' . $attributes . ' src="' . $source['content'] . '"></script>';
                    } else {
                        $output .= '<link ' . $attributes . ' rel="stylesheet" type="text/css" href="' . $source['content'] . '">';
                    }
                }
                $this->_source[$name]['isOutput'] = true;
            }
        }
        if (!empty($outputJs)) {
            $output .= '<script type="text/javascript">' . $outputJs . '</script>';
        }
        if (!empty($outputCss)) {
            $output .= '<link rel="stylesheet" href="' . $outputCss . '">';
        }
        return $output;
    }

    public function outputJs($collection = null)
    {
        return $this->output('js', null, $collection);
    }

    public function outputCss($collection = null)
    {
        return $this->output('css', null, $collection);
    }

    public function outputInlineJs($collection = null)
    {
        return $this->output('js', true, $collection);
    }

    public function outputInlineCss($collection = null)
    {
        return $this->output('css', true, $collection);
    }
}
