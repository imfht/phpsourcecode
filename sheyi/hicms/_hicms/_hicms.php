<?php
/**
 * HiCMS_Auth
 * Handles user authentication within HiCMS
 */
class HiCMS_Auth {

  /**
   * login
   * Attempts to log in a user
   *
   * @param string  $username  Username of the user
   * @param string  $password  Password of the user
   * @param boolean  $remember  Remember this user later?
   * @return boolean
   */
  public static function login($username, $password, $remember=false) {
    $u = self::get_user($username);
    
    if ($u && $u->correct_password($password)) {
      $app = \Slim\Slim::getInstance();
      $hash = $username.":".md5($u->get_encrypted_password().$app->config['_cookies.secret_key']);
      $expire = $app->config['_cookies.lifetime'];
      $app->setEncryptedCookie('stat_auth_cookie', $hash, $expire);

      return true;
    }

    return false;
  }


  /**
   * logout
   * Logs a user out
   *
   * @return void
   */
  public static function logout() {
    $app = \Slim\Slim::getInstance();
    $cookie = $app->deleteCookie('stat_auth_cookie');
  }


  /**
   * user_exists
   * Determines if a given $username exists
   *
   * @param string  $username  Username to check for existence
   * @return boolean
   */
  public static function user_exists($username) {
    return !(self::get_user($username) == null);
  }


  /**
   * is_logged_in
   * Checks to see if the current session is logged in
   *
   * @return mixed
   */
  public static function is_logged_in() {
    $user = null;

    $app = \Slim\Slim::getInstance();
    $cookie = $app->getEncryptedCookie('stat_auth_cookie');

    if ($cookie) {
      list($username, $hash) = explode(":", $cookie);
      $user = self::get_user($username);

      if ($user) {
        $hash = $username.":".md5($user->get_encrypted_password().$app->config['_cookies.secret_key']);

        if ($cookie === $hash) {
          # validated
          $expire = $app->config['_cookies.lifetime'];
          $app->setEncryptedCookie('stat_auth_cookie', $cookie, $expire);
          return $user;
        }
      }
    }
    
    return false;
  }


  /**
   * get_user
   * Gets complete information about a given $username
   *
   * @param string  $username  Username to look up
   * @return HiCMS_User object
   */
  public static function get_user($username) {
    $u = HiCMS_User::load($username);
    return $u;
  }


  /**
   * get_current_user
   * Gets complete information about the currently logged-in user
   *
   * @return HiCMS_User object
   */
  public static function get_current_user() {
    $u = self::is_logged_in();
    return $u;
  }


  /**
   * get_user_list
   * Gets a full list of registered users
   *
   * @param boolean  $protected  Displaying information in a protected area?
   * @return array
   */
  public static function get_user_list($protected = true) {
    $users = array();
    $folder = "_config/users/*.yaml";
    $list = glob($folder);
    if ($list) {
      foreach ($list as $name) {
        $start = strrpos($name, "/")+1;
        $end = strrpos($name, ".");
        $username = substr($name, $start, $end-$start);
        if ($protected) {
          $users[$username] = self::get_user($username);
        } else {
          $users[$username] = HiCMS_User::get_profile($username);
        }
      }
    }
    return $users;
  }
}
class HiCMS_Fieldset {
  protected $data = array();
  protected $name = null;

  public function HiCMS_Fieldset($data) {
    $this->data = $data;
  }

  public function set_name($name) {
    $this->name = $name;
  }

  public function get_name() {
    return $this->name;
  }

  public function get_data()
  {
    return $this->data;
  }

  // STATIC FUNCTIONS
  // ------------------------------------------------------
  public static function load($fieldsets) {

    $fields = array('fields' => array());
    $included_fields = array('fields' => array());
    $fieldset_names = array();

    if (! is_array($fieldsets)) {
      $fieldsets = array($fieldsets);
    }

    foreach ($fieldsets as $key => $name) {
      if (file_exists("_config/fieldsets/{$name}.yaml")) { 
        $meta = self::fetch_fieldset($name);

        if (isset($meta['include'])) {

          if ( ! is_array($meta['include'])) {
            $meta['include'] = array($meta['include']);
          }

          foreach ($meta['include'] as $include_key => $include_name) {
            $include = self::fetch_fieldset($include_name);
            $included_fields['fields'] = array_merge($included_fields['fields'], $include['fields']);
          }
        }
        
        $fields['fields'] = array_merge($fields['fields'], $included_fields['fields'], $meta['fields']);
        $fieldset_names[] = $name;
      }
    }
    
    $set = new HiCMS_Fieldset($fields);
    $set->set_name($fieldset_names);
    return $set;
  }

  public static function fetch_fieldset($fieldset) {
    if (file_exists("_config/fieldsets/{$fieldset}.yaml")) { 
      $meta_raw = file_get_contents("_config/fieldsets/{$fieldset}.yaml");
      $meta = Spyc::YAMLLoad($meta_raw);
      return $meta;
    }
    return false;
  }

  public static function get_list() {
    $sets = array();
    $folder = "_config/fieldsets/*";
    $list = glob($folder);
    if ($list) {
      foreach ($list as $name) {
        if (is_dir($name)) {
        } else {
          $start = strrpos($name, "/")+1;
          $end = strrpos($name, ".");

          $key = substr($name, $start, $end-$start);
          $sets[$key] = self::fetch_fieldset($key);
        }
      }
    }
    return $sets;
  }

}

class HiCMS_Helper {
  /* commonly used regex patterns */
  static $date_regex              = "/\d{4}[\-_\.](?:\d{2}[\-_\.]){2}/";
  static $datetime_regex          = "/\d{4}[\-_\.](?:\d{2}[\-_\.]){2}\d{4}[\-_\.]/";
  static $date_or_datetime_regex  = "/\d{4}[\-_\.](?:\d{2}[\-_\.]){2}(?:\d{4}[\-_\.])?/";
  static $numeric_regex           = "/\d+[\-_\.]/";
  

  /**
   * starts_with
   * Determines if a given $haystack starts with $needle
   *
   * @param string  $haystack  String to inspect
   * @param string  $needle  Character to look for
   * @return boolean
   */
  public static function starts_with($haystack, $needle) {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
  }


