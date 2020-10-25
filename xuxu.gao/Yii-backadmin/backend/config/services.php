<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/17
 * Time: 12:35
 */
//Auth service
Yii::$container->set('backend\\services\\auth\\IAuthService', 'backend\\services\\auth\\Impl\\AuthServiceImpl');
Yii::$container->set('authservice','backend\\services\\auth\\IAuthService');

//用户service
Yii::$container->set('backend\\services\\user\\IUserService', 'backend\\services\\user\\Impl\\UserServiceImpl');
Yii::$container->set('userservice','backend\\services\\user\\IUserService');

//菜单service
Yii::$container->set('backend\\services\\menu\\IMenuService', 'backend\\services\\menu\\Impl\\MenuServiceImpl');
Yii::$container->set('menuservice','backend\\services\\menu\\IMenuService');

//角色service
Yii::$container->set('backend\\services\\role\\IRoleService', 'backend\\services\\role\\Impl\\RoleServiceImpl');
Yii::$container->set('roleservice','backend\\services\\role\\IRoleService');

//权限service
Yii::$container->set('backend\\services\\permission\\IPermissionService', 'backend\\services\\permission\\Impl\\PermissionServiceImpl');
Yii::$container->set('permissionservice','backend\\services\\permission\\IPermissionService');