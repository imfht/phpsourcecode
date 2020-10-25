<?php
namespace app\common\helper;

class Form
{

    ##表单元素默认设置
    protected $inputDefaults = array('label' => false, 'token' => true);
    protected $requestObj;
    protected $request = array();

    public $data = array();

    public function __construct()
    {
        $this->requestObj = request();
        $this->request = $this->requestObj->post() ? $this->requestObj->post() : $this->requestObj->get();
        $this->data = isset($this->request['data']) ? $this->request['data'] : [];
    }


    public function create($options = array('type' => 'post', 'mdl' => null, 'url' => null, 'inputDefaults' => array('label' => false, 'token' => true)))
    {
        if ($options['inputDefaults'] && is_array($options['inputDefaults'])) $this->inputDefaults = $options['inputDefaults'];
        unset($options['inputDefaults']);
        $type = $options['type'] ? strtolower($options['type']) : 'post';
        switch ($type) {
            case 'post':
                // no break
            case 'get':
                $method = $type;
                break;
            case 'file':
                $method = 'post';
                $enctype = 'enctype="multipart/form-data"';
                break;
            default:
                $method = 'post';
        }
        unset($options['type']);

        if ($options['url'] && is_string($options['url'])) {
            $url = trim($options['url']);
        } else {
            $url = $this->requestObj->url();
        }
        unset($options['url']);

        if ($options['id'] && is_string($options['id'])) {
            $id = trim($options['id']);
        } else {
            $id = $options['mdl'] ? $options['mdl'] : $this->requestObj->controller() . 'Form';
        }
        unset($options['id']);
        unset($options['mdl']);

        $else = '';
        if (!empty($options)) {
            foreach ($options as $attr => $attr_value) {
                $else .= ' ' . $attr . '="' . strval($attr_value) . '"';
            }
        }
        if ($this->inputDefaults['token']) {
            $token = session('__token__');
            if (isset($token['code']) && $token['created'] >= time() - 3600) {
                $code = $token['code'];
            } else {
                $code = md5(mt_rand(1, 100000));
                session('__token__', array('code' => $code, 'created' => time()));
            }
            $tokenstr = "\r\n" . '<input type="hidden" name="__token__" value="' . $code . '"/>';
        }

        return sprintf('<form method="%s" action="%s"  id="%s" %s %s>%s', $method, $url, $id, $enctype, $else, $tokenstr);
    }

    public function check_token()
    {
        $flag = false;
        if ($this->request['__token__'] == session('__token__.code')) $flag = true;
        unset($this->request['__token__']);
        session('__token__', null);
        return $flag;
    }

    public function end()
    {
        return '</form>';
    }

    public function text($name, $infos = array())
    {
        return $this->simple('text', $name, $infos);
    }

    public function password($name, $infos = array())
    {
        return $this->simple('password', $name, $infos);
    }

    public function number($name, $infos = array())
    {
        return $this->simple('number', $name, $infos);
    }

    public function email($name, $infos = array())
    {
        return $this->simple('email', $name, $infos);
    }

    public function mobile($name, $infos = array())
    {
        return $this->simple('mobile', $name, $infos);
    }

    public function url($name, $infos = array())
    {
        return $this->simple('url', $name, $infos);
    }

    public function range($name, $infos = array())
    {
        return $this->simple('range', $name, $infos);
    }

    public function date($name, $infos = array())
    {
        return $this->simple('date', $name, $infos);
    }

    public function color($name, $infos = array())
    {
        return $this->simple('color', $name, $infos);
    }

    public function hidden($name, $infos = array())
    {
        return $this->simple('hidden', $name, $infos);
    }

    public function file($name, $infos = array())
    {
        $info = $this->getInfo($name, $infos);
        return sprintf('%s<input type="file" name="%s"  id="%s" %s>', $this->inputDefaults['label'] && $info['lable'] ? '<label for="' . $info['id'] . '">' . $info['lable'] . '</label>' : '', $info['nameStr'], $info['id'], $info['else']);
    }

