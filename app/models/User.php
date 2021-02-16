<?php


namespace App\Models;


use Core\Model;
use Valitron\Validator;


class User extends Model
{

    /**Filling user-entered data
     * @var array|string[]
     */
    protected array $attributesAuth = [
        'login' => '',
        'password' => ''
    ];

    /**Filling user-entered data
     * @var array|string[]
     */
    protected array $attributesCheckLogin = [
        'login' => ''
    ];

    /**Rules for validate
     * @var array
     */
    protected array $rulesAuth = [
        'require' => [
            ['login'],
            ['password']
        ],
        'lengthMin' => [
            ['password', 6]
        ]
    ];

    /**Rules for validate
     * @var array|\string[][][]
     */
    protected array $rulesCheckLogin = [
        'require' => [
            ['login']
        ]
    ];

    /**Verification of entered data
     * @param string $attributes
     * @param string $rules
     * @return bool
     */
    public function validate(string $attributes, string $rules): bool
    {
        Validator::lang('ru');
        $validator = new Validator($this->$attributes);
        $validator->rules($this->$rules);
        return $validator->validate();
    }


}