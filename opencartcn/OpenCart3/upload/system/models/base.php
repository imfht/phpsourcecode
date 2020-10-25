<?php
/**
 * base.php
 *
 * @copyright 2020 opencart.cn - All Rights Reserved
 * @link https://www.guangdawangluo.com
 * @author stiffer.chen <chenlin@opencart.cn>
 * @created 2020-06-2020/6/29 14:39
 * @modified 2020-06-2020/6/29 14:39
 */

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Base extends Model
{
    public $timestamps = false;
    protected $modelName = '';

    public function __construct(array $attributes = [])
    {
        if (!$this->table) {
            $this->setTable($this->getCurrentClassName());
        }

        if ($this->primaryKey == 'id') {
            $this->setKeyName($this->getPrimaryName());
        }
        $this->modelName = str_replace('\\', '', Str::snake(static::class));
        parent::__construct($attributes);
    }

    public function getCurrentClassName()
    {
        return Str::snake(class_basename($this));
    }

    public function getPrimaryName()
    {
        return $this->getTable() . '_id';
    }

    public function getForeignKey()
    {
        return Str::snake(class_basename($this)) . '_id';
    }

    public function primaryValue()
    {
        return $this->{$this->getPrimaryName()};
    }

    public function getAllFields()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($row) {
            $table = $row->getTable();
            if (\Schema::hasColumn($table, 'created_at')) {
                $row->created_at = Carbon::now()->toDateTimeString();
            }
            if (\Schema::hasColumn($table, 'date_added')) {
                $row->date_added = Carbon::now()->toDateTimeString();
            }

            if (\Schema::hasColumn($table, 'updated_at')) {
                $row->updated_at = Carbon::now()->toDateTimeString();
            }
            if (\Schema::hasColumn($table, 'date_modified')) {
                $row->date_modified = Carbon::now()->toDateTimeString();
            }
        });

        self::saving(function ($row) {
            $table = $row->getTable();
            if (\Schema::hasColumn($table, 'updated_at')) {
                $row->updated_at = Carbon::now()->toDateTimeString();
            }
            if (\Schema::hasColumn($table, 'date_modified')) {
                $row->date_modified = Carbon::now()->toDateTimeString();
            }
        });
    }
}