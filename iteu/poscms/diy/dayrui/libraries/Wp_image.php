<?php


/**
 * Base WordPress Image Editor
 *
 * @package WordPress
 * @subpackage Image_Editor
 */

/**
 * Base image editor class from which implementations extend
 *
 * @since 3.5.0
 */
abstract class WP_Image_Editor {
    protected $file = null;
    protected $size = null;
    protected $mime_type = null;
    protected $default_mime_type = 'image/jpeg';
    protected $quality = false;
    protected $default_quality = 90;

    /**
     * Each instance handles a single file.
     */
    public function __construct( $file ) {
        $this->file = $file;
    }

    /**
     * Checks to see if current environment supports the editor chosen.
     * Must be overridden in a sub-class.
     *
     * @since 3.5.0
     *
     * @static
     * @access public
     * @abstract
     *
     * @param array $args
     * @return bool
     */
    public static function test( $args = array() ) {
        return false;
    }

    /**
     * Checks to see if editor supports the mime-type specified.
     * Must be overridden in a sub-class.
     *
     * @since 3.5.0
     *
     * @static
     * @access public
     * @abstract
     *
     * @param string $mime_type
     * @return bool
     */
    public static function supports_mime_type( $mime_type ) {
        return false;
    }

    /**
     * Loads image from $this->file into editor.
     *
     * @since 3.5.0
     * @access protected
     * @abstract
     *
     * @return bool|WP_Error True if loaded; WP_Error on failure.
     */
    abstract public function load();

    /**
     * Saves current image to file.
     *
     * @since 3.5.0
     * @access public
     * @abstract
     *
     * @param string $destfilename
     * @param string $mime_type
     * @return array|WP_Error {'path'=>string, 'file'=>string, 'width'=>int, 'height'=>int, 'mime-type'=>string}
     */
    abstract public function save( $destfilename = null, $mime_type = null );

    /**
     * Resizes current image.
     *
     * At minimum, either a height or width must be provided.
     * If one of the two is set to null, the resize will
     * maintain aspect ratio according to the provided dimension.
     *
     * @since 3.5.0
     * @access public
     * @abstract
     *
     * @param  int|null $max_w Image width.
     * @param  int|null $max_h Image height.
     * @param  bool     $crop
     * @return bool|WP_Error
     */
    abstract public function resize( $max_w, $max_h, $crop = false );

    /**
     * Resize multiple images from a single source.
     *
     * @since 3.5.0
     * @access public
     * @abstract
     *
     * @param array $sizes {
     *     An array of image size arrays. Default sizes are 'small', 'medium', 'large'.
     *
     *     @type array $size {
     *         @type int  $width  Image width.
     *         @type int  $height Image height.
     *         @type bool $crop   Optional. Whether to crop the image. Default false.
     *     }
     * }
     * @return array An array of resized images metadata by size.
     */
    abstract public function multi_resize( $sizes );

    /**
     * Crops Image.
     *
     * @since 3.5.0
     * @access public
     * @abstract
     *
     * @param int $src_x The start x position to crop from.
     * @param int $src_y The start y position to crop from.
     * @param int $src_w The width to crop.
     * @param int $src_h The height to crop.
     * @param int $dst_w Optional. The destination width.
     * @param int $dst_h Optional. The destination height.
     * @param bool $src_abs Optional. If the source crop points are absolute.
     * @return bool|WP_Error
     */
    abstract public function crop( $src_x, $src_y, $src_w, $src_h, $dst_w = null, $dst_h = null, $src_abs = false );

    /**
     * Rotates current image counter-clockwise by $angle.
     *
     * @since 3.5.0
     * @access public
     * @abstract
     *
     * @param float $angle
     * @return bool|WP_Error
     */
    abstract public function rotate( $angle );

    /**
     * Flips current image.
     *
     * @since 3.5.0
     * @access public
     * @abstract
     *
     * @param bool $horz Flip along Horizontal Axis
     * @param bool $vert Flip along Vertical Axis
     * @return bool|WP_Error
     */
    abstract public function flip( $horz, $vert );

    /**
     * Streams current image to browser.
     *
     * @since 3.5.0
     * @access public
     * @abstract
     *
     * @param string $mime_type
     * @return bool|WP_Error
     */
    abstract public function stream( $mime_type = null );

    /**
     * Gets dimensions of image.
     *
     * @since 3.5.0
     * @access public
     *
     * @return array {'width'=>int, 'height'=>int}
     */
    public function get_size() {
        return $this->size;
    }

    /**
     * Sets current image size.
     *
     * @since 3.5.0
     * @access protected
     *
     * @param int $width
     * @param int $height
     * @return true
     */
    protected function update_size( $width = null, $height = null , $name = 0) {
        $this->size = array(
            'name' => $name,
            'width' => (int) $width,
            'height' => (int) $height
        );
        return true;
    }

    /**
     * Gets the Image Compression quality on a 1-100% scale.
     *
     * @since 4.0.0
     * @access public
     *
     * @return int $quality Compression Quality. Range: [1,100]
     */
    public function get_quality() {
        if ( ! $this->quality ) {
            $this->set_quality();
        }

        return $this->quality;
    }

