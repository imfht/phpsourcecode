<?php
namespace TYPO3;

/**
 * Created by PhpStorm.
 * Author: BoBo
 * For Typo3: page.includeLibs.common = fileadmin/scripts/common.php
 * For Normal: require_once("your path/common.php");
 * HOW USE:
 * * use common;
 * * common::resizeAndCropImage($source, $destination, $width, $height);
 */
class common{

    protected function ____COMMON____(){}

    /*************************
     *
     * GET/POST Variables
     *
     * Background:
     * Input GET/POST variables in PHP may have their quotes escaped with "\" or not depending on configuration.
     * TYPO3 has always converted quotes to BE escaped if the configuration told that they would not be so.
     * But the clean solution is that quotes are never escaped and that is what the functions below offers.
     * Eventually TYPO3 should provide this in the global space as well.
     * In the transitional phase (or forever..?) we need to encourage EVERY to read and write GET/POST vars through the API functions below.
     *
     *************************/
    /**
     * Returns the 'GLOBAL' value of incoming data from POST or GET, with priority to POST (that is equalent to 'GP' order)
     * Strips slashes from all output, both strings and arrays.
     * To enhancement security in your scripts, please consider using \TYPO3\CMS\Core\Utility\GeneralUtility::_GET or \TYPO3\CMS\Core\Utility\GeneralUtility::_POST if you already
     * know by which method your data is arriving to the scripts!
     *
     * @param string $var GET/POST var to return
     * @return mixed POST var named $var and if not set, the GET var of the same name.
     */
    static public function _GP($var) {
        if (empty($var)) {
            return '';
        }
        $value = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];
        if (isset($value)) {
            if (is_array($value)) {
                self::stripSlashesOnArray($value);
            } else {
                $value = stripslashes($value);
            }
        }
        return $value;
    }

    /**
     * StripSlash array
     * This function traverses a multidimensional array and strips slashes to the values.
     * NOTE that the input array is and argument by reference.!!
     * Twin-function to addSlashesOnArray
     *
     * @param array $theArray Multidimensional input array, (REFERENCE!)
     * @return array
     */
    static public function stripSlashesOnArray(array &$theArray) {
        foreach ($theArray as &$value) {
            if (is_array($value)) {
                self::stripSlashesOnArray($value);
            } else {
                $value = stripslashes($value);
            }
        }
        unset($value);
        reset($theArray);
    }

    /**
     * Explodes a string and trims all values for whitespace in the ends.
     * If $onlyNonEmptyValues is set, then all blank ('') values are removed.
     *
     * @param string $delim Delimiter string to explode with
     * @param string $string The string to explode
     * @param boolean $removeEmptyValues If set, all empty values will be removed in output
     * @param integer $limit If positive, the result will contain a maximum of
     * @return array Exploded values
     */
    static public function trimExplode($delim, $string, $removeEmptyValues = FALSE, $limit = 0) {
        $result = array_map('trim', explode($delim, $string));
        if ($removeEmptyValues) {
            $temp = array();
            foreach ($result as $value) {
                if ($value !== '') {
                    $temp[] = $value;
                }
            }
            $result = $temp;
        }
        if ($limit > 0 && count($result) > $limit) {
            $lastElements = array_slice($result, $limit - 1);
            $result = array_slice($result, 0, $limit - 1);
            $result[] = implode($delim, $lastElements);
        } elseif ($limit < 0) {
            $result = array_slice($result, 0, $limit);
        }
        return $result;
    }

    /**
     * Explodes a $string delimited by $delim and casts each item in the array to (int).
     * Corresponds to \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(), but with conversion to integers for all values.
     *
     * @param string $delimiter Delimiter string to explode with
     * @param string $string The string to explode
     * @param boolean $removeEmptyValues If set, all empty values (='') will NOT be set in output
     * @param integer $limit If positive, the result will contain a maximum of limit elements,
     * @return array Exploded values, all converted to integers
     */
    static public function intExplode($delimiter, $string, $removeEmptyValues = FALSE, $limit = 0) {
        $result = explode($delimiter, $string);
        foreach ($result as $key => &$value) {
            if ($removeEmptyValues && ($value === '' || trim($value) === '')) {
                unset($result[$key]);
            } else {
                $value = (int)$value;
            }
        }
        unset($value);
        if ($limit !== 0) {
            if ($limit < 0) {
                $result = array_slice($result, 0, $limit);
            } elseif (count($result) > $limit) {
                $lastElements = array_slice($result, $limit - 1);
                $result = array_slice($result, 0, $limit - 1);
                $result[] = implode($delimiter, $lastElements);
            }
        }
        return $result;
    }

    protected function ____TEMPLATE____(){}
    /**
     * Returns the first subpart encapsulated in the marker, $marker
     * (possibly present in $content as a HTML comment)
     *
     * @param string $content Content with subpart wrapped in fx. "###CONTENT_PART###" inside.
     * @param string $marker Marker string, eg. "###CONTENT_PART###
     * @return string
     */
    static public function getSubpart($content, $marker) {
        $start = strpos($content, $marker);
        if ($start === FALSE) {
            return '';
        }
        $start += strlen($marker);
        $stop = strpos($content, $marker, $start);
        // Q: What shall get returned if no stop marker is given
        // Everything till the end or nothing?
        if ($stop === FALSE) {
            return '';
        }
        $content = substr($content, $start, $stop - $start);
        $matches = array();
        if (preg_match('/^([^\\<]*\\-\\-\\>)(.*)(\\<\\!\\-\\-[^\\>]*)$/s', $content, $matches) === 1) {
            return $matches[2];
        }
        // Resetting $matches
        $matches = array();
        if (preg_match('/(.*)(\\<\\!\\-\\-[^\\>]*)$/s', $content, $matches) === 1) {
            return $matches[1];
        }
        // Resetting $matches
        $matches = array();
        if (preg_match('/^([^\\<]*\\-\\-\\>)(.*)$/s', $content, $matches) === 1) {
            return $matches[2];
        }
        return $content;
    }

    /**
     * Substitutes a subpart in $content with the content of $subpartContent.
     *
     * @param string $content Content with subpart wrapped in fx. "###CONTENT_PART###" inside.
     * @param string $marker Marker string, eg. "###CONTENT_PART###
     * @param array $subpartContent If $subpartContent happens to be an array, it's [0] and [1] elements are wrapped around the content of the subpart (fetched by getSubpart())
     * @param boolean $recursive If $recursive is set, the function calls itself with the content set to the remaining part of the content after the second marker. This means that proceding subparts are ALSO substituted!
     * @param boolean $keepMarker If set, the marker around the subpart is not removed, but kept in the output
     * @return string Processed input content
     */
    static public function substituteSubpart($content, $marker, $subpartContent, $recursive = TRUE, $keepMarker = FALSE) {
        $start = strpos($content, $marker);
        if ($start === FALSE) {
            return $content;
        }
        $startAM = $start + strlen($marker);
        $stop = strpos($content, $marker, $startAM);
        if ($stop === FALSE) {
            return $content;
        }
        $stopAM = $stop + strlen($marker);
        $before = substr($content, 0, $start);
        $after = substr($content, $stopAM);
        $between = substr($content, $startAM, $stop - $startAM);
        if ($recursive) {
            $after = self::substituteSubpart($after, $marker, $subpartContent, $recursive, $keepMarker);
        }
        if ($keepMarker) {
            $matches = array();
            if (preg_match('/^([^\\<]*\\-\\-\\>)(.*)(\\<\\!\\-\\-[^\\>]*)$/s', $between, $matches) === 1) {
                $before .= $marker . $matches[1];
                $between = $matches[2];
                $after = $matches[3] . $marker . $after;
            } elseif (preg_match('/^(.*)(\\<\\!\\-\\-[^\\>]*)$/s', $between, $matches) === 1) {
                $before .= $marker;
                $between = $matches[1];
                $after = $matches[2] . $marker . $after;
            } elseif (preg_match('/^([^\\<]*\\-\\-\\>)(.*)$/s', $between, $matches) === 1) {
                $before .= $marker . $matches[1];
                $between = $matches[2];
                $after = $marker . $after;
            } else {
                $before .= $marker;
                $after = $marker . $after;
            }
        } else {
            $matches = array();
            if (preg_match('/^(.*)\\<\\!\\-\\-[^\\>]*$/s', $before, $matches) === 1) {
                $before = $matches[1];
            }
            if (is_array($subpartContent)) {
                $matches = array();
                if (preg_match('/^([^\\<]*\\-\\-\\>)(.*)(\\<\\!\\-\\-[^\\>]*)$/s', $between, $matches) === 1) {
                    $between = $matches[2];
                } elseif (preg_match('/^(.*)(\\<\\!\\-\\-[^\\>]*)$/s', $between, $matches) === 1) {
                    $between = $matches[1];
                } elseif (preg_match('/^([^\\<]*\\-\\-\\>)(.*)$/s', $between, $matches) === 1) {
                    $between = $matches[2];
                }
            }
            $matches = array();
            // resetting $matches
            if (preg_match('/^[^\\<]*\\-\\-\\>(.*)$/s', $after, $matches) === 1) {
                $after = $matches[1];
            }
        }
        if (is_array($subpartContent)) {
            $between = $subpartContent[0] . $between . $subpartContent[1];
        } else {
            $between = $subpartContent;
        }
        return $before . $between . $after;
    }

    /**
     * Traverses the input $markContentArray array and for each key the marker
     * by the same name (possibly wrapped and in upper case) will be
     * substituted with the keys value in the array. This is very useful if you
     * have a data-record to substitute in some content. In particular when you
     * use the $wrap and $uppercase values to pre-process the markers. Eg. a
     * key name like "myfield" could effectively be represented by the marker
     * "###MYFIELD###" if the wrap value was "###|###" and the $uppercase
     * boolean TRUE.
     *
     * @param string $content The content stream, typically HTML template content.
     * @param array $markContentArray The array of key/value pairs being marker/content values used in the substitution. For each element in this array the function will substitute a marker in the content stream with the content.
     * @param string $wrap A wrap value - [part 1] | [part 2] - for the markers before substitution
     * @param boolean $uppercase If set, all marker string substitution is done with upper-case markers.
     * @param boolean $deleteUnused If set, all unused marker are deleted.
     * @return string The processed output stream
     * @see substituteMarker(), substituteMarkerInObject(), TEMPLATE()
     */
    static public function substituteMarkerArray($content, $markContentArray, $wrap = '', $uppercase = FALSE, $deleteUnused = FALSE) {
        if (is_array($markContentArray)) {
            $wrapArr = self::trimExplode('|', $wrap);
            $search = array();
            $replace = array();
            foreach ($markContentArray as $marker => $markContent) {
                if ($uppercase) {
                    // use strtr instead of strtoupper to avoid locale problems with Turkish
                    $marker = strtr($marker, 'abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
                }
                if (count($wrapArr) > 0) {
                    $marker = $wrapArr[0] . $marker . $wrapArr[1];
                }
                $search[] = $marker;
                $replace[] = $markContent;
            }
            $content = str_replace($search, $replace, $content);
            unset($search, $replace);
            if ($deleteUnused) {
                if (empty($wrap)) {
                    $wrapArr = array('###', '###');
                }
                $content = preg_replace('/' . preg_quote($wrapArr[0], '/') . '([A-Z0-9_|\\-]*)' . preg_quote($wrapArr[1], '/') . '/is', '', $content);
            }
        }
        return $content;
    }


    protected function ____IMAGE____(){}

    /**
     * resize the image
     * @param $source
     * @param $destination
     * @param string $width
     * @param string $height
     * @param int $quality
     */
    static public function resizeImage($source, $destination, $width = '',$height = '',$quality=100) {
        if(is_file($source)) {
            list($source_width,$source_height) = getimagesize($source);
            if(($width == '') && ($height != '')) {
                $width = ($source_width * $height) / $source_height;
            }
            if(($width != '') && ($height == '')) {
                $height = ($source_height * $width) / $source_width;
            }
            if(($width == '') && ($height == '')) {
                $width = $source_width;
                $height = $source_height;
            }
            if($width!=''&&$height!=''){
                $imgAimRate = $width / $height;
                if ($source_width / $imgAimRate <= $source_height) {
                    $width = ($source_width * $height) / $source_height;
                }else {
                    $height = ($source_height * $width) / $source_width;
                }
            }
            $created = '';
            $path_parts = pathinfo($source);
            switch(strtolower($path_parts["extension"])) {
                case "jpg": case "jpeg": $created = imagecreatefromjpeg($source); break;
                case "gif": $created = imagecreatefromgif($source);  break;
                case "png": $created = imagecreatefrompng($source);  break;
            }
            if($created){
                $im = imagecreatetruecolor($width,$height);
                imagecopyresampled($im,$created,0,0,0,0,$width,$height,$source_width,$source_height);
                $path_parts = pathinfo($destination);
                switch(strtolower($path_parts["extension"])) {
                    case "gif" :
                        imagegif($im,$destination);
                        break;
                    case "png" :
                        $quality = ceil($quality/10) - 1;
                        imagepng($im,$destination,$quality);
                        break;
                    default    :
                        imagejpeg($im,$destination,$quality);
                        break;
                }
            }
        }
    }

    /**
     * crop the image, default center
     */
    static public function cropImage($source, $destination, $width, $height, $x=0, $y=0, $quality=100) {
        if(is_file($source)) {
            $created = '';
            $path_parts = pathinfo($source);
            switch(strtolower($path_parts["extension"])) {
                case "jpg": case "jpeg": $created = imagecreatefromjpeg($source); break;
                case "gif": $created = imagecreatefromgif($source);  break;
                case "png": $created = imagecreatefrompng($source);  break;
            }
            if($created){
                $im = imagecreatetruecolor($width,$height);
                imagecopy($im,$created,0,0,$x,$y,$width,$height);
                $path_parts = pathinfo($destination);
                switch(strtolower($path_parts["extension"])) {
                    case "gif" :
                        imagegif($im,$destination);
                        break;
                    case "png" :
                        $quality = ceil($quality/10) - 1;
                        imagepng($im,$destination,$quality);
                        break;
                    default    :
                        imagejpeg($im,$destination,$quality);
                        break;
                }
            }
        }
    }

    /*
     * first resize, then crop
     */
    static public function resizeAndCropImage($source, $destination, $width, $height, $quality=100){
        if(!is_file($destination)||(is_file($destination)&&(time()-filemtime($destination))>3600)||isset($_GET['updateCache'])){
            if(is_file($source)) {
                list($source_width,$source_height) = getimagesize($source);
                if ($source_width <= $width && $source_height <= $height){
                    copy($source, $destination);
                }else{
                    //resize first
                    $imgAimRate = $width / $height;
                    if ($source_width / $imgAimRate <= $source_height) {
                        self::resizeImage($source, $destination, $width);
                    }else {
                        self::resizeImage($source, $destination, '', $height);
                    }
                    //crop second
                    list($tempWidth, $tempHeight) = getimagesize($destination);
                    if ($tempWidth > $width) {
                        self::cropImage($destination, $destination, $width, $tempHeight, ($tempWidth-$width)/2, 0, $quality);
                    }else {
                        self::cropImage($destination, $destination, $tempWidth, $height, 0, ($tempHeight-$height)/2, $quality);
                    }
                }
            }
        }
    }
}