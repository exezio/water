<?php


namespace App\Models;


use Core\lib\DataBase;
use Core\lib\Registry;
use Core\Model;

class Roles extends Model
{

    private static ?array $permissions = [];

    public static function hasPermission($action): bool
    {
        self::fillPermissions();
        $user = Registry::get('user');
        if(in_array($action, self::$permissions[$user['role']])) return true;
        else{
            self::addError(400, 'Недостаточно прав для действия');
            return false;
        }
    }

    public static function fillPermissions(): void
    {
        $db = DataBase::instance()->getClient();
        $permissionsCollection = $db->water->permissions;
        $permissions = $permissionsCollection->find();
        foreach ($permissions as $permission){
            foreach ($permission['roles'] as $role){
                self::$permissions[$role][] = $permission['permission'];
            }
        }
    }





}