    /**
     * Sets Image Compression quality on a 1-100% scale.
     *
     * @since 3.5.0
     * @access public
     *
     * @param int $quality Compression Quality. Range: [1,100]
     * @return true|WP_Error True if set successfully; WP_Error on failure.
     */
    public function set_quality( $quality = null ) {
        if ( null === $quality ) {
            /**
             * Filter the default image compression quality setting.
             *
             * Applies only during initial editor instantiation, or when set_quality() is run
             * manually without the `$quality` argument.
             *
             * set_quality() has priority over the filter.
             *
             * @since 3.5.0
             *
             * @param int    $quality   Quality level between 1 (low) and 100 (high).
             * @param string $mime_type Image mime type.
             */
            //$quality = apply_filters( 'wp_editor_set_quality', $this->default_quality, $this->mime_type );
            $quality =  $this->default_quality;

            if ( 'image/jpeg' == $this->mime_type ) {
                /**
                 * Filter the JPEG compression quality for backward-compatibility.
                 *
                 * Applies only during initial editor instantiation, or when set_quality() is run
                 * manually without the `$quality` argument.
                 *
                 * set_quality() has priority over the filter.
                 *
                 * The filter is evaluated under two contexts: 'image_resize', and 'edit_image',
                 * (when a JPEG image is saved to file).
                 *
                 * @since 2.5.0
                 *
                 * @param int    $quality Quality level between 0 (low) and 100 (high) of the JPEG.
                 * @param string $context Context of the filter.
                 */
                //$quality = apply_filters( 'jpeg_quality', $quality, 'image_resize' );
            }

            if ( $quality < 0 || $quality > 100 ) {
                $quality = $this->default_quality;
            }
        }

        // Allow 0, but squash to 1 due to identical images in GD, and for backwards compatibility.
        if ( 0 === $quality ) {
            $quality = 1;
        }

        if ( ( $quality >= 1 ) && ( $quality <= 100 ) ) {
            $this->quality = $quality;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns preferred mime-type and extension based on provided
     * file's extension and mime, or current file's extension and mime.
     *
     * Will default to $this->default_mime_type if requested is not supported.
     *
     * Provides corrected filename only if filename is provided.
     *
     * @since 3.5.0
     * @access protected
     *
     * @param string $filename
     * @param string $mime_type
     * @return array { filename|null, extension, mime-type }
     */
    protected function get_output_format( $filename = null, $mime_type = null ) {
        $new_ext = null;

        // By default, assume specified type takes priority
        if ( $mime_type ) {
            $new_ext = $this->get_extension( $mime_type );
        }

        if ( $filename ) {
            $file_ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
            $file_mime = $this->get_mime_type( $file_ext );
        }
        else {
            // If no file specified, grab editor's current extension and mime-type.
            $file_ext = strtolower( pathinfo( $this->file, PATHINFO_EXTENSION ) );
            $file_mime = $this->mime_type;
        }

        // Check to see if specified mime-type is the same as type implied by
        // file extension.  If so, prefer extension from file.
        if ( ! $mime_type || ( $file_mime == $mime_type ) ) {
            $mime_type = $file_mime;
            $new_ext = $file_ext;
        }

        // Double-check that the mime-type selected is supported by the editor.
        // If not, choose a default instead.
        if ( ! $this->supports_mime_type( $mime_type ) ) {
            /**
             * Filter default mime type prior to getting the file extension.
             *
             * @see wp_get_mime_types()
             *
             * @since 3.5.0
             *
             * @param string $mime_type Mime type string.
             */
            $mime_type = apply_filters( 'image_editor_default_mime_type', $this->default_mime_type );
            $new_ext = $this->get_extension( $mime_type );
        }

        if ( $filename ) {
            $ext = '';
            $info = pathinfo( $filename );
            $dir  = $info['dirname'];

            if ( isset( $info['extension'] ) )
                $ext = $info['extension'];

            $filename = trailingslashit( $dir ) . wp_basename( $filename, ".$ext" ) . ".{$new_ext}";
        }

        return array( $filename, $new_ext, $mime_type );
    }

    /**
     * Builds an output filename based on current file, and adding proper suffix
     *
     * @since 3.5.0
     * @access public
     *
     * @param string $suffix
     * @param string $dest_path
     * @param string $extension
     * @return string filename
     */
    public function generate_filename( $suffix = null, $dest_path = null, $extension = null ) {
        // $suffix will be appended to the destination filename, just before the extension
        if ( ! $suffix )
            $suffix = $this->get_suffix();

        $info = pathinfo( $this->file );
        $dir  = $info['dirname'];
        $ext  = $info['extension'];

        $name = wp_basename( $this->file, ".$ext" );
        $new_ext = strtolower( $extension ? $extension : $ext );

        if ( ! is_null( $dest_path ) && $_dest_path = realpath( $dest_path ) )
            $dir = $_dest_path;

        return trailingslashit( $dir ) . "{$name}_{$suffix}.{$new_ext}";
    }

    /**
     * Builds and returns proper suffix for file based on height and width.
     *
     * @since 3.5.0
     * @access public
     *
     * @return false|string suffix
     */
    public function get_suffix() {
        if ( ! $this->get_size() )
            return false;

        return "{$this->size['name']}";
    }

    /**
     * Either calls editor's save function or handles file as a stream.
     *
     * @since 3.5.0
     * @access protected
     *
     * @param string|stream $filename
     * @param callable $function
     * @param array $arguments
     * @return bool
     */
    protected function make_image( $filename, $function, $arguments ) {
        if ( $stream = wp_is_stream( $filename ) ) {
            ob_start();
        } else {
            // The directory containing the original file may no longer exist when using a replication plugin.
            wp_mkdir_p( dirname( $filename ) );
        }

        $result = call_user_func_array( $function, $arguments );

        if ( $result && $stream ) {
            $contents = ob_get_contents();

            $fp = fopen( $filename, 'w' );

            if ( ! $fp )
                return false;

            fwrite( $fp, $contents );
            fclose( $fp );
        }

        if ( $stream ) {
            ob_end_clean();
        }

        return $result;
    }

    /**
     * Returns first matched mime-type from extension,
     * as mapped from wp_get_mime_types()
     *
     * @since 3.5.0
     *
     * @static
     * @access protected
     *
     * @param string $extension
     * @return string|false
     */
    protected static function get_mime_type( $extension = null ) {
        if ( ! $extension )
            return false;

        $mime_types = wp_get_mime_types();
        $extensions = array_keys( $mime_types );

        foreach ( $extensions as $_extension ) {
            if ( preg_match( "/{$extension}/i", $_extension ) ) {
                return $mime_types[$_extension];
            }
        }

        return false;
    }

    /**
     * Returns first matched extension from Mime-type,
     * as mapped from wp_get_mime_types()
     *
     * @since 3.5.0
     *
     * @static
     * @access protected
     *
     * @param string $mime_type
     * @return string|false
     */
    protected static function get_extension( $mime_type = null ) {
        $extensions = explode( '|', array_search( $mime_type, wp_get_mime_types() ) );

        if ( empty( $extensions[0] ) )
            return false;

        return $extensions[0];
    }
}




/**
 * WordPress GD Image Editor
 *
 * @package WordPress
 * @subpackage Image_Editor
 */

/**
 * WordPress Image Editor Class for Image Manipulation through GD
 *
 * @since 3.5.0
 * @package WordPress
 * @subpackage Image_Editor
 * @uses WP_Image_Editor Extends class
 */
class WP_Image_Editor_GD extends WP_Image_Editor {
    /**
     * GD Resource.
     *
     * @access protected
     * @var resource
     */
    protected $image;

    public function __destruct() {
        if ( $this->image ) {
            // we don't need the original in memory anymore
            imagedestroy( $this->image );
        }
    }

    /**
     * Checks to see if current environment supports GD.
     *
     * @since 3.5.0
     *
     * @static
     * @access public
     *
     * @param array $args
     * @return bool
     */
    public static function test( $args = array() ) {
        if ( ! extension_loaded('gd') || ! function_exists('gd_info') )
            return false;

        // On some setups GD library does not provide imagerotate() - Ticket #11536
        if ( isset( $args['methods'] ) &&
            in_array( 'rotate', $args['methods'] ) &&
            ! function_exists('imagerotate') ){

            return false;
        }

        return true;
    }

    /**
     * Checks to see if editor supports the mime-type specified.
     *
     * @since 3.5.0
     *
     * @static
     * @access public
     *
     * @param string $mime_type
     * @return bool
     */
    public static function supports_mime_type( $mime_type ) {
        $image_types = imagetypes();
        switch( $mime_type ) {
            case 'image/jpeg':
                return ($image_types & IMG_JPG) != 0;
            case 'image/png':
                return ($image_types & IMG_PNG) != 0;
            case 'image/gif':
                return ($image_types & IMG_GIF) != 0;
        }

        return false;
    }

    /**
     * Loads image from $this->file into new GD Resource.
     *
     * @since 3.5.0
     * @access protected
     *
     * @return bool|WP_Error True if loaded successfully; WP_Error on failure.
     */
    public function load() {
        if ( $this->image )
            return true;

        if ( ! is_file( $this->file ) && ! preg_match( '|^https?://|', $this->file ) ) {
            return false;
        }

        /**
         * Filter the memory limit allocated for image manipulation.
         *
         * @since 3.5.0
         *
         * @param int|string $limit Maximum memory limit to allocate for images. Default WP_MAX_MEMORY_LIMIT.
         *                          Accepts an integer (bytes), or a shorthand string notation, such as '256M'.
         */

        $this->image = @imagecreatefromstring( file_get_contents( $this->file ) );

        if ( ! is_resource( $this->image ) ) {
            return false;
        }

        $size = @getimagesize( $this->file );

        if ( ! $size ) {
            return false;
        }

        if ( function_exists( 'imagealphablending' ) && function_exists( 'imagesavealpha' ) ) {
            imagealphablending( $this->image, false );
            imagesavealpha( $this->image, true );
        }

        $this->update_size( $size[0], $size[1] );
        $this->mime_type = $size['mime'];

        return $this->set_quality();
    }

    /**
     * Sets or updates current image size.
     *
     * @since 3.5.0
     * @access protected
     *
     * @param int $width
     * @param int $height
     * @return true
     */
    protected function update_size( $width = false, $height = false , $name = 0) {
        if ( ! $width )
            $width = imagesx( $this->image );

        if ( ! $height )
            $height = imagesy( $this->image );

        return parent::update_size( $width, $height, $name );
    }

    /**
     * Resizes current image.
     * Wraps _resize, since _resize returns a GD Resource.
     *
     * At minimum, either a height or width must be provided.
     * If one of the two is set to null, the resize will
     * maintain aspect ratio according to the provided dimension.
     *
     * @since 3.5.0
     * @access public
     *
     * @param  int|null $max_w Image width.
     * @param  int|null $max_h Image height.
     * @param  bool     $crop
     * @return true|WP_Error
     */
    public function resize( $max_w, $max_h, $crop = false, $name = 0 ) {
        if ( ( $this->size['width'] == $max_w ) && ( $this->size['height'] == $max_h ) )
            return true;

        $resized = $this->_resize( $max_w, $max_h, $crop, $name = 0 );

        if ( is_resource( $resized ) ) {
            imagedestroy( $this->image );
            $this->image = $resized;
            return true;

        } elseif ( is_wp_error( $resized ) )
            return $resized;

        return 'Image resize failed.'. $this->file;
    }

    /**
     *
     * @param int $max_w
     * @param int $max_h
     * @param bool|array $crop
     * @return resource|WP_Error
     */
    protected function _resize( $max_w, $max_h, $crop = false, $name = 0 ) {

        $dims = image_resize_dimensions( $this->size['width'], $this->size['height'], $max_w, $max_h, $crop );

        if ( ! $dims ) {
            return 'Could not calculate resized image dimensions';
        }
        list( $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h ) = $dims;

        $resized = wp_imagecreatetruecolor( $dst_w, $dst_h );
        imagecopyresampled( $resized, $this->image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h );

        if ( is_resource( $resized ) ) {
            $this->update_size( $dst_w, $dst_h, $name );
            return $resized;
        }

        return 'Image resize failed.';
    }

    /**
     * Resize multiple images from a single source.
     *
     * @since 3.5.0
     * @access public
     *
     * @param array $sizes {
     *     An array of image size arrays. Default sizes are 'small', 'medium', 'medium_large', 'large'.
     *
     *     Either a height or width must be provided.
     *     If one of the two is set to null, the resize will
     *     maintain aspect ratio according to the provided dimension.
     *
     *     @type array $size {
     *         Array of height, width values, and whether to crop.
     *
     *         @type int  $width  Image width. Optional if `$height` is specified.
     *         @type int  $height Image height. Optional if `$width` is specified.
     *         @type bool $crop   Optional. Whether to crop the image. Default false.
     *     }
     * }
     * @return array An array of resized images' metadata by size.
     */
    public function multi_resize( $sizes ) {
        $metadata = array();
        $orig_size = $this->size;

        foreach ( $sizes as $size => $size_data ) {
            if ( ! isset( $size_data['width'] ) && ! isset( $size_data['height'] ) ) {
                continue;
            }

            if ( ! isset( $size_data['width'] ) ) {
                $size_data['width'] = null;
            }
            if ( ! isset( $size_data['height'] ) ) {
                $size_data['height'] = null;
            }

            if ( ! isset( $size_data['crop'] ) ) {
                $size_data['crop'] = false;
            }

            $image = $this->_resize( $size_data['width'], $size_data['height'], $size_data['crop'], $size );

            $duplicate = ( ( $orig_size['width'] == $size_data['width'] ) && ( $orig_size['height'] == $size_data['height'] ) );

            if ( ! is_wp_error( $image ) && ! $duplicate ) {
                $resized = $this->_save( $image );
                imagedestroy( $image );

                if ( ! is_wp_error( $resized ) && $resized ) {
                    unset( $resized['path'] );
                    $metadata[$size] = $resized;
                }
            }

            $this->size = $orig_size;
        }

        return $metadata;
    }

    /**
     * Crops Image.
     *
     * @since 3.5.0
     * @access public
     *
     * @param int  $src_x   The start x position to crop from.
     * @param int  $src_y   The start y position to crop from.
     * @param int  $src_w   The width to crop.
     * @param int  $src_h   The height to crop.
     * @param int  $dst_w   Optional. The destination width.
     * @param int  $dst_h   Optional. The destination height.
     * @param bool $src_abs Optional. If the source crop points are absolute.
     * @return bool|WP_Error
     */
    public function crop( $src_x, $src_y, $src_w, $src_h, $dst_w = null, $dst_h = null, $src_abs = false ) {
        // If destination width/height isn't specified, use same as
        // width/height from source.
        if ( ! $dst_w )
            $dst_w = $src_w;
        if ( ! $dst_h )
            $dst_h = $src_h;

        $dst = wp_imagecreatetruecolor( $dst_w, $dst_h );

        if ( $src_abs ) {
            $src_w -= $src_x;
            $src_h -= $src_y;
        }

        if ( function_exists( 'imageantialias' ) )
            imageantialias( $dst, true );

        imagecopyresampled( $dst, $this->image, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h );

        if ( is_resource( $dst ) ) {
            imagedestroy( $this->image );
            $this->image = $dst;
            $this->update_size();
            return true;
        }

        return 'Image crop failed.' .$this->file;
    }

    /**
     * Rotates current image counter-clockwise by $angle.
     * Ported from image-edit.php
     *
     * @since 3.5.0
     * @access public
     *
     * @param float $angle
     * @return true|WP_Error
     */
    public function rotate( $angle ) {
        if ( function_exists('imagerotate') ) {
            $transparency = imagecolorallocatealpha( $this->image, 255, 255, 255, 127 );
            $rotated = imagerotate( $this->image, $angle, $transparency );

            if ( is_resource( $rotated ) ) {
                imagealphablending( $rotated, true );
                imagesavealpha( $rotated, true );
                imagedestroy( $this->image );
                $this->image = $rotated;
                $this->update_size();
                return true;
            }
        }
        return 'Image rotate failed.'.$this->file;
    }

    /**
     * Flips current image.
     *
     * @since 3.5.0
     * @access public
     *
     * @param bool $horz Flip along Horizontal Axis
     * @param bool $vert Flip along Vertical Axis
     * @return true|WP_Error
     */
    public function flip( $horz, $vert ) {
        $w = $this->size['width'];
        $h = $this->size['height'];
        $dst = wp_imagecreatetruecolor( $w, $h );

        if ( is_resource( $dst ) ) {
            $sx = $vert ? ($w - 1) : 0;
            $sy = $horz ? ($h - 1) : 0;
            $sw = $vert ? -$w : $w;
            $sh = $horz ? -$h : $h;

            if ( imagecopyresampled( $dst, $this->image, 0, 0, $sx, $sy, $w, $h, $sw, $sh ) ) {
                imagedestroy( $this->image );
                $this->image = $dst;
                return true;
            }
        }
        return 'Image flip failed.'. $this->file;
    }

    /**
     * Saves current in-memory image to file.
     *
     * @since 3.5.0
     * @access public
     *
     * @param string|null $filename
     * @param string|null $mime_type
     * @return array|WP_Error {'path'=>string, 'file'=>string, 'width'=>int, 'height'=>int, 'mime-type'=>string}
     */
    public function save( $filename = null, $mime_type = null ) {
        $saved = $this->_save( $this->image, $filename, $mime_type );

        if ( ! is_wp_error( $saved ) ) {
            $this->file = $saved['path'];
            $this->mime_type = $saved['mime-type'];
        }

        return $saved;
    }

    /**
     * @param resource $image
     * @param string|null $filename
     * @param string|null $mime_type
     * @return WP_Error|array
     */
    protected function _save( $image, $filename = null, $mime_type = null ) {
        list( $filename, $extension, $mime_type ) = $this->get_output_format( $filename, $mime_type );

        if ( ! $filename )
            $filename = $this->generate_filename( null, null, $extension );

        if ( 'image/gif' == $mime_type ) {
            if ( ! $this->make_image( $filename, 'imagegif', array( $image, $filename ) ) )
                return 'Image Editor Save Failed';
        }
        elseif ( 'image/png' == $mime_type ) {
            // convert from full colors to index colors, like original PNG.
            if ( function_exists('imageistruecolor') && ! imageistruecolor( $image ) )
                imagetruecolortopalette( $image, false, imagecolorstotal( $image ) );

            if ( ! $this->make_image( $filename, 'imagepng', array( $image, $filename ) ) )
                return 'Image Editor Save Failed';
        }
        elseif ( 'image/jpeg' == $mime_type ) {
            if ( ! $this->make_image( $filename, 'imagejpeg', array( $image, $filename, $this->get_quality() ) ) )
                return 'Image Editor Save Failed';
        }
        else {
            return 'Image Editor Save Failed';
        }

        // Set correct file permissions
        $stat = stat( dirname( $filename ) );
        $perms = $stat['mode'] & 0000666; //same permissions as parent folder, strip off the executable bits
        @ chmod( $filename, $perms );

        /**
         * Filter the name of the saved image file.
         *
         * @since 2.6.0
         *
         * @param string $filename Name of the file.
         */
        return array(
            'file'      => $filename,
            'width'     => $this->size['width'],
            'height'    => $this->size['height'],
            'mime-type' => $mime_type,
        );
    }

    /**
     * Returns stream of current image.
     *
     * @since 3.5.0
     * @access public
     *
     * @param string $mime_type
     * @return bool
     */
    public function stream( $mime_type = null ) {
        list( $filename, $extension, $mime_type ) = $this->get_output_format( null, $mime_type );

        switch ( $mime_type ) {
            case 'image/png':
                header( 'Content-Type: image/png' );
                return imagepng( $this->image );
            case 'image/gif':
                header( 'Content-Type: image/gif' );
                return imagegif( $this->image );
            default:
                header( 'Content-Type: image/jpeg' );
                return imagejpeg( $this->image, null, $this->get_quality() );
        }
    }

    /**
     * Either calls editor's save function or handles file as a stream.
     *
     * @since 3.5.0
     * @access protected
     *
     * @param string|stream $filename
     * @param callable $function
     * @param array $arguments
     * @return bool
     */
    protected function make_image( $filename, $function, $arguments ) {
        if ( wp_is_stream( $filename ) )
            $arguments[1] = null;

        return parent::make_image( $filename, $function, $arguments );
    }
}




/**
 * WordPress API for media display.
 *
 * @package WordPress
 * @subpackage Media
 */

/**
 * Scale down the default size of an image.
 *
 * This is so that the image is a better fit for the editor and theme.
 *
 * The `$size` parameter accepts either an array or a string. The supported string
 * values are 'thumb' or 'thumbnail' for the given thumbnail size or defaults at
 * 128 width and 96 height in pixels. Also supported for the string value is
 * 'medium', 'medium_large' and 'full'. The 'full' isn't actually supported, but any value other
 * than the supported will result in the content_width size or 500 if that is
 * not set.
 *
 * Finally, there is a filter named {@see 'editor_max_image_size'}, that will be
 * called on the calculated array for width and height, respectively. The second
 * parameter will be the value that was in the $size parameter. The returned
 * type for the hook is an array with the width as the first element and the
 * height as the second element.
 *
 * @since 2.5.0
 *
 * @global int   $content_width
 * @global array $_wp_additional_image_sizes
 *
 * @param int          $width   Width of the image in pixels.
 * @param int          $height  Height of the image in pixels.
 * @param string|array $size    Optional. Image size. Accepts any valid image size, or an array
 *                              of width and height values in pixels (in that order).
 *                              Default 'medium'.
 * @param string       $context Optional. Could be 'display' (like in a theme) or 'edit'
 *                              (like inserting into an editor). Default null.
 * @return array Width and height of what the result image should resize to.
 */
function image_constrain_size_for_editor( $width, $height, $size = 'medium', $context = null ) {
	global $content_width, $_wp_additional_image_sizes;

	if ( ! $context )
		$context = is_admin() ? 'edit' : 'display';

	if ( is_array($size) ) {
		$max_width = $size[0];
		$max_height = $size[1];
	}
	elseif ( $size == 'thumb' || $size == 'thumbnail' ) {
		$max_width = intval(get_option('thumbnail_size_w'));
		$max_height = intval(get_option('thumbnail_size_h'));
		// last chance thumbnail size defaults
		if ( !$max_width && !$max_height ) {
			$max_width = 128;
			$max_height = 96;
		}
	}
	elseif ( $size == 'medium' ) {
		$max_width = intval(get_option('medium_size_w'));
		$max_height = intval(get_option('medium_size_h'));

	}
	elseif ( $size == 'medium_large' ) {
		$max_width = intval( get_option( 'medium_large_size_w' ) );
		$max_height = intval( get_option( 'medium_large_size_h' ) );

		if ( intval( $content_width ) > 0 ) {
			$max_width = min( intval( $content_width ), $max_width );
		}
	}
	elseif ( $size == 'large' ) {
		/*
		 * We're inserting a large size image into the editor. If it's a really
		 * big image we'll scale it down to fit reasonably within the editor
		 * itself, and within the theme's content width if it's known. The user
		 * can resize it in the editor if they wish.
		 */
		$max_width = intval(get_option('large_size_w'));
		$max_height = intval(get_option('large_size_h'));
		if ( intval($content_width) > 0 ) {
			$max_width = min( intval($content_width), $max_width );
		}
	} elseif ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) && in_array( $size, array_keys( $_wp_additional_image_sizes ) ) ) {
		$max_width = intval( $_wp_additional_image_sizes[$size]['width'] );
		$max_height = intval( $_wp_additional_image_sizes[$size]['height'] );
		if ( intval($content_width) > 0 && 'edit' == $context ) // Only in admin. Assume that theme authors know what they're doing.
			$max_width = min( intval($content_width), $max_width );
	}
	// $size == 'full' has no constraint
	else {
		$max_width = $width;
		$max_height = $height;
	}

