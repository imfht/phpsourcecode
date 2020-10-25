<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Jenssegers\Mongodb\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Helper\ImageHelper;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

    /**
     * 获取用户基本信息
     *
     * 返回的数据格式：
     * {
     *      nickname: 用户的昵称
     *      username: 用户名
     *      cellphone_number: 手机号码
     *      sex: 性别
     *      email: 电子邮件
     *      description: 个人简介
     *      address: 用户的常住地
     * }
     *
     *
     * @param bool $web 浏览器请求为: true, 移动端请求为: false
     * @param Model $currUser 用户的模型，若为空，则默认为当前已登陆的用户
     * @return mixed 已关联数组的形式返回用户信息
     */
    public static function makeUserInfo($web = true, Model $currUser = null)
    {
        if( is_null($currUser) ){
            $currUser = \Auth::user();
        }


        $keys = ['nickname', 'username', 'cellphone_number', 'sex', 'email', 'description', 'address'];

        foreach( $keys as $key ) {
            if( $currUser[$key] ){
                $respData[$key] = $currUser[$key];
            } else {
                $respData[$key] = '';
            }

        }

        if( $web ){
            $respData['head_image'] = $currUser['head_image'];
        } else {
            $respData['head_image'] = ImageHelper::imageToDataUrl(
                public_path(). '/image/header/'. $currUser['head_image']
            );
        }

        return $respData;
    }

    public function collection()
    {
        return $this->hasMany('App\Collection', 'creator_id', '_id');
    }

    public function routes()
    {
        return $this->hasMany('App\Route', 'creator_id', '_id');
    }

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
//	protected $fillable = ['name', 'email', 'password', 'description'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

    protected $guarded = ['_id'];

}
