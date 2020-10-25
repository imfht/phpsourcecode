<?php

define('HDOM_TYPE_ELEMENT',1);
define('HDOM_TYPE_COMMENT',2);
define('HDOM_TYPE_TEXT',3);
define('HDOM_TYPE_ENDTAG',4);
define('HDOM_TYPE_ROOT',5);
define('HDOM_TYPE_UNKNOWN',6);
define('HDOM_QUOTE_DOUBLE',0);
define('HDOM_QUOTE_SINGLE',1);
define('HDOM_QUOTE_NO',3);
define('HDOM_INFO_BEGIN',0);
define('HDOM_INFO_END',1);
define('HDOM_INFO_QUOTE',2);
define('HDOM_INFO_SPACE',3);
define('HDOM_INFO_TEXT',4);
define('HDOM_INFO_INNER',5);
define('HDOM_INFO_OUTER',6);
define('HDOM_INFO_ENDSPACE',7);
function file_get_html() {
$dom = new simple_html_dom;
$args = func_get_args();
$dom->load(call_user_func_array('file_get_contents',$args),true);
return $dom;
}
function str_get_html($str,$lowercase=true) {
$dom = new simple_html_dom;
$dom->load($str,$lowercase);
return $dom;
}
function file_get_dom() {
$dom = new simple_html_dom;
$args = func_get_args();
$dom->load(call_user_func_array('file_get_contents',$args),true);
return $dom;
}
function str_get_dom($str,$lowercase=true) {
$dom = new simple_html_dom;
$dom->load($str,$lowercase);
return $dom;
}
class simple_html_dom_node {
public $nodetype = HDOM_TYPE_TEXT;
public $tag = 'text';
public $attr = array();
public $children = array();
public $nodes = array();
public $parent = null;
public $_ = array();
private $dom = null;
function __construct($dom) {
$this->dom = $dom;
$dom->nodes[] = &$this;
}
function __destruct() {
$this->clear();
}
function __toString() {
return $this->outertext();
}
function clear() {
$this->dom = null;
$this->nodes = null;
$this->parent = null;
$this->children = null;
}
function parent() {
return $this->parent;
}
function children($idx=-1) {
if ($idx===-1) return $this->children;
if (isset($this->children[$idx])) return $this->children[$idx];
return null;
}
function first_child() {
if (count($this->children)>0) return $this->children[0];
return null;
}
function last_child() {
if (($count=count($this->children))>0) return $this->children[$count-1];
return null;
}
function next_sibling() {
if ($this->parent===null) return null;
$idx = 0;
$count = count($this->parent->children);
while ($idx<$count &&$this!==$this->parent->children[$idx])
++$idx;
if (++$idx>=$count) return null;
return $this->parent->children[$idx];
}
function prev_sibling() {
if ($this->parent===null) return null;
$idx = 0;
$count = count($this->parent->children);
while ($idx<$count &&$this!==$this->parent->children[$idx])
++$idx;
if (--$idx<0) return null;
return $this->parent->children[$idx];
}
function innertext() {
if (isset($this->_[HDOM_INFO_INNER])) return $this->_[HDOM_INFO_INNER];
if (isset($this->_[HDOM_INFO_TEXT])) return $this->dom->restore_noise($this->_[HDOM_INFO_TEXT]);
$ret = '';
foreach($this->nodes as $n)
$ret .= $n->outertext();
return $ret;
}
function outertext() {
if ($this->tag==='root') return $this->innertext();
if ($this->dom->callback!==null)
call_user_func_array($this->dom->callback,array($this));
if (isset($this->_[HDOM_INFO_OUTER])) return $this->_[HDOM_INFO_OUTER];
if (isset($this->_[HDOM_INFO_TEXT])) return $this->dom->restore_noise($this->_[HDOM_INFO_TEXT]);
$ret = $this->dom->nodes[$this->_[HDOM_INFO_BEGIN]]->makeup();
if (isset($this->_[HDOM_INFO_INNER]))
$ret .= $this->_[HDOM_INFO_INNER];
else {
foreach($this->nodes as $n)
$ret .= $n->outertext();
}
if(isset($this->_[HDOM_INFO_END]) &&$this->_[HDOM_INFO_END]!=0)
$ret .= '</'.$this->tag.'>';
return $ret;
}
function plaintext() {
if (isset($this->_[HDOM_INFO_INNER])) return $this->_[HDOM_INFO_INNER];
switch ($this->nodetype) {
case HDOM_TYPE_TEXT: return $this->dom->restore_noise($this->_[HDOM_INFO_TEXT]);
case HDOM_TYPE_COMMENT: return '';
case HDOM_TYPE_UNKNOWN: return '';
}
if (strcasecmp($this->tag,'script')===0) return '';
if (strcasecmp($this->tag,'style')===0) return '';
$ret = '';
foreach($this->nodes as $n)
$ret .= $n->plaintext();
return $ret;
}
function makeup() {
if (isset($this->_[HDOM_INFO_TEXT])) return $this->dom->restore_noise($this->_[HDOM_INFO_TEXT]);
$ret = '<'.$this->tag;
$i = -1;
foreach($this->attr as $key=>$val) {
++$i;
if ($val===null ||$val===false)
continue;
$ret .= $this->_[HDOM_INFO_SPACE][$i][0];
if ($val===true)
$ret .= $key;
else {
switch($this->_[HDOM_INFO_QUOTE][$i]) {
case HDOM_QUOTE_DOUBLE: $quote = '"';break;
case HDOM_QUOTE_SINGLE: $quote = '\'';break;
default: $quote = '';
}
$ret .= $key.$this->_[HDOM_INFO_SPACE][$i][1].'='.$this->_[HDOM_INFO_SPACE][$i][2].$quote.$val.$quote;
}
}
$ret = $this->dom->restore_noise($ret);
return $ret .$this->_[HDOM_INFO_ENDSPACE] .'>';
}
function find($selector,$idx=-1) {
$selectors = $this->parse_selector($selector);
if (($count=count($selectors))===0) return array();
$found_keys = array();
for ($c=0;$c<$count;++$c) {
if (($levle=count($selectors[0]))===0) return array();
if (!isset($this->_[HDOM_INFO_BEGIN])) return array();
$head = array($this->_[HDOM_INFO_BEGIN]=>1);
for ($l=0;$l<$levle;++$l) {
$ret = array();
foreach($head as $k=>$v) {
$n = ($k===-1) ?$this->dom->root : $this->dom->nodes[$k];
$n->seek($selectors[$c][$l],$ret);
}
$head = $ret;
}
foreach($head as $k=>$v) {
if (!isset($found_keys[$k]))
$found_keys[$k] = 1;
}
}
ksort($found_keys);
$found = array();
foreach($found_keys as $k=>$v)
$found[] = $this->dom->nodes[$k];
if ($idx<0) return $found;
return (isset($found[$idx])) ?$found[$idx] : null;
}
protected function seek($selector,&$ret) {
list($tag,$key,$val,$exp) = $selector;
$end = (!empty($this->_[HDOM_INFO_END])) ?$this->_[HDOM_INFO_END] : 0;
if ($end==0) {
$parent = $this->parent;
while (!isset($parent->_[HDOM_INFO_END]) &&$parent!==null) {
$end -= 1;
$parent = $parent->parent;
}
$end += $parent->_[HDOM_INFO_END];
}
for($i=$this->_[HDOM_INFO_BEGIN]+1;$i<$end;++$i) {
$node = $this->dom->nodes[$i];
$pass = true;
if ($tag==='*') {
if (in_array($node,$this->children,true))
$ret[$i] = 1;
continue;
}
if ($tag &&$tag!=$node->tag) {$pass=false;}
if ($pass &&$key &&!(isset($node->attr[$key]))) {$pass=false;}
if ($pass &&$key &&$val) {
$check = $this->match($exp,$val,$node->attr[$key]);
if (!$check &&strcasecmp($key,'class')===0) {
foreach(explode(' ',$node->attr[$key]) as $k) {
$check = $this->match($exp,$val,$k);
if ($check) break;
}
}
if (!$check) $pass = false;
}
if ($pass) $ret[$i] = 1;
unset($node);
}
}
protected function match($exp,$pattern,$value) {
$check = true;
switch ($exp) {
case '=':
$check = ($value===$pattern) ?true : false;break;
case '!=':
$check = ($value!==$pattern) ?true : false;break;
case '^=':
$check = (preg_match("/^".preg_quote($pattern,'/')."/",$value)) ?true : false;break;
case '$=':
$check = (preg_match("/".preg_quote($pattern,'/')."$/",$value)) ?true : false;break;
case '*=':
$check = (preg_match("/".preg_quote($pattern,'/')."/i",$value)) ?true : false;break;
}
return $check;
}
protected function parse_selector($selector_string) {
$pattern = "/([\w-:\*]*)(?:\#([\w-]+)|\.([\w-]+))?(?:\[(\w+)(?:([!*^$]?=)[\"']?(.*?)[\"']?)?\])?([, ]+)/is";
preg_match_all($pattern,trim($selector_string).' ',$matches,PREG_SET_ORDER);
$selectors = array();
$result = array();
foreach ($matches as $m) {
if (trim($m[0])==='') continue;
list($tag,$key,$val,$exp) = array($m[1],null,null,'=');
if(!empty($m[2])) {$key='id';$val=$m[2];}
if(!empty($m[3])) {$key='class';$val=$m[3];}
if(!empty($m[4])) {$key=$m[4];}
if(!empty($m[5])) {$exp=$m[5];}
if(!empty($m[6])) {$val=$m[6];}
if ($this->dom->lowercase) {$tag=strtolower($tag);$key=strtolower($key);}
$result[] = array($tag,$key,$val,$exp);
if (trim($m[7])===',') {
$selectors[] = $result;
$result = array();
}
}
if (count($result)>0)
$selectors[] = $result;
return $selectors;
}
function __get($name) {
if (isset($this->attr[$name])) return $this->attr[$name];
switch($name) {
case 'outertext': return $this->outertext();
case 'innertext': return $this->innertext();
case 'plaintext': return $this->plaintext();
default: return array_key_exists($name,$this->attr);
}
}
function __set($name,$value) {
switch($name) {
case 'outertext': return $this->_[HDOM_INFO_OUTER] = $value;
case 'innertext':
if (isset($this->_[HDOM_INFO_TEXT])) return $this->_[HDOM_INFO_TEXT] = $value;
return $this->_[HDOM_INFO_INNER] = $value;
}
if (!isset($this->attr[$name])) {
$this->_[HDOM_INFO_SPACE][] = array(' ','','');
$this->_[HDOM_INFO_QUOTE][] = HDOM_QUOTE_DOUBLE;
}
$this->attr[$name] = $value;
}
function __isset($name) {
switch($name) {
case 'outertext': return true;
case 'innertext': return true;
case 'plaintext': return true;
}
return (array_key_exists($name,$this->attr)) ?true : isset($this->attr[$name]);
}
function __unset($name) {
if (isset($this->attr[$name]))
unset($this->attr[$name]);
}
function getAllAttributes() {return $this->attr;}
function getAttribute($name) {return $this->__get($name);}
function setAttribute($name,$value) {$this->__set($name,$value);}
function hasAttribute($name) {return $this->__isset($name);}
function removeAttribute($name) {$this->__set($name,null);}
function getElementById($id) {return $this->find("#$id",0);}
function getElementsById($id,$idx=-1) {return $this->find("#$id",$idx);}
function getElementByTagName($name) {return $this->find($name,0);}
function getElementsByTagName($name,$idx=-1) {return $this->find($name,$idx);}
function parentNode() {return $this->parent();}
function childNodes($idx=-1) {return $this->children($idx);}
function firstChild() {return $this->first_child();}
function lastChild() {return $this->last_child();}
function nextSibling() {return $this->next_sibling();}
function previousSibling() {return $this->prev_sibling();}
}
class simple_html_dom {
public $root = null;
public $nodes = array();
public $callback = null;
public $lowercase = false;
protected $pos;
protected $doc;
protected $char;
protected $size;
protected $cursor;
protected $parent;
protected $noise = array();
protected $token_blank = " \t\r\n";
protected $token_equal = ' =/><';
protected $token_slash = " />\r\n\t";
protected $token_attr = ' >';
protected $self_closing_tags = array('img'=>1,'br'=>1,'input'=>1,'meta'=>1,'link'=>1,'hr'=>1,'base'=>1,'embed'=>1,'spacer'=>1,'nobr'=>1);
protected $block_tags = array('root'=>1,'body'=>1,'form'=>1,'div'=>1,'span'=>1,'table'=>1);
protected $optional_closing_tags = array(
'tr'=>array('tr'=>1,'td'=>1,'th'=>1),
'th'=>array('th'=>1),
'td'=>array('td'=>1),
'ul'=>array('li'=>1),
'li'=>array('li'=>1),
'dt'=>array('dt'=>1,'dd'=>1),
'dd'=>array('dd'=>1,'dt'=>1),
'dl'=>array('dd'=>1,'dt'=>1),
'p'=>array('p'=>1),
);
function __destruct() {
$this->clear();
}
function load($str,$lowercase=true) {
$this->prepare($str,$lowercase);
$this->remove_noise("'<!--(.*?)-->'is");
$this->remove_noise("'<\s*style[^>]*[^/]>(.*?)<\s*/\s*style\s*>'is");
$this->remove_noise("'<\s*style\s*>(.*?)<\s*/\s*style\s*>'is");
$this->remove_noise("'<\s*script[^>]*[^/]>(.*?)<\s*/\s*script\s*>'is");
$this->remove_noise("'<\s*script\s*>(.*?)<\s*/\s*script\s*>'is");
$this->remove_noise("'<\s*(?:pre|code)[^>]*>(.*?)<\s*/\s*(?:pre|code)\s*>'is");
$this->remove_noise("'(<\?)(.*?)(\?>)'is",true);
while ($this->parse());
$this->root->_[HDOM_INFO_END] = $this->cursor;
}
function load_file() {
$args = func_get_args();
$this->load(call_user_func_array('file_get_contents',$args),true);
}
function set_callback($function_name) {
$this->callback = $function_name;
}
function remove_callback() {
$this->callback = null;
}
function save($filepath='') {
$ret = $this->root->innertext();
if ($filepath!=='') file_put_contents($filepath,$ret);
return $ret;
}
function find($selector,$idx=-1) {
return $this->root->find($selector,$idx);
}
function clear() {
foreach($this->nodes as $n) {$n->clear();$n = null;}
if (isset($this->parent)) {$this->parent->clear();unset($this->parent);}
if (isset($this->root)) {$this->root->clear();unset($this->root);}
unset($this->doc);
unset($this->noise);
}
protected function prepare($str,$lowercase=true) {
$this->clear();
$this->doc = $str;
$this->pos = 0;
$this->cursor = 1;
$this->noise = array();
$this->nodes = array();
$this->lowercase = $lowercase;
$this->root = new simple_html_dom_node($this);
$this->root->tag = 'root';
$this->root->_[HDOM_INFO_BEGIN] = -1;
$this->root->nodetype = HDOM_TYPE_ROOT;
$this->parent = $this->root;
$this->size = strlen($str);
if ($this->size>0) $this->char = $this->doc[0];
}
protected function parse() {
if (($s = $this->copy_until_char('<'))==='')
return $this->read_tag();
$node = new simple_html_dom_node($this);
++$this->cursor;
$node->_[HDOM_INFO_TEXT] = $s;
$this->link_nodes($node,false);
return true;
}
protected function read_tag() {
if ($this->char!=='<') {
$this->root->_[HDOM_INFO_END] = $this->cursor;
return false;
}
$this->char = (++$this->pos<$this->size) ?$this->doc[$this->pos] : null;
if ($this->char==='/') {
$this->char = (++$this->pos<$this->size) ?$this->doc[$this->pos] : null;
$this->skip($this->token_blank_t);
$tag = $this->copy_until_char('>');
if (($pos = strpos($tag,' '))!==false)
$tag = substr($tag,0,$pos);
$parent_lower = strtolower($this->parent->tag);
$tag_lower = strtolower($tag);
if ($parent_lower!==$tag_lower) {
if (isset($this->optional_closing_tags[$parent_lower]) &&isset($this->block_tags[$tag_lower])) {
$this->parent->_[HDOM_INFO_END] = 0;
while (($this->parent->parent) &&strtolower($this->parent->tag)!==$tag_lower)
$this->parent = $this->parent->parent;
if (strtolower($this->parent->tag)!==$tag_lower) {
$this->as_text_node($tag);
$this->char = (--$this->pos>-1) ?$this->doc[$this->pos] : null;
}
}
else if (($this->parent->parent) &&strtolower($this->parent->parent->tag)===$tag_lower) {
$this->parent->_[HDOM_INFO_END] = 0;
$this->parent = $this->parent->parent;
}
else
return $this->as_text_node($tag);
}
$this->parent->_[HDOM_INFO_END] = $this->cursor;
if ($this->parent->parent) $this->parent = $this->parent->parent;
$this->char = (++$this->pos<$this->size) ?$this->doc[$this->pos] : null;
return true;
}
$node = new simple_html_dom_node($this);
$node->_[HDOM_INFO_BEGIN] = $this->cursor;
++$this->cursor;
$tag = $this->copy_until($this->token_slash);
if (isset($tag[0]) &&$tag[0]==='!') {
$node->_[HDOM_INFO_TEXT] = '<'.$tag .$this->copy_until_char('>');
if (isset($tag[2]) &&$tag[1]==='-'&&$tag[2]==='-') {
$node->nodetype = HDOM_TYPE_COMMENT;
$node->tag = 'comment';
}else {
$node->nodetype = HDOM_TYPE_UNKNOWN;
$node->tag = 'unknown';
}
if ($this->char==='>') $node->_[HDOM_INFO_TEXT].='>';
$this->link_nodes($node,false);
$this->char = (++$this->pos<$this->size) ?$this->doc[$this->pos] : null;
return true;
}
if (!preg_match("/^[\w-:]+$/",$tag)) {
$node->_[HDOM_INFO_TEXT] = '<'.$tag .$this->copy_until_char('>');
if ($this->char==='>') $node->_[HDOM_INFO_TEXT].='>';
$this->link_nodes($node,false);
$this->char = (++$this->pos<$this->size) ?$this->doc[$this->pos] : null;
return true;
}
$node->nodetype = HDOM_TYPE_ELEMENT;
$tag_lower = strtolower($tag);
$node->tag = ($this->lowercase) ?$tag_lower : $tag;
if (isset($this->optional_closing_tags[$tag_lower]) ) {
while (isset($this->optional_closing_tags[$tag_lower][strtolower($this->parent->tag)])) {
$this->parent->_[HDOM_INFO_END] = 0;
$this->parent = $this->parent->parent;
}
$node->parent = $this->parent;
}
$this->link_nodes($node,true);
$guard = 0;
$space = array($this->copy_skip($this->token_blank),'','');
do {
if ($this->char!==null &&$space[0]==='') break;
$name = $this->copy_until($this->token_equal);
if($guard===$this->pos) {
$this->char = (++$this->pos<$this->size) ?$this->doc[$this->pos] : null;
continue;
}
$guard = $this->pos;
if($this->pos>=$this->size-1 &&$this->char!=='>') {
$node->nodetype = HDOM_TYPE_TEXT;
$node->_[HDOM_INFO_END] = 0;
$node->_[HDOM_INFO_TEXT] = '<'.$tag .$space[0] .$name;
$node->tag = 'text';
return true;
}
if ($name!=='/'&&$name!=='') {
$space[1] = $this->copy_skip($this->token_blank);
if ($this->lowercase) $name = strtolower($name);
if ($this->char==='=') {
$this->char = (++$this->pos<$this->size) ?$this->doc[$this->pos] : null;
$this->parse_attr($node,$name,$space);
}
else {
$node->_[HDOM_INFO_QUOTE][] = HDOM_QUOTE_NO;
$node->attr[$name] = true;
if ($this->char!='>') $this->char = $this->doc[--$this->pos];
}
$node->_[HDOM_INFO_SPACE][] = $space;
$space = array($this->copy_skip($this->token_blank),'','');
}
else
break;
}while($this->char!=='>'&&$this->char!=='/');
$node->_[HDOM_INFO_ENDSPACE] = $space[0];
if ($this->copy_until_char_escape('>')==='/') {
$node->_[HDOM_INFO_ENDSPACE] .= '/';
$node->_[HDOM_INFO_END] = 0;
}
else {
if (!isset($this->self_closing_tags[strtolower($node->tag)])) $this->parent = $node;
}
$this->char = (++$this->pos<$this->size) ?$this->doc[$this->pos] : null;
return true;
}
protected function parse_attr($node,$name,&$space) {
$space[2] = $this->copy_skip($this->token_blank);
switch($this->char) {
case '"':
$node->_[HDOM_INFO_QUOTE][] = HDOM_QUOTE_DOUBLE;
$this->char = (++$this->pos<$this->size) ?$this->doc[$this->pos] : null;
$node->attr[$name] = $this->restore_noise($this->copy_until_char_escape('"'));
$this->char = (++$this->pos<$this->size) ?$this->doc[$this->pos] : null;
break;
case '\'':
$node->_[HDOM_INFO_QUOTE][] = HDOM_QUOTE_SINGLE;
$this->char = (++$this->pos<$this->size) ?$this->doc[$this->pos] : null;
$node->attr[$name] = $this->restore_noise($this->copy_until_char_escape('\''));
$this->char = (++$this->pos<$this->size) ?$this->doc[$this->pos] : null;
break;
default:
$node->_[HDOM_INFO_QUOTE][] = HDOM_QUOTE_NO;
$node->attr[$name] = $this->restore_noise($this->copy_until($this->token_attr));
}
}
protected function link_nodes(&$node,$is_child) {
$node->parent = $this->parent;
$this->parent->nodes[] = &$node;
if ($is_child)
$this->parent->children[] = &$node;
}
protected function as_text_node($tag) {
$node = new simple_html_dom_node($this);
++$this->cursor;
$node->_[HDOM_INFO_TEXT] = '</'.$tag .'>';
$this->link_nodes($node,false);
$this->char = (++$this->pos<$this->size) ?$this->doc[$this->pos] : null;
return true;
}
protected function skip($chars) {
$this->pos += strspn($this->doc,$chars,$this->pos);
$this->char = ($this->pos<$this->size) ?$this->doc[$this->pos] : null;
}
protected function copy_skip($chars) {
$pos = $this->pos;
$len = strspn($this->doc,$chars,$pos);
$this->pos += $len;
$this->char = ($this->pos<$this->size) ?$this->doc[$this->pos] : null;
if ($len===0) return '';
return substr($this->doc,$pos,$len);
}
protected function copy_until($chars) {
$pos = $this->pos;
$len = strcspn($this->doc,$chars,$pos);
$this->pos += $len;
$this->char = ($this->pos<$this->size) ?$this->doc[$this->pos] : null;
return substr($this->doc,$pos,$len);
}
protected function copy_until_char($char) {
if ($this->char===null) return '';
if (($pos = strpos($this->doc,$char,$this->pos))===false) {
$ret = substr($this->doc,$this->pos,$this->size-$this->pos);
$this->char = null;
$this->pos = $this->size;
return $ret;
}
if ($pos===$this->pos) return '';
$pos_old = $this->pos;
$this->char = $this->doc[$pos];
$this->pos = $pos;
return substr($this->doc,$pos_old,$pos-$pos_old);
}
protected function copy_until_char_escape($char) {
if ($this->char===null) return '';
$start = $this->pos;
while(1) {
if (($pos = strpos($this->doc,$char,$start))===false) {
$ret = substr($this->doc,$this->pos,$this->size-$this->pos);
$this->char = null;
$this->pos = $this->size;
return $ret;
}
if ($pos===$this->pos) return '';
if ($this->doc[$pos-1]==='\\') {
$start = $pos+1;
continue;
}
$pos_old = $this->pos;
$this->char = $this->doc[$pos];
$this->pos = $pos;
return substr($this->doc,$pos_old,$pos-$pos_old);
}
}
protected function remove_noise($pattern,$remove_tag=false) {
$count = preg_match_all($pattern,$this->doc,$matches,PREG_SET_ORDER|PREG_OFFSET_CAPTURE);
for ($i=$count-1;$i>-1;--$i) {
$key = '___noise___'.sprintf('% 3d',count($this->noise)+100);
$idx = ($remove_tag) ?0 : 1;
$this->noise[$key] = $matches[$i][$idx][0];
$this->doc = substr_replace($this->doc,$key,$matches[$i][$idx][1],strlen($matches[$i][$idx][0]));
}
$this->size = strlen($this->doc);
if ($this->size>0) $this->char = $this->doc[0];
}
function restore_noise($text) {
while(($pos=strpos($text,'___noise___'))!==false) {
$key = '___noise___'.$text[$pos+11].$text[$pos+12].$text[$pos+13];
if (isset($this->noise[$key]))
$text = substr($text,0,$pos).$this->noise[$key].substr($text,$pos+14);
}
return $text;
}
function __toString() {
return $this->root->innertext();
}
function __get($name) {
switch($name) {
case 'outertext': return $this->root->innertext();
case 'innertext': return $this->root->innertext();
case 'plaintext': return $this->root->plaintext();
}
}
function childNodes($idx=-1) {return $this->root->childNodes($idx);}
function firstChild() {return $this->root->first_child();}
function lastChild() {return $this->root->last_child();}
function getElementById($id) {return $this->find("#$id",0);}
function getElementsById($id,$idx=-1) {return $this->find("#$id",$idx);}
function getElementByTagName($name) {return $this->find($name,0);}
function getElementsByTagName($name,$idx=-1) {return $this->find($name,$idx);}
function loadFile() {$args = func_get_args();$this->load(call_user_func_array('file_get_contents',$args),true);}
}

?>