  /**
   * ends_with
   * Determines if a given $haystack ends with $needle
   *
   * @param string  $haystack  String to inspect
   * @param string  $needle  Character to look for
   * @param boolean  $case  Perform a case-sensitive search?
   * @return boolean
   */
  public static function ends_with($haystack, $needle, $case=true) {
    if ($case) {
      return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);
    }
    return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);
  }


  /**
   * is_date_slug
   * Determines if a given $slug is date-based and is valid or not
   *
   * @param string  $slug  Slug to inspect
   * @return boolean
   */
  public static function is_date_slug($slug) {
    if (preg_match(self::$date_regex, $slug, $m)) {
      return self::is_valid_date($m[0]);
    }

    return false;
  }


  /**
   * is_datetime_slug
   * Determines if a given $slug is datetime-based and is valid or not
   *
   * @param string  $slug  Slug to inspect
   * @return boolean
   */
  public static function is_datetime_slug($slug) {
    if (preg_match(self::$datetime_regex, $slug, $m)) {
      return self::is_valid_date($m[0]);
    }

    return false;
  }


  /**
   * is_valid_date
   * Determines if a given date or datetime represents a real date
   *
   * @param string  $date  A yyyy-mm-dd(-hhii)[-ish] formatted string for checking
   * @return boolean
   */
  public static function is_valid_date($date) {
    // trim string down to just yyyy-mm-dd
    $date = substr($date, 0, 10);

    // grab the delimiter (character after yyyy)
    $delimiter = substr($date, 4, 1);

    // explode that into chunks
    $chunks = explode($delimiter, $date);

    return checkdate((int) $chunks[1], (int) $chunks[2], (int) $chunks[0]);
  }


  /**
   * is_numeric_slug
   * Determines if a given $slug is numeric-based or not
   *
   * @param string  $slug  Slug to inspect
   * @return boolean
   */
  public static function is_numeric_slug($slug) {
    return (bool) preg_match("/\d[\.\-]/i", $slug);
  }


  /**
   * get_datestamp
   * Gets a timestamp from a given $date_slug
   *
   * @deprecated  Use self::get_datetimestamp instead
   *
   * @param string  $date_slug  Date slug to inspect
   * @return integer
   */
  public static function get_datestamp($date_slug) {
    return self::get_datetimestamp($date_slug);
  }


  /**
   * get_datetimestamp
   * Gets a timestamp from a given $date_slug
   *
   * @param string  $date_slug  Date (or datetime) slug to inspect
   * @return integer
   */
  public static function get_datetimestamp($date_slug) {
    if (!preg_match(self::$date_or_datetime_regex, $date_slug, $m) || !self::is_valid_date($m[0])) {
      return false;
    }
    
    $date_string = substr($m[0], 0, 10);
    $delimiter = substr($date_string, 4, 1);
    $date_array = explode($delimiter, $date_string);

    // check to see if this is a full date and time
    $time_string = (strlen($m[0]) > 11) ? substr($m[0], 11, 4) : '0000';

    // construct the stringed time
    $d = $date_array[2] . '-' . $date_array[1] . '-' . $date_array[0];
    $t = substr($time_string, 0, 2) . ":" . substr($time_string, 2);

    return strtotime("{$d} {$t}");
  }


  /**
   * get_numeric
   * Gets the numeric value of the $numeric_slug
   *
   * @param string  $numberic_slug  Numeric slug to inspect
   * @return integer
   */
  public static function get_numeric($numeric_slug) {

    preg_match("/\d*/i", $numeric_slug, $matches);
    return $matches[0];
  }


  /**
   * reduce_double_slashes
   * Removes instances of "//" from a given $string except for URL protocols
   *
   * @param string  $string  String to reduce
   * @return string
   */
  public static function reduce_double_slashes($string) {
    return preg_replace("#(^|[^:])//+#", "\\1/", $string);
  }


  /**
   * trim_slashes
   * Removes any extra "/" at the beginning or end of a given $string
   *
   * @param string  $string  String to trim
   * @return string
   */
  public static function trim_slashes($string) {
    return trim($string, '/');
  }


  /**
   * remove_numerics_from_path
   * Strips out any instances of a numeric ordering from a given $path
   *
   * @todo this should be named something more generic to incorporate datetimes that it accommodates
   *
   * @param string  $path  String to strip out numerics from
   * @return string
   */
  public static function remove_numerics_from_path($path) {
    if ($path) {
      $parts = explode("/", substr($path, 1));
      $fixedpath = "/";
      foreach ($parts as $part) {
        if (preg_match("/\d[\.\-\_]/i", $part)) {
          if (self::is_date_slug($part)) {
            $part  = preg_replace(self::$date_regex, '', $part);
          } else {
            $part  = preg_replace(self::$numeric_regex, '', $part);
          }
        }
        if ($fixedpath <> '/') $fixedpath .= '/';
        $fixedpath .= $part;
      }
      $path = $fixedpath;
    }
    return $path;
  }


  /**
   * pop_last_segment
   * Pops the last segment off of a given $url and returns the appropriate array.
   *
   * @param string  $url  URL to derive segments from
   * @return string
   */
  public static function pop_last_segment($url) {
    $url_array = explode('/', $url);
    
    array_pop($url_array);

    if (is_array($url_array))
      return implode('/', $url_array);

    return $url_array;
  }


  /**
   * resolve_path
   * Finds the actual path from a URL-friendly $path
   *
   * @param string  $path  Path to resolve
   * @return string
   */
  public static function resolve_path($path) {
    $content_root = HiCMS::get_content_root();
    $content_type = HiCMS::get_content_type();

    if (strpos($path, "/") === 0) {
      $parts = explode("/", substr($path, 1));
    } else {
      $parts = explode("/", $path);
    }

    $fixedpath = "/";

    foreach ($parts as $part) {
      if (file_exists("{$content_root}{$fixedpath}/{path}.{$content_type}") || is_dir("{$content_root}{$fixedpath}/{$part}")) {
        // don't rename it exists!
      } else {
        // check folders
        $list = HiCMS::get_content_tree("{$fixedpath}", 1, 1, false, true, false);
        foreach ($list as $item) {
          $t = basename($item['slug']);
          if (self::is_numeric_slug($t)) {
            $nl = strlen(self::get_numeric($t)) + 1;
            if (strlen($part) >= (strlen($item['slug'])-$nl) && self::ends_with($item['slug'], $part)) {
              $part = $item['slug'];
              break;
            }
          } else {
            if (self::ends_with($item['slug'], $part)) {
              if (strlen($part) >= strlen($t)) {
                $part = $item['slug'];
                break;
              }
            }
          }
        }

        // check files
        $list = HiCMS::get_file_list("{$fixedpath}");
        foreach ($list as $key => $item) {
          if (self::ends_with($key, $part)) {
            $t = basename($item);
            if (HiCMS::get_entry_timestamps() && self::is_datetime_slug($t)) {
              if (strlen($part) >= (strlen($key)-16)) {
                $part = $key;
                break;
              }
            } else if (self::is_date_slug($t)) {
              if (strlen($part) >= (strlen($key)-11)) {
                $part = $key;
                break;
              }
            } else if (self::is_numeric_slug($t)) {
              $nl = strlen(self::get_numeric($key)) + 1;
              if (strlen($part) >= (strlen($key)-$nl)) {
                $part = $key;
                break;
              }
            } else {
              $t = basename($item);
              if (strlen($part) >= strlen($t)) {
                $part = $key;
                break;
              }
            }
          }
        }
      }

      if ($fixedpath <> '/') $fixedpath .= '/';
      $fixedpath .= $part;
    }
    return $fixedpath;
  }


  /**
   * is_file_newer
   * Checks to see if $file is newer than $compare_to_this_one
   *
   * @param string  $file  File for comparing
   * @param string  $compare_to_this_one  Path and name of file to compare against $file
   * @return boolean
   */
  public static function is_file_newer($file, $compare_to_this_one) {
    return (filemtime($file) > filemtime($compare_to_this_one));
  }


  /**
   * is_valid
   * Determines if the given $uuid is valid
   *
   * @param string  $uuid  UUID to validate
   * @return boolean
   */
  public static function is_valid($uuid) {
    return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
  }


  /**
   * random_string
   * Returns a random string $length characters long
   *
   * @param string  $length  Length of random string to return
   * @return string
   */
  public static function random_string($length = 32) {
    $s = '';
    $c = "BCDFGHJKLMNPQRSTVWXYZbcdfghjklmnpqrstvwxwz0123456789";
    for(;$length > 0;$length--) $s .= $c{rand(0,strlen($c)-1)};
    return str_shuffle($s);
  }


  /**
   * build_file_content
   * Creates a file content from $data_array and $content
   * 
   * @param array  $data_array  Data to load into the file's front-matter
   * @param string  $content  Content to append to the file
   * @return string
   */
  public static function build_file_content($data_array, $content) {
    $file_content = "";
    $file_content .= Spyc::YAMLDump($data_array, false, 0);
    $file_content .= "---\n";
    $file_content .= $content;

    return $file_content;
  }


  /**
   * get_template
   * Get a fully-parsed HTML template
   *
   * @param string  $template  Template name to use
   * @param array  $data  Option array of data to incorporate into the template
   * @return string
   */
  public static function get_template($template, $data = array()) {
    $app = \Slim\Slim::getInstance();

    Lex_Autoloader::register();
    $parser = new Lex_Parser();
    $parser->scope_glue(':');

    $template_path = $app->config['templates.path'] . '/templates/' . ltrim($template, '/').'.html';

    if (file_exists($template_path)) {
      $html = $parser->parse(file_get_contents($template_path), $data, false);
    }

    return $html;
  }

  /**
   * is
   * Determine if a given string matches a given pattern
   *
   * @param string  $pattern  Pattern to look for in $value
   * @param string  $value  String to look through
   * @return boolean
   */
  public static function is($pattern, $value) {
    if ($pattern !== '/') {
      $pattern = str_replace('*', '(.*)', $pattern).'\z';
    } else {
      $pattern = '^/$';
    }

    return preg_match('#' . $pattern . '#', $value);
  }


  /**
   * prettify
   * Converts a string from underscore-slug-format to normal-format
   *
   * @param string  $string  String to convert
   * @return string
   */
  public static function prettify($string) {
    return ucwords(preg_replace('/[_]+/', ' ', strtolower(trim($string))));
  }


  /**
   * slugify
   * Converts a string from normal-format to slug-format
   *
   * credit: http://sourcecookbook.com/en/recipes/8/function-to-slugify-strings-in-php
   *
   * @param string  $text  String to convert
   * @return string
   */
  public static function slugify($text) {
    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
 
    // trim
    $text = trim($text, '-');
 
    // transliterate
    // if (function_exists('iconv'))
    // {
    //     $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    // }
 
    // lowercase
    // ### $MUBS$ is this necesary
    // $text = strtolower($text);
 
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
 
    if (empty($text)) {
        return 'n-a';
    }
 
    return $text;
  }


  /**
   * deslugify
   * Converts a string from slug-format to normal-format
   *
   * @param string  $string  String to convert
   * @return string
   */
  public static function deslugify($text) {
      // replace dash with a 'space'
      $text = preg_replace('~-~', ' ', $text);
   
      // trim
      $text = trim($text, ' ');
   
      return $text;
  }

    public static function explode_options($string, $keyed = false) {
      
    $options = explode('|', $string);

    if ($keyed) {
      $temp_options = array();
      foreach ($options as $key => $value) {

        if (strpos($value, ':')) {
          # key:value pair present
          list($option_key, $option_value) = explode(':', $value);
        } else {

          # default value is false
          $option_key = $value;
          $option_value = false;
        }
        # set the main options array
        $temp_options[$option_key] = $option_value;
      }
      # reassign and override
      $options = $temp_options;
    }
    return $options;
  }


  /**
   * array_empty
   * Determines if a given $mixed value is an empty array or not
   *
   * @param mixed  $array  Value to check for an empty array
   * @return boolean
   */
  public static function array_empty($mixed) {
    if (is_array($mixed)) {
      foreach ($mixed as $value) {
        if ( ! self::array_empty($value)) {
          return false;
        }
      }
    } elseif ( ! empty($mixed) || $mixed !== '') {
      return false;
    }

    return true;
  }
}


class Hooks {

  public static function current_route() {
    $app = \Slim\Slim::getInstance();

    return trim($app->request()->getResourceUri(), '/');
  }

