<?php
if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}
//DEBUG ARRAY 与 XML 相互转换类
class arrayxml {
	private static $xml = null;
	private static $encoding = 'UTF-8';
    /**
     * Initialize the root XML node [optional]
     * @param $version
     * @param $encoding
     * @param $format_output
     */
    public static function init($version = '1.0', $encoding = 'UTF-8', $format_output = true) {
        self::$xml = new DomDocument($version, $encoding);
        self::$xml->formatOutput = $format_output;
		self::$encoding = $encoding;
    }

    /**
     * Convert an Array to XML
     * @param string $node_name - name of the root node to be converted
     * @param array $arr - aray to be converterd
     * @return DomDocument
     */
    public static function &createXML($node_name, $arr=array()) {
        $xml = self::getXMLRoot();
        $xml->appendChild(self::convert($node_name, $arr));
        self::$xml = null;    // clear the xml node in the class for 2nd time use.
        return $xml;
    }

    /**
     * Convert an Array to XML
     * @param string $node_name - name of the root node to be converted
     * @param array $arr - aray to be converterd
     * @return DOMNode
     */
    private static function &convert($node_name, $arr=array()) {

        //print_arr($node_name);
        $xml = self::getXMLRoot();
        $node = $xml->createElement($node_name);

        if(is_array($arr)){
            // get the attributes first.;
            if(isset($arr['@attributes'])) {
                foreach($arr['@attributes'] as $key => $value) {
                    if(!self::isValidTagName($key)) {
                        throw new Exception('[array2xml] Illegal character in attribute name. attribute: '.$key.' in node: '.$node_name);
                    }
                    $node->setAttribute($key, self::bool2str($value));
                }
                unset($arr['@attributes']); //remove the key from the array once done.
            }

            // check if it has a value stored in @value, if yes store the value and return
            // else check if its directly stored as string
            if(isset($arr['@value'])) {
                $node->appendChild($xml->createTextNode(self::bool2str($arr['@value'])));
                unset($arr['@value']);    //remove the key from the array once done.
                //return from recursion, as a note with value cannot have child nodes.
                return $node;
            } else if(isset($arr['@cdata'])) {
                $node->appendChild($xml->createCDATASection(self::bool2str($arr['@cdata'])));
                unset($arr['@cdata']);    //remove the key from the array once done.
                //return from recursion, as a note with cdata cannot have child nodes.
                return $node;
            }
        }

        //create subnodes using recursion
        if(is_array($arr)){
            // recurse to get the node for that key
            foreach($arr as $key=>$value){
                if(!self::isValidTagName($key)) {
                    throw new Exception('[array2xml] Illegal character in tag name. tag: '.$key.' in node: '.$node_name);
                }
                if(is_array($value) && is_numeric(key($value))) {
                    // MORE THAN ONE NODE OF ITS KIND;
                    // if the new array is numeric index, means it is array of nodes of the same kind
                    // it should follow the parent key name
                    foreach($value as $k=>$v){
                        $node->appendChild(self::convert($key, $v));
                    }
                } else {
                    // ONLY ONE NODE OF ITS KIND
                    $node->appendChild(self::convert($key, $value));
                }
                unset($arr[$key]); //remove the key from the array once done.
            }
        }

        // after we are done with all the keys in the array (if it is one)
        // we check if it has any text value, if yes, append it.
        if(!is_array($arr)) {
            $node->appendChild($xml->createTextNode(self::bool2str($arr)));
        }

        return $node;
    }

    /*
     * Get the root XML node, if there isn't one, create it.
     */
    private static function getXMLRoot(){
        if(empty(self::$xml)) {
            self::init();
        }
        return self::$xml;
    }

    /*
     * Get string representation of boolean value
     */
    private static function bool2str($v){
        //convert boolean to text value.
        $v = $v === true ? 'true' : $v;
        $v = $v === false ? 'false' : $v;
        return $v;
    }

    /*
     * Check if the tag name or attribute name contains illegal characters
     * Ref: http://www.w3.org/TR/xml/#sec-common-syn
     */
    private static function isValidTagName($tag){
        $pattern = '/^[a-z_]+[a-z0-9\:\-\.\_]*[^:]*$/i';
        return preg_match($pattern, $tag, $matches) && $matches[0] == $tag;
    }
    
		public static function xml2array($contents, $get_attributes=1, $priority = 'tag') {
		    if(!$contents) return array();
		
		    if(!function_exists('xml_parser_create')) {
		        //print "'xml_parser_create()' function not found!";
		        return array();
		    }
		
		    //Get the XML parser of PHP - PHP must have this module for the parser to work
		    $parser = xml_parser_create('');
		    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
		    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		    xml_parse_into_struct($parser, trim($contents), $xml_values);
		    xml_parser_free($parser);
		
		    if(!$xml_values) return;//Hmm...
		
		    //Initializations
		    $xml_array = array();
		    $parents = array();
		    $opened_tags = array();
		    $arr = array();
		
		    $current = &$xml_array; //Refference
		
		    //Go through the tags.
		    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
		    foreach($xml_values as $data) {
		        unset($attributes,$value);//Remove existing values, or there will be trouble
		
		        //This command will extract these variables into the foreach scope
		        // tag(string), type(string), level(int), attributes(array).
		        extract($data);//We could use the array by itself, but this cooler.
		
		        $result = array();
		        $attributes_data = array();
		        
		        if(isset($value)) {
		            if($priority == 'tag') $result = $value;
		            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
		        }
		
		        //Set the attributes too.
		        if(isset($attributes) and $get_attributes) {
		            foreach($attributes as $attr => $val) {
		                if($priority == 'tag') $attributes_data[$attr] = $val;
		                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
		            }
		        }
		
		        //See tag status and do the needed.
		        if($type == "open") {//The starting of the tag '<tag>'
		            $parent[$level-1] = &$current;
		            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
		                $current[$tag] = $result;
		                if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
		                $repeated_tag_index[$tag.'_'.$level] = 1;
		
		                $current = &$current[$tag];
		
		            } else { //There was another element with the same tag name
		
		                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
		                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
		                    $repeated_tag_index[$tag.'_'.$level]++;
		                } else {//This section will make the value an array if multiple tags with the same name appear together
		                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
		                    $repeated_tag_index[$tag.'_'.$level] = 2;
		                    
		                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
		                        $current[$tag]['0_attr'] = $current[$tag.'_attr'];
		                        unset($current[$tag.'_attr']);
		                    }
		
		                }
		                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
		                $current = &$current[$tag][$last_item_index];
		            }
		
		        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
		            //See if the key is already taken.
		            if(!isset($current[$tag])) { //New Key
		                $current[$tag] = $result;
		                $repeated_tag_index[$tag.'_'.$level] = 1;
		                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;
		
		            } else { //If taken, put all things inside a list(array)
		                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...
		
		                    // ...push the new element into that array.
		                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
		                    
		                    if($priority == 'tag' and $get_attributes and $attributes_data) {
		                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
		                    }
		                    $repeated_tag_index[$tag.'_'.$level]++;
		
		                } else { //If it is not an array...
		                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
		                    $repeated_tag_index[$tag.'_'.$level] = 1;
		                    if($priority == 'tag' and $get_attributes) {
		                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
		                            
		                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
		                            unset($current[$tag.'_attr']);
		                        }
		                        
		                        if($attributes_data) {
		                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
		                        }
		                    }
		                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
		                }
		            }
		
		        } elseif($type == 'close') { //End of tag '</tag>'
		            $current = &$parent[$level-1];
		        }
		    }
		    
		    return($xml_array);
		}
}