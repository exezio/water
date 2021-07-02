<?php


namespace App\Models\Admin;


use Core\Model;

class PutPermission extends Model
{

    private array $attributesPutPermission = [
        'roles' => [],
        'permission' => '',
        'new_permission' => ''
    ];

    private array $rulesPutPermission = [
        'required' => [
            ['roles'],
            ['permission'],
            ['new_permission']
        ]
    ];

    public function putPermission(): bool
    {
        $this->loadAttributes($this->inputData, $this->attributesPutPermission);
        if ($this->validate($this->attributesPutPermission, $this->rulesPutPermission)) {
            $permission = $this->permissionsCollection->findOne([
                'permission' => $this->attributesPutPermission['permission']
            ]);

            if ($permission) {
                $this->permissionsCollection->updateOne(
                    ['_id' => $permission['_id']],
                    [
                        '$set' =>
                            [
                                'permission' => $this->attributesPutPermission['new_permission'],
                                'roles' => $this->attributesPutPermission['roles']
                            ]
                    ]);
                return true;
            } else {
                self::addError(code: 400, message: "Действие {$this->attributesPutPermission['permission']} не найдено");
            }

        } else {
            self::addError(code: 400, message: 'Проверьте введенные данные');
        }
        return false;
    }

}