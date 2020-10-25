<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ImageCopies.
 *
 * @property int $id
 * @property int $image_id
 * @property int $storage_id
 * @property string $url
 * @property int $access_count
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property bool $status
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ImageCopies whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ImageCopies whereImageId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ImageCopies whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ImageCopies whereAccessCount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ImageCopies whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ImageCopies whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ImageCopies whereStatus($value)
 */
class ImageCopies extends Model
{
    const UNAVAILABLE = 0;
    const HTTP_ONLY = 1;
    const HTTPS_ONLY = 2;
    const AVAILABLE = 3;

    protected static $schemes = [
        self::HTTP_ONLY => 'http',
        self::HTTPS_ONLY => 'https',
    ];

    protected $fillable = [
        'image_id',
        'storage_id',
        'url',
    ];

    protected $attributes = [
        'access_count' => 0,
        'status' => 3,
    ];

    public static function getSchemes()
    {
        return static::$schemes;
    }

    public static function avail()
    {
        return static::where(['status' => self::AVAILABLE]);
    }

    public function setAvailability($status)
    {
        $this->status = $status;

        return $this->save();
    }

    public function getAvailability()
    {
        foreach (static::getSchemes() as $key => $scheme) {
            $url = $this->getUrl($scheme);
            $this->status = is_available($url, config('app.check_timeout')) ?
                $this->status | $key :
                $this->status ^ $key;
        }

        return $this->status;
    }

    public function getUrl($scheme = 'relative')
    {
        $scheme = in_array($scheme, self::getSchemes()) ? $scheme : \Input::getScheme();

        return $scheme.'://'.$this->url;
    }

    public static function storage(Image $image, $file, $storage_id = 1)
    {
        $url = ImageStorage::upload($file, $storage_id);
        if ($url) {
            $image->increaseCopyCount();

            return static::create([
                'image_id' => $image->id,
                'url' => $url,
                'storage_id' => $storage_id,
            ]);
        }

        return false;
    }

    public function increaseAccessCount()
    {
        ++$this->access_count;

        return $this->save();
    }
}