	/**
	 * Filter the maximum image size dimensions for the editor.
	 *
	 * @since 2.5.0
	 *
	 * @param array        $max_image_size An array with the width as the first element,
	 *                                     and the height as the second element.
	 * @param string|array $size           Size of what the result image should be.
	 * @param string       $context        The context the image is being resized for.
	 *                                     Possible values are 'display' (like in a theme)
	 *                                     or 'edit' (like inserting into an editor).
	 */
	list( $max_width, $max_height ) = apply_filters( 'editor_max_image_size', array( $max_width, $max_height ), $size, $context );

	return wp_constrain_dimensions( $width, $height, $max_width, $max_height );
}

/*

/**
 * Calculates the new dimensions for a down-sampled image.
 *
 * If either width or height are empty, no constraint is applied on
 * that dimension.
 *
 * @since 2.5.0
 *
 * @param int $current_width  Current width of the image.
 * @param int $current_height Current height of the image.
 * @param int $max_width      Optional. Max width in pixels to constrain to. Default 0.
 * @param int $max_height     Optional. Max height in pixels to constrain to. Default 0.
 * @return array First item is the width, the second item is the height.
 */
function wp_constrain_dimensions( $current_width, $current_height, $max_width = 0, $max_height = 0 ) {
	if ( !$max_width && !$max_height )
		return array( $current_width, $current_height );

	$width_ratio = $height_ratio = 1.0;
	$did_width = $did_height = false;

	if ( $max_width > 0 && $current_width > 0 && $current_width > $max_width ) {
		$width_ratio = $max_width / $current_width;
		$did_width = true;
	}

	if ( $max_height > 0 && $current_height > 0 && $current_height > $max_height ) {
		$height_ratio = $max_height / $current_height;
		$did_height = true;
	}

	// Calculate the larger/smaller ratios
	$smaller_ratio = min( $width_ratio, $height_ratio );
	$larger_ratio  = max( $width_ratio, $height_ratio );

	if ( (int) round( $current_width * $larger_ratio ) > $max_width || (int) round( $current_height * $larger_ratio ) > $max_height ) {
 		// The larger ratio is too big. It would result in an overflow.
		$ratio = $smaller_ratio;
	} else {
		// The larger ratio fits, and is likely to be a more "snug" fit.
		$ratio = $larger_ratio;
	}

	// Very small dimensions may result in 0, 1 should be the minimum.
	$w = max ( 1, (int) round( $current_width  * $ratio ) );
	$h = max ( 1, (int) round( $current_height * $ratio ) );

	// Sometimes, due to rounding, we'll end up with a result like this: 465x700 in a 177x177 box is 117x176... a pixel short
	// We also have issues with recursive calls resulting in an ever-changing result. Constraining to the result of a constraint should yield the original result.
	// Thus we look for dimensions that are one pixel shy of the max value and bump them up

	// Note: $did_width means it is possible $smaller_ratio == $width_ratio.
	if ( $did_width && $w == $max_width - 1 ) {
		$w = $max_width; // Round it up
	}

	// Note: $did_height means it is possible $smaller_ratio == $height_ratio.
	if ( $did_height && $h == $max_height - 1 ) {
		$h = $max_height; // Round it up
	}

	/**
	 * Filter dimensions to constrain down-sampled images to.
	 *
	 * @since 4.1.0
	 *
	 * @param array $dimensions     The image width and height.
	 * @param int 	$current_width  The current width of the image.
	 * @param int 	$current_height The current height of the image.
	 * @param int 	$max_width      The maximum width permitted.
	 * @param int 	$max_height     The maximum height permitted.
	 */
	return apply_filters( 'wp_constrain_dimensions', array( $w, $h ), $current_width, $current_height, $max_width, $max_height );
}

