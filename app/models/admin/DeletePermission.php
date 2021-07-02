<?php


namespace App\Models\Admin;


use Core\Model;

class deletePermission extends Model
{

    private array $attributesDeletePermission = [
        'permission' => []
    ];

    private array $rulesDeletePermission = [
        'required' => [
            ['permission']
        ]
    ];

    public function deletePermission(): bool
    {
        $this->loadAttributes($this->inputData, $this->attributesDeletePermission);
        if ($this->validate($this->attributesDeletePermission, $this->rulesDeletePermission)) {
            $permission = $this->permissionsCollection->findOne(['permission' => $this->attributesDeletePermission['permission']]);
            if($permission){
                $this->permissionsCollection->deleteOne(['permission' => $this->attributesDeletePermission['permission']]);
                return true;
            }else self::addError(code: 400, message: "Действие {$this->attributesDeletePermission['permission']} не найдено");
        }
        self::addError(code: 400, message: 'Проверьте введенные данные');
        return false;
    }
}