    public function radio($name, $options = array(), $infos = array())
    {
        $info = $this->getInfo($name, $infos);
        $radioval = $this->getValue($info['nameStr']);
        $radioval = $info['value'] ? $info['value'] : $radioval;
        $optStr = sprintf("\r\n<input type=\"hidden\" value=\"\" name=\"%s\"/>", $info['nameStr']);
        if (empty($options)) {
            $options = array(1 => '');
        }
        foreach ((array)$options as $key => $value) {
            $optStr .= sprintf("\r\n%s<input type=\"radio\" name=\"%s\"  value=\"%s\" %s id=\"%s%s\" %s %s>", $info['div'] ? '<div class="div_radio">' : '', $info['nameStr'], $key, $radioval == $key ? 'checked=""' : '', $info['id'], ucfirst($key), $info['title'] ? 'title="' . strval($value) . '"' : '', $info['else']);
            if (!$info['notext']) {
                $optStr .= sprintf('%s%s%s%s', $this->inputDefaults['label'] || $info['lable'] ? '<label for="' . $info['id'] . $key . '">' : '', strval($value), $this->inputDefaults['label'] || $info['lable'] ? '</label>' : '', $info['div'] ? '</div>' : '');
            }
        }
        return $optStr;
    }

    public function checkbox($name, $options = array(), $infos = array())
    {
        $info = $this->getInfo($name, $infos);
        $checkboxval = $this->getValue($info['nameStr']);
        $checkboxval = $info['value'] ? $info['value'] : $checkboxval;
        if (!is_array($checkboxval) && static::isJson($checkboxval)) {
            $checkboxval = json_decode($checkboxval, true);
        }
        $optStr = '';
        if (!empty($options)) {
            $optStr .= sprintf("\r\n<input type=\"hidden\" id=\"%s\" value=\"\" name=\"%s\">", $info['id'], $info['nameStr']);
            foreach ((array)$options as $key => $value) {
                $optStr .= sprintf("\r\n%s<input type=\"checkbox\" name=\"%s[]\" value=\"%s\" %s id=\"%s%s\" %s>%s%s%s%s", $info['div'] ? '<div class="div_checkbox">' : '', $info['nameStr'], $key, in_array($key, (array)$checkboxval) ? 'checked=""' : '', $info['id'], ucfirst($key), $info['else'], $this->inputDefaults['label'] || $info['lable'] ? '<label for="' . $info['id'] . $key . '">' : '', strval($value), $this->inputDefaults['label'] || $info['lable'] ? '</label>' : '', $info['div'] ? '</div>' : '');
            }
        } else {
            $optStr .= sprintf("\r\n<input type=\"hidden\" id=\"%s_\" value=\"0\" name=\"%s\">", $info['id'], $info['nameStr']);
            $optStr .= sprintf("\r\n<input type=\"checkbox\" name=\"%s\" value=\"1\" id=\"%s\" %s %s>", $info['nameStr'], $info['id'], $checkboxval == 1 ? 'checked=""' : '', $info['else']);

        }
        return $optStr;
    }

    public function textarea($name, $infos = array())
    {
        $info = $this->getInfo($name, $infos);
        $value = $this->getValue($info['nameStr']);       
        $value = $info['value'] ? $info['value'] : $value;

        return sprintf("<textarea name=\"%s\" id=\"%s\" %s>%s</textarea>", $info['nameStr'], $info['id'], $info['else'], $value);
    }


