<?php


namespace App\Models\Admin;


use Core\lib\HelperFirstLetterOfArrayToUpperCase;
use Core\Model;

class addPermission extends Model
{

    use HelperFirstLetterOfArrayToUpperCase;

    private array $attributesAddPermission = [
        'roles' => [],
        'permission' => ''
    ];

    private array $rulesAddPermission = [
        'required' => [
            ['roles'],
            ['permission']
        ],
        'array' => [['roles']],
        'checkRoles' => ['roles']
    ];

    public function addPermission(): bool
    {

        $this->loadAttributes($this->inputData, $this->attributesAddPermission);
        $this->attributesAddPermission['roles'] =
            is_array($this->attributesAddPermission['roles']) && count($this->attributesAddPermission['roles']) ?
                $this->firstLetterToUpperCase($this->attributesAddPermission['roles']) : null;
        if ($this->validate($this->attributesAddPermission, $this->rulesAddPermission)) {
            $checkPermission = $this->permissionsCollection->findOne(['permission' => $this->attributesAddPermission['permission']]);
            if ($checkPermission) {
                self::addError(code: 400, message: 'Разрешение на такое действие уже существует');
                return false;
            }
            $this->permissionsCollection->insertOne([
                'permission' => $this->attributesAddPermission['permission'],
                'roles' => $this->attributesAddPermission['roles']
            ]);
            return true;
        }
        self::addError(code: 400, message: 'Проверьте введенные данные');
        return false;
    }


}