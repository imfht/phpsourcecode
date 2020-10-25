<?php declare (strict_types = 1);
namespace msqphp\test\main\template;

class TemplateTest extends \msqphp\test\Test
{
    public function testStart(): void
    {
        $this->class('\msqphp\main\template\Template')->method('commpileString');
        // 得到当前模版配置
        $template_config = \msqphp\core\config\Config::get('template');
        // 设置测试值
        \msqphp\core\config\Config::set('template', ['left_delimiter' => '<{', 'right_delimiter' => '}>']);
        // 测试
        $this->testThis();
        // 还原配置值
        \msqphp\core\config\Config::set('template', $template_config);
    }
    public function testParVar(): void
    {
        $content  = '<{a}>';
        $vars     = [];
        $language = [];
        $result   = '<?php echo (string) $a;?>';
        $this->args($content, $vars, $language)->result($result)->test();
        $vars = ['a' => ['cache' => false, 'value' => 'a']];
        $this->args($content, $vars, $language)->result($result)->test();
        $vars   = ['a' => ['cache' => true, 'value' => 'a']];
        $result = '<?php echo \'a\';?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParArr(): void
    {
        $content  = '<{a.nihao}>';
        $vars     = [];
        $language = [];
        $result   = '<?php echo (string) $a[\'nihao\'];?>';
        $this->args($content, $vars, $language)->result($result)->test();
        $vars = ['a' => ['cache' => false, 'value' => ['nihao' => 'nihao']]];
        $this->args($content, $vars, $language)->result($result)->test();
        $vars   = ['a' => ['cache' => true, 'value' => ['nihao' => 'nihao']]];
        $result = '<?php echo \'nihao\';?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParArr5(): void
    {
        $content  = '<{a.1.2.3.4.5.6.7.8}>';
        $vars     = ['a' => ['cache' => true, 'value' => [1 => [2 => [3 => [4 => [5 => [6 => [7 => [8 => 'nihao']]]]]]]]]];
        $language = [];
        $result   = '<?php echo \'nihao\';?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParFunc(): void
    {
        $content  = '<{substr(a, 0, 2)}>';
        $vars     = ['a' => ['cache' => true, 'value' => 'test']];
        $language = [];
        $result   = '<?php echo \'te\';?>';
        $this->args($content, $vars, $language)->result($result)->test();
        $vars   = ['a' => ['cache' => false, 'value' => 'test']];
        $result = '<?php echo (string) substr($a,0,2);?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParFunc2(): void
    {
        $content  = '<{php_sapi_name()}>';
        $vars     = ['a' => ['cache' => false, 'value' => 'test']];
        $language = [];
        $result   = '<?php echo (string) php_sapi_name();?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParFunc3(): void
    {
        $content  = '<{substr(a.name, 0, 2)}>';
        $vars     = ['a' => ['cache' => true, 'value' => ['name' => 'test']]];
        $language = [];
        $result   = '<?php echo \'te\';?>';

        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParForeach(): void
    {
        $content  = '<{foreach arr as v}><{v}><{endforeach}>';
        $vars     = ['arr' => ['cache' => true, 'value' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 0]]];
        $language = [];
        $result   = '<?php echo \'1\';?><?php echo \'2\';?><?php echo \'3\';?><?php echo \'4\';?><?php echo \'5\';?><?php echo \'6\';?><?php echo \'7\';?><?php echo \'8\';?><?php echo \'9\';?><?php echo \'0\';?>';
        $this->args($content, $vars, $language)
            ->result($result)
            ->test();
    }
    public function testParForeach1(): void
    {
        $content  = '<{foreach arr as k => v}><{k}><{endforeach}>';
        $vars     = ['arr' => ['cache' => true, 'value' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 0]]];
        $language = [];
        $result   = '<?php echo \'0\';?><?php echo \'1\';?><?php echo \'2\';?><?php echo \'3\';?><?php echo \'4\';?><?php echo \'5\';?><?php echo \'6\';?><?php echo \'7\';?><?php echo \'8\';?><?php echo \'9\';?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParForeach2(): void
    {
        $content = '<{foreach arr as key => value}><{key}>:<{foreach value as v}><{v}><{endforeach}><{endforeach}>';
        $vars    = [
            'arr' => [
                'cache' => true,
                'value' => [
                    'a' => ['A', 'B', 'C'],
                    'b' => ['D', 'E', 'F'],
                ],
            ],
        ];
        $language = [];
        $result   = '<?php echo \'a\';?>:<?php echo \'A\';?><?php echo \'B\';?><?php echo \'C\';?><?php echo \'b\';?>:<?php echo \'D\';?><?php echo \'E\';?><?php echo \'F\';?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParForeach3(): void
    {
        $content = '<{foreach arr.list as key => value}><{key}>:<{foreach value as v}><{v}><{endforeach}><{endforeach}>';
        $vars    = [
            'arr' => [
                'cache' => true,
                'value' => [
                    'list' => [
                        'a' => ['A', 'B', 'C'],
                        'b' => ['D', 'E', 'F'],
                    ],
                ],
            ],
        ];
        $language = [];
        $result   = '<?php echo \'a\';?>:<?php echo \'A\';?><?php echo \'B\';?><?php echo \'C\';?><?php echo \'b\';?>:<?php echo \'D\';?><?php echo \'E\';?><?php echo \'F\';?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testParForeach4(): void
    {
        $content = '<{foreach arr.list as key => value}><{key}>:<{foreach value as v}><{v}><{endforeach}><{endforeach}>';
        $vars    = [
            'arr' => [
                'cache' => true,
                'value' => [
                    'list' => [
                        'a' => ['A', 'B', 'C'],
                        'b' => ['D', 'E', 'F'],
                    ],
                ],
            ],
        ];
        $language = [];
        $result   = '<?php echo \'a\';?>:<?php echo \'A\';?><?php echo \'B\';?><?php echo \'C\';?><?php echo \'b\';?>:<?php echo \'D\';?><?php echo \'E\';?><?php echo \'F\';?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testIf(): void
    {
        $content  = '<{if a === \'a\'}><{a}><{endif}>';
        $vars     = ['a' => ['value' => 'a', 'cache' => true]];
        $language = [];
        $result   = '<?php echo \'a\';?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testIf2(): void
    {
        $content  = '<{if a === \'a\'}>nihao<{endif}>';
        $vars     = ['a' => ['value' => 'a', 'cache' => true]];
        $language = [];
        $result   = 'nihao';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testIf3(): void
    {
        $content  = '<{if a === \'a\'}><{a}><{endif}>';
        $vars     = ['a' => ['value' => 'a', 'cache' => false]];
        $language = [];
        $result   = '<?php if($a===\'a\') : ?><?php echo (string) $a;?><?php endif;?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
    public function testIf4(): void
    {
        $content  = '<{if isset(a)}><{a}><{endif}>';
        $vars     = ['a' => ['value' => 'a', 'cache' => false]];
        $language = [];
        $result   = '<?php if(isset($a)) : ?><?php echo (string) $a;?><?php endif;?>';
        $this->args($content, $vars, $language)->result($result)->test();
    }
}