    public function select($name, $options = array(), $infos = array())
    {
        $info = $this->getInfo($name, $infos); 
        $value = $this->getValue($info['nameStr']);        
        $value = $info['value'] ? $info['value'] : $value;
        
        $options = (array)$options; 
        $empty = isset($info['empty']) ? $info['empty'] : '请选择';
        if ($empty)
            $optStr = "\r\n\t" . '<option value="">' . $empty . '</option>';

        foreach ($options as $level_key => $level) {
            if (is_array($level)) {
                $optStr .= "\r\n\t" . '<optgroup label="' . $level_key . '"></optgroup> ';
                foreach ($level as $level_key1 => $level1) {
                    $optStr .= sprintf("\r\n\t<option value=\"%s\" %s>%s</option>", $level_key1, $level_key1 == $value ? 'selected=""' : '', strval($level1));
                }
            } else {
                $optStr .= sprintf("\r\n\t<option value=\"%s\" %s>%s</option>", $level_key, $level_key == $value ? 'selected=""' : '', $level);
            }
        }
        return sprintf('%s<select name="%s" id="%s" %s %s>%s' . "\r\n" . '</select>', $this->inputDefaults['label'] && $info['lable'] ? '<label for="' . $info['id'] . '">' . $info['lable'] . '</label>' : '', $info['nameStr'], $info['id'], $info['multiple'] ? 'multiple="' . intval($info['multiple']) . '"' : '', $info['else'], $optStr);
    }

    private function simple($type = 'text', $name, $infos = array())
    {
        $info = $this->getInfo($name, $infos);
        $value = $this->getValue($info['nameStr']);
        $value = $info['value'] ? $info['value'] : $value;
        return sprintf('%s<input type="%s" name="%s" %s id="%s" %s>', $this->inputDefaults['label'] && $info['lable'] ? '<label for="' . $info['id'] . '">' . $info['lable'] . '</label>' : '', $type, $info['nameStr'], $value !== false ? 'value="' . htmlspecialchars($value) . '"' : '', $info['id'], $info['else']);
    }
    
    private function getValue($nameStr = '') 
    {
        preg_match_all('/\[([^\]]*?)\]/', $nameStr, $matchs);        
        $return = substr($nameStr, 0, 4) === 'data' ? $this->data : $this->request;
        foreach ((array)$matchs[1] as $field) {
            if (isset($return[$field])) {
                $return = $return[$field];
            }
        }
        if (!is_array($return)) {
            return $return;
        } else {
            return '';
        }
    }
    
    private function getInfo($name, $infos)
    {
        if (!isset($infos['name'])) {
            if (strpos($name, '.') !== false) {
                $nameArr = explode('.', $name);
                $mdl = array_shift($nameArr);
                $field = array_pop($nameArr);
                if (!empty($nameArr)) {
                    foreach ($nameArr as $f) {
                        if ($f)
                            $mid .= '[' . $f . ']';
                    }
                }
            } else {
                $mdl = $this->requestObj->controller();
                $field = $name;
                $mid = '';
            }
            $nameStr = sprintf('%s%s%s[%s]', 'data', $mdl ? '[' . $mdl . ']' : '', $mid, $field);
            $infos = (array)$infos;
            if ($infos['name']) $nameStr = $infos['name']; 
        } else {
            $nameStr = $infos['name'] ;
        }       
        unset($infos['name']);

        if ($infos['id'] && is_string($infos['id'])) $id = trim($infos['id']);
        else $id = ucfirst_deep($mdl . '_' . $field);
        unset($infos['id']);

        $lable = isset($infos['lable']) ? $infos['lable'] : $field;
        unset($infos['lable']);

        $empty = isset($infos['empty']) ? $infos['empty'] : '';
        unset($infos['empty']);

        $div = isset($infos['div']) ? $infos['div'] : false;
        unset($infos['div']);
        $value = isset($infos['value']) ? $infos['value'] : false;
        unset($infos['value']);

        $multiple = $infos['multiple'] ? true : false;
        unset($infos['multiple']);

        if (is_bool($infos['title'])) {
            $title = $infos['title'];
            unset($infos['title']);
        }
        if (is_bool($infos['notext'])) {
            $notext = $infos['notext'];
            unset($infos['notext']);
        }


        $else = '';
        if (!empty($infos)) {
            foreach ($infos as $attr => $attr_value) {
                $else .= ' ' . $attr . '="' . strval($attr_value) . '"';
            }
        }

        return compact('nameStr', 'id', 'lable', 'div', 'empty', 'value', 'else', 'multiple', 'title', 'notext');
    }

    static function isJson($jsonstr)
    {
        return preg_match('/^(\[|\{).*(\}|\])$/', $jsonstr);
    }
}