  public static function include_js($file, $root = '_add-ons/') {

    $class = get_called_class();
    $add_on = substr($class, 6);

    if ( ! is_array($file)) {
      $files[] = $file;
    } else {
      $files = $file;
    }

    $html = '';
    foreach ($files as $key => $file) {
      $file = HiCMS::get_site_root().$root.$add_on.'/js/'.$file;

      if (substr($file, -3) != '.js') {
        $file .= '.js';
      }

      $html .= "<script type=\"text/javascript\" src=\"{$file}\"></script>\n";
    }

    return $html;
  }

  public static function include_css($file, $root = '_add-ons/') {
    $class = get_called_class();
    $add_on = substr($class, 6);

    if ( ! is_array($file)) {
      $files[] = $file;
    } else {
      $files = $file;
    }

    $html = '';
    foreach ($files as $key => $file) {
      $file = HiCMS::get_site_root().$root.$add_on.'/css/'.$file;

      if (substr($file, -4) != '.css') {
        $file .= '.css';
      }

      $html .= "<link rel=\"stylesheet\" href=\"{$file}\">\n";
    }

    return $html;
  }

  public static function inline_js($html) {
    return "<script type=\"text/javascript\">\n$html\n</script>\n";
  }
}
class Plugin {

	public $attributes;
	public $content;

  public function __construct() {
    Lex_Autoloader::register();
    $this->parser = new Lex_Parser();
    $this->parser->scope_glue(':');
    $this->parser->cumulative_noparse(true);
  }

	/**
     * Fetch Parameter
     *
     * This method fetches tag parameters if they exist, and returns their value
     * or a given default if not found
     *
     * @param   string    $param       Parameter to be checked
     * @param   string    $default     Default value
     * @param   boolean   $is_valid    Allows a boolean callback function to validate parameter
     * @param   boolean   $is_boolean  Indicates parameter is boolean (yes/no)
     * @return  mixed     Returns the parameter's value if found, default if not, or boolean if yes/no style
     */
	public function fetch_param($param, $default=null, $is_valid=false, $is_boolean=false, $force_lower=true) {
		
    if (isset($this->attributes[$param])) {

      $found_param = $force_lower ? strtolower($this->attributes[$param]) : $this->attributes[$param];

			if ($is_valid == false || ($is_valid !== false && function_exists($is_valid) && $is_valid($found_param) === true)) {

        # Yes/no parameters
				if ($is_boolean === true) {
          return $found_param === 'yes';
				}

        # Standard result
				return $found_param;
			}
		}

    # Not found
		return $default;
	}

  public function explode_options($string, $keyed = false) {
      
    $options = explode('|', $string);

    if ($keyed) {
      $temp_options = array();
      foreach ($options as $key => $value) {

        if (strpos($value, ':')) {
          # key:value pair present
          list($option_key, $option_value) = explode(':', $value);
        } else {

          # default value is false
          $option_key = $value;
          $option_value = false;
        }
        # set the main options array
        $temp_options[$option_key] = $option_value;
      }
      # reassign and override
      $options = $temp_options;
    }
    return $options;

  }

  public function parse_loop($content, $assoc_array) {

    $output = "";

    $count = 1;
    $total_results = count($assoc_array);
    foreach ($assoc_array as $key => $post) {
      
      $assoc_array[$key]['count'] = $count;
      
      $assoc_array[$key]['total_results'] = $total_results;

      if ($count === 1) {
        $assoc_array[$key]['first'] = true;
      }

      if ($count == $total_results) {
        $assoc_array[$key]['last'] = true;
      }
      $count++;
    }

    foreach ($assoc_array  as $item) {
      $c = $content;

      // replace all instances of { variable } with variable
      $regex = '/\{(?!\{)\s*(([|a-zA-Z0-9_\.]+))\s*\}(?!\})/im';
      if (preg_match_all($regex, $c, $data_matches, PREG_SET_ORDER + PREG_OFFSET_CAPTURE)) {
        foreach ($data_matches as $match) {
          $tag = $match[0][0];
          $name = $match[1][0];
          if (isset($item[$name])) {
            $c = str_replace($tag, $item[$name], $c);
          }
       }
      }

      $output .= $this->parser->parse($c, $item);
    }

    return $output;
  }

}
/**
 * HiCMS
 *
 * @author      She Yi
 * @copyright   2015 HiCMS
 * @link        http://higrid.net
 *
 */

if ( ! defined('HIGRDIDCMS_VERSION')) define('HIGRDIDCMS_VERSION', '3.0.2');

class HiCMS {
  protected static $_yaml_cache = array();
  public static $folder_list = array();

  public static $publication_states = array('live' => 'Live', 'draft' => 'Draft', 'hidden' => 'Hidden');

  public static function loadYamlCached($content) {
    $yaml = array();
    $hash = md5($content);

    if (isset(self::$_yaml_cache[$hash])) {
      $yaml = self::$_yaml_cache[$hash];
    } else {
      $yaml = Spyc::YAMLLoad($content);
      self::$_yaml_cache[$hash] = $yaml;
    }
    return $yaml;
  }

  public static function load_all_configs() {
    // do all the other files then do the settings

    $pattern = "_config/*.yaml";
    $list = glob($pattern);

    $config = array();
    $file_config = Spyc::YAMLLoad('_config/settings.yaml');
    $config += $file_config;

    $routes = array();
    if (file_exists('_config/routes.yaml')) {
      $routes['_routes'] = Spyc::YAMLLoad('_config/routes.yaml');
    }

    if ($list) {
      foreach ($list as $name) {
        if ( ! HiCMS_Helper::ends_with($name, "_config/settings.yaml")
          && ! HiCMS_Helper::ends_with($name, "_config/routes.yaml") ) {
          $file_config = Spyc::YAMLLoad($name);
          $config = array_merge($config, $file_config, $routes);
        }
      }
    }

    // theme configs, allowing override
    $theme_list = glob('_themes/'.$config['_theme'].'/*.yaml');
    foreach ($theme_list as $name) {
      $config = array_merge($config, Spyc::YAMLLoad($name));
    }

    return $config;
  }

  public static function get_folder_list($folder,$future=false,$past=true) {
    if (isset(self::$folder_list[$folder])) {
      $folder_list = self::$folder_list[$folder];
    } else {
      $folder_list = HiCMS::get_content_list($folder, null, 0, $future, $past, 'date', 'desc', null, null, false, false, null, null);
      self::$folder_list[$folder] = $folder_list;
    }
    return $folder_list;
  }

  public static function get_site_root() {
    // default: default
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_site_root'])) {
      return $app->config['_site_root'];
    }

