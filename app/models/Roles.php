<?php


namespace App\Models;


use Core\lib\DataBase;
use Core\lib\Registry;
use Core\Model;

class Roles extends Model
{

    private static ?array $permissions = [];

    public static function setPermissions(): void
    {
        $db = DataBase::instance()->getClient();
        $permissionsCollection = $db->water->permissions;
        $permissions = $permissionsCollection->find();
        foreach ($permissions as $permission){
            foreach ($permission['roles'] as $role){
//                $this->permissions[$role] = [];
//                array_push($this->permissions[$role], $permission['permission']);
                self::$permissions[$role][] = $permission['permission'];
            }
        }
    }

    public static function hasPermission()
    {
        self::setPermissions();
        $user = Registry::get('user');
        debug($user);
    }

}