/**
 * Retrieves calculated resize dimensions for use in WP_Image_Editor.
 *
 * Calculates dimensions and coordinates for a resized image that fits
 * within a specified width and height.
 *
 * Cropping behavior is dependent on the value of $crop:
 * 1. If false (default), images will not be cropped.
 * 2. If an array in the form of array( x_crop_position, y_crop_position ):
 *    - x_crop_position accepts 'left' 'center', or 'right'.
 *    - y_crop_position accepts 'top', 'center', or 'bottom'.
 *    Images will be cropped to the specified dimensions within the defined crop area.
 * 3. If true, images will be cropped to the specified dimensions using center positions.
 *
 * @since 2.5.0
 *
 * @param int        $orig_w Original width in pixels.
 * @param int        $orig_h Original height in pixels.
 * @param int        $dest_w New width in pixels.
 * @param int        $dest_h New height in pixels.
 * @param bool|array $crop   Optional. Whether to crop image to specified width and height or resize.
 *                           An array can specify positioning of the crop area. Default false.
 * @return false|array False on failure. Returned array matches parameters for `imagecopyresampled()`.
 */
function image_resize_dimensions( $orig_w, $orig_h, $dest_w, $dest_h, $crop = false ) {

	if ($orig_w <= 0 || $orig_h <= 0)
		return false;
	// at least one of dest_w or dest_h must be specific
	if ($dest_w <= 0 && $dest_h <= 0)
		return false;

	/**
	 * Filter whether to preempt calculating the image resize dimensions.
	 *
	 * Passing a non-null value to the filter will effectively short-circuit
	 * image_resize_dimensions(), returning that value instead.
	 *
	 * @since 3.4.0
	 *
	 * @param null|mixed $null   Whether to preempt output of the resize dimensions.
	 * @param int        $orig_w Original width in pixels.
	 * @param int        $orig_h Original height in pixels.
	 * @param int        $dest_w New width in pixels.
	 * @param int        $dest_h New height in pixels.
	 * @param bool|array $crop   Whether to crop image to specified width and height or resize.
	 *                           An array can specify positioning of the crop area. Default false.
	 */
	$output = null;

	if ( $crop ) {
		// crop the largest possible portion of the original image that we can size to $dest_w x $dest_h
		$aspect_ratio = $orig_w / $orig_h;
		$new_w = min($dest_w, $orig_w);
		$new_h = min($dest_h, $orig_h);

		if ( ! $new_w ) {
			$new_w = (int) round( $new_h * $aspect_ratio );
		}

		if ( ! $new_h ) {
			$new_h = (int) round( $new_w / $aspect_ratio );
		}

		$size_ratio = max($new_w / $orig_w, $new_h / $orig_h);

		$crop_w = round($new_w / $size_ratio);
		$crop_h = round($new_h / $size_ratio);

		if ( ! is_array( $crop ) || count( $crop ) !== 2 ) {
			$crop = array( 'center', 'center' );
		}

		list( $x, $y ) = $crop;

		if ( 'left' === $x ) {
			$s_x = 0;
		} elseif ( 'right' === $x ) {
			$s_x = $orig_w - $crop_w;
		} else {
			$s_x = floor( ( $orig_w - $crop_w ) / 2 );
		}

		if ( 'top' === $y ) {
			$s_y = 0;
		} elseif ( 'bottom' === $y ) {
			$s_y = $orig_h - $crop_h;
		} else {
			$s_y = floor( ( $orig_h - $crop_h ) / 2 );
		}
	} else {
		// don't crop, just resize using $dest_w x $dest_h as a maximum bounding box
		$crop_w = $orig_w;
		$crop_h = $orig_h;

		$s_x = 0;
		$s_y = 0;

		list( $new_w, $new_h ) = wp_constrain_dimensions( $orig_w, $orig_h, $dest_w, $dest_h );
	}

	// if the resulting image would be the same size or larger we don't want to resize it
	if ( $new_w >= $orig_w && $new_h >= $orig_h && $dest_w != $orig_w && $dest_h != $orig_h ) {
		// 同样生成新图片,忽略这里return false;
	}

	// the return array matches the parameters to imagecopyresampled()
	// int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
	return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );

}

