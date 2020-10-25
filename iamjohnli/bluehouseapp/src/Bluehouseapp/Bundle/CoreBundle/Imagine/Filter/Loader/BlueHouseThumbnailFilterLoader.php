<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 14-12-29
 * Time: 上午11:24
 */

namespace Bluehouseapp\Bundle\CoreBundle\Imagine\Filter\Loader;
use Imagine\Filter\Basic\Thumbnail;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

class BlueHouseThumbnailFilterLoader implements LoaderInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ImageInterface $image, array $options = array())
    {
        $mode = ImageInterface::THUMBNAIL_OUTBOUND;

        list($width, $height) = $options['max'];

        $size = $image->getSize();
        $origWidth = $size->getWidth();
        $origHeight = $size->getHeight();

        $targetWidth=null;
        $targetHeight=null;

        $targetHeight = (int)(($width / $origWidth) * $origHeight);

        $targetWidth = (int)(($height / $origHeight) * $origWidth);


      if($targetHeight>$height){
          $targetWidth = (int)(($height / $origHeight) * $origWidth);
          $targetHeight=$height;
      }

        if($targetWidth>$width){
            $targetHeight = (int)(($width / $origWidth) * $origHeight);
            $targetWidth=$width;
        }



            $filter = new Thumbnail(new Box($targetWidth, $targetHeight), $mode);
            $image = $filter->apply($image);


        return $image;
    }

} 