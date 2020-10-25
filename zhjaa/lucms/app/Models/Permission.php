<?php

namespace App\Models;


use Spatie\Permission\Guard;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\RefreshesPermissionCache;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use DB;

class Permission extends Model implements PermissionContract
{
    use HasRoles;
    use RefreshesPermissionCache;

    protected $fillable = ['name', 'guard_name', 'description'];

    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');

        parent::__construct($attributes);

        $this->setTable(config('permission.table_names.permissions'));
    }


    /**
     * A permission can be applied to roles.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.role'),
            config('permission.table_names.role_has_permissions')
        );
    }

    /**
     * A permission belongs to some users of the model associated with its guard.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name']),
            'model',
            config('permission.table_names.model_has_permissions'),
            'permission_id',
            'model_id'
        );
    }

    /**
     * Find a permission by its name (and optionally guardName).
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @throws \Spatie\Permission\Exceptions\PermissionDoesNotExist
     *
     * @return \Spatie\Permission\Contracts\Permission
     */
    public static function findByName(string $name, $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $permission = static::getPermissions()->filter(function ($permission) use ($name, $guardName) {
            return $permission->name === $name && $permission->guard_name === $guardName;
        })->first();

        if (!$permission) {
            throw PermissionDoesNotExist::create($name, $guardName);
        }

        return $permission;
    }

    /**
     * Find a permission by its id (and optionally guardName).
     *
     * @param int $id
     * @param string|null $guardName
     *
     * @throws \Spatie\Permission\Exceptions\PermissionDoesNotExist
     *
     * @return \Spatie\Permission\Contracts\Permission
     */
    public static function findById(int $id, $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $permission = static::getPermissions()->filter(function ($permission) use ($id, $guardName) {
            return $permission->id === $id && $permission->guard_name === $guardName;
        })->first();

        if (!$permission) {
            throw PermissionDoesNotExist::withId($id, $guardName);
        }

        return $permission;
    }

    /**
     * Find or create permission by its name (and optionally guardName).
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @return \Spatie\Permission\Contracts\Permission
     */
    public static function findOrCreate(string $name, $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $permission = static::getPermissions()->filter(function ($permission) use ($name, $guardName) {
            return $permission->name === $name && $permission->guard_name === $guardName;
        })->first();

        if (!$permission) {
            return static::create(['name' => $name, 'guard_name' => $guardName]);
        }

        return $permission;
    }

    /**
     * Get the current cached permissions.
     */
    protected static function getPermissions(): Collection
    {
        return app(PermissionRegistrar::class)->getPermissions();
    }

    public function destroyAction()
    {
        DB::beginTransaction();
        try {
            $this->delete();
            DB::commit();
            return $this->baseSucceed([], '权限删除成功');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->baseFailed('内部错误');
        }
    }
}