/**
 * Resizes an image to make a thumbnail or intermediate size.
 *
 * The returned array has the file size, the image width, and image height. The
 * filter 'image_make_intermediate_size' can be used to hook in and change the
 * values of the returned array. The only parameter is the resized file path.
 *
 * @since 2.5.0
 *
 * @param string $file   File path.
 * @param int    $width  Image width.
 * @param int    $height Image height.
 * @param bool   $crop   Optional. Whether to crop image to specified width and height or resize.
 *                       Default false.
 * @return false|array False, if no image was created. Metadata array on success.
 */
function image_make_intermediate_size( $file, $width, $height, $crop = false ) {
	if ( $width || $height ) {
		$editor = wp_get_image_editor( $file );

		if ( is_wp_error( $editor ) || is_wp_error( $editor->resize( $width, $height, $crop ) ) )
			return false;

		$resized_file = $editor->save();

		if ( ! is_wp_error( $resized_file ) && $resized_file ) {
			unset( $resized_file['path'] );
			return $resized_file;
		}
	}
	return false;
}


/**
 * Create new GD image resource with transparency support
 *
 * @todo: Deprecate if possible.
 *
 * @since 2.9.0
 *
 * @param int $width  Image width in pixels.
 * @param int $height Image height in pixels..
 * @return resource The GD image resource.
 */
