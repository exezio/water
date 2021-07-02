<?php


namespace App\Models\Admin;


use Core\Model;

class GetPermissions extends Model
{

    public function getPermissions(): bool|array
    {
        $permissions = $this->permissionsCollection->find()->toArray();
        if ($permissions) {
            $result = ['permissions' => []];
            foreach ($permissions as $item => $permission){
                array_push($result['permissions'], ['permission' => $permission['permission'], 'roles' => $permission['roles']]);
            }
            return $result;
        }
        self::addError(code: 400, message: 'Список пуст');
        return false;
    }

}