    return "/";
  }

  public static function get_site_url() {
    // default: default
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_site_url'])) {
      return $app->config['_site_url'];
    }

    return "";
  }

  public static function get_site_name() {
    // default: default
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_site_name'])) {
      return $app->config['_site_name'];
    }

    return "";
  }

  public static function get_license_key() {
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_license_key'])) {
      return $app->config['_license_key'];
    }

    return '';
  }

  public static function get_theme_name() {
    // default: default
    $app = \Slim\Slim::getInstance();

    if (isset($app->config['_theme'])) {
      return $app->config['_theme'];
    }

    return "denali";
  }

  public static function get_theme_type() {
    // default: lex
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['theme_type'])) {
      return $app->config['theme_type'];
    }

    return "lex";
  }

  public static function get_theme() {
    // default: default
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_theme'])) {
      return $app->config['_theme'];
    }

    return "default";
  }

  public static function get_theme_assets_path() {
    // default: default
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_theme_assets_path'])) {
      return $app->config['_theme_assets_path'];
    }

    return "";
  }

  public static function get_theme_path() {
    // default: default
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['theme_path'])) {
      return $app->config['theme_path'];
    }

    return "_themes/".self::get_theme_name()."/";
  }

  public static function get_templates_path() {
    // default: default
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['templates.path'])) {
      return $app->config['templates.path'];
    }

    return "./_themes/".self::get_theme_name()."/";
  }

  public static function get_admin_path() {
   $app = \Slim\Slim::getInstance();
    if (isset($app->config['_admin_path'])) {
      return HiCMS_Helper::reduce_double_slashes(ltrim($app->config['_admin_path'], '/').'/');
    }

    return "admin/";
  }

  public static function get_addon_path($addon=null) {
    $addon_path = HiCMS_Helper::reduce_double_slashes(self::get_site_root()."_add-ons/".$addon."/");
    return $addon_path;
  }

  public static function get_content_root() {
    // default: content
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_content_root'])) {
      return $app->config['_content_root'];
    }

    return "_content";
  }

  public static function get_content_type() {
    $app = \Slim\Slim::getInstance();

    $content_type = 'md'; # default: markdown
    if (isset($app->config['_content_type']) && $app->config['_content_type'] != 'markdown') {
      $content_type = $app->config['_content_type'] == "markdown_edge" ? "md" : $app->config['_content_type'];
    }
    return $content_type;
  }

  public static function get_date_format() {
    // default: Ymd
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_date_format'])) {
      return $app->config['_date_format'];
    }

    return "Y-m-d";
  }

  public static function get_time_format() {
    // default: Ymd
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_time_format'])) {
      return $app->config['_time_format'];
    }

    return "h:ia";
  }


  public static function get_entry_timestamps() {
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_entry_timestamps'])) {
      return (bool) $app->config['_entry_timestamps'];
    }

    return false;
  }

  public static function get_setting($setting, $default = false){

    $app = \Slim\Slim::getInstance();

    if (isset($app->config[$setting])) {
      return $app->config[$setting];
    }

    return $default;
  }

  public static function get_entry_type($path) {
    $type = 'none';

    $content_root = HiCMS::get_content_root();
    if (file_exists("{$content_root}/{$path}/fields.yaml")) { 

      $fields_raw = file_get_contents("{$content_root}/{$path}/fields.yaml");
      $fields_data = Spyc::YAMLLoad($fields_raw);

      if (isset($fields_data['type']) && ! is_array($fields_data['type'])) {
        $type = $fields_data['type']; # simplify, no "prefix" necessary
      } else if (isset($fields_data['type']['prefix'])) {
        $type = $fields_data['type']['prefix'];
      }
    }

    return $type;
  }

  # @todo: make recursive/helper
  public static function get_templates_list() {
    $templates = array();
    $folder = "_themes/".self::get_theme_name()."/templates/*";
    $list = glob($folder);
    if ($list) {
      foreach ($list as $name) {
        if (is_dir($name)) {
          $folder_array = explode('/',rtrim($name,'/'));
          $folder_name = end($folder_array);

          $sub_list = glob($name.'/*');
          
          foreach ($sub_list as $sub_name) {
            $start = strrpos($sub_name, "/")+1;
            $end = strrpos($sub_name, ".");
            $templates[] = $folder_name.'/'.substr($sub_name, $start, $end-$start);
          }
        } else {
          $start = strrpos($name, "/")+1;
          $end = strrpos($name, ".");
          $templates[] = substr($name, $start, $end-$start);
        }
      }
    }
    return $templates;
  }

  public static function get_layouts_list() {
    $templates = array();
    $folder = "_themes/".self::get_theme_name()."/layouts/*";
    $list = glob($folder);
    if ($list) {
      foreach ($list as $name) {
        $start = strrpos($name, "/")+1;
        $end = strrpos($name, ".");
        $templates[] = substr($name, $start, $end-$start);
      }
    }
    return $templates;
  }

  public static function get_pagination_variable() {
    $app = \Slim\Slim::getInstance();

    $var = 'page'; # default: page
    if (isset($app->config['_pagination_variable'])) {
      $var = $app->config['_pagination_variable'];
    }
    return $var;
  }

  public static function get_pagination_style() {
    $app = \Slim\Slim::getInstance();

    $var = 'prev_next'; # default: prev_next
    if (isset($app->config['_pagination_style'])) {
      $var = $app->config['_pagination_style'];
    }
    return $var;
  }

    public static function get_parse_order() {
    // default: default
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_parse_order'])) {
      return $app->config['_parse_order'];
    }

    return array('tags', 'content');
  }

  public static function is_content_writable() {
    $content_root = self::get_content_root();
    if (is_writable($content_root)) {
      return true;
    }
    return false;
  }

    public static function are_users_writable() {

    if (is_writable('_config/users/')) {
      return true;
    }
    return false;
  }

  public static function content_exists($slug, $folder=null) {
    $app = \Slim\Slim::getInstance();
    $initialize = $app->config;

    $site_root    = self::get_site_root();
    $content_root = self::get_content_root();
    $content_type = self::get_content_type();

    $meta_raw = "";
    if ($folder) {
      if (file_exists("{$content_root}/{$folder}/{$slug}.{$content_type}")) { 
        return true;
      }
    } else {
      if (file_exists("{$content_root}/{$slug}.{$content_type}")) { 
        return true;
      }
    }

    return false;
  }

  public static function get_content_meta($slug, $folder=null, $raw=false, $parse=true) {
    $app = \Slim\Slim::getInstance();
    $initialize = $app->config;

    $site_root    = self::get_site_root();
    $content_root = self::get_content_root();
    $content_type = self::get_content_type();

    $file = $folder ? "{$content_root}/{$folder}/{$slug}.{$content_type}" : "{$content_root}/{$slug}.{$content_type}";
    $file = HiCMS_Helper::reduce_double_slashes($file);

    $meta_raw = file_exists($file) ? file_get_contents($file) : '';

    if (HiCMS_Helper::ends_with($meta_raw, "---")) {
      $meta_raw .= "\n"; # prevent parse failure
    }
    # Parse YAML Front Matter
    if (stripos($meta_raw, "---") === FALSE) {
      //$meta = Spyc::YAMLLoad($meta_raw);
      $meta = array_merge(self::loadYamlCached($meta_raw), $app->config);
      $meta['content'] = "";
      if ($raw) {
        $meta['content_raw'] = "";
      }
    } else {
      list($yaml, $content) = preg_split("/---/", $meta_raw, 2, PREG_SPLIT_NO_EMPTY);
      $meta = self::loadYamlCached($yaml);
      
      if ($raw) {
        $meta['content_raw'] = $content;
      }
      
      // Parse the content if necessary
      $meta['content'] = $parse ? self::parse_content($content, $meta) : $content;
    }
    if (file_exists($file)) {
      $meta['last_modified'] = filemtime($file);  
    }
    
    if ( ! $raw) {
      $meta['homepage'] = self::get_site_root();
      $meta['raw_url']  = $app->request()->getResourceUri();
      $meta['page_url'] = $app->request()->getResourceUri();

      # Is date formatted correctly?
      if (self::get_entry_timestamps() && HiCMS_Helper::is_datetime_slug($slug)) {
        $datetimestamp = HiCMS_Helper::get_datetimestamp($slug);
        $datestamp = HiCMS_Helper::get_datestamp($slug);

        $meta['datetimestamp'] = $datetimestamp;
        $meta['datestamp'] = $datestamp;
        $meta['date']      = date(self::get_date_format(), $datestamp);
        $meta['time']      = date(self::get_time_format(), $datetimestamp);
        $meta['page_url']  = preg_replace(HiCMS_Helper::$datetime_regex, '', $meta['page_url']); # clean url override

      } else if (HiCMS_Helper::is_date_slug($slug)) {
        $datestamp = HiCMS_Helper::get_datestamp($slug);
        
        $meta['datestamp'] = $datestamp;
        $meta['date']      = date(self::get_date_format(), $datestamp);
        $meta['page_url']  = preg_replace(HiCMS_Helper::$date_regex, '', $meta['page_url']); # clean url override

      } else if (HiCMS_Helper::is_numeric_slug($slug)) {
        $meta['numeric'] = HiCMS_Helper::get_numeric($slug);
      }
      
      $meta['permalink'] = HiCMS_Helper::reduce_double_slashes(HiCMS::get_site_url().'/'.$meta['page_url']);


      $taxonomy_slugify = false;
      if (isset($app->config['_taxonomy_slugify'])) {
        if ($app->config['_taxonomy_slugify']) {
          $taxonomy_slugify = true;
        }
      }

      # Jam it all together, brother.
      # @todo: functionize/abstract this method for more flexibility and readability
      foreach($meta as $key => $value) {

        if (! is_array($value) && self::is_taxonomy($key)) {
          $value = array($value);
          $meta[$key] = $value;
        }

        if (is_array($value)) {
          $list = array();
          $url_list = array();

          $i = 1;
          $total_results = count($meta[$key]);
          foreach ($meta[$key] as $k => $v) {
            
            $url = null;
            if (self::is_taxonomy($key)) {

              // DO NOT DO numerical regex replace on the actual taxonomy item
              $url = HiCMS_Helper::reduce_double_slashes(strtolower($site_root.'/'.$folder.'/'.$key));
              $url = preg_replace(HiCMS_Helper::$numeric_regex, '', $url);
              if($taxonomy_slugify) {
                $url .= "/".(strtolower(HiCMS_Helper::slugify($v)));
              } else {
                $url .= "/".(strtolower($v));
              }


              $list[] = array(
                'name'  => $v,
                'count' => $i,
                'url'   => $url,
                'total_results' => $total_results,
                'first' => $i == 1 ? TRUE : FALSE,
                'last' => $i == $total_results ? TRUE : FALSE
              );
              
              $url_list[] = '<a href="'.$url.'">'.$v.'</a>'; 
            
            } elseif ( ! is_array($v)) {
              
              $list[] = array(
                'name'  => $v,
                'count' => $i,
                'url'   => $url,
                'total_results' => $total_results,
                'first' => $i == 1 ? TRUE : FALSE,
                'last' => $i == $total_results ? TRUE : FALSE
              );
            }

            $i++;

          }

          if ($url) {
            $meta[$key.'_url_list'] = implode(', ', $url_list);
          }
          if ( isset($meta[$key][0]) && ! is_array($meta[$key][0])) {
            $meta[$key.'_list'] = implode(', ', $meta[$key]);
            $meta[$key.'_option_list'] = implode('|', $meta[$key]);
            $meta[$key] = $list;
          }
        }
      }
    }  
    return $meta;
  }

  public static function get_content_list($folder=null,$limit=null,$offset=0,$future=false,$past=true,$sort_by='date',$sort_dir='desc',$conditions=null,$switch=null,$skip_status=false,$parse=true,$since=null,$until=null) {
    $app = \Slim\Slim::getInstance();

    $content_type = self::get_content_type();

    $folder_list = HiCMS_Helper::explode_options($folder);

    $list = array();
    foreach ($folder_list as $list_item) {
      $results = self::get_content_all($list_item, $future, $past, $conditions, $skip_status, $parse, $since, $until);
      $list = $list+$results;
    }

    // default sort is by date
    if ($sort_by == 'date') {
      uasort($list, 'hicms_sort_by_datetime');
    } else if ($sort_by == 'title') {
      uasort($list, "hicms_sort_by_title");
    } else if ($sort_by == 'random') {
      shuffle($list);
    } else if ($sort_by == 'numeric' || $sort_by == 'number') {
      ksort($list);
    } else if ($sort_by != 'date') {
      # sort by any other field
      uasort($list, function($a, $b) use ($sort_by) {
        if (isset($a[$sort_by]) && isset($b[$sort_by])) {
          return strcmp($b[$sort_by], $a[$sort_by]);
        }
      });
    }

    // default sort is asc
    if ($sort_dir == 'desc') {
      $list = array_reverse($list);
    }

    // handle offset/limit
    if ($offset > 0) {
      $list = array_splice($list, $offset);
    }

    if ($limit) {
      $list = array_splice($list, 0, $limit);
    }

    if ($switch) {
      $switch_vars = explode('|',$switch);
      $switch_count = count($switch_vars);

      $count = 1;
      foreach ($list as $key => $post) {
        $list[$key]['switch'] = $switch_vars[($count -1) % $switch_count];
        $count++;
      }
    }

    return $list;
  }

  public static function fetch_content_by_url($path) {
      $data = null;
      $content_root = HiCMS::get_content_root();
      $content_type = HiCMS::get_content_type();

      if (file_exists("{$content_root}/{$path}.{$content_type}") || is_dir("{$content_root}/{$path}")) {
        // endpoint or folder exists!
      } else {
        $path = HiCMS_Helper::resolve_path($path);
      }

      if (file_exists("{$content_root}/{$path}.{$content_type}")) {

        $page     = basename($path);
        $folder   = substr($path, 0, (-1*strlen($page))-1);

        $data = HiCMS::get_content_meta($page, $folder);
      } else if (is_dir("{$content_root}/{$path}")) {
        $data = HiCMS::get_content_meta("page", $path);
      }

      return $data;
  }

  public static function get_next_numeric($folder=null) {
    $next = '01';

    $list = self::get_content_all($folder, true, true, null, true);
    if (sizeof($list) > 0) {
      $item = array_pop($list);
      $current = $item['numeric'];
      if ($current <> '') {
        $next = $current + 1;
        $format= '%1$0'.strlen($current).'d';
        $next = sprintf($format, $next);
      }
    }

    return $next;
  }

  public static function get_next_numeric_folder($folder=null) {
    $next = '01';

    $list = self::get_content_tree($folder,1,1,true,false,true);
    if (sizeof($list) > 0) {
      $item = array_pop($list);
      if (isset($item['numeric'])) {
        $current = $item['numeric'];
        if ($current <> '') {
          $next = $current + 1;
          $format= '%1$0'.strlen($current).'d';
          $next = sprintf($format, $next);
        }
      }
    }

    return $next;
  }

  public static function get_content_count($folder=null,$future=false,$past=true,$conditions=null,$since=null,$until=null) {

    $folder_list = HiCMS_Helper::explode_options($folder);

    $list = array();
    foreach ($folder_list as $list_item) {
      $results = self::get_content_all($list_item, $future, $past, $conditions, false, false, $since, $until);
      $list = $list+$results;
    }

    return sizeof($list);
  }

  public static function get_content_all($folder=null,$future=false,$past=true,$conditions=null,$skip_status=false,$parse=true,$since=null,$until=null) {
    $app = \Slim\Slim::getInstance();

    $content_type = self::get_content_type();
    $site_root = self::get_site_root();

    $absolute_folder = HiCMS_Helper::resolve_path($folder);

    $posts = self::get_file_list($absolute_folder);
    $list = array();

    foreach ($posts as $key => $post) {
      // starts with numeric value
      unset($list[$key]);

      if ((preg_match(HiCMS_Helper::$date_regex, $key) || preg_match(HiCMS_Helper::$numeric_regex, $key)) && file_exists($post.".{$content_type}")) {

        $data = HiCMS::get_content_meta($key, $absolute_folder, false, $parse);

        $list[$key] = $data;

        $list[$key]['slug']    = $site_root.'/'.$key;
        $list[$key]['url']     = $folder ? $site_root.$folder."/".$key : $site_root.$key;

        $list[$key]['raw_url'] = $list[$key]['url'];
        
        $date_entry = false;
        if (self::get_entry_timestamps() && HiCMS_Helper::is_datetime_slug($key)) {
          $datestamp = HiCMS_Helper::get_datestamp($key);
          $date_entry = true;

          # strip the date

          $list[$key]['slug'] = ltrim(preg_replace(HiCMS_Helper::$datetime_regex, '', $key),'/');
          $list[$key]['url']  = preg_replace(HiCMS_Helper::$datetime_regex, '', $list[$key]['url']); #override
          
          $list[$key]['datestamp'] = $data['datestamp'];
          $list[$key]['date'] = $data['date'];

        } else if (HiCMS_Helper::is_date_slug($key)) {          
          $datestamp = HiCMS_Helper::get_datestamp($key);
          $date_entry = true;

          # strip the date
          $list[$key]['slug'] = substr($key, 11);
          $list[$key]['slug'] = ltrim(preg_replace(HiCMS_Helper::$date_regex, '', $key),'/');

          $list[$key]['url']  = preg_replace(HiCMS_Helper::$date_regex, '', $list[$key]['url']); #override

          $list[$key]['datestamp'] = $data['datestamp'];
          $list[$key]['date'] = $data['date'];

        } else {
          $list[$key]['slug'] = ltrim(preg_replace(HiCMS_Helper::$numeric_regex, '', $key),'/');
          $list[$key]['url']  = preg_replace(HiCMS_Helper::$numeric_regex, '', $list[$key]['url'], 1); #override
        }

        $list[$key]['url'] = HiCMS_Helper::reduce_double_slashes('/'.$list[$key]['url']);

        # fully qualified url
        $list[$key]['permalink'] = HiCMS_Helper::reduce_double_slashes(HiCMS::get_site_url().'/'.$list[$key]['url']);

        /* $content  = preg_replace('/<img(.*)src="(.*?)"(.*)\/?>/', '<img \/1 src="'.HiCMS::get_asset_path(null).'/\2" /\3 />', $data['content']); */
        //$list[$key]['content'] = HiCMS::transform_content($data['content']);

        if ( ! $skip_status) {
          if (isset($data['status']) && $data['status'] != 'live') {
            unset($list[$key]);
          }
        }

        // Remove future entries
        if ($date_entry && $future === FALSE && $datestamp > time()) {
          unset($list[$key]);
        }

        // Remove past entries
        if ($date_entry && $past === FALSE && $datestamp < time()) {
          unset($list[$key]);
        }

        // Remove entries before $since
        if ($date_entry && !is_null($since) && $datestamp < strtotime($since)) {
          unset($list[$key]);
        }

        // Remove entries after $until
        if ($date_entry && !is_null($until) && $datestamp > strtotime($until)) {
          unset($list[$key]);
        }

        if ($conditions) {
          $keepers = array();
          $conditions_array = explode(",", $conditions);
          foreach ($conditions_array as $condition) {
            $condition = trim($condition);
            $inclusive = true;

            list($condition_key, $condition_values) = explode(":", $condition);
            
            # yay php!
            $pos = strpos($condition_values, 'not ');
            if ($pos === FALSE) {
            } else { 
              if ($pos == 0) {
                $inclusive = false;
                $condition_values = substr($condition_values, 4);
              }
            }

            $condition_values = explode("|", $condition_values);

            foreach ($condition_values as $k => $condition_value) {
              $keep = false;
              if (isset($list[$key][$condition_key])) {
                if (is_array($list[$key][$condition_key])) {
                  foreach ($list[$key][$condition_key] as $key2 => $value2) {
                    #todo add regex driven taxonomy matching here

                    if ($inclusive) {

                      if (strtolower($value2['name']) == strtolower($condition_value)) {
                        $keepers[$key] = $key;
                        break;
                      }
                    } else {

                      if (strtolower($value2['name']) != strtolower($condition_value)) {
                        $keepers[$key] = $key;
                      } else {
                        // EXCLUDE!
                        unset($keepers[$key]);
                        break;
                      }
                    }
                  }
                } else {
                  if ($list[$key][$condition_key] == $condition_value)  {
                    if ($inclusive) {
                      $keepers[$key] = $key;
                    } else {
                      unset($keepers[$key]);
                    }

                 } else {
                    if ( ! $inclusive) {
                      $keepers[$key] = $key;
                    }
                  }
                }
              } else {
                $keep = false;
              }
            }
            if ( ! $keep && ! in_array($key, $keepers)) {
              unset($list[$key]);
            }
          }
        }
      }
    }

    return $list;
  }


  public static function get_content_tree($directory='/',$depth=1,$max_depth=5,$folders_only=false,$include_entries=false,$hide_hidden=true,$include_content=false,$site_root=false) {
    // $folders_only=true only page.md 
    // folders_only=false includes any numbered or non-numbered page (excluding anything with a fields.yaml file)
    // if include_entries is true then any numbered files are included
    $app = \Slim\Slim::getInstance();

    $content_root = self::get_content_root();
    $content_type = self::get_content_type();
    $site_root = $site_root ? $site_root : self::get_site_root();

    $current_url = HiCMS_Helper::reduce_double_slashes($site_root.'/'.$app->request()->getResourceUri());

    $taxonomy_url = FALSE;
    if (self::is_taxonomy_url($current_url)) {
      list($taxonomy_type, $taxonomy_name) = self::get_taxonomy_criteria($current_url);
      $taxonomy_url = self::remove_taxonomy_from_path($current_url, $taxonomy_type, $taxonomy_name);
    }

    $directory = '/'.$directory.'/'; #ensure proper slashing

    $base = ''; 
    if ($directory <> '/') {
      $base = HiCMS_Helper::reduce_double_slashes("{$content_root}/{$directory}");
    } else if ($directory == '/') {
      $base = "{$content_root}";
    } else {
      $base = "{$content_root}";
    }
    
    $files = glob("{$base}/*");


    $data = array();
    if ($files) {
      foreach($files as $path) {
        $current_name = basename($path);

        if (!HiCMS_Helper::starts_with($current_name, '_') && !HiCMS_Helper::ends_with($current_name, '.yaml')) {
          $node = array();
          $file = substr($path, strlen($base)+1, strlen($path)-strlen($base)-strlen($content_type)-2);

          if (is_dir($path)) {
            $folder = substr($path, strlen($base)+1);
            $node['type']     = 'folder';
            $node['slug']     = basename($folder);
            $node['title']    = ucwords(basename($folder));

            $node['numeric']  = HiCMS_Helper::get_numeric($folder);

            $node['file_path'] = HiCMS_Helper::reduce_double_slashes($site_root.'/'.$directory.'/'.$folder.'/page');
            
            if (HiCMS_Helper::is_numeric_slug($folder)) {
              $pos = stripos($folder, ".");
              if ($pos !== false) {
                $node['raw_url']      = HiCMS_Helper::reduce_double_slashes(
                                      HiCMS_Helper::remove_numerics_from_path(
                                        $site_root.'/'.$directory.'/'.$folder
                                      )
                                    );
                $node['url'] = rtrim(preg_replace(HiCMS_Helper::$numeric_regex, '', $node['raw_url']),'/');
                $node['title']    = ucwords(basename(substr($folder, $pos+1)));
              } else {
                $node['title']    = ucwords(basename($folder));
                $node['raw_url']      = HiCMS_Helper::reduce_double_slashes($site_root.'/'.$directory.'/'.$folder);
                $node['url'] = rtrim(preg_replace(HiCMS_Helper::$numeric_regex, '', $node['raw_url']),'/');
              }
            } else {
              $node['title']    = ucwords(basename($folder));
              $node['raw_url']      = HiCMS_Helper::reduce_double_slashes($site_root.'/'.$directory.'/'.$folder);
              $node['url'] = rtrim(preg_replace(HiCMS_Helper::$numeric_regex, '', $node['raw_url']), '/');
            }

            $node['depth']    = $depth;
            $node['children'] = $depth < $max_depth ? self::get_content_tree($directory.$folder.'/', $depth+1, $max_depth, $folders_only, $include_entries, $hide_hidden, $include_content, $site_root) : null;
            $node['is_current'] = $node['raw_url'] == $current_url || $node['url'] == $current_url ? TRUE : FALSE;
          
            $node['is_parent'] = FALSE;
            if ($node['url'] == HiCMS_Helper::pop_last_segment($current_url) || ($taxonomy_url && $node['url'] == $taxonomy_url)) {
              $node['is_parent'] = TRUE;
            }

            $node['has_children'] = $node['children'] ? TRUE : FALSE;

            // has entries?
            if (file_exists(HiCMS_Helper::reduce_double_slashes($path."/fields.yaml"))) {
              $node['has_entries'] = TRUE;
            } else {
              $node['has_entries'] = FALSE;
            }

            $meta = self::get_content_meta("page", HiCMS_Helper::reduce_double_slashes($directory."/".$folder), false, true); 
            //$meta = self::get_content_meta("page", HiCMS_Helper::reduce_double_slashes($directory."/".$folder)); 

            if (isset($meta['title'])) {
              $node['title'] = $meta['title'];
            }

            if (isset($meta['last_modified'])) {
              $node['last_modified'] = $meta['last_modified'];
            }

            if ($hide_hidden === true && (isset($meta['status']) && (($meta['status'] == 'hidden' || $meta['status'] == 'draft')))) {
              // placeholder condition
            } else {
              $data[] = $include_content ? array_merge($meta, $node) : $node;
              // print_r($data);
            }

          } else {
            if (HiCMS_Helper::ends_with($path, $content_type)) {
              if ($folders_only == false) {
                if ($file == 'page' || $file == 'feed' || $file == '404') {
                  // $node['url'] = $directory;
                  // $node['title'] = basename($directory);

                  // $meta = self::get_content_meta('page', substr($directory, 1));
                  // $node['depth'] = $depth;
                } else {
                  $include = true;

                  // date based is never included
                  if (self::get_entry_timestamps() && HiCMS_Helper::is_datetime_slug(basename($path))) {
                    $include = false;
                  } else if (HiCMS_Helper::is_date_slug(basename($path))) {
                      $include = false;
                  } else if (HiCMS_Helper::is_numeric_slug(basename($path))) {
                    if ($include_entries == false) {
                      if (file_exists(HiCMS_Helper::reduce_double_slashes(dirname($path)."/fields.yaml"))) {
                        $include = false;
                      }
                    }
                  }

                  if ($include) {
                    $node['type'] = 'file';
                    $node['raw_url'] = HiCMS_Helper::reduce_double_slashes($directory).basename($path);

                    $pretty_url = rtrim(preg_replace(HiCMS_Helper::$numeric_regex, '', $node['raw_url']),'/');
                    $node['url'] = substr($pretty_url, 0, -1*(strlen($content_type)+1));
                    $node['is_current'] = $node['url'] == $current_url || $node['url'] == $current_url ? TRUE : FALSE;

                    $node['slug'] = substr(basename($path), 0, -1*(strlen($content_type)+1));

                    $meta = self::get_content_meta(substr(basename($path), 0, -1*(strlen($content_type)+1)), substr($directory, 1), false, true);

                    //$node['meta'] = $meta;

                    if (isset($meta['title'])) $node['title'] = $meta['title'];
                    $node['depth'] = $depth;
                    
                    if ($hide_hidden === true && (isset($meta['status']) && (($meta['status'] == 'hidden' || $meta['status'] == 'draft')))) {
                    } else {
                      $data[] = $include_content ? array_merge($meta, $node) : $node;
                    }
                  }
                }
              }
            }
          }
        }
      }
    }

    return $data;
  }

  public static function get_file_list($directory=null) {
    $content_root = self::get_content_root();
    $content_type = self::get_content_type();

    if ($directory) {
      $files = glob("{$content_root}{$directory}/*.{$content_type}");
    } else {
      $files = glob('{$content_root}*.{$content_type}');
    }
    $posts = array();

    if ($files) {
      foreach ($files as $file) {
        $len = strlen($content_type);
        $len = $len + 1;
        $len = $len * -1;

        $key = substr(basename($file), 0, $len);
        // HiCMS_helper::reduce_double_slashes($key = '/'.$key);
        $posts[$key] = substr($file, 0, $len);
      }
    }
    return $posts;
  }

  public static function find_prev($current,$folder=null,$future=false,$past=true) {
    if ($folder == '') {
      $folder = '/';
    }

    $list = self::get_folder_list($folder, $future, $past);
    $keys = array_keys($list);
    $current_key = array_search($current, $keys);
    if ($current_key !== FALSE) {
      while (key($keys) !== $current_key) next($keys);
        return next($keys);
    }
    return FALSE;
  }

  public static function find_next($current,$folder=null,$future=false,$past=true) {
    if ($folder == '') {
      $folder = '/';
    }
    $list = self::get_folder_list($folder, $future, $past);
    $keys = array_keys($list);
    $current_key = array_search($current, $keys);
    if ($current_key !== FALSE) {
      while (key($keys) !== $current_key) next($keys);
        return prev($keys); 
    }

    return FALSE;
  }

  public static function get_asset_path($asset) {
    $content_root = self::get_content_root();
    $app = \Slim\Slim::getInstance();
    return "{$content_root}".$app->request()->getResourceUri().''.$asset;
  }

  public static function parse_content($template_data, $data) {

    $app = \Slim\Slim::getInstance();

    $data = array_merge($data, $app->config);

    $parser = new Lex_Parser();
    $parser->cumulative_noparse(true);
    $parser->scope_glue(':');

    $parse_order = HiCMS::get_parse_order();
    
    if ($parse_order[0] == 'tags') {
      $output = $parser->parse($template_data, $data);
      $output = self::transform_content($output);
    } else {
      $output = self::transform_content($template_data);
      $output = $parser->parse($output, $data);
    }

    return $output;
  }

  public static function yamlize_content($meta_raw, $content_key = 'content') {

    if (HiCMS_Helper::ends_with($meta_raw, "---")) {
      $meta_raw .= "\n"; # prevent parse failure
    }
    # Parse YAML Front Matter
    if (stripos($meta_raw, "---") === FALSE) {
      $meta = Spyc::YAMLLoad($meta_raw);
      $meta['content'] = "";
    } else {

      list($yaml, $content) = preg_split("/---/", $meta_raw, 2, PREG_SPLIT_NO_EMPTY);
      $meta = Spyc::YAMLLoad($yaml);
      $meta[$content_key.'_raw'] = trim($content);
      $meta[$content_key] = trim(HiCMS::transform_content($content));

      return $meta;
    }
  }

  public static function transform_content($content) {
    $content_type = self::get_content_type();

    if ($content_type == "markdown" || $content_type == 'md') {
      $content = Markdown($content);

    } else if ($content_type == 'html') {
      // no modifications

    } else if ($content_type == 'txt') {
      $content = nl2br(strip_tags($content));

    } else if ($content_type == 'textile') {
      $textile = new Textile();
      $content = $textile->TextileThis($content);
    }

    if (HiCMS::get_setting('_enable_smartypants', true) == true) {
      $content = SmartyPants($content);
    }

    return $content;
  }

  public static function is_taxonomy($tax) {
    $app = \Slim\Slim::getInstance();
    
    if (isset($app->config['_taxonomy'])) {
      $taxonomies = $app->config['_taxonomy'];
      if (in_array($tax, $taxonomies)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  public static function is_taxonomy_url($path) {
    $app = \Slim\Slim::getInstance();

    if (isset($app->config['_taxonomy'])) {
      $taxonomies = $app->config['_taxonomy'];

      $items = explode("/", $path); # get the last 2 segments of the path, format: /<taxonomy_type>/<slug>
      if (sizeof($items) > 2) {
        $slug = array_pop($items);
        $type = array_pop($items);

        foreach ($taxonomies as $key => $taxonomy) {
          if ($type == $taxonomy) {
            return TRUE;
          }
        }
      }
    }

    return FALSE;
  }

  public static function get_taxonomy_criteria($path) {
    $app = \Slim\Slim::getInstance();
    if (isset($app->config['_taxonomy'])) {
      $taxonomies = $app->config['_taxonomy'];
      // get the last 2 segments of the path
      // format: /<taxonomy_type>/<slug>
      $items = explode("/", $path);
      if (sizeof($items) > 2) {
        $slug = array_pop($items);
        $type = array_pop($items);

        foreach ($taxonomies as $key => $taxonomy) {

          if ($type == $taxonomy) {
            return array($type, $slug);
          }
        }
      }
    }

    return FALSE;
  }

  public static function remove_taxonomy_from_path($path, $type, $slug) {
    $return = $path;

    $return = substr($path, 0, -1 * strlen("/{$type}/{$slug}"));

    return $return;
  }

  public static function detect_environment(array $environments, $uri) {
    foreach ($environments as $environment => $patterns) {
      foreach ($patterns as $pattern) {
        if (HiCMS_Helper::is($pattern, $uri)) {
          return $environment;
        }
      }
    }
  }
}

function hicms_sort_by_title($a, $b) {
  return strcmp($a['title'], $b['title']);
}

function hicms_sorty_by_field($field, $a, $b) {
  if (isset($a[$field]) && isset($b[$field])) {
    return strcmp($a[$field], $b[$field]);  
  } else {
    return strcmp($a['title'], $b['title']);
  }
}

function hicms_sort_by_datetime($a, $b) {
  if (isset($a['datetimestamp']) && isset($b['datetimestamp'])) {
    return strcmp($a['datetimestamp'], $b['datetimestamp']);  
  } else if (isset($a['datestamp']) && isset($b['datestamp'])) {
    return strcmp($a['datestamp'], $b['datestamp']);
  }
}

class HiCMS_User {
  protected $data = array();
  protected $name = null;

  public function HiCMS_User($data) {
    $this->data = $data;
  }

  public function set_name($name) {
    $this->name = $name;
  }

  public function get_name() {
    return $this->name;
  }

  public function set_first_name($name) {
    $this->data['first_name'] = $name;
  }

  public function get_first_name() {
    if (isset($this->data['first_name'])) {
      return $this->data['first_name'];
    }

    return '';
  }

  public function set_password($password, $encrypted=false) {
    if ($encrypted) {

      if (!isset($this->data['salt']) || $this->data['salt'] == '') {
        $this->data['salt'] = HiCMS_Helper::random_string(32);
      }

      $encrypted_password = sha1($password.$this->data['salt']);
      $this->data['encrypted_password'] = $encrypted_password;
      $this->data['password'] = '';
    } else {
      $this->data['password'] = $password;
      $this->data['encrypted_password'] = '';
      $this->data['salt'] = '';
    }
  }

  public function get_last_name() {
    if (isset($this->data['last_name'])) {
      return $this->data['last_name'];
    }

    return '';
  }

  public function get_password() {
    if (isset($this->data['password'])) {
      return $this->data['password'];
    }
  }

  public function set_last_name($name) {
    $this->data['last_name'] = $name;
  }


  public function get_biography() {
    if (isset($this->data['biography'])) {
      return $this->data['biography'];
    }

    return '';
  }

  public function set_biography_raw($biography) {
    $this->data['biography_raw'] = $biography;
    $this->data['biography'] = HiCMS::transform_content($biography);
  }

  public function get_biography_raw() {
    if (isset($this->data['biography_raw'])) {
      return $this->data['biography_raw'];
    }

    return '';
  }

  public function set_roles($string) {
    $this->data['roles'] = explode(",", $string);
  }

  public function get_roles_list($delim=', ') {
    if (isset($this->data['roles'])) {
      return implode($delim, $this->data['roles']);
    }

    return '';
  }

  public function correct_password($password) {
    if (isset($this->data['password']) && $this->data['password'] <> '') {
      if ($this->data['password'] == $password) {
        return true;
      }
    } else if (isset($this->data['encrypted_password']) && $this->data['encrypted_password'] <> '') {
      $salt = "";
      if (isset($this->data['salt'])) {
        $salt = $this->data['salt'];
      }
      if (sha1($password.$salt) == $this->data['encrypted_password']) {
        return true;
      }
    }

    return false;
  }

  public function get_encrypted_password($value='') {
    $ep = '';
    if (isset($this->data['password'])) {
      // ### TODO ENCRYPT THE PASSWORD
      $ep = $this->data['password'];
    } else if (isset($this->data['encrypted_password'])) {
      $ep = $this->data['encrypted_password'];
    }

    return $ep;
  }

  public function is_password_encrypted() {
    if (isset($this->data['encrypted_password']) && $this->data['encrypted_password'] != "") {
      return true;
    }

    return false;
  }

  public function has_role($role) {
    if (isset($this->data['roles'])) {
      $roles = $this->data['roles'];

      if (in_array($role, $roles)) {
        return true;
      }
    }

    return false;
  }

  public function rename($name) {
    $file = "_config/users/{$this->name}.yaml";
    $new_file = "_config/users/{$name}.yaml";
    rename($file, $new_file);
  }


  public function save() {
    $file_content = "";
    $file_content .= "---\n";
    $file_content .= "first_name: {$this->data['first_name']}\n";
    $file_content .= "last_name: {$this->data['last_name']}\n";
    $file_content .= "roles: [".implode(",",$this->data['roles'])."]\n";

    if (isset($this->data['password'])) 
      $file_content .= "password: {$this->data['password']}\n";

    if (isset($this->data['encrypted_password'])) 
      $file_content .= "encrypted_password: {$this->data['encrypted_password']}\n";

    if (isset($this->data['salt'])) 
      $file_content .= "salt: {$this->data['salt']}\n";
    
    $file_content .= "---\n";
    $file_content .= $this->data['biography_raw'];
    $file_content .= "\n";

    $file = "_config/users/{$this->name}.yaml";
    file_put_contents($file, $file_content);
  }

  public function delete() {
    $file = "_config/users/{$this->name}.yaml";
    unlink($file);
  }


  // STATIC FUNCTIONS
  // ------------------------------------------------------
  public static function load($username) {

    $meta_raw = "";
    if (file_exists("_config/users/{$username}.yaml")) { 
      $meta_raw = file_get_contents("_config/users/{$username}.yaml");
    } else {
      return null;
    }

    if (HiCMS_Helper::ends_with($meta_raw, "---")) {
      $meta_raw .= "\n"; # prevent parse failure
    }
    # Parse YAML Front Matter
    if (stripos($meta_raw, "---") === FALSE) {
      $meta = Spyc::YAMLLoad($meta_raw);
      $meta['content'] = "";
    } else {

      list($yaml, $content) = preg_split("/---/", $meta_raw, 2, PREG_SPLIT_NO_EMPTY);
      $meta = Spyc::YAMLLoad($yaml);
      $meta['biography_raw'] = trim($content);
      $meta['biography'] = trim(HiCMS::transform_content($content));

      $u = new HiCMS_User($meta);
      $u->set_name($username);
      return $u;
    }
  }

  public static function get_profile($username) {

    if (file_exists("_config/users/{$username}.yaml")) { 
      $protected_fields = array_fill_keys(
          array('password', 'encrypted_password', 'salt'),
        null);

      $profile_content = file_get_contents("_config/users/{$username}.yaml");
      $profile_data = HiCMS::yamlize_content($profile_content, 'biography');

      return array_diff_key($profile_data, $protected_fields);

    }
    return null;
  }

}
/**
 * HiCMS_Validate
 * Provides validation utility functionality for HiCMS
 *
 */
class HiCMS_Validate {

  /**
   * required
   * Checks to see that a given $field's value exists
   *
   * @param array  $data  List of data
   * @param string  $field  Key within the array to check
   * @return boolean
   */
  public static function required($data, $field) {
    return isset($data[$field]);
  }


  /**
   * numeric
   * Checks to see if a given $field's value is numeric
   *
   * @param array  $data  List of data
   * @param string  $field  Key within the array to check
   * @return boolean
   */
  public static function numeric($data, $field) {
    return (isset($data[$field]) && is_numeric($data[$field]));
  }


  /**
   * date
   * Checks to see if a given $field's value is a date in a given $format
   *
   * @param array  $data  List of data
   * @param string  $field  Key within the array to check
   * @return boolean
   */
  public static function date($data, $field, $format="Y-m-d") {
    if (isset($data[$field])) {
      $value = $data[$field];
      $converted = strtotime($value);
      if ($value == date($format, $converted)) {
        return true;
      }
    }
    return false;
  }


  /**
   * folder_slug_exists
   * Checks to see if a given folder $slug exists within any of the given $folders
   *
   * @param array  $folders  List of folders to look through
   * @param string  $slug  Slug to check for
   * @return boolean
   */
  public static function folder_slug_exists($folders, $slug) {
    foreach ($folders as $key => $entry) {
      $nslug = substr(HiCMS_Helper::remove_numerics_from_path($entry['slug']), 2);
      if ($nslug == $slug) {
        return true;
      }
    }
    return false;
  }


  /**
   * content_slug_exists
   * Checks to see if a given content $slug exists with any of the given $entries
   *
   * @param array  $entries  List of entries to look through
   * @param string  $slug  Slug to check for
   * @return boolean
   */
  public static function content_slug_exists($entries, $slug) {
    foreach ($entries as $key => $entry) {
      //rint " CHECKING {$slug} against {$entry['slug']}";
      if ($entry['slug'] == $slug) {
        return true;
      }
    }
    return false;
  }


  /**
   * _test
   * Unit-testing for this object
   *
   * @return void
   */
  public static function _test()
  {
    $data = array();
    $data['first_name'] = 'John';
    $data['last_name'] = 'Doe';
    $data['valid_age'] = 33;
    $data['invalid_age'] = 'a';
    $data['valid_dob'] = "1970-10-01";
    $data['invalid_dob'] = "1970-14-01";

    // required
    if (self::required($data, 'first_name')) {
      print "\nRequired field: first_name is valid";
    } else {
      print "\nRequired field: first_name is not valid";
    }

    if (self::required($data, 'middle_name')) {
      print "\nRequired field: middle is valid";
    } else {
      print "\nRequired field: middle is not valid";
    }

    // numeric
    if (self::numeric($data, 'valid_age')) {
      print "\nNumeric field: valid_age is valid";
    } else {
      print "\nNumeric field: valid_age is not valid";
    }

    if (self::numeric($data, 'invalid_age')) {
      print "\nNumeric field: invalid_age is valid";
    } else {
      print "\nNumeric field: invalid_age is not valid";
    }

    // date
    if (self::date($data, 'valid_dob')) {
      print "\nDate field: valid_dob is valid";
    } else {
      print "\nDate field: valid_dob is not valid";
    }

    if (self::date($data, 'invalid_dob')) {
      print "\nDate field: invalid_dob is valid";
    } else {
      print "\nDate field: invalid_dob is not valid";
    }
  }
}

// TO TEST
// HiCMS_Validate::_test();<?php
/**
 * HiCMS_View
 * Manages display rendering within HiCMS
 *
 */
class HiCMS_View extends \Slim\View
{
  static protected  $_layout     = NULL;
  static protected  $_templates  = NULL;
  static public     $_dataStore  = array();


  /**
   * __construct
   * Starts up HiCMS_View
   *
   * @return void
   */
  public function __construct() {
    Lex_Autoloader::register();
    $this->parser = new Lex_Parser();
    $this->parser->scope_glue(':');
    $this->parser->cumulative_noparse(true);
  }


  /**
   * set_templates
   * Interface for setting templates
   *
   * @param mixed  $list  Template (or array of templates, in order of preference) to use for page render
   * @return void
   */
  public static function set_templates($list) {
    self::$_templates = $list;
  }


  /**
   * set_layout
   * Interface for setting page layout
   *
   * @param string  $layout  Layout to use for page render
   * @return void
   */
  public static function set_layout($layout=NULL) {
    self::$_layout = $layout;
  }


  /**
   * render
   * Finds and chooses the correct template, then renders the page
   *
   * @param string  $template  Template (or array of templates, in order of preference) to render the page with
   * @return string
   */
  public function render($template) {
    $html = '<p style="text-align:center; font-size:28px; font-style:italic; padding-top:50px;">No template found.</p>';
    if ($template) {
      $list = array($template);
    } else {
      $list = self::$_templates;
    }

    $allow_php = HiCMS::get_setting('_allow_php', false);
    
    foreach ($list as $template) {
      $template_path = $this->getTemplatesDirectory() . '/templates/' . ltrim($template, '/');
      $template_type = 'html';

      if (file_exists($template_path.'.html') || file_exists($template_path.'.php')) {
        # standard lex-parsed template
        if (file_exists($template_path.'.html')) {
          
          HiCMS_View::$_dataStore = array_merge(HiCMS_View::$_dataStore, $this->data);
          $html = $this->parser->parse(file_get_contents($template_path.'.html'), HiCMS_View::$_dataStore, array($this, 'callback'), $allow_php);

        # lets forge into raw data
        } elseif (file_exists($template_path.'.php')) {

          $template_type = 'php';

          extract($this->data);
          ob_start();
          require $template_path.".php";
          $html = ob_get_clean();
     
        } else {

          # you broke it.
          throw new RuntimeException('View cannot render template `' . $template_path . '`. Template does not exist.');
        }

        break;          
      }
    }

    return $this->_render_layout($html, $template_type);
  }

  
  /**
   * _render_layout
   * Renders the page
   *
   * @param string  $_html  HTML of the template to use
   * @param string  $template_type  Content type of the template
   * @return string
   */
  public function _render_layout($_html, $template_type='html') {
      if (self::$_layout <> '') {

        $this->data['layout_content'] = $_html;
        $layout_path = $this->getTemplatesDirectory() . '/' . ltrim(self::$_layout, '/');

        if ($template_type == 'html') {

          if ( ! file_exists($layout_path.".html")) {
            return '<p style="text-align:center; font-size:28px; font-style:italic; padding-top:50px;">We can\'t find your theme files. Please check your settings.';
          }

          
          HiCMS_View::$_dataStore = array_merge(HiCMS_View::$_dataStore, $this->data);
          $html = $this->parser->parse(file_get_contents($layout_path.".html"), HiCMS_View::$_dataStore, array($this, 'callback'), true);
          $html = Lex_Parser::inject_noparse($html);
         
        } else {

          extract($this->data);
          ob_start();
          require $layout_path.".php";
          $html = ob_get_clean();
        }

        return $html;

      }
      
      return $_html;
  }


  /**
   * callback
   * Attempts to load a plugin?
   *
   * @param string  $name
   * @param array  $attributes
   * @param string  $content
   * @return string
   */
  public static function callback($name, $attributes, $content) {
    Lex_Autoloader::register();
    $parser = new Lex_Parser();
    $parser->scope_glue(':');
    $parser->cumulative_noparse(true);

    $output = null;

    # single function plugins
    if (strpos($name, ':') === FALSE) {
      
      $plugin = $name;
      $call   = "index";

    } else {
      
      $pieces = explode(':', $name, 2);
      
      # no function exists
      if (count($pieces) != 2) return NULL;
        
      $plugin = $pieces[0];
      $call   = $pieces[1];
    }

    # check the plugin directories
    $plugin_folders = array('_add-ons/', '_hicms/_hicms_plugin/');
    foreach ($plugin_folders as $folder) {

      if (is_dir($folder.$plugin) && is_file($folder.$plugin.'/pi.'.$plugin.'.php')) {
      
        $file = $folder.$plugin.'/pi.'.$plugin.'.php';
        break;
      
      } elseif (is_file($folder.'/pi.'.$plugin.'.php')) {
      
        $file = $folder.'/pi.'.$plugin.'.php';
        break;
      }
    }

    # plugin exists
    if (isset($file)) {

      require_once($file);
      $class = 'Plugin_'.$plugin;

      #formatted properly
      if (class_exists($class)) {
        $plug = new $class();
      }

      $output = false;

      # function exists
      if (method_exists($plug, $call)) {
        $plug->attributes = $attributes;
        $plug->content    = $content;

        $output = $plug->$call();
      } elseif (class_exists($class) && ! method_exists($plug, $call)) {
        $output = $class::$call();
      } 
        
      if (is_array($output)) {
        $output = $parser->parse($content, $output, array('HiCMS_View', 'callback'));
      }
    }
    return $output;
    
  }
}