function wp_imagecreatetruecolor($width, $height) {
	$img = imagecreatetruecolor($width, $height);
	if ( is_resource($img) && function_exists('imagealphablending') && function_exists('imagesavealpha') ) {
		imagealphablending($img, false);
		imagesavealpha($img, true);
	}
	return $img;
}

/**
 * Based on a supplied width/height example, return the biggest possible dimensions based on the max width/height.
 *
 * @since 2.9.0
 *
 * @see wp_constrain_dimensions()
 *
 * @param int $example_width  The width of an example embed.
 * @param int $example_height The height of an example embed.
 * @param int $max_width      The maximum allowed width.
 * @param int $max_height     The maximum allowed height.
 * @return array The maximum possible width and height based on the example ratio.
 */
function wp_expand_dimensions( $example_width, $example_height, $max_width, $max_height ) {
	$example_width  = (int) $example_width;
	$example_height = (int) $example_height;
	$max_width      = (int) $max_width;
	$max_height     = (int) $max_height;

	return wp_constrain_dimensions( $example_width * 1000000, $example_height * 1000000, $max_width, $max_height );
}

/**
 * Converts a shorthand byte value to an integer byte value.
 *
 * @since 2.3.0
 *
 * @param string $size A shorthand byte value.
 * @return int An integer byte value.
 */
