<?php
namespace ebcms;
\think\Loader::import('controller/Jump', TRAIT_PATH, EXT);
class Form
{
    use \traits\controller\Jump;

    public static function fetch($data = array(), $config = array())
    {
        $form = self::get($data, $config);
        if (is_string($form)) {
            throw new \Exception("发生错误：".$form, 1);
        }
        $view = new \think\View();
        $view->assign($form);
        $view->assign('data', $data);
        if ($form['form']['html']) {
            return $view -> display(htmlspecialchars_decode($form['form']['html']));
        }else{
            return $view->fetch('ebcms@common/form');
        }
    }

    // 返回表单
    public static function get($data = array(), $config = array())
    {

        $res = [];
        $config['formname'] = isset($config['formname']) ? $config['formname'] : request()->module() . '_' . request()->controller() . '_' . request()->action();
        $config['action'] = isset($config['action']) ? $config['action'] : request()->module() . '/' . request()->controller() . '/' . request()->action();
        $_where = [
            'name' => array('eq', strtolower($config['formname'])),
        ];

        $sorts = [];
        $groups = [];
        $groups_sort = [];
        if ($form = \think\Db::name('form')->where($_where)->find()) {
            $form['action'] = \think\Url::build(strtolower($config['action']));
            $form['unique'] = md5(uniqid().$form['id']);
            $_where = [
                'category_id' => array('eq', $form['id']),
            ];
            $_fields = \app\ebcms\model\Formfield::where($_where)->order('sort desc,id asc')->select();
            
            foreach ($_fields as $key => $obj) {
                // 字段配置
                $tmp = [];
                $tmp['id'] = $obj['id'];
                $tmp['config'] = array_merge(['disabled'=>0,'readonly'=>0],(Array)$obj['config']);
                $tmp['title'] = $obj['title'];
                $tmp['remark'] = $obj['remark'];
                $tmp['sort'] = $obj['sort'];
                $tmp['type'] = substr($obj['type'], 5);
                $tmp['unique'] = md5(uniqid().$obj['id']);
                // 字段名称
                if ($obj['subtable'] && $obj['extfield']) {
                    $tmp['field'] = $obj['subtable'] . '[' . $obj['extfield'] . ']' . '[' . $obj['name'] . ']';
                } elseif ($obj['extfield']) {
                    $tmp['field'] = $obj['extfield'] . '[' . $obj['name'] . ']';
                } elseif ($obj['subtable']) {
                    $tmp['field'] = $obj['subtable'] . '[' . $obj['name'] . ']';
                } else {
                    $tmp['field'] = $obj['name'];
                }
                // 字段值
                switch ($obj['defaultvaluetype']) {
                    case '0':
                        $tmp['value'] = $obj['defaultvalue'];
                        break;

                    case '1':
                        $tmp['value'] = input($obj['defaultvalue']);
                        break;

                    case '2':
                        $tmp['value'] = config($obj['defaultvalue']);
                        break;

                    case '3':
                        if ($obj['subtable'] && $obj['extfield']) {
                            $_value = $data[$obj['subtable']][$obj['extfield']];
                        } elseif ($obj['extfield']) {
                            $_value = $data[$obj['extfield']];
                        } elseif ($obj['subtable']) {
                            $_value = $data[$obj['subtable']];
                        } else {
                            $_value = $data;
                        }
                        $tmp['value'] = self::get_point_value($_value, $obj['defaultvalue']);
                        break;

                    default:
                        $tmp['value'] = $obj['defaultvalue'];
                        break;
                }
                $groups[$obj['group']][$tmp['field']] = $tmp;
                $groups_sort[$obj['group']] = isset($groups_sort[$obj['group']])?max($groups_sort[$obj['group']],$obj['sort']):$obj['sort'];
            }

            // 扩展字段
            if (isset($config['ext_id']) && $config['ext_id']) {
                $_where = array(
                    'category_id' => array('eq', $config['ext_id']),
                );
                if ($extfields = \app\ebcms\model\Extendfield::where($_where)->order('sort desc,id asc')->select()) {
                    foreach ($extfields as $key => $obj) {
                        // 字段配置
                        $tmp = [];
                        $tmp['id'] = $obj['id'];
                        $tmp['config'] = array_merge(['disabled'=>0,'readonly'=>0],(Array)$obj['config']);
                        $tmp['config'] = $obj['config'];
                        $tmp['title'] = $obj['title'];
                        $tmp['remark'] = $obj['remark'];
                        $tmp['sort'] = $obj['sort'];
                        $tmp['type'] = substr($obj['type'], 5);
                        $tmp['unique'] = md5(uniqid().$obj['id']);
                        // 字段名称
                        if (isset($config['ext_table']) && $config['ext_table']) {
                            $tmp['field'] = $config['ext_table'] . '[ext]' . '[' . $obj['name'] . ']';
                        } else {
                            $tmp['field'] = 'ext[' . $obj['name'] . ']';
                        }
                        // 字段值
                        $tmp['value'] = '';
                        if ($data) {
                            if (isset($config['ext_table']) && $config['ext_table']) {
                                $_value = $data[$config['ext_table']]['ext'];
                                $tmp['value'] = isset($_value[$obj['name']]) ? $_value[$obj['name']] : $obj['value'];
                            } elseif (isset($data['ext'])) {
                                $_value = $data['ext'];
                                $tmp['value'] = isset($_value[$obj['name']]) ? $_value[$obj['name']] : $obj['value'];
                            }
                        }
                        $groups[$obj['group']][$tmp['field']] = $tmp;
                        $groups_sort[$obj['group']] = isset($groups_sort[$obj['group']])?max($groups_sort[$obj['group']],$obj['sort']):$obj['sort'];
                    }
                }
            }
            // 排序
            array_multisort(array_values($groups_sort), SORT_DESC, $groups);
            foreach ($groups as $key => $value) {
                $groups[$key] = self::group_sort($value);
            }
            
            $res['form'] = $form;
            $res['groups'] = $groups;
            return $res;
        } else {
            return '表单配置错误！';
        }
    }

    private static function group_sort($data = [], $field='sort', $sort = SORT_DESC)
    {
        $flag=array();
        foreach($data as $v){
            $flag[]=$v[$field];
        }
        array_multisort($flag, $sort, $data);
        return $data;
    }

    private static function get_point_value($data = [], $str)
    {
        $pos = strpos($str, '.');
        if (false === $pos) {
            return isset($data[$str]) ? $data[$str] : null;
        } else {
            $key = mb_substr($str, 0, $pos);
            if (isset($data[$key])) {
                return self::get_point_value($data[$key], mb_substr($str, $pos + 1));
            } else {
                return null;
            }
        }
    }

}