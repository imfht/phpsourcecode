<?php
/**
 * AndroidManifest.xml解析相关
 * ---------------------------------------------
 * @Author: 80056038
 * @Date: 2012-5-22
 * @package
 * @name :Cast.php
 * @version v1.0
 *
 */
namespace APKParser;
class Cast {
	public static final function toCharSequence($string) {
		if ($string==null) {
			return null;
		}
		return new CSString($string);
	}
}

?>