function wp_convert_hr_to_bytes( $size ) {
	$size  = strtolower( $size );
	$bytes = (int) $size;
	if ( strpos( $size, 'k' ) !== false )
		$bytes = intval( $size ) * KB_IN_BYTES;
	elseif ( strpos( $size, 'm' ) !== false )
		$bytes = intval($size) * MB_IN_BYTES;
	elseif ( strpos( $size, 'g' ) !== false )
		$bytes = intval( $size ) * GB_IN_BYTES;
	return $bytes;
}

/**
 * Determines the maximum upload size allowed in php.ini.
 *
 * @since 2.5.0
 *
 * @return int Allowed upload size.
 */
function wp_max_upload_size() {
	$u_bytes = wp_convert_hr_to_bytes( ini_get( 'upload_max_filesize' ) );
	$p_bytes = wp_convert_hr_to_bytes( ini_get( 'post_max_size' ) );

	/**
	 * Filter the maximum upload size allowed in php.ini.
	 *
	 * @since 2.5.0
	 *
	 * @param int $size    Max upload size limit in bytes.
	 * @param int $u_bytes Maximum upload filesize in bytes.
	 * @param int $p_bytes Maximum size of POST data in bytes.
	 */
	return apply_filters( 'upload_size_limit', min( $u_bytes, $p_bytes ), $u_bytes, $p_bytes );
}

