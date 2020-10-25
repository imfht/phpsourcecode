<?php

/**
 * Litwit
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\View\Compiler;

class Litwit
{
    /**
     * 开始标签
     */
    public $startTag = "{{";

    /**
     * 结束标签
     */
    public $endTag = "}}";

    public function __construct()
    {
        
    }

    /**
     * 解析模板
     * @param $str	模板内容
     * @return $str
     */
    public function parse($str)
    {
        $str = $this->parseIf($str);
        $str = $this->parseElseif($str);
        $str = $this->parseElse($str);
        $str = $this->parseEndif($str);
        $str = $this->parseForeach($str);
        $str = $this->parseEndforeach($str);
        $str = $this->parseFor($str);
        $str = $this->parseEndfor($str);
        // 解析php 代码
        $str = $this->parsePhp($str);
        // 解析输出
        $str = $this->parseEchos($str);
        return $str;
    }

    /**
     * 解析输出
     * @param $str	模板内容
     * @return $str
     */
    public function parseEchos($str)
    {
        return $this->parseRegularEchos($str);
    }

    protected function parseRegularEchos($str)
    {
        $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s/s', $this->startTag, $this->endTag);
        $callback = function($matches)
        {
            return $matches[1] ? substr($matches[0], 1) : '<?php echo '.$this->parseEchoDefaults($matches[2]).'; ?>';
        };

        return preg_replace_callback($pattern, $callback, $str);
    }

    public function parseEchoDefaults($str)
    {
        return preg_replace('/^(?=\$)(.+?)(?:\s+or\s+)(.+?)$/s', 'isset($1) ? $1 : $2', $str);
    }

    /**
     * 解析php 代码
     * @param $str	模板内容
     * @return $str
     */
    public function parsePhp($str)
    {
        return preg_replace("/{{{([\s\S]*)}}}/U", "<?php \\1 ?>", $str);
    }

    /**
     * 解析if开始标签
     * @param $str	模板内容
     * @return $str
     */
    public function parseIf($str)
    {
        return preg_replace("/{{\s*if\s+(.+?)\s*}}/", "<?php if (\\1): ?>", $str);
    }

    /**
     * 解析if结束标签
     * @param $str	模板内容
     * @return $str
     */
    public function parseEndif($str)
    {
        return preg_replace("/{{\s*\/if\s*}}/", "<?php endif; ?>", $str);
    }

    /**
     * 解析else标签
     * @param $str	模板内容
     * @return $str
     */
    public function parseElse($str)
    {
        return preg_replace("/{{\s*else\s*}}/", "<?php else: ?>", $str);
    }

    /**
     * 解析elseif标签
     * @param $str	模板内容
     * @return $str
     */
    public function parseElseif($str)
    {
        return preg_replace("/{{\s*elseif\s+(.+?)\s*}}/", "<?php elseif (\\1): ?>", $str);
    }

    /**
     * 解析for 循环开始
     * @param $str	模板内容
     * @return $str
     */
    public function parseFor($str)
    {
        return preg_replace("/{{\s*for\s+(.+?)\s*}}/", "<?php for (\\1): ?>", $str);
    }

    /**
     * 解析for 循环结束
     * @param $str	模板内容
     * @return $str
     */
    public function parseEndfor($str)
    {
        return preg_replace("/{{\s*\/for\s*}}/", "<?php endfor; ?>", $str);
    }

    /**
     * 解析foreach 循环开始
     * @param $str	模板内容
     * @return $str
     */
    public function parseForeach($str)
    {
        return preg_replace("/{{\s*loop(.+?)\s*}}/", "<?php foreach \\1: ?>", $str);
    }

    /**
     * 解析foreach 循环结束
     * @param $str	模板内容
     * @return $str
     */
    public function parseEndforeach($str)
    {
        return preg_replace("/{{\s*\/loop\s*}}/", "<?php endforeach; ?>", $str);
    }

}
