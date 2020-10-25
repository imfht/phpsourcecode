<?php

namespace App\Models;

use App\Http\Controllers\Api\Traits\BaseResponseTrait;
use App\Models\Traits\ExcuteTrait;
use App\Models\Traits\ScopeTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Config;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use DB;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, ScopeTrait, ExcuteTrait, HasRoles, BaseResponseTrait;

    protected $fillable = [
        'name', 'password', 'head_image', 'last_login_at', 'is_admin'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function getHeadImageAttribute($value)
    {
        $attachment_info = Attachment::enable()->find($value);
        if ($attachment_info) {
            $url = $attachment_info->domain . '/' . $attachment_info->link_path . '/' . $attachment_info->storage_name;
            $attachment_id = $attachment_info->id;
        } else {
            $url = Config::get('set_file_path.default_head_image');
            $attachment_id = 0;
        }
        return [
            'url' => $url,
            'attachment_id' => $attachment_id
        ];
    }

    public function scopeIsAdminSearch($query, $value)
    {
        return $query->where('is_admin', $value);
    }

    public function storeAction($input)
    {
        DB::beginTransaction();
        try {
            if ($input['head_image']) {
                $this->saveAttachmentAfterSave($input['head_image']);
            }
            $this->fill($input);
            $this->email = $input['email'];
            $this->password = bcrypt($input['password']);
            $this->save();

            DB::commit();
            return $this->baseSucceed([], '操作成功');
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return $this->baseFailed('内部错误');
        }
    }

    public function updateAction($input)
    {
        $old_head_image = $this->head_image['attachment_id'];
        $new_head_image = $input['head_image'];
        if ($input['id'] === 1) {
            unset($input['email']);
        }
        DB::beginTransaction();
        try {
            if (($old_head_image != $new_head_image)) {
                if ($old_head_image > 0) {
                    $this->updateAttachmentAfterNotUseAgain($old_head_image);
                }
                if ($new_head_image > 0) {
                    $this->saveAttachmentAfterSave($new_head_image);
                }
            }
            $this->fill($input)->save();

            DB::commit();
            return $this->baseSucceed([], '操作成功');
        } catch (\Exception $e) {
            throw  $e;
            DB::rollBack();
            return $this->baseFailed('内部错误');
        }
    }

    public function destroyAction()
    {
        DB::beginTransaction();
        try {
            $this->syncRoles([]);
            $this->delete();
            $attachment_id = $this->head_image['attachment_id'];
            if ($attachment_id) {
                $this->deleteAttachmentAfterDelete($attachment_id);
            }
            DB::commit();
            return $this->baseSucceed([], '用户删除成功');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->baseFailed('内部错误');
        }

    }


}
