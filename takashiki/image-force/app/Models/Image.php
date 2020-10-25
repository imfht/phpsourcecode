<?php

namespace App\Models;

use App\Jobs\CheckImage;
use App\Jobs\DuplicateImage;

/**
 * App\Models\Image.
 *
 * @property int $id
 * @property string $sha1
 * @property int $copy_count
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Image whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Image whereSha1($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Image whereCopyCount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Image whereUpdatedAt($value)
 */
class Image extends \Eloquent
{
    protected $table = 'image';

    protected $fillable = [
        'sha1',
    ];

    protected $attributes = [
        'copy_count' => 0,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function copies()
    {
        return $this->hasMany(\App\Models\ImageCopies::class);
    }

    /**
     * @return ImageCopies[]|null
     */
    public function getAvailableCopies()
    {
        return $this->copies()->where('status', ImageCopies::AVAILABLE)->get();
    }

    /**
     * @return ImageCopies|null
     */
    public function firstAvailableCopy()
    {
        return $this->copies()
            ->where('status', ImageCopies::AVAILABLE)
            ->orderByRaw('FIELD(`storage_id`, '.ImageStorage::preferOrder().')')
            ->first();
    }

    public static function getModel($file)
    {
        $sha1 = sha1_file($file);
        $image = static::where(['sha1' => $sha1])->first();
        if (!$image) {
            $image = static::create(['sha1' => $sha1]);
        }

        if ($image->copy_count < 1) {
            if (!ImageCopies::storage($image, $file, config('app.default_storage'))) {
                return false;
            }
            dispatch((new DuplicateImage($image))->onQueue('duplicate'));
        }

        return $image;
    }

    public function check()
    {
        foreach ($this->getAvailableCopies() as $copy) {
            $this->copy_count -= $copy->getAvailability() !== ImageCopies::AVAILABLE ? 1 : 0;
        }

        $this->checkDuplicate();
    }

    /**
     * 批量检测时只检测http或https其中之一，因为绝大多数情况下同图床的图片这两者的可用性是相同的
     */
    public function checkMulti()
    {
        $copies = $this->getAvailableCopies();
        $images = [];
        foreach ($copies as $key => $copy) {
            $images[$key] = $copy->getUrl();
        }

        $results = are_available($images, config('app.check_timeout'));

        $avail_count = count(array_filter($results));
        if ($avail_count == 0) {
            $this->check();
            return;
        }
        $this->setCopyCount($avail_count);

        foreach ($results as $key => $available) {
            if (!$available) {
                $copies[$key]->setAvailability(ImageCopies::UNAVAILABLE);
            }
        }

        $this->checkDuplicate();
    }

    public function checkDuplicate()
    {
        if ($this->copy_count < ImageStorage::count()) {
            $this->duplicate();
        }
    }

    public function duplicate()
    {
        $copy = $this->firstAvailableCopy();
        if (!$copy) {
            throw new \Exception("Boom! Image id: {$this->id}");
        }

        foreach (ImageStorage::getUploaders() as $id => $uploader) {
            if (!ImageCopies::avail()->where(['image_id' => $this->id, 'storage_id' => $id])->first()) {
                ImageCopies::storage($this, $copy->getUrl(), $id);
            }
        }
    }

    public function getUrl()
    {
        return \URL::to($this->sha1);
    }

    public function getRealUrl($scheme = 'relative')
    {
        $copy = $this->firstAvailableCopy();
        $copy->increaseAccessCount();
        $url = $copy->getUrl($scheme);

        if (\Cache::add("image_check_{$this->id}", $url, config('app.check_interval'))) {
            dispatch((new CheckImage($this))->onQueue('check'));
        }

        return $url;
    }

    public function increaseCopyCount()
    {
        ++$this->copy_count;

        return $this->save();
    }

    public function setCopyCount($count)
    {
        $this->copy_count = $count;

        return $this->save();
    }
}