/**
 * Returns a WP_Image_Editor instance and loads file into it.
 *
 * @since 3.5.0
 *
 * @param string $path Path to the file to load.
 * @param array  $args Optional. Additional arguments for retrieving the image editor.
 *                     Default empty array.
 * @return WP_Image_Editor|WP_Error The WP_Image_Editor object if successful, an WP_Error
 *                                  object otherwise.
 */
function wp_get_image_editor( $path, $args = array() ) {
	$args['path'] = $path;

	if ( ! isset( $args['mime_type'] ) ) {
		$file_info = wp_check_filetype( $args['path'] );
		// If $file_info['type'] is false, then we let the editor attempt to
		// figure out the file type, rather than forcing a failure based on extension.
		if ( isset( $file_info ) && $file_info['type'] )
			$args['mime_type'] = $file_info['type'];
	}

	$implementation = _wp_image_editor_choose( $args );

    //WP_Image_Editor_GD

	if ( $implementation ) {
		$editor = new $implementation( $path );
		$loaded = $editor->load();

		if ( is_wp_error( $loaded ) )
			return $loaded;

		return $editor;
	}

	return 'No editor could be selected.';
}

/**
 * Tests whether there is an editor that supports a given mime type or methods.
 *
 * @since 3.5.0
 *
 * @param string|array $args Optional. Array of arguments to retrieve the image editor supports.
 *                           Default empty array.
 * @return bool True if an eligible editor is found; false otherwise.
 */
function wp_image_editor_supports( $args = array() ) {
	return (bool) _wp_image_editor_choose( $args );
}

function apply_filters($tag, $value) {
    return $value;
}
function is_wp_error($s) {
    return is_string($s);
}


/**
 * i18n friendly version of basename()
 *
 * @since 3.1.0
 *
 * @param string $path   A path.
 * @param string $suffix If the filename ends in suffix this will also be cut off.
 * @return string
 */
function wp_basename( $path, $suffix = '' ) {
	return urldecode( basename( str_replace( array( '%2F', '%5C' ), '/', urlencode( $path ) ), $suffix ) );
}


/**
 * Recursive directory creation based on full path.
 *
 * Will attempt to set permissions on folders.
 *
 * @since 2.0.1
 *
 * @param string $target Full path to attempt to create.
 * @return bool Whether the path was created. True if path already exists.
 */
function wp_mkdir_p( $target ) {
	$wrapper = null;

	// Strip the protocol.
	if ( wp_is_stream( $target ) ) {
		list( $wrapper, $target ) = explode( '://', $target, 2 );
	}

	// From php.net/mkdir user contributed notes.
	$target = str_replace( '//', '/', $target );

	// Put the wrapper back on the target.
	if ( $wrapper !== null ) {
		$target = $wrapper . '://' . $target;
	}

	/*
	 * Safe mode fails with a trailing slash under certain PHP versions.
	 * Use rtrim() instead of untrailingslashit to avoid formatting.php dependency.
	 */
	$target = rtrim($target, '/');
	if ( empty($target) )
		$target = '/';

	if ( file_exists( $target ) )
		return @is_dir( $target );

	// We need to find the permissions of the parent folder that exists and inherit that.
	$target_parent = dirname( $target );
	while ( '.' != $target_parent && ! is_dir( $target_parent ) ) {
		$target_parent = dirname( $target_parent );
	}

	// Get the permission bits.
	if ( $stat = @stat( $target_parent ) ) {
		$dir_perms = $stat['mode'] & 0007777;
	} else {
		$dir_perms = 0777;
	}

	if ( @mkdir( $target, $dir_perms, true ) ) {

		/*
		 * If a umask is set that modifies $dir_perms, we'll have to re-set
		 * the $dir_perms correctly with chmod()
		 */
		if ( $dir_perms != ( $dir_perms & ~umask() ) ) {
			$folder_parts = explode( '/', substr( $target, strlen( $target_parent ) + 1 ) );
			for ( $i = 1, $c = count( $folder_parts ); $i <= $c; $i++ ) {
				@chmod( $target_parent . '/' . implode( '/', array_slice( $folder_parts, 0, $i ) ), $dir_perms );
			}
		}

		return true;
	}

	return false;
}

/**
 * Test if a given path is a stream URL
 *
 * @param string $path The resource path or URL.
 * @return bool True if the path is a stream URL.
 */
function wp_is_stream( $path ) {
	$wrappers = stream_get_wrappers();
	$wrappers_re = '(' . join('|', $wrappers) . ')';

	return preg_match( "!^$wrappers_re://!", $path ) === 1;
}

/**
 * Appends a trailing slash.
 *
 * Will remove trailing forward and backslashes if it exists already before adding
 * a trailing forward slash. This prevents double slashing a string or path.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * @since 1.2.0
 *
 * @param string $string What to add the trailing slash to.
 * @return string String with trailing slash added.
 */
function trailingslashit( $string ) {
	return untrailingslashit( $string ) . '/';
}

/**
 * Removes trailing forward slashes and backslashes if they exist.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * @since 2.2.0
 *
 * @param string $string What to remove the trailing slashes from.
 * @return string String without the trailing slashes.
 */
function untrailingslashit( $string ) {
	return rtrim( $string, '/\\' );
}