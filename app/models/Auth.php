<?php


namespace App\Models;


use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;

use Valitron\Validator;
use MongoDBRef;

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
    protected array $attributesLogin = [
        'login' => ''
    ];

    /**Filling user-entered data
     * @var array|string[]
     */
    protected array $attributesCreatePassword = [
        'login' => '',
        'password' => '',
        'password_confirm' => ''
    ];

    /**Rules for validate
     * @var array
     */
    protected array $rulesAuth = [
        'required' => [
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
    protected array $rulesLogin = [
        'required' => [
            ['login']
        ]
    ];

    /**Rules for adding a password
     * @var array
     */
    protected array $rulesCreatePassword = [
        'required' => [
            ['login'],
            ['password'],
            ['password_confirm']
        ],
        'lengthMin' => [
            ['password', 6]
        ]
    ];

    /**Name of collection of database
     * @var string
     */
    private string $collectionName = 'users';

    /**Check user in database
     * @return bool
     */
    public function checkLogin(): void
    {
        if ($this->validateLogin())
        {
            $login = $this->attributesLogin['login'];
            $db = $this->mongoClient->selectCollection($this->dataBaseName, $this->collectionName);
            $result = $db->findOne(['email' => $login]);
            if(!$result)
            {
                http_response_code(400);
                echo json_encode(array("error" => array(
                    "code" => 400,
                    "message" => "Пользователь не найден. Обратитесь к системному администратору",
                    "error_code" => 2
                )), JSON_UNESCAPED_UNICODE);
            }
            elseif(!$result['password'])
            {
                $generator = new ComputerPasswordGenerator();
                $generator
                    ->setNumbers(true)
                    ->setLength(4)
                    ->setUppercase(false)
                    ->setLowercase(false);
                $key = $generator->generatePassword();
                $db->updateOne(
                    ['email' => $login],
                    ['$set' => ['key' => $key]]
                );


                http_response_code(401);
                echo json_encode(array("message" => "Пароль не задан, необходимо задать пароль. Ключ выслан на почтовый ящик ". $result['email']), JSON_UNESCAPED_UNICODE);
            }else {
                http_response_code(200);
            }

        }else{
            http_response_code(400);
            echo json_encode(array(
                "error" => array(
                    "code" => 400,
                    "message" => "Неверный логин",
                    "error_code" => 1
                )
            ), JSON_UNESCAPED_UNICODE);
        }

    }

    /**Check correctness login and password on database
     * @return void
     */
    public function auth(): void
    {
        if($this->validateAuth())
        {
            $login = $this->attributesAuth['login'];
            $password = $this->attributesAuth['password'];
            $db = $this->mongoClient->selectCollection($this->dataBaseName, $this->collectionName);
            $user = $db->findOne(['email' => $login]);
            if($user)
            {
                if(password_verify($password, $user['password']))
                {
                    $token = bin2hex(random_bytes(16));
                    $db->updateOne(
                        ['email' => $login],
                        ['$set' => ['token' => $token]]
                    );
                    http_response_code(200);
                    echo json_encode(array(
                        "message" => "Вы успешно авторизовались",
                        "token" => $token
                    ),JSON_UNESCAPED_UNICODE);
                }
            }
            else
            {
                http_response_code(400);
                echo json_encode(array(
                    "error" => array(
                        "code" => 400,
                        "message" => "Неверный логин или пароль",
                        "error_code" => 1
                    )
                ), JSON_UNESCAPED_UNICODE);
            }
        }else{
            http_response_code(400);
            echo json_encode(array(
                "error" => array(
                    "code" => 400,
                    "message" => "Неверный логин или пароль",
                    "error_code" => 1
                )
            ), JSON_UNESCAPED_UNICODE);
        }
    }

    public function createPassword(): void
    {
        if ($this->validatePassword()) {
            $login = $this->attributesCreatePassword['login'];
            $password = $this->attributesCreatePassword['password'];
            $password_confirm = $this->attributesCreatePassword['password_confirm'];
            if (($password === $password_confirm)) {
                $db = $this->mongoClient->selectCollection($this->dataBaseName, $this->collectionName);

            } else {
                http_response_code(400);
                echo json_encode(array(
                    "error" => array(
                        "code" => 400,
                        "message" => "Введенные пароли должны совпадать",
                        "error_code" => 1
                    )
                ), JSON_UNESCAPED_UNICODE);
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                "error" => array(
                    "code" => 400,
                    "message" => "Введенные пароли должны совпадать",
                    "error_code" => 1
                )
            ), JSON_UNESCAPED_UNICODE);
        }
    }


    /**Verification of entered data
     * @param string $attributes
     * @param string $rules
     * @return bool
     */
    public function validator(array $attributes, array $rules): bool
    {
        Validator::lang('ru');
        $validator = new Validator($attributes);
        $validator->rules($rules);
        return $validator->validate();
    }

    /**Wrapper over a validator
     * @return bool
     */
    public function validateLogin(): bool
    {
        return $this->validator($this->attributesLogin, $this->rulesLogin);
    }

    /**Wrapper over a validator
     * @return bool
     */
    public function validateAuth(): bool
    {
        return $this->validator($this->attributesAuth, $this->rulesAuth);
    }

    /**Wrapper over a validator
     * @return bool
     */
    public function validatePassword(): bool
    {
        return $this->validator($this->attributesCreatePassword, $this->rulesCreatePassword);
    }

    /**Filling the array with user data
     * @param array $data
     * @param array $attributes Transmitted by link
     * @return void
     */
    public function loadAttributes(array $data, array &$attributes): void
    {
        foreach ($attributes as $item => $value) {
            if (isset($data[$item])) {
                $attributes[$item] = $data[$item];
            }
        }
    }

    /**Wrapper over an attributes loader
     * @param $data
     * @return void
     */
    public function loadAttributesLogin($data): void
    {
        $this->loadAttributes($data, $this->attributesLogin);
    }

    /**Wrapper over an attributes loader
     * @param $data
     * @return void
     */
    public function loadAttributesAuth($data): void
    {
        $this->loadAttributes($data, $this->attributesAuth);
    }

    /**Wrapper over an attributes loader
     * @param $data
     * @return void
     */
    public function loadAttributesCreatePassword($data): void
    {
        $this->loadAttributes($data, $this->attributesCreatePassword);
    }


}