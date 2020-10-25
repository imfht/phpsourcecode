<?php declare (strict_types = 1);
namespace msqphp\main\template;

final class TemplateStringIterator implements TemplateIterator
{
    private $content = '';
    private $count   = 0;
    private $current_content;
    private $left_pos = -1;

    private $left_delimiter  = '';
    private $right_delimiter = '';

    public function __construct(string $content)
    {
        $this->content                                  = $content;
        [$this->left_delimiter, $this->right_delimiter] = Template::getDelimiter();
    }

    private function exception(string $message): void
    {
        throw new TemplateException($message);
    }

    public function current()
    {
        return $this->current_content;
    }
    public function key()
    {
        return $count;
    }
    public function next()
    {
        $this->count++;

        // 左定界符
        $left_delimiter = $this->left_delimiter;
        // 右定界符
        $right_delimiter = $this->right_delimiter;
        // 右定界符长度
        $right_delimiter_len = strlen($right_delimiter);
        $left_pos            = $this->left_pos;
        if (false === $left_pos) {
            $this->current_content = $this->content;
            $this->content         = '';
        } else {
            // 如果左定界符不在数据最前面,则将其前所有数据直接添加到结果数组中
            if ($left_pos !== 0) {
                $this->current_content = substr($this->content, 0, $left_pos);
                $this->content         = substr($this->content, $left_pos);
                return;
            }
            // 获取右定界符位置
            $regith_pos = strpos($this->content, $right_delimiter);
            // 如果不存在,即未闭合
            $regith_pos === false && static::exception('定界符未闭合');

            // 加上右定界符长度
            $regith_pos += $right_delimiter_len;

            // 下一个左定界符位置
            $next_left_pos = strpos($this->content, $left_delimiter, 1);
            // 不存在取php最大值
            $next_left_pos === false && $next_left_pos = PHP_INT_MAX;

            /**
             * 右定界符大于下一个左定界符位置,即与下一个左定界符形成一个标签
             * <{ if $a = <{$a}> }>
             * ---->   <{ if $a = 解析后 }>
             * ---->   解析
             */
            while ($regith_pos > $next_left_pos) {
                // 取中间内容,并模版编译
                $middle        = substr($this->content, $next_left_pos, $regith_pos);
                $this->content = str_replace($middle, Template::commpileString($middle, $data, $language), $this->content);
                // 重新获取右定界符位置
                $regith_pos = strpos($this->content, $right_delimiter);
                // 如果不存在,即未闭合
                $regith_pos === false && $this->exception('定界符未闭合');
                // 加上右定界符长度
                $regith_pos += $right_delimiter_len;
                // 下一个左定界符位置
                $next_left_pos = strpos($this->content, $left_delimiter, 1);
                // 不存在取php最大值
                $next_left_pos === false && $next_left_pos = PHP_INT_MAX;
            }
            $this->current_content = substr($this->content, 0, $regith_pos);
            // 赋值给结果
            $this->content = substr($this->content, $regith_pos);
        }

    }
    public function rewind()
    {
        $this->exception('暂不支持');
    }
    public function valid()
    {
        // 当有下一左定界符时
        return false !== ($this->left_pos = strpos($this->content, $this->left_delimiter)) || !empty($this->content);
    }
}
