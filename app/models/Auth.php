<?php


namespace App\Models;


use Valitron\Validator;

class Auth extends User
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
     * @var array|string[][][]
     */
    protected array $rulesCheckLogin = [
        'require' => [
            ['login']
        ]
    ];

    /**Name of collection of database
     * @var string
     */
    private string $collectionName = 'users';

    /**Check user in database
     * @return bool
     */

    public function checkLogin(): bool
    {
        if ($this->validate(attributes: 'attributesCheckLogin', rules: 'rulesCheckLogin')) {
            $login = $this->attributesLogin['login'];
            $usersDB = $this->mongoClient->selectCollection($this->collectionName);
//            $usersDB->insertMany()
        }
    }

    /**Correctness check login and password on database
     * @return bool
     */
    public function auth(): bool
    {
        $db = $this->mongoClient->selectCollection('departments');
        $res = $db->findOne(array('department' => 'Цех 04'));
        debug($res['phone']);
    }

    /**Verification of entered data
     * @param string $attributes
     * @param string $rules
     * @return bool
     */
    public function validate(): bool
    {
        Validator::lang('ru');
        $validator = new Validator($this->attributesAuth);
        $validator->rules($this->$rules);
        return $validator->validate();
    }


    /**Filling the array with user data
     * @param array $data
     * @param string $subject
     * @return void
     */
    public function loadAttributes(array $data, string $subject): void
    {
        foreach ($this->$subject as $item => $value) {
            if (isset($data[$item])) {
                $this->$subject[$item] = $data[$item];
            }
